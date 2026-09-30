import {Extension, Mark, Node, markInputRule, mergeAttributes, nodeInputRule} from '@tiptap/core';
import {Plugin} from '@tiptap/pm/state';
import {Decoration, DecorationSet} from '@tiptap/pm/view';

/**
 * Шорткод — служебная метка вида [code], которую разбирает сайт, а не редактор.
 * В сохранённом HTML она остаётся текстом. В редакторе метка показана неделимой плашкой
 * с самим кодом и подписью: её нельзя испортить набором или оформлением,
 * а блочная не может оказаться посреди абзаца.
 * Парная метка [code]…[/code] обнимает текст: в редакторе это выделение с кнопкой в панели.
 * Набор кодов приходит снаружи, пакет не знает ни одного заранее.
 */
const PAIR = 'pair';

// Цвет метки задаёт приложение; сами цвета — в tiptap.css
const COLORS = ['gray', 'green', 'yellow', 'blue', 'red', 'purple'];

// Виды, которые в редакторе становятся узлами; парная метка — не узел, а выделение текста
const KINDS = {
    block: {
        name: 'shortcodeBlock',
        tag: 'div',
    },
    inline: {
        name: 'shortcodeInline',
        tag: 'span',
    },
};

// В этих узлах блочная метка может стоять сама; из остальных её нужно вынести наружу.
const BLOCK_CONTAINERS = ['LI', 'BLOCKQUOTE', 'TD', 'TH', 'DIV'];

/** Определения в виде {code, label, kind}; негодные отбрасываются, чтобы не ломать редактор. */
export function normalizeShortcodes(shortcodes) {
    if (!Array.isArray(shortcodes)) {
        return [];
    }

    return shortcodes
        .filter((shortcode) => typeof shortcode?.code === 'string' && /^[\w-]+$/.test(shortcode.code))
        .map((shortcode) => ({
            code: shortcode.code,
            // Подпись, повторяющая саму метку, на плашке не нужна
            label: shortcode.label && shortcode.label !== `[${shortcode.code}]` ? shortcode.label : null,
            kind: KINDS[shortcode.kind] || shortcode.kind === PAIR ? shortcode.kind : 'inline',
            color: COLORS.includes(shortcode.color) ? shortcode.color : null,
        }));
}

/** Расширения редактора: узел на каждый вид меток и выделение на каждую парную метку. */
export function shortcodeExtensions(shortcodes) {
    const nodes = Object.keys(KINDS)
        .map((kind) => [kind, shortcodes.filter((shortcode) => shortcode.kind === kind)])
        .filter(([, ofKind]) => ofKind.length > 0)
        .map(([kind, ofKind]) => shortcodeNode(kind, ofKind));

    const pairs = shortcodes.filter(isPair);

    return [
        ...nodes,
        ...pairs.map(shortcodeMark),
        ...(pairs.length > 0 ? [pairMarkers(pairs)] : []),
    ];
}

export function shortcodeNodeName(kind) {
    return KINDS[kind].name;
}

export function shortcodeMarkName(code) {
    return `shortcodePair_${code}`;
}

export function isPair(shortcode) {
    return shortcode.kind === PAIR;
}

/**
 * У каждой парной метки своё выделение: одно общее не дало бы вложить [c] в [hl].
 * Приоритет выше обычного оформления, чтобы метка обнимала жирный и курсив снаружи
 * и в сохранённом тексте не рвалась на куски.
 * Пара не тянется за курсором: текст, набранный сразу за закрывающей меткой, остаётся снаружи.
 */
function shortcodeMark(shortcode) {
    const {code, color, label} = shortcode;

    return Mark.create({
        name: shortcodeMarkName(code),
        priority: 1000,
        inclusive: false,

        parseHTML() {
            return [{tag: `span[data-shortcode="${code}"][data-kind="${PAIR}"]`}];
        },

        renderHTML({HTMLAttributes}) {
            return ['span', mergeAttributes(HTMLAttributes, {
                'data-shortcode': code,
                'data-kind': PAIR,
                'data-color': color,
                title: label,
                class: 'tiptap-shortcode-pair',
            }), 0];
        },

        // Пара, набранная руками, сразу становится выделением
        addInputRules() {
            return [markInputRule({
                find: new RegExp(`(?:\\[${code}\\])([^\\[\\]]+)(?:\\[/${code}\\])$`),
                type: this.type,
            })];
        },
    });
}

/**
 * Метки [code] и [/code] по краям пары. Это не часть документа, а украшения:
 * в сохранённый HTML и в буфер обмена они не попадают. Метки стоят снаружи подсветки,
 * поэтому курсор у края пары редактор ставит за ними — там же, куда пойдёт набранный текст.
 */
function pairMarkers(pairs) {
    const names = new Map(pairs.map((shortcode) => [shortcodeMarkName(shortcode.code), shortcode]));

    return Extension.create({
        name: 'shortcodePairMarkers',

        addProseMirrorPlugins() {
            return [new Plugin({
                props: {
                    decorations: (state) => DecorationSet.create(state.doc, markerDecorations(pairRanges(state.doc, names))),
                },
                view: () => ({
                    update: (view) => caretAfterClosingMarker(view, names),
                }),
            })];
        },
    });
}

/**
 * Границы пар в документе: {from, to, mark, code}. Пара тянется по соседним узлам
 * строки; открытая пара закрывается на конце блока.
 */
function pairRanges(doc, names) {
    const ranges = [];

    doc.descendants((block, blockPos) => {
        if (!block.isTextblock) {
            return true;
        }

        const open = new Map();

        block.forEach((child, offset) => {
            const from = blockPos + 1 + offset;

            for (const [name, shortcode] of names) {
                const mark = child.marks.find((candidate) => candidate.type.name === name);

                if (mark && !open.has(name)) {
                    open.set(name, {from, shortcode});
                }

                if (!mark && open.has(name)) {
                    ranges.push({...open.get(name), to: from});
                    open.delete(name);
                }
            }
        });

        for (const range of open.values()) {
            ranges.push({...range, to: blockPos + block.nodeSize - 1});
        }

        return false;
    });

    return ranges;
}

function markerDecorations(ranges) {
    return ranges.flatMap(({from, to, shortcode}) => [
        Decoration.widget(from, () => marker(`[${shortcode.code}]`, shortcode), {side: 1, marks: [], key: `open-${shortcode.code}-${from}`}),
        Decoration.widget(to, () => marker(`[/${shortcode.code}]`, shortcode), {side: -1, marks: [], key: `close-${shortcode.code}-${to}`}),
    ]);
}

/**
 * На конце пары набор идёт снаружи, а курсор редактор рисует внутри текста, перед меткой [/code]:
 * для него оба места — одна и та же позиция. Переставляем курсор за метку, когда он там оказался.
 */
function caretAfterClosingMarker(view, names) {
    const {selection, doc} = view.state;

    if (!selection.empty || !view.hasFocus()) {
        return;
    }

    const closing = pairRanges(doc, names).some((range) => range.to === selection.head);

    if (!closing) {
        return;
    }

    const {node, offset} = view.domAtPos(selection.head, 1);
    const domSelection = view.root.getSelection();

    if (!domSelection || (domSelection.anchorNode === node && domSelection.anchorOffset === offset)) {
        return;
    }

    domSelection.collapse(node, offset);
    view.domObserver.setCurSelection();
}

function marker(text, shortcode) {
    const element = document.createElement('span');
    element.className = 'tiptap-shortcode-pair-marker';
    element.textContent = text;
    element.title = shortcode.label ?? '';

    if (shortcode.color) {
        element.setAttribute('data-color', shortcode.color);
    }

    return element;
}

function shortcodeNode(kind, shortcodes) {
    const {name, tag} = KINDS[kind];
    const labels = new Map(shortcodes.map((shortcode) => [shortcode.code, shortcode.label]));
    const colors = new Map(shortcodes.map((shortcode) => [shortcode.code, shortcode.color]));

    return Node.create({
        name,
        group: kind,
        inline: kind === 'inline',
        atom: true,
        selectable: true,
        draggable: true,

        addAttributes() {
            return {
                code: {
                    default: null,
                    parseHTML: (element) => element.getAttribute('data-shortcode'),
                    renderHTML: (attributes) => ({'data-shortcode': attributes.code}),
                },
            };
        },

        parseHTML() {
            return [{
                tag: `${tag}[data-shortcode][data-kind="${kind}"]`,
                getAttrs: (element) => (labels.has(element.getAttribute('data-shortcode')) ? null : false),
            }];
        },

        renderHTML({node, HTMLAttributes}) {
            return [tag, mergeAttributes(HTMLAttributes, {
                'data-kind': kind,
                'data-label': labels.get(node.attrs.code),
                'data-color': colors.get(node.attrs.code),
                title: labels.get(node.attrs.code),
                class: `tiptap-shortcode tiptap-shortcode-${kind}`,
            })];
        },

        renderText({node}) {
            return `[${node.attrs.code}]`;
        },

        // Метка, набранная руками, сразу становится плашкой
        addInputRules() {
            return shortcodes.map((shortcode) => nodeInputRule({
                find: new RegExp(`\\[${shortcode.code}\\]$`),
                type: this.type,
                getAttributes: () => ({code: shortcode.code}),
            }));
        },
    });
}

/** Сохранённый HTML → HTML для редактора: текстовые метки становятся элементами-плашками. */
export function shortcodesToEditor(html, shortcodes) {
    if (typeof html !== 'string' || !shortcodes.some((shortcode) => html.includes(`[${shortcode.code}]`))) {
        return html;
    }

    // Пара обнимает разметку, поэтому ищется по строке; одиночные метки — по текстовым узлам
    const template = parse(wrapPairs(html, shortcodes.filter(isPair)));
    const single = shortcodes.filter((shortcode) => !isPair(shortcode));

    if (single.length > 0) {
        const kinds = new Map(single.map((shortcode) => [shortcode.code, shortcode.kind]));
        const pattern = new RegExp(`\\[(${single.map((shortcode) => shortcode.code).join('|')})\\]`);

        textNodes(template).forEach((text) => replaceMarkers(text, pattern, kinds));
    }

    template.content
        .querySelectorAll('[data-shortcode][data-kind="block"]')
        .forEach((element) => lift(element, template.content));

    return template.innerHTML;
}

/** HTML редактора → сохраняемый HTML: плашки снова становятся текстом [code]. */
export function shortcodesFromEditor(html) {
    if (typeof html !== 'string' || !html.includes('data-shortcode')) {
        return html;
    }

    const template = parse(html);

    // Пары разворачиваются изнутри наружу: вложенная к этому моменту уже стала текстом
    [...template.content.querySelectorAll(`[data-shortcode][data-kind="${PAIR}"]`)]
        .reverse()
        .forEach((element) => {
            const code = element.getAttribute('data-shortcode');

            element.replaceWith(`[${code}]`, ...element.childNodes, `[/${code}]`);
        });

    template.content
        .querySelectorAll('[data-shortcode]')
        .forEach((element) => element.replaceWith(`[${element.getAttribute('data-shortcode')}]`));

    return template.innerHTML;
}

/** Открывающая и закрывающая метки становятся обёрткой; метка без пары остаётся текстом. */
function wrapPairs(html, pairs) {
    return pairs.reduce(
        (wrapped, {code}) => wrapped.replace(
            new RegExp(`\\[${code}\\]([\\s\\S]*?)\\[/${code}\\]`, 'g'),
            `<span data-shortcode="${code}" data-kind="${PAIR}">$1</span>`,
        ),
        html,
    );
}

// template держит разметку в неактивном документе: картинки не грузятся, обработчики не срабатывают
function parse(html) {
    const template = document.createElement('template');
    template.innerHTML = html;

    return template;
}

function textNodes(template) {
    const walker = template.ownerDocument.createTreeWalker(template.content, NodeFilter.SHOW_TEXT);
    const nodes = [];

    while (walker.nextNode()) {
        nodes.push(walker.currentNode);
    }

    return nodes;
}

function replaceMarkers(text, pattern, kinds) {
    let rest = text;
    let match = pattern.exec(rest.data);

    while (match !== null) {
        const marker = rest.splitText(match.index);
        rest = marker.splitText(match[0].length);

        marker.replaceWith(markerElement(marker.ownerDocument, match[1], kinds.get(match[1])));

        match = pattern.exec(rest.data);
    }
}

function markerElement(document, code, kind) {
    const element = document.createElement(KINDS[kind].tag);
    element.setAttribute('data-shortcode', code);
    element.setAttribute('data-kind', kind);

    return element;
}

/** Выносит блочную метку из абзаца или оформления: то, что стояло после неё, уходит в отдельный узел. */
function lift(element, root) {
    while (element.parentNode !== root && !BLOCK_CONTAINERS.includes(element.parentNode.nodeName)) {
        const parent = element.parentNode;
        const tail = parent.cloneNode(false);

        while (element.nextSibling) {
            tail.appendChild(element.nextSibling);
        }

        parent.after(element, tail);

        [parent, tail]
            .filter((node) => node.textContent.trim() === '' && node.querySelector('img, table, [data-shortcode]') === null)
            .forEach((node) => node.remove());
    }
}
