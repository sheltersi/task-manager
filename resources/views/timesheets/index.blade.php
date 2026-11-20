<x-app-layout>
    @php
        $qsBase = function (array $overrides = []) use ($timesheet, $view, $search, $billable) {
            $params = array_filter([
                'week' => array_key_exists('week', $overrides) ? $overrides['week'] : $timesheet['weekStart'],
                'view' => array_key_exists('view', $overrides) ? $overrides['view'] : $view,
                'search' => array_key_exists('search', $overrides) ? $overrides['search'] : $search,
                'billable' => array_key_exists('billable', $overrides) ? $overrides['billable'] : $billable,
            ], fn ($v) => $v !== null && $v !== '' && $v !== 'all');

            return route('timesheets.index', $params);
        };

        $exportHref = route('timesheets.export', array_filter([
            'week' => $timesheet['weekStart'],
            'search' => $search,
            'billable' => $billable !== 'all' ? $billable : null,
        ]));

        $defaultTaskId = $openTaskId ?: ($timesheet['allTasks'][0]['id'] ?? null);
    @endphp

    <div x-data="timesheetWorkspace()"
         x-init="init()"
         @keydown.escape.window="closeModal()"
         class="bg-slate-50 min-h-[calc(100vh-4rem)] pb-14">

        <!-- Page Top Bar -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <a href="{{ route('dashboard') }}" class="hover:text-slate-800 transition-colors">Workspace</a>
                        <span aria-hidden="true">/</span>
                        <span class="text-slate-700 font-medium">Timesheets</span>
                    </div>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                        Weekly Timesheet
                    </h1>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ $exportHref }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium whitespace-nowrap shrink-0 transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        Export CSV
                    </a>

                    <button type="button"
                            @click="openNewModal({{ $defaultTaskId ? $defaultTaskId : 'null' }}, @js($todayStr))"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold whitespace-nowrap shrink-0 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Log Time
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-6">

            <!-- Weekly Capacity Summary Strip -->
            <section aria-label="Weekly summary" class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                    <div class="p-5">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Logged This Week</span>
                            <span class="font-mono tabular-nums text-xs font-medium text-slate-500">
                                {{ $timesheet['summary']['capacityPct'] }}% of {{ $timesheet['summary']['weeklyCapacity'] }}h
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-1.5">
                            <span class="font-mono tabular-nums text-2xl font-semibold text-slate-900">
                                {{ number_format($timesheet['summary']['totalHours'], 1) }}h
                            </span>
                            <span class="font-mono tabular-nums text-sm text-slate-400">
                                / {{ number_format($timesheet['summary']['weeklyCapacity'], 1) }}h
                            </span>
                        </div>
                        <div class="mt-3 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $timesheet['summary']['capacityPct'] >= 100 ? 'bg-emerald-600' : 'bg-indigo-600' }}"
                                 style="width: {{ $timesheet['summary']['capacityPct'] }}%"></div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Billable Hours</span>
                            <span class="font-mono tabular-nums text-xs font-medium text-emerald-700">
                                {{ $timesheet['summary']['billablePct'] }}% billable
                            </span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="font-mono tabular-nums text-2xl font-semibold text-slate-900">
                                {{ number_format($timesheet['summary']['billableHours'], 1) }}h
                            </span>
                        </div>
                        <p class="mt-2.5 text-xs text-slate-500 font-mono tabular-nums">
                            Non-billable: {{ number_format($timesheet['summary']['nonBillableHours'], 1) }}h
                        </p>
                    </div>

                    <div class="p-5">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Remaining Capacity</span>
                            <span class="text-xs text-slate-500">Target 8.0h/day</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="font-mono tabular-nums text-2xl font-semibold text-slate-900">
                                {{ number_format($timesheet['summary']['remainingHours'], 1) }}h
                            </span>
                        </div>
                        <p class="mt-2.5 text-xs {{ $timesheet['summary']['remainingHours'] == 0 ? 'text-emerald-700 font-medium' : 'text-slate-500' }}">
                            {{ $timesheet['summary']['remainingHours'] == 0 ? 'Weekly target reached' : 'Mon–Fri standard schedule' }}
                        </p>
                    </div>

                    <div class="p-5">
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="text-xs font-medium text-slate-500">Worklog Entries</span>
                            <span class="text-xs text-slate-500 font-mono tabular-nums">{{ count($timesheet['matrixRows']) }} active tasks</span>
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="font-mono tabular-nums text-2xl font-semibold text-slate-900">
                                {{ $timesheet['summary']['entryCount'] }}
                            </span>
                        </div>
                        <p class="mt-2.5 text-xs text-slate-500">
                            Period: {{ $timesheet['weekLabel'] }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Live Work Timer Bar -->
            <section aria-label="Live time tracker" class="bg-white border border-slate-200 rounded-xl px-4 py-3">
                <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-xs font-semibold text-slate-700 whitespace-nowrap">Live Tracker</span>
                        <span class="font-mono tabular-nums text-base font-semibold px-2.5 py-1 rounded-md border"
                              :class="timer.running ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 border-slate-200 text-slate-700'"
                              x-text="formatElapsed(timerElapsed)">00:00:00</span>
                    </div>

                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                        <div class="sm:col-span-5">
                            <select x-model="timer.taskId"
                                    aria-label="Task for timer"
                                    class="w-full rounded-lg border-slate-300 text-sm text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                                @if (count($timesheet['allTasks']) === 0)
                                    <option value="">No tasks available — create a task first</option>
                                @else
                                    @foreach ($timesheet['allTasks'] as $t)
                                        <option value="{{ $t['id'] }}">{{ $t['taskKey'] }} · {{ $t['title'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <input type="text"
                                   x-model="timer.description"
                                   placeholder="What are you working on right now?"
                                   aria-label="Worklog note for timer"
                                   class="w-full rounded-lg border-slate-300 text-sm text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="sm:col-span-2 flex items-center">
                            <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer select-none whitespace-nowrap">
                                <input type="checkbox" x-model="timer.billable" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Billable</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <template x-if="!timer.running && timerElapsed === 0">
                            <button type="button"
                                    @click="startTimer()"
                                    :disabled="!timer.taskId"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 disabled:opacity-40 text-white text-xs font-semibold whitespace-nowrap shrink-0 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                                Start Timer
                            </button>
                        </template>

                        <template x-if="timer.running">
                            <button type="button"
                                    @click="pauseTimer()"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 text-xs font-semibold whitespace-nowrap shrink-0 transition-colors">
                                Pause
                            </button>
                        </template>

                        <template x-if="!timer.running && timerElapsed > 0">
                            <button type="button"
                                    @click="resumeTimer()"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold whitespace-nowrap shrink-0 transition-colors">
                                Resume
                            </button>
                        </template>

                        <template x-if="timerElapsed > 0">
                            <button type="button"
                                    @click="stopAndLogTimer()"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold whitespace-nowrap shrink-0 transition-colors">
                                Stop &amp; Log
                            </button>
                        </template>

                        <template x-if="timerElapsed > 0">
                            <button type="button"
                                    @click="resetTimer()"
                                    class="px-2.5 py-2 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 whitespace-nowrap shrink-0">
                                Discard
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            <!-- Controls Toolbar -->
            <section aria-label="Timesheet controls" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden">
                        <a href="{{ $qsBase(['week' => $timesheet['prevWeek']]) }}"
                           aria-label="Previous week"
                           class="px-3 py-2 text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-r border-slate-200 transition-colors">
                            ←
                        </a>
                        <span class="px-4 py-2 text-sm font-semibold text-slate-900 whitespace-nowrap font-mono tabular-nums">
                            {{ $timesheet['weekLabel'] }}
                        </span>
                        <a href="{{ $qsBase(['week' => $timesheet['nextWeek']]) }}"
                           aria-label="Next week"
                           class="px-3 py-2 text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-l border-slate-200 transition-colors">
                            →
                        </a>
                    </div>

                    @if (! $timesheet['isCurrentWeek'])
                        <a href="{{ $qsBase(['week' => $timesheet['thisWeek']]) }}"
                           class="px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-indigo-600 whitespace-nowrap shrink-0 transition-colors">
                            This Week
                        </a>
                    @endif

                    <form method="GET" action="{{ route('timesheets.index') }}" class="inline-flex items-center">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="billable" value="{{ $billable }}">
                        <input type="date"
                               name="week"
                               value="{{ $timesheet['weekStart'] }}"
                               onchange="this.form.submit()"
                               aria-label="Jump to week containing date"
                               class="rounded-lg border-slate-200 bg-white text-xs font-mono tabular-nums text-slate-700 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </form>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('timesheets.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="week" value="{{ $timesheet['weekStart'] }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <input type="hidden" name="billable" value="{{ $billable }}">
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Filter tasks or notes..."
                                   class="w-52 sm:w-60 rounded-lg border-slate-200 bg-white pl-3 pr-8 py-2 text-xs text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                            @if ($search)
                                <a href="{{ $qsBase(['search' => '']) }}"
                                   aria-label="Clear search"
                                   class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-slate-700 text-xs">✕</a>
                            @endif
                        </div>
                    </form>

                    <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-lg">
                        <a href="{{ $qsBase(['billable' => 'all']) }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap shrink-0 transition-colors {{ $billable === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            All
                        </a>
                        <a href="{{ $qsBase(['billable' => 'billable']) }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap shrink-0 transition-colors {{ $billable === 'billable' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Billable
                        </a>
                        <a href="{{ $qsBase(['billable' => 'non_billable']) }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap shrink-0 transition-colors {{ $billable === 'non_billable' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Non-billable
                        </a>
                    </div>

                    <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-lg">
                        <a href="{{ $qsBase(['view' => 'grid']) }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap shrink-0 transition-colors {{ $view === 'grid' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Grid
                        </a>
                        <a href="{{ $qsBase(['view' => 'list']) }}"
                           class="px-3 py-1.5 text-xs font-medium rounded-md whitespace-nowrap shrink-0 transition-colors {{ $view === 'list' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            Timeline
                        </a>
                    </div>
                </div>
            </section>

            @if ($view === 'grid')
                <!-- TEMPO MATRIX GRID VIEW -->
                <section aria-label="Weekly timesheet matrix" class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/80">
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 min-w-[260px] w-[32%]">
                                        Task / Issue
                                    </th>
                                    @foreach ($timesheet['weekDays'] as $wd)
                                        <th class="py-2.5 px-2 text-center min-w-[84px] {{ $wd['isToday'] ? 'bg-indigo-50/70 text-indigo-700' : ($wd['isWeekend'] ? 'text-slate-400' : 'text-slate-600') }}">
                                            <div class="text-xs font-semibold">{{ $wd['dayShort'] }}</div>
                                            <div class="text-[11px] font-mono tabular-nums mt-0.5 {{ $wd['isToday'] ? 'text-indigo-600 font-semibold' : 'text-slate-400' }}">
                                                {{ $wd['monthShort'] }} {{ $wd['dayNum'] }}
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="py-3 px-4 text-right text-xs font-semibold text-slate-700 min-w-[88px] border-l border-slate-200 bg-slate-50">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                @forelse ($timesheet['matrixRows'] as $row)
                                    <tr class="hover:bg-slate-50/60 transition-colors group">
                                        <td class="py-2.5 px-4 align-middle">
                                            <div class="flex items-start gap-2.5">
                                                <span class="font-mono tabular-nums text-xs font-medium text-indigo-600 pt-0.5 shrink-0">
                                                    {{ $row['taskKey'] }}
                                                </span>
                                                <div class="min-w-0">
                                                    <a href="{{ route('tasks.show', $row['task']) }}"
                                                       class="text-sm font-medium text-slate-900 hover:text-indigo-600 line-clamp-1">
                                                        {{ $row['task']->title }}
                                                    </a>
                                                    <div class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                                                        <span>{{ ucfirst(str_replace('_', ' ', $row['task']->status)) }}</span>
                                                        <span aria-hidden="true">·</span>
                                                        <span class="font-mono tabular-nums">{{ $row['entryCount'] }} {{ Str::plural('log', $row['entryCount']) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        @foreach ($timesheet['weekDays'] as $wd)
                                            @php $cell = $row['days'][$wd['date']]; @endphp
                                            <td class="p-1.5 text-center align-middle {{ $wd['isToday'] ? 'bg-indigo-50/25' : ($wd['isWeekend'] ? 'bg-slate-50/40' : '') }}">
                                                <button type="button"
                                                        @click="openCellModal({{ $row['task']->id }}, @js($wd['date']), @js($cell['entries']))"
                                                        class="w-full h-10 rounded-lg font-mono tabular-nums text-xs transition-colors flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 {{ $cell['totalHours'] > 0 ? 'bg-indigo-50 hover:bg-indigo-100 text-indigo-900 font-semibold border border-indigo-200/70' : 'text-slate-300 hover:bg-slate-100 hover:text-slate-600 border border-transparent' }}">
                                                    @if ($cell['totalHours'] > 0)
                                                        <span>{{ rtrim(rtrim(number_format($cell['totalHours'], 2), '0'), '.') }}h</span>
                                                    @else
                                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">+</span>
                                                        <span class="group-hover:hidden">—</span>
                                                    @endif
                                                </button>
                                            </td>
                                        @endforeach

                                        <td class="py-2.5 px-4 text-right align-middle border-l border-slate-200 bg-slate-50/40">
                                            <span class="font-mono tabular-nums text-sm font-semibold text-slate-900">
                                                {{ rtrim(rtrim(number_format($row['rowTotal'], 2), '0'), '.') }}h
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center">
                                            <p class="text-sm font-medium text-slate-800">No time logged for this week yet</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                Click any task or press Log Time to record your hours for {{ $timesheet['weekLabel'] }}.
                                            </p>
                                            <button type="button"
                                                    @click="openNewModal({{ $defaultTaskId ? $defaultTaskId : 'null' }}, @js($timesheet['weekStart']))"
                                                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-colors">
                                                Log First Entry
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr class="border-t-2 border-slate-200 bg-slate-50">
                                    <td class="py-3 px-4 text-xs font-semibold text-slate-700">
                                        <div class="flex items-center justify-between gap-2">
                                            <span>Daily Total</span>
                                            <button type="button"
                                                    @click="openNewModal({{ $defaultTaskId ? $defaultTaskId : 'null' }}, @js($todayStr))"
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">
                                                + Add task row
                                            </button>
                                        </div>
                                    </td>
                                    @foreach ($timesheet['dailyTotals'] as $dt)
                                        <td class="pt-3 pb-2 px-2 text-center align-bottom {{ $dt['isToday'] ? 'bg-indigo-50/60' : '' }}">
                                            <div class="font-mono tabular-nums text-xs font-semibold {{ $dt['totalHours'] >= 8 ? 'text-emerald-700' : ($dt['totalHours'] > 0 ? 'text-slate-900' : 'text-slate-400') }}">
                                                {{ number_format($dt['totalHours'], 1) }}h
                                            </div>
                                            <div class="mt-2 h-1 w-full bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $dt['totalHours'] >= 8 ? 'bg-emerald-600' : ($dt['totalHours'] > 0 ? 'bg-indigo-600' : 'bg-transparent') }}"
                                                     style="width: {{ $dt['pctOfTarget'] }}%"></div>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="py-3 px-4 text-right align-middle border-l border-slate-200 bg-slate-100/80">
                                        <span class="font-mono tabular-nums text-sm font-bold text-slate-900">
                                            {{ number_format($timesheet['summary']['totalHours'], 1) }}h
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            @else
                <!-- TIMELINE VIEW -->
                <section aria-label="Daily timeline worklogs" class="space-y-4">
                    @foreach ($timesheet['entriesByDay'] as $day)
                        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-sm font-semibold {{ $day['isToday'] ? 'text-indigo-700' : 'text-slate-900' }}">
                                        {{ $day['dayFull'] }}, {{ $day['monthShort'] }} {{ (int) $day['dayNum'] }}
                                    </span>
                                    @if ($day['isToday'])
                                        <span class="text-xs font-semibold text-indigo-600">Today</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-4">
                                    <span class="font-mono tabular-nums text-xs font-semibold {{ $day['totalHours'] >= 8 ? 'text-emerald-700' : 'text-slate-700' }}">
                                        {{ number_format($day['totalHours'], 1) }}h logged
                                    </span>
                                    <button type="button"
                                            @click="openNewModal({{ $defaultTaskId ? $defaultTaskId : 'null' }}, @js($day['date']))"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 whitespace-nowrap">
                                        + Log time
                                    </button>
                                </div>
                            </div>

                            @if (count($day['entries']) > 0)
                                <div class="divide-y divide-slate-200">
                                    @foreach ($day['entries'] as $entry)
                                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 transition-colors">
                                            <div class="min-w-0 flex items-start gap-3">
                                                <span class="font-mono tabular-nums text-xs text-slate-400 pt-0.5 shrink-0">
                                                    {{ $entry['started_at'] }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                                        <span class="font-mono tabular-nums text-xs font-semibold text-indigo-600">{{ $entry['taskKey'] }}</span>
                                                        <a href="{{ route('tasks.show', $entry['task_id']) }}" class="font-medium text-slate-900 hover:text-indigo-600 truncate">
                                                            {{ $entry['task']['title'] ?? 'Task' }}
                                                        </a>
                                                        <span aria-hidden="true" class="text-slate-300">·</span>
                                                        <span class="text-xs {{ $entry['billable'] ? 'text-emerald-700 font-medium' : 'text-slate-500' }}">
                                                            {{ $entry['billable'] ? 'Billable' : 'Non-billable' }}
                                                        </span>
                                                    </div>
                                                    @if (! empty($entry['description']))
                                                        <p class="mt-0.5 text-xs text-slate-600">{{ $entry['description'] }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4 shrink-0">
                                                <span class="font-mono tabular-nums text-sm font-semibold text-slate-900">
                                                    {{ rtrim(rtrim(number_format($entry['hours'], 2), '0'), '.') }}h
                                                </span>

                                                <button type="button"
                                                        @click="openEditModal(@js($entry))"
                                                        class="text-xs font-medium text-slate-600 hover:text-indigo-700">
                                                    Edit
                                                </button>

                                                <button type="button"
                                                        @click="deleteWorklog({{ $entry['id'] }})"
                                                        class="text-xs font-medium text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-4 text-xs text-slate-400">
                                    No entries recorded on this day.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </section>
            @endif

            <!-- Time Distribution Breakdown -->
            @if (count($timesheet['matrixRows']) > 0)
                <section aria-label="Time distribution by task" class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">Time Distribution by Task</h2>
                            <p class="text-xs text-slate-500">Proportional share of logged hours for {{ $timesheet['weekLabel'] }}</p>
                        </div>
                        <span class="font-mono tabular-nums text-xs font-medium text-slate-600">
                            Total {{ number_format($timesheet['summary']['totalHours'], 1) }}h
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach ($timesheet['matrixRows'] as $row)
                            @php
                                $pct = $timesheet['summary']['totalHours'] > 0
                                    ? (int) round(($row['rowTotal'] / $timesheet['summary']['totalHours']) * 100)
                                    : 0;
                            @endphp
                            <div class="px-5 py-3 flex items-center gap-4">
                                <div class="w-48 sm:w-72 shrink-0 min-w-0">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="font-mono tabular-nums text-indigo-600 font-medium shrink-0">{{ $row['taskKey'] }}</span>
                                        <a href="{{ route('tasks.show', $row['task']) }}" class="font-medium text-slate-800 hover:text-indigo-600 truncate">
                                            {{ $row['task']->title }}
                                        </a>
                                    </div>
                                </div>

                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>

                                <div class="w-28 text-right shrink-0 font-mono tabular-nums text-xs">
                                    <span class="font-semibold text-slate-900">{{ number_format($row['rowTotal'], 1) }}h</span>
                                    <span class="text-slate-400 ml-1">({{ $pct }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <!-- LOG / EDIT WORKLOG MODAL -->
        <div x-show="modal.open"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
             @click.self="closeModal()">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-lg overflow-hidden"
                 @click.stop>
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900" x-text="modal.mode === 'edit' ? 'Edit Worklog' : 'Log Time'">Log Time</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="modal.date"></p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-700 text-sm px-2 py-1">✕</button>
                </div>

                <template x-if="modal.cellEntries.length > 0 && modal.mode === 'create'">
                    <div class="px-6 py-3 bg-slate-50 border-b border-slate-200 space-y-2 max-h-48 overflow-y-auto">
                        <div class="text-xs font-semibold text-slate-600">Recorded entries on this date</div>
                        <template x-for="item in modal.cellEntries" :key="item.id">
                            <div class="flex items-center justify-between gap-3 bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono tabular-nums font-semibold text-slate-900" x-text="Number(item.hours).toFixed(2).replace(/\.00$/, '.0') + 'h'"></span>
                                        <span class="text-slate-300">·</span>
                                        <span class="font-mono tabular-nums text-slate-500" x-text="item.started_at"></span>
                                        <span class="text-slate-300">·</span>
                                        <span :class="item.billable ? 'text-emerald-700 font-medium' : 'text-slate-500'"
                                              x-text="item.billable ? 'Billable' : 'Non-billable'"></span>
                                    </div>
                                    <p class="text-slate-600 truncate mt-0.5" x-text="item.description || 'No description'"></p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" @click="switchToEditEntry(item)" class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                                    <button type="button" @click="deleteWorklog(item.id)" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <form @submit.prevent="submitModal()" class="p-6 space-y-4">
                    <template x-if="modal.error">
                        <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700" x-text="modal.error"></div>
                    </template>

                    <div>
                        <label for="modal_task_id" class="block text-xs font-medium text-slate-700 mb-1">Task</label>
                        <select id="modal_task_id" x-model="modal.taskId" required
                                class="w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($timesheet['allTasks'] as $t)
                                <option value="{{ $t['id'] }}">{{ $t['taskKey'] }} · {{ $t['title'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_date" class="block text-xs font-medium text-slate-700 mb-1">Date</label>
                            <input id="modal_date" type="date" x-model="modal.date" required
                                   class="w-full rounded-lg border-slate-300 text-sm font-mono tabular-nums text-slate-900 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="modal_started_at" class="block text-xs font-medium text-slate-700 mb-1">Start Time</label>
                            <input id="modal_started_at" type="time" x-model="modal.startedAt"
                                   class="w-full rounded-lg border-slate-300 text-sm font-mono tabular-nums text-slate-900 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="modal_hours" class="block text-xs font-medium text-slate-700">Duration (hours)</label>
                            <span class="font-mono tabular-nums text-xs text-slate-500" x-text="Number(modal.hours || 0).toFixed(2) + 'h'"></span>
                        </div>
                        <input id="modal_hours" type="number" step="0.25" min="0.25" max="24" x-model="modal.hours" required
                               class="w-full rounded-lg border-slate-300 text-sm font-mono tabular-nums text-slate-900 focus:ring-indigo-500 focus:border-indigo-500">
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ([0.5, 1, 1.5, 2, 4, 8] as $preset)
                                <button type="button"
                                        @click="modal.hours = '{{ $preset }}'"
                                        :class="Number(modal.hours) === {{ $preset }} ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded-md border font-mono tabular-nums text-xs font-medium transition-colors">
                                    {{ $preset }}h
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="modal_description" class="block text-xs font-medium text-slate-700 mb-1">Work Description</label>
                        <textarea id="modal_description" rows="2" x-model="modal.description"
                                  placeholder="Describe what you accomplished..."
                                  class="w-full rounded-lg border-slate-300 text-sm text-slate-900 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-700 cursor-pointer">
                            <input type="checkbox" x-model="modal.billable" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Mark as billable time</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                        <button type="button"
                                @click="closeModal()"
                                class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="modal.saving"
                                class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-semibold">
                            <span x-text="modal.saving ? 'Saving...' : (modal.mode === 'edit' ? 'Update Entry' : 'Save Worklog')">Save Worklog</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.timesheetWorkspace = function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const defaultTaskId = @js((string) ($defaultTaskId ?? ''));
            const todayStr = @js($todayStr);
            const openTaskId = @js($openTaskId);
            const openDate = @js($openDate ?: $todayStr);
            const returnUrl = @js($qsBase());

            return {
                modal: {
                    open: false,
                    mode: 'create',
                    entryId: null,
                    taskId: defaultTaskId,
                    date: openDate,
                    startedAt: '09:00',
                    hours: '1.0',
                    description: '',
                    billable: true,
                    cellEntries: [],
                    saving: false,
                    error: '',
                },
                timer: {
                    running: false,
                    startedTimestamp: null,
                    accumulatedMs: 0,
                    taskId: defaultTaskId,
                    description: '',
                    billable: true,
                },
                timerElapsed: 0,
                timerInterval: null,

                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('tm_live_timer') || 'null');
                        if (saved) {
                            this.timer = { ...this.timer, ...saved };
                        }
                    } catch {}

                    this.syncElapsed();
                    this.timerInterval = setInterval(() => this.syncElapsed(), 1000);

                    if (openTaskId) {
                        this.openNewModal(openTaskId, openDate);
                    }
                },

                persistTimer() {
                    try {
                        localStorage.setItem('tm_live_timer', JSON.stringify(this.timer));
                    } catch {}
                },

                syncElapsed() {
                    const runningDelta = this.timer.running && this.timer.startedTimestamp
                        ? Math.max(0, Date.now() - this.timer.startedTimestamp)
                        : 0;
                    this.timerElapsed = Math.floor((this.timer.accumulatedMs + runningDelta) / 1000);
                },

                formatElapsed(totalSeconds) {
                    const hrs = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                    const mins = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                    const secs = String(totalSeconds % 60).padStart(2, '0');
                    return `${hrs}:${mins}:${secs}`;
                },

                startTimer() {
                    if (!this.timer.taskId) return;
                    this.timer.running = true;
                    this.timer.startedTimestamp = Date.now();
                    this.persistTimer();
                    this.syncElapsed();
                },

                pauseTimer() {
                    if (!this.timer.running) return;
                    const delta = this.timer.startedTimestamp ? Math.max(0, Date.now() - this.timer.startedTimestamp) : 0;
                    this.timer.accumulatedMs += delta;
                    this.timer.running = false;
                    this.timer.startedTimestamp = null;
                    this.persistTimer();
                    this.syncElapsed();
                },

                resumeTimer() {
                    this.timer.running = true;
                    this.timer.startedTimestamp = Date.now();
                    this.persistTimer();
                    this.syncElapsed();
                },

                resetTimer() {
                    this.timer.running = false;
                    this.timer.startedTimestamp = null;
                    this.timer.accumulatedMs = 0;
                    this.timerElapsed = 0;
                    this.persistTimer();
                },

                async stopAndLogTimer() {
                    this.pauseTimer();
                    const rawHours = this.timerElapsed / 3600;
                    const roundedHours = Math.max(0.25, Math.ceil(rawHours * 4) / 4);
                    this.openNewModal(this.timer.taskId, todayStr);
                    this.modal.hours = String(roundedHours);
                    this.modal.description = this.timer.description || 'Tracked work session';
                    this.modal.billable = Boolean(this.timer.billable);
                    this.resetTimer();
                },

                openNewModal(taskId, dateStr) {
                    this.modal.mode = 'create';
                    this.modal.entryId = null;
                    this.modal.taskId = String(taskId || defaultTaskId);
                    this.modal.date = dateStr || todayStr;
                    this.modal.startedAt = '09:00';
                    this.modal.hours = '1.0';
                    this.modal.description = '';
                    this.modal.billable = true;
                    this.modal.cellEntries = [];
                    this.modal.error = '';
                    this.modal.open = true;
                },

                openCellModal(taskId, dateStr, entries) {
                    this.modal.mode = 'create';
                    this.modal.entryId = null;
                    this.modal.taskId = String(taskId);
                    this.modal.date = dateStr;
                    this.modal.startedAt = '09:00';
                    this.modal.hours = '1.0';
                    this.modal.description = '';
                    this.modal.billable = true;
                    this.modal.cellEntries = Array.isArray(entries) ? entries : [];
                    this.modal.error = '';
                    this.modal.open = true;
                },

                openEditModal(entry) {
                    this.modal.mode = 'edit';
                    this.modal.entryId = entry.id;
                    this.modal.taskId = String(entry.task_id);
                    this.modal.date = entry.date;
                    this.modal.startedAt = entry.started_at || '09:00';
                    this.modal.hours = String(entry.hours);
                    this.modal.description = entry.description || '';
                    this.modal.billable = Boolean(entry.billable);
                    this.modal.cellEntries = [];
                    this.modal.error = '';
                    this.modal.open = true;
                },

                switchToEditEntry(entry) {
                    this.modal.mode = 'edit';
                    this.modal.entryId = entry.id;
                    this.modal.taskId = String(entry.task_id);
                    this.modal.date = entry.date;
                    this.modal.startedAt = entry.started_at || '09:00';
                    this.modal.hours = String(entry.hours);
                    this.modal.description = entry.description || '';
                    this.modal.billable = Boolean(entry.billable);
                    this.modal.error = '';
                },

                closeModal() {
                    this.modal.open = false;
                    this.modal.error = '';
                },

                async submitModal() {
                    this.modal.saving = true;
                    this.modal.error = '';

                    const url = this.modal.mode === 'edit'
                        ? `/timesheets/worklogs/${this.modal.entryId}`
                        : '/timesheets/worklogs';
                    const method = this.modal.mode === 'edit' ? 'PUT' : 'POST';

                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                task_id: Number(this.modal.taskId),
                                work_date: this.modal.date,
                                started_at: this.modal.startedAt,
                                hours: Number(this.modal.hours),
                                description: this.modal.description,
                                billable: Boolean(this.modal.billable),
                            }),
                        });

                        const payload = await res.json();
                        if (!res.ok) {
                            this.modal.error = payload.message || payload.error || 'Could not save worklog.';
                            this.modal.saving = false;
                            return;
                        }

                        window.location.href = returnUrl;
                    } catch (err) {
                        this.modal.error = 'Network error while saving worklog.';
                        this.modal.saving = false;
                    }
                },

                async deleteWorklog(entryId) {
                    const confirmDelete = async () => {
                        const res = await fetch(`/timesheets/worklogs/${entryId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        });
                        if (res.ok) {
                            window.location.href = returnUrl;
                        }
                    };

                    if (window.Swal) {
                        const result = await window.Swal.fire({
                            title: 'Remove worklog?',
                            text: 'This time entry will be permanently deleted.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, remove it',
                        });
                        if (result.isConfirmed) {
                            await confirmDelete();
                        }
                    } else {
                        await confirmDelete();
                    }
                },
            };
        };
    </script>
</x-app-layout>
