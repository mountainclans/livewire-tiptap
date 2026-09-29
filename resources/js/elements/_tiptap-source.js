/**
 * Режим исходного кода: редактор отдаёт HTML одной строкой, читать её глазами нельзя.
 * Для показа между блоками ставятся переносы строк; на сам текст и на то, как его
 * разберёт сайт, они не влияют.
 */
const BLOCKS = 'p|h[1-6]|ul|ol|blockquote|table|thead|tbody|div|pre';
const ROWS = 'li|tr|td|th';
const CONTAINERS = 'ul|ol|table|thead|tbody|tr';

const RULES = [
    // после закрытого блока, если за ним не закрывается ячейка или пункт списка
    [new RegExp(`(</(?:${BLOCKS})>|<hr\\s*/?>)(?!\\n|\\s*</(?:li|td|th|blockquote)>|$)`, 'gi'), '$1\n'],
    // после каждого пункта списка и строки таблицы
    [new RegExp(`(</(?:${ROWS})>)(?!\\n|$)`, 'gi'), '$1\n'],
    // после открытого списка или таблицы
    [new RegExp(`(<(?:${CONTAINERS})\\b[^>]*>)(?!\\n)`, 'gi'), '$1\n'],
    // перед блоком, который идёт сразу за текстом или служебной меткой
    [new RegExp(`([^>\\s])(<(?:${BLOCKS})\\b)`, 'gi'), '$1\n$2'],
];

export function formatSource(html) {
    if (typeof html !== 'string') {
        return '';
    }

    return RULES
        .reduce((source, [pattern, replacement]) => source.replace(pattern, replacement), html)
        .trim();
}
