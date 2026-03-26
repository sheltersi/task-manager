<div x-data="{ showFilters: false }" class="mx-10 my-10 md:mx-24">
@if($this->totalShared > 0)

{{-- Section Header --}}
<div class="relative mb-6">
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="relative">
<div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl blur opacity-30"></div>
<div class="relative bg-gradient-to-r from-blue-500/10 to-indigo-500/10 border border-blue-200/50 rounded-xl px-4 py-2">
<h2 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.88 12.938 9 12.482 9 12c0-.482-.12-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.317m-6.632-6l6.632-3.317m0 0a3 3 0 100-2.684 3 3 0 000 2.684m-6.316 0a3 3 0 110-2.684 3 3 0 010 2.684m0 0l6.632 3.317m-6.632 6l6.632-3.317" />
</svg>
Shared with me
</h2>
</div>
</div>
<span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2.5 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs font-bold shadow-md shadow-blue-500/25">
{{ $this->totalShared }}
</span>
</div>

<button @click="showFilters = !showFilters"
class="text-xs font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1.5 transition-colors">
<svg class="w-4 h-4" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
</svg>
Filters
</button>
</div>
</div>

{{-- Toolbar --}}
<div class="bg-gradient-to-br from-white via-white to-gray-50/50 border border-gray-200/60 rounded-2xl p-4 shadow-sm shadow-gray-100/50 mb-4">
<div class="flex flex-col gap-3">
{{-- Search Row --}}
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
</svg>
</div>
<input
wire:model.live.debounce.300ms="search"
type="text"
placeholder="Search shared tasks..."
class="w-full h-[42px] pl-10 pr-4 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 transition-all"
/>
</div>

{{-- Filters Row --}}
<div class="flex flex-wrap items-center gap-2.5" x-show="showFilters" x-collapse>
<select
wire:model.live="status"
class="h-[38px] bg-white border border-gray-200 rounded-xl px-3.5 text-sm text-gray-700 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 transition-all cursor-pointer hover:border-gray-300">
<option value="">All statuses</option>
<option value="to_do">To do</option>
<option value="in_progress">In progress</option>
<option value="in_review">In review</option>
<option value="completed">Completed</option>
</select>
<select
wire:model.live="sort"
class="h-[38px] bg-white border border-gray-200 rounded-xl px-3.5 text-sm text-gray-700 outline-none focus:border-blue-300 focus:ring-4 focus:ring-blue-100 transition-all cursor-pointer hover:border-gray-300">
<option value="latest">Latest first</option>
<option value="due_date">By due date</option>
<option value="priority">By priority</option>
</select>
</div>
</div>
</div>

{{-- Task Cards --}}
<div class="space-y-3">
@forelse($this->sharedTasks as $task)
<div
wire:key="{{ $task->id }}"
class="group relative bg-white border border-gray-200/60 rounded-2xl p-4 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300 overflow-hidden">

{{-- Hover gradient border effect --}}
<div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>

<div class="relative flex items-start gap-4">
{{-- Left: Avatar/Icon --}}
<div class="shrink-0">
<div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center">
<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
</svg>
</div>
</div>

{{-- Center: Content --}}
<div class="flex-1 min-w-0 py-0.5">
<div class="flex items-start justify-between gap-3 mb-2">
<a href="{{ route('tasks.show', $task) }}" wire:navigate
class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors line-clamp-1">
{{ $task->title }}
</a>

{{-- Status Badge --}}
@php
$statusMap = [
'to_do' => ['label' => 'To do', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200'],
'in_progress' => ['label' => 'In progress', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
'in_review' => ['label' => 'In review', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
'completed' => ['label' => 'Completed', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
];
$s = $statusMap[$task->status] ?? $statusMap['to_do'];
@endphp
<span class="shrink-0 inline-flex items-center gap-1.5 h-6 px-2.5 rounded-full text-[10px] font-semibold {{ $s['bg'] }} {{ $s['text'] }} border {{ $s['border'] }}">
<span class="w-1.5 h-1.5 rounded-full currentColor opacity-60"></span>
{{ $s['label'] }}
</span>
</div>

{{-- Meta info --}}
<div class="flex items-center gap-3 text-xs text-gray-500">
<div class="flex items-center gap-1.5">
<div class="w-5 h-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-[10px] font-semibold text-gray-600">
{{ strtoupper(substr($task->user->name, 0, 1)) }}
</div>
<span>Owned by <span class="font-medium text-gray-700">{{ $task->user->name }}</span></span>
</div>
<span class="text-gray-300">·</span>
<div class="flex items-center gap-1.5">
<svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
</svg>
<span class="font-medium">{{ $task->due_date ? 'Due '.$task->due_date->format('M j, Y') : 'No due date' }}</span>
</div>
</div>
</div>

{{-- Right: Role Badge --}}
@php $role = $task->shares->first()?->role @endphp
@php
$roleStyles = [
'commenter' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
'viewer' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
];
$r = $roleStyles[$role] ?? $roleStyles['viewer'];
@endphp
<div class="shrink-0">
<span class="inline-flex items-center gap-1.5 h-7 px-3 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $r['bg'] }} {{ $r['text'] }} border {{ $r['border'] }}">
<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $r['icon'] }}" />
</svg>
{{ ucfirst($role) }}
</span>
</div>
</div>
</div>
@empty
<div class="text-center py-16">
<div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center">
<svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
</svg>
</div>
<p class="text-sm font-medium text-gray-600 mb-1">No shared tasks found</p>
<p class="text-xs text-gray-400">Try adjusting your search or filters</p>
</div>
@endforelse
</div>

{{-- Pagination --}}
@if($this->sharedTasks->hasPages())
<div class="mt-6 flex justify-center">
<nav class="inline-flex items-center gap-1.5 bg-white rounded-xl border border-gray-200 p-1.5 shadow-sm">
{{ $this->sharedTasks->links('livewire::tailwind') }}
</nav>
</div>
@endif

@else
{{-- Empty state when no tasks shared at all --}}
<div class="text-center py-20">
<div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center">
<svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.88 12.938 9 12.482 9 12c0-.482-.12-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.317m-6.632-6l6.632-3.317m0 0a3 3 0 100-2.684 3 3 0 000 2.684m-6.316 0a3 3 0 110-2.684 3 3 0 010 2.684m0 0l6.632 3.317m-6.632 6l6.632-3.317" />
</svg>
</div>
<h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks shared yet</h3>
<p class="text-sm text-gray-500 max-w-sm mx-auto">
Tasks shared with you by others will appear here. Ask your teammates to share their tasks!
</p>
</div>
@endif
</div>
