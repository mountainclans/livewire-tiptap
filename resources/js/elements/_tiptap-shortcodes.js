import {Node, mergeAttributes, nodeInputRule} from '@tiptap/core';

/**
 * Шорткод — служебная метка вида [code], которую разбирает сайт, а не редактор.
 * В сохранённом HTML она остаётся текстом. В редакторе метка показана неделимой плашкой
 * с самим кодом и подписью: её нельзя испортить набором или оформлением,
 * а блочная не может оказаться посреди абзаца.
 * Набор кодов приходит снаружи, пакет не знает ни одного заранее.
 */
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
            kind: KINDS[shortcode.kind] ? shortcode.kind : 'inline',
        }));
}

/** Узлы редактора: по одному на каждый вид, для которого есть хотя бы один код. */
export function shortcodeExtensions(shortcodes) {
    return Object.keys(KINDS)
        .map((kind) => [kind, shortcodes.filter((shortcode) => shortcode.kind === kind)])
        .filter(([, ofKind]) => ofKind.length > 0)
        .map(([kind, ofKind]) => shortcodeNode(kind, ofKind));
}

export function shortcodeNodeName(kind) {
    return KINDS[kind].name;
}

function shortcodeNode(kind, shortcodes) {
    const {name, tag} = KINDS[kind];
    const labels = new Map(shortcodes.map((shortcode) => [shortcode.code, shortcode.label]));

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

    const template = parse(html);
    const kinds = new Map(shortcodes.map((shortcode) => [shortcode.code, shortcode.kind]));
    const pattern = new RegExp(`\\[(${shortcodes.map((shortcode) => shortcode.code).join('|')})\\]`);

    textNodes(template).forEach((text) => replaceMarkers(text, pattern, kinds));

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

    template.content
        .querySelectorAll('[data-shortcode]')
        .forEach((element) => element.replaceWith(`[${element.getAttribute('data-shortcode')}]`));

    return template.innerHTML;
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
