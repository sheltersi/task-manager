<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorklogRequest;
use App\Models\Task;
use App\Models\Worklog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $weekParam = $request->input('week');
        $view = $request->input('view') === 'list' ? 'list' : 'grid';
        $search = (string) $request->input('search', '');
        $billable = in_array($request->input('billable'), ['billable', 'non_billable'])
            ? $request->input('billable')
            : 'all';
        $openTaskId = $request->integer('taskId') ?: null;
        $openDate = $request->input('date');

        $timesheet = $this->buildWeekData($user, $weekParam, $search, $billable);
        $todayStr = Carbon::today()->format('Y-m-d');

        return view('timesheets.index', compact(
            'timesheet',
            'view',
            'search',
            'billable',
            'todayStr',
            'openTaskId',
            'openDate'
        ));
    }

    public function store(WorklogRequest $request)
    {
        $validated = $request->validated();
        $task = Task::findOrFail($validated['task_id']);
        Gate::authorize('view', $task);

        $entry = $request->user()->worklogs()->create([
            'task_id' => $task->id,
            'work_date' => $validated['work_date'],
            'hours' => round((float) $validated['hours'], 2),
            'started_at' => $validated['started_at'] ?? '09:00',
            'description' => $validated['description'] ?? '',
            'billable' => (bool) ($validated['billable'] ?? true),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['entry' => $entry], 201);
        }

        return redirect($request->input('return_to') ?: route('timesheets.index'))
            ->with('success', sprintf('Logged %.1fh on TM-%d.', $entry->hours, $task->id));
    }

    public function update(WorklogRequest $request, Worklog $worklog)
    {
        Gate::authorize('update', $worklog);

        $validated = $request->validated();
        $task = Task::findOrFail($validated['task_id']);
        Gate::authorize('view', $task);

        $worklog->update([
            'task_id' => $task->id,
            'work_date' => $validated['work_date'],
            'hours' => round((float) $validated['hours'], 2),
            'started_at' => $validated['started_at'] ?? $worklog->started_at,
            'description' => $validated['description'] ?? '',
            'billable' => (bool) ($validated['billable'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['entry' => $worklog->fresh()]);
        }

        return back()->with('success', 'Worklog updated successfully.');
    }

    public function destroy(Request $request, Worklog $worklog)
    {
        Gate::authorize('delete', $worklog);

        $worklog->delete();

        if ($request->expectsJson()) {
            return response()->json(['deleted' => true]);
        }

        return back()->with('success', 'Worklog removed.');
    }

    public function export(Request $request): StreamedResponse
    {
        $timesheet = $this->buildWeekData(
            $request->user(),
            $request->input('week'),
            (string) $request->input('search', ''),
            (string) $request->input('billable', 'all')
        );

        $filename = 'timesheet-'.$timesheet['weekStart'].'.csv';

        return response()->streamDownload(function () use ($timesheet) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Started At', 'Task Key', 'Task Title', 'Description', 'Hours', 'Billable']);

            foreach ($timesheet['weekEntries'] as $entry) {
                fputcsv($handle, [
                    $entry['date'],
                    $entry['started_at'],
                    $entry['taskKey'],
                    $entry['task']['title'] ?? '',
                    $entry['description'],
                    number_format((float) $entry['hours'], 2, '.', ''),
                    $entry['billable'] ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    private function buildWeekData($user, ?string $weekParam, string $search, string $billable): array
    {
        $thisMonday = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $weekMonday = $weekParam
            ? Carbon::parse($weekParam)->startOfWeek(Carbon::MONDAY)
            : $thisMonday->copy();

        $weekStart = $weekMonday->format('Y-m-d');
        $weekEnd = $weekMonday->copy()->addDays(6)->format('Y-m-d');
        $prevWeek = $weekMonday->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekMonday->copy()->addWeek()->format('Y-m-d');
        $thisWeek = $thisMonday->format('Y-m-d');
        $todayStr = Carbon::today()->format('Y-m-d');

        $firstDay = $weekMonday->copy();
        $lastDay = $weekMonday->copy()->addDays(6);
        $weekLabel = $firstDay->format('M') === $lastDay->format('M')
            ? $firstDay->format('M j').' – '.$lastDay->format('j, Y')
            : $firstDay->format('M j').' – '.$lastDay->format('M j, Y');

        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $weekMonday->copy()->addDays($i);
            $dateStr = $d->format('Y-m-d');
            $weekDays[] = [
                'date' => $dateStr,
                'dayShort' => $d->format('D'),
                'dayFull' => $d->format('l'),
                'dayNum' => $d->format('d'),
                'monthShort' => $d->format('M'),
                'year' => (int) $d->format('Y'),
                'isToday' => $dateStr === $todayStr,
                'isWeekend' => $i >= 5,
                'targetHours' => $i >= 5 ? 0 : 8,
            ];
        }

        $query = $user->worklogs()
            ->with('task')
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->orderByDesc('work_date')
            ->orderByDesc('started_at');

        if ($billable === 'billable') {
            $query->where('billable', true);
        } elseif ($billable === 'non_billable') {
            $query->where('billable', false);
        }

        $trimmedSearch = trim(mb_strtolower($search));
        $entries = $query->get()->filter(function (Worklog $log) use ($trimmedSearch) {
            if ($trimmedSearch === '') {
                return true;
            }
            $taskTitle = mb_strtolower($log->task?->title ?? '');
            $taskKey = 'tm-'.$log->task_id;
            $desc = mb_strtolower($log->description ?? '');

            return str_contains($taskTitle, $trimmedSearch)
                || str_contains($taskKey, $trimmedSearch)
                || str_contains($desc, $trimmedSearch);
        })->values();

        $rowMap = [];
        foreach ($entries as $entry) {
            if (! $entry->task) {
                continue;
            }
            $taskId = $entry->task_id;
            $dateStr = $entry->work_date->format('Y-m-d');

            if (! isset($rowMap[$taskId])) {
                $daysObj = [];
                foreach ($weekDays as $wd) {
                    $daysObj[$wd['date']] = ['totalHours' => 0.0, 'billableHours' => 0.0, 'entries' => []];
                }
                $rowMap[$taskId] = [
                    'task' => $entry->task,
                    'taskKey' => 'TM-'.$taskId,
                    'days' => $daysObj,
                    'rowTotal' => 0.0,
                    'rowBillable' => 0.0,
                    'entryCount' => 0,
                ];
            }

            $serializedEntry = [
                'id' => $entry->id,
                'task_id' => $entry->task_id,
                'date' => $dateStr,
                'hours' => $entry->hours,
                'started_at' => $entry->started_at,
                'description' => $entry->description ?? '',
                'billable' => $entry->billable,
            ];

            if (isset($rowMap[$taskId]['days'][$dateStr])) {
                $rowMap[$taskId]['days'][$dateStr]['totalHours'] = round($rowMap[$taskId]['days'][$dateStr]['totalHours'] + $entry->hours, 2);
                if ($entry->billable) {
                    $rowMap[$taskId]['days'][$dateStr]['billableHours'] = round($rowMap[$taskId]['days'][$dateStr]['billableHours'] + $entry->hours, 2);
                }
                $rowMap[$taskId]['days'][$dateStr]['entries'][] = $serializedEntry;
            }

            $rowMap[$taskId]['rowTotal'] = round($rowMap[$taskId]['rowTotal'] + $entry->hours, 2);
            if ($entry->billable) {
                $rowMap[$taskId]['rowBillable'] = round($rowMap[$taskId]['rowBillable'] + $entry->hours, 2);
            }
            $rowMap[$taskId]['entryCount']++;
        }

        $matrixRows = collect(array_values($rowMap))->sortByDesc('rowTotal')->values()->all();

        $dailyTotals = array_map(function ($wd) use ($entries) {
            $dayEntries = $entries->filter(fn (Worklog $e) => $e->work_date->format('Y-m-d') === $wd['date']);
            $totalHours = round((float) $dayEntries->sum('hours'), 2);
            $billableHours = round((float) $dayEntries->where('billable', true)->sum('hours'), 2);
            $pctOfTarget = $wd['targetHours'] > 0
                ? min(100, (int) round(($totalHours / $wd['targetHours']) * 100))
                : ($totalHours > 0 ? 100 : 0);

            return array_merge($wd, [
                'totalHours' => $totalHours,
                'billableHours' => $billableHours,
                'entriesCount' => $dayEntries->count(),
                'pctOfTarget' => $pctOfTarget,
            ]);
        }, $weekDays);

        $totalHours = round((float) $entries->sum('hours'), 2);
        $billableHours = round((float) $entries->where('billable', true)->sum('hours'), 2);
        $nonBillableHours = round($totalHours - $billableHours, 2);
        $weeklyCapacity = 40;
        $capacityPct = min(100, (int) round(($totalHours / $weeklyCapacity) * 100));
        $remainingHours = max(0, round($weeklyCapacity - $totalHours, 2));
        $billablePct = $totalHours > 0 ? (int) round(($billableHours / $totalHours) * 100) : 0;

        $weekEntries = $entries->map(function (Worklog $entry) {
            return [
                'id' => $entry->id,
                'task_id' => $entry->task_id,
                'taskKey' => 'TM-'.$entry->task_id,
                'task' => $entry->task ? ['id' => $entry->task->id, 'title' => $entry->task->title] : null,
                'date' => $entry->work_date->format('Y-m-d'),
                'hours' => $entry->hours,
                'started_at' => $entry->started_at,
                'description' => $entry->description ?? '',
                'billable' => $entry->billable,
            ];
        })->all();

        $entriesByDay = array_reverse(array_map(function ($wd) use ($weekEntries) {
            $dayEntries = array_values(array_filter($weekEntries, fn ($e) => $e['date'] === $wd['date']));
            $dayTotal = round(array_sum(array_column($dayEntries, 'hours')), 2);

            return array_merge($wd, [
                'totalHours' => $dayTotal,
                'entries' => $dayEntries,
            ]);
        }, $weekDays));

        $allTasks = $user->tasks()->latest()->get()->map(fn (Task $t) => [
            'id' => $t->id,
            'title' => $t->title,
            'taskKey' => 'TM-'.$t->id,
        ])->all();

        return [
            'weekStart' => $weekStart,
            'thisWeek' => $thisWeek,
            'prevWeek' => $prevWeek,
            'nextWeek' => $nextWeek,
            'isCurrentWeek' => $weekStart === $thisWeek,
            'weekLabel' => $weekLabel,
            'weekDays' => $weekDays,
            'matrixRows' => $matrixRows,
            'dailyTotals' => $dailyTotals,
            'entriesByDay' => $entriesByDay,
            'weekEntries' => $weekEntries,
            'allTasks' => $allTasks,
            'summary' => [
                'totalHours' => $totalHours,
                'billableHours' => $billableHours,
                'nonBillableHours' => $nonBillableHours,
                'billablePct' => $billablePct,
                'weeklyCapacity' => $weeklyCapacity,
                'capacityPct' => $capacityPct,
                'remainingHours' => $remainingHours,
                'entryCount' => count($weekEntries),
            ],
        ];
    }
}
