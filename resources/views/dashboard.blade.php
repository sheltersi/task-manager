<x-app-layout>
    <div class="my-4 md:mx-12 mx-4 bg-[#F7F6F2] p-6">

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

        {{-- Task list component (owns stats too) --}}
        <livewire:task-list />
    </div>
</x-app-layout>
