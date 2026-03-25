<x-app-layout>
    <div class="min-h-screen bg-[#F7F6F2] p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-7">
            <h1 class="text-[15px] font-medium tracking-tight text-gray-900">
                <span class="inline-block w-2 h-2 rounded-full bg-gray-900 mr-2 align-middle"></span>
                taskly
            </h1>
            <div class="w-7 h-7 rounded-full bg-gray-100 border border-black/10 flex items-center justify-center text-[11px] font-medium text-gray-500">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-4 gap-2.5 mb-6">
            @foreach([
                ['label' => 'Total',       'value' => $tasks->total()],
                ['label' => 'In progress', 'value' => auth()->user()->tasks()->where('status','in_progress')->count()],
                ['label' => 'In review',   'value' => auth()->user()->tasks()->where('status','in_review')->count()],
                ['label' => 'Completed',   'value' => auth()->user()->tasks()->where('status','completed')->count()],
            ] as $stat)
            <div class="bg-white border border-black/[0.07] rounded-xl p-4">
                <div class="text-[11px] uppercase tracking-wide text-gray-400 mb-1">{{ $stat['label'] }}</div>
                <div class="text-[22px] font-medium font-mono tracking-tight text-gray-900">{{ str_pad($stat['value'], 2, '0', STR_PAD_LEFT) }}</div>
            </div>
            @endforeach
        </div>

        {{-- Task list component --}}
        <livewire:task-list />
    </div>
</x-app-layout>
