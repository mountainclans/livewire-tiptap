@props([
    'label',
    'height' => null,
    'withImage' => false,
])

@php
    $editorId = 'tiptap-' . uniqid();
    $name = $attributes->wire('model')->value();
@endphp

<div>
    @if ($label)
        <div class="flex justify-between items-center">
            <label class="flex flex-wrap w-full mb-2 text-sm font-medium text-gray-900 dark:text-white"
                   for="{{ $editorId }}"
            >
                <p class="w-full">{{ $label }}</p>
            </label>
        </div>
    @endif

    <div x-data="tiptap($wire.entangle('{{ $attributes->wire('model')->value() }}'))"
         x-init="() => init($refs.editor)"
         data-x-template="tiptap($wire.entangle('::replace::'))"
         data-model="{{ $attributes->wire('model')->value() }}"
         wire:ignore
         {{ $attributes->whereDoesntStartWith('wire:model') }}
         class="w-full border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
    >
        {{-- BUTTONS --}}
        <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600">
            <div class="flex flex-wrap items-center">
                <div class="flex items-center space-x-1 rtl:space-x-reverse flex-wrap">

                    <x-ui.tiptap-button :label="__('Bold')"
                                        click-action="toggleBold()"
                                        is-active="isActive('bold', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M8 5h4.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0-7H6m2 7h6.5a3.5 3.5 0 1 1 0 7H8m0-7v7m0 0H6"/>
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Italic')"
                                        click-action="toggleItalic()"
                                        is-active="isActive('italic', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="m8.874 19 6.143-14M6 19h6.33m-.66-14H18"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Underline')"
                                        click-action="toggleUnderline()"
                                        is-active="isActive('underline', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-width="2"
                                  d="M6 19h12M8 5v9a4 4 0 0 0 8 0V5M6 5h4m4 0h4"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Strike')"
                                        click-action="toggleStrike()"
                                        is-active="isActive('strike', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M7 6.2V5h12v1.2M7 19h6m.2-14-1.677 6.523M9.6 19l1.029-4M5 5l6.523 6.523M19 19l-7.477-7.477"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Link')"
                                        click-action="addLink()"
                                        is-active="false"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Remove link')"
                                        click-action="removeLink()"
                                        is-active="isActive('link', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-width="2"
                                  d="M13.2 9.8a3.4 3.4 0 0 0-4.8 0L5 13.2A3.4 3.4 0 0 0 9.8 18l.3-.3m-.3-4.5a3.4 3.4 0 0 0 4.8 0L18 9.8A3.4 3.4 0 0 0 13.2 5l-1 1m7.4 14-1.8-1.8m0 0L16 16.4m1.8 1.8 1.8-1.8m-1.8 1.8L16 20"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Bullet list')"
                                        click-action="toggleBulletList()"
                                        is-active="isActive('bulletList', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-width="2"
                                  d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"
                            ></path>
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Ordered list')"
                                        click-action="toggleOrderedList()"
                                        is-active="isActive('orderedList', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 6h8m-8 6h8m-8 6h8M4 16a2 2 0 1 1 3.321 1.5L4 20h5M4 5l2-1v6m-2 0h4"
                            ></path>
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Blockquote')"
                                        click-action="toggleBlockquote()"
                                        is-active="isActive('blockquote', updatedAt)"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="currentColor"
                             viewBox="0 0 24 24"
                        >
                            <path fill-rule="evenodd"
                                  d="M6 6a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a3 3 0 0 1-3 3H5a1 1 0 1 0 0 2h1a5 5 0 0 0 5-5V8a2 2 0 0 0-2-2H6Zm9 0a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a3 3 0 0 1-3 3h-1a1 1 0 1 0 0 2h1a5 5 0 0 0 5-5V8a2 2 0 0 0-2-2h-3Z"
                                  clip-rule="evenodd"
                            ></path>
                        </svg>
                    </x-ui.tiptap-button>

                    <div class="px-1">
                        <span class="block w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
                    </div>

                    <x-ui.tiptap-button :label="__('Text size')"
                                        :showDropdown="true"
                                        is-active="isTextSized()"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M3 6.2V5h11v1.2M8 5v14m-3 0h6m2-6.8V11h8v1.2M17 11v8m-1.5 0h3"
                            />
                        </svg>

                        <x-slot name="dropdown">
                            <ul class="space-y-1 text-sm font-medium">
                                <li>
                                    <button x-on:click="setTextSize('16px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('16px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('16px (Default)') }}</button>
                                </li>
                                <li>
                                    <button x-on:click="setTextSize('12px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('12px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('12px') }}</button>
                                </li>
                                <li>
                                    <button x-on:click="setTextSize('14px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('14px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('14px') }}</button>
                                </li>
                                <li>
                                    <button x-on:click="setTextSize('18px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('18px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('18px') }}</button>
                                </li>
                                <li>
                                    <button x-on:click="setTextSize('24px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('24px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('24px') }}</button>
                                </li>
                                <li>
                                    <button x-on:click="setTextSize('32px'); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isTextSize('32px') }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >{{ __('36px') }}</button>
                                </li>
                            </ul>
                        </x-slot>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Format')"
                                        :showDropdown="true"
                    >
                        <span class="flex items-center justify-center rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-50 dark:bg-gray-600 dark:text-gray-400 dark:hover:bg-gray-500 dark:hover:text-white dark:focus:ring-gray-600 -m-1.5"
                              :class="{ 'text-yellow-700 dark:text-yellow-500': isAnyHeading() }"
                        >
                            {{ __('Format') }}

                            <svg class="-me-0.5 ms-1.5 h-3.5 w-3.5"
                                 aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                            >
                                <path stroke="currentColor"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="m19 9-7 7-7-7"
                                />
                            </svg>
                        </span>

                        <x-slot name="dropdown">
                            <ul class="space-y-1 text-sm font-medium">
                                <li>
                                    <button x-on:click="setParagraph(); showDropdown = false;"
                                        type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Paragraph') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">0</kbd>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button x-on:click="toggleHeading(2); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isHeading(2) }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Heading 2') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">2</kbd>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button x-on:click="toggleHeading(3); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isHeading(3) }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Heading 3') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">3</kbd>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button x-on:click="toggleHeading(4); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isHeading(4) }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Heading 4') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">4</kbd>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button x-on:click="toggleHeading(5); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isHeading(5) }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Heading 5') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">5</kbd>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button x-on:click="toggleHeading(6); showDropdown = false;"
                                            :class="{ 'text-yellow-700 dark:text-yellow-500': isHeading(6) }"
                                            type="button"
                                            class="flex justify-between items-center w-full text-base rounded-sm px-3 py-2 hover:bg-gray-100 text-gray-900 dark:hover:bg-gray-600 dark:text-white cursor-pointer"
                                    >
                                        {{ __('Heading 6') }}

                                        <div class="space-x-1.5">
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Cmd</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">Alt</kbd>
                                            <kbd
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-400 dark:border-gray-500">6</kbd>
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </x-slot>
                    </x-ui.tiptap-button>

                    <div class="px-1">
                        <span class="block w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
                    </div>

                    <x-ui.tiptap-button :label="__('Align left')"
                                        click-action="setTextAlignment('left')"
                                        is-active="isTextAligned('left')"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M6 6h8m-8 4h12M6 14h8m-8 4h12"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Align center')"
                                        click-action="setTextAlignment('center')"
                                        is-active="isTextAligned('center')"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M8 6h8M6 10h12M8 14h8M6 18h12"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <x-ui.tiptap-button :label="__('Align right')"
                                        click-action="setTextAlignment('right')"
                                        is-active="isTextAligned('right')"
                    >
                        <svg class="w-5 h-5"
                             aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             fill="none"
                             viewBox="0 0 24 24"
                        >
                            <path stroke="currentColor"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M18 6h-8m8 4H6m12 4h-8m8 4H6"
                            />
                        </svg>
                    </x-ui.tiptap-button>

                    <div class="px-1">
                        <span class="block w-px h-4 bg-gray-300 dark:bg-gray-600"></span>
                    </div>

                    @if($withImage)
                        <x-ui.tiptap-button :label="__('Add image')"
                                            click-action="addImage()"
                                            is-active="false"
                        >
                            <svg class="w-5 h-5"
                                 aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="24"
                                 height="24"
                                 fill="currentColor"
                                 viewBox="0 0 24 24"
                            >
                                <path fill-rule="evenodd"
                                      d="M13 10a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2H14a1 1 0 0 1-1-1Z"
                                      clip-rule="evenodd"
                                ></path>
                                <path fill-rule="evenodd"
                                      d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12c0 .556-.227 1.06-.593 1.422A.999.999 0 0 1 20.5 20H4a2.002 2.002 0 0 1-2-2V6Zm6.892 12 3.833-5.356-3.99-4.322a1 1 0 0 0-1.549.097L4 12.879V6h16v9.95l-3.257-3.619a1 1 0 0 0-1.557.088L11.2 18H8.892Z"
                                      clip-rule="evenodd"
                                ></path>
                            </svg>
                        </x-ui.tiptap-button>
                    @endif

                </div>
            </div>
        </div>

        {{-- EDITOR --}}
        <div class="px-4 py-2 bg-white rounded-b-lg dark:bg-gray-800">
            <div x-ref="editor"
                 id="{{ $editorId }}"
                 class="block w-full px-0 text-sm text-gray-800 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:border-none overflow-y-auto"
                 style="font-size: 16px; @if($height)height: {{ $height }}px;@endif"
            ></div>
        </div>
    </div>

    @if (!isset($attributes['translatable']))
        @error ($name)
        <p error-bag class="mt-2 text-base text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    @endif
</div>

