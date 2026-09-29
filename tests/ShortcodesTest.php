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
        ],
        [
            'code' => 'br',
            'label' => '[br]',
            'kind' => 'inline',
            'hint' => '[br]',
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
        ->toContain('tiptap($wire.entangle(\'content\'), null, [{"code":"CUT","label":"Под кат","kind":"block","hint":"Под кат"}])')
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
