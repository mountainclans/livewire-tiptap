<?php

namespace MountainClans\LivewireTiptap\Support;

use InvalidArgumentException;

/**
 * Шорткоды редактора — служебные метки вида [code], которые разбирает сайт, а не редактор.
 * Набор задаёт приложение: пакет не знает ни одного кода заранее.
 */
final class Shortcodes
{
    /** Метка занимает отдельную строку между блоками текста. */
    public const KIND_BLOCK = 'block';

    /** Метка стоит внутри строки, как символ. */
    public const KIND_INLINE = 'inline';

    /**
     * @param  array<int, array{code: string, label?: string, kind?: string, hint?: string}>  $definitions
     * @return list<array{code: string, label: string, kind: string, hint: string}>
     */
    public static function normalize(array $definitions): array
    {
        $shortcodes = [];

        foreach ($definitions as $definition) {
            $code = (string) ($definition['code'] ?? '');
            $kind = (string) ($definition['kind'] ?? self::KIND_INLINE);

            if (preg_match('/^[A-Za-z0-9_-]+$/', $code) !== 1) {
                throw new InvalidArgumentException("Shortcode code [{$code}] may contain only latin letters, digits, underscore and hyphen.");
            }

            if (! in_array($kind, [self::KIND_BLOCK, self::KIND_INLINE], true)) {
                throw new InvalidArgumentException("Shortcode [{$code}] has unknown kind [{$kind}].");
            }

            if (isset($shortcodes[$code])) {
                throw new InvalidArgumentException("Shortcode [{$code}] is defined twice.");
            }

            $label = (string) ($definition['label'] ?? "[{$code}]");

            $shortcodes[$code] = [
                'code' => $code,
                'label' => $label,
                'kind' => $kind,
                'hint' => (string) ($definition['hint'] ?? $label),
            ];
        }

        return array_values($shortcodes);
    }
}
