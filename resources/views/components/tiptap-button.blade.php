@props([
    'label',
    'clickAction' => null,
    'isActive' => 'false',
    'showDropdown' => false,
])

<div x-data="{
        showTooltip: false,
        showDropdown: false,
    }"
     x-on:close-dropdown-in-editor="showDropdown = false"
     class="relative inline-block"
>

    {{-- Кнопка --}}
    <button
        x-on:click="{{ $clickAction ? $clickAction : ($showDropdown ? 'showDropdown = !showDropdown' : '') }}"
        x-on:mouseenter="showTooltip = true"
        x-on:mouseleave="showTooltip = false"
        type="button"
        :class="{ 'text-yellow-700 dark:text-yellow-500': {{ $isActive }} }"
        class="p-1.5 text-gray-500 rounded-sm cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600"
    >
        {{ $slot }}
    </button>

    {{-- Tooltip --}}
    <div x-show="showTooltip"
         x-transition.opacity
         class="absolute z-10 px-3 py-2 text-sm text-white font-medium bg-gray-900 rounded shadow top-10 left-1/2 transform -translate-x-1/2 transition-opacity"
         style="white-space: nowrap;"
    >
        {{ $label }}
    </div>

    {{-- Dropdown (дополнительный слот) --}}
    @if($showDropdown)
        <div
            x-show="showDropdown"
            x-transition
            x-on:click.away="showDropdown = false"
            class="absolute z-20 w-72 mt-2 rounded-sm bg-white p-2 shadow-sm dark:bg-gray-700"
        >
            {{ $dropdown ?? '' }}
        </div>
    @endif
</div>
