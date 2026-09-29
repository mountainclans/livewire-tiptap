import {Extension} from '@tiptap/core';
import Document from '@tiptap/extension-document';
import {Fragment, Slice} from '@tiptap/pm/model';
import {Plugin} from '@tiptap/pm/state';
import {configuredStarterKit} from './_tiptap-configured-starter-kit.js';
import {shortcodesFromEditor, shortcodesToEditor} from './_tiptap-shortcodes.js';

/**
 * Однострочный режим — редактор вместо текстового поля: заголовок, подпись, короткая фраза.
 * В нём одна строка без оформления, а в поле уходит не HTML, а чистый текст с шорткодами,
 * как его набрали бы руками. Сайт по-прежнему экранирует такой текст сам.
 */
const SingleLineDocument = Document.extend({
    content: 'paragraph',
});

// Вторую строку схема не примет; клавишу гасим, чтобы перенос не вставил сам браузер
const NoLineBreaks = Extension.create({
    name: 'noLineBreaks',

    addKeyboardShortcuts() {
        return {
            Enter: () => true,
            'Shift-Enter': () => true,
            'Mod-Enter': () => true,
        };
    },

    // Из вставленных абзацев схема взяла бы только первый, остальной текст пропал бы
    addProseMirrorPlugins() {
        return [new Plugin({
            props: {
                transformPasted: (slice, view) => inOneLine(slice, view.state.schema),
            },
        })];
    },
});

/** Содержимое всех вставленных абзацев подряд, через пробел. */
function inOneLine(slice, schema) {
    const lines = [];

    slice.content.descendants((node) => {
        if (!node.isTextblock) {
            return true;
        }

        if (node.content.size > 0) {
            lines.push(node.content);
        }

        return false;
    });

    if (lines.length < 2) {
        return slice;
    }

    const joined = lines.reduce(
        (line, next) => line.addToEnd(schema.text(' ')).append(next),
    );

    return new Slice(Fragment.from(joined), 0, 0);
}

export function singleLineExtensions() {
    return [
        SingleLineDocument,
        NoLineBreaks,
        configuredStarterKit({
            document: false,
            italic: false,
            strike: false,
            code: false,
            codeBlock: false,
            blockquote: false,
            heading: false,
            bulletList: false,
            orderedList: false,
            listItem: false,
            horizontalRule: false,
            hardBreak: false,
        }),
    ];
}

/** Текст поля → разметка редактора: текст экранируется, переводы строк становятся пробелами. */
export function singleLineToEditor(text, shortcodes) {
    const line = typeof text === 'string' ? text.replace(/\s*\n\s*/g, ' ') : '';

    return shortcodesToEditor(`<p>${escape(line)}</p>`, shortcodes);
}

/** Разметка редактора → текст поля: без абзаца и без сущностей, шорткоды текстом. */
export function singleLineFromEditor(html) {
    const template = document.createElement('template');
    template.innerHTML = shortcodesFromEditor(html);

    return template.content.textContent;
}

function escape(text) {
    const holder = document.createElement('div');
    holder.textContent = text;

    return holder.innerHTML;
}
