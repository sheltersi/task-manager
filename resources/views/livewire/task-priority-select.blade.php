<div>
    <select
        wire:change="setPriority($event.target.value)"
        class="text-xs font-semibold rounded-full border-gray-300 dark:bg-gray-900 focus:border-black/20 cursor-pointer"
    >
        @foreach([
            1 => 'Lowest',
            2 => 'Low',
            3 => 'Medium',
            4 => 'High',
            5 => 'Highest',
        ] as $p => $label)
            <option value="{{ $p }}" @selected($priority === $p)>
                P{{ $p }} — {{ $label }}
            </option>
        @endforeach
    </select>
</div>
