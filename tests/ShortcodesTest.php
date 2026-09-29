<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\View\ViewException;
use MountainClans\LivewireTiptap\Support\Shortcodes;

it('normalizes shortcode definitions: inline by default, label and hint fall back', function () {
    expect(Shortcodes::normalize([
        [
            'code' => 'CUT',
            'label' => 'Под кат',
            'kind' => 'block',
            'hint' => 'Всё ниже свёрнуто',
        ],
        ['code' => 'br'],
    ]))->toBe([
        [
            'code' => 'CUT',
            'label' => 'Под кат',
            'kind' => 'block',
            'hint' => 'Всё ниже свёрнуто',
            'color' => 'gray',
        ],
        [
            'code' => 'br',
            'label' => '[br]',
            'kind' => 'inline',
            'hint' => '[br]',
            'color' => 'gray',
        ],
    ]);
});

it('rejects malformed shortcode definitions', function (array $definitions) {
    Shortcodes::normalize($definitions);
})->throws(InvalidArgumentException::class)->with([
    'code with a bracket' => [[['code' => 'a]b']]],
    'empty code' => [[['label' => 'No code']]],
    'unknown kind' => [[
        [
            'code' => 'x',
            'kind' => 'wrap',
        ],
    ]],
    'slash in a paired code' => [[
        [
            'code' => '/hl',
            'kind' => 'pair',
        ],
    ]],
    'unknown color' => [[
        [
            'code' => 'x',
            'color' => 'pink',
        ],
    ]],
    'duplicate' => [[
        ['code' => 'x'],
        ['code' => 'x'],
    ]],
]);

it('hands shortcodes to the editor and renders a button for each', function () {
    $html = html_entity_decode(Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :shortcodes="$shortcodes" />',
        [
            'shortcodes' => [
                [
                    'code' => 'CUT',
                    'label' => 'Под кат',
                    'kind' => 'block',
                ],
            ],
        ],
    ));

    // Набор инструментов не задан — редактор получает null и остаётся в режиме совместимости
    expect($html)
        ->toContain('tiptap($wire.entangle(\'content\'), null, [{"code":"CUT","label":"Под кат","kind":"block","hint":"Под кат","color":"gray"}])')
        ->toContain('tiptap($wire.entangle(\'::replace::\'), null, [{"code":"CUT"')
        ->toContain("insertShortcode('CUT')")
        ->toContain('toggleBold()');
});

it('passes both the tools list and the shortcodes', function () {
    $html = html_entity_decode(Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :tools="[\'bold\']" :shortcodes="[[\'code\' => \'br\']]" />'
    ));

    expect($html)
        ->toContain('tiptap($wire.entangle(\'content\'), ["bold"], [{"code":"br"')
        ->toContain("insertShortcode('br')")
        ->not->toContain('toggleItalic()');
});

it('fails loudly on a malformed shortcode in the component', function () {
    Blade::render('<x-ui.tiptap label="Content" wire:model="content" :shortcodes="[[\'code\' => \'a b\']]" />');
})->throws(ViewException::class);

it('accepts paired shortcodes and lets their buttons show the active state', function () {
    $html = html_entity_decode(Blade::render(
        '<x-ui.tiptap label="Content" wire:model="content" :shortcodes="$shortcodes" />',
        [
            'shortcodes' => [
                [
                    'code' => 'hl',
                    'label' => 'Акцент',
                    'kind' => 'pair',
                ],
                ['code' => 'br'],
            ],
        ],
    ), ENT_QUOTES);

    expect($html)
        // Парная метка без указаний жёлтая, как маркер
        ->toContain('{"code":"hl","label":"Акцент","kind":"pair","hint":"Акцент","color":"yellow"}')
        ->toContain("insertShortcode('hl')")
        // Парная метка включается на выделении, поэтому кнопка знает, активна ли она
        ->toContain("isShortcodeActive('hl')")
        ->not->toContain("isShortcodeActive('br')");
});
