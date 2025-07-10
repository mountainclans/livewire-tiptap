import {Editor} from '@tiptap/core'
import Highlight from '@tiptap/extension-highlight';
import Underline from '@tiptap/extension-underline';
import FontFamily from '@tiptap/extension-font-family';
import {Color} from '@tiptap/extension-color';
import {ImageUploadPlaceholder} from './elements/_tiptap-upload-image'
import {ConfiguredLink} from './elements/_tiptap-configured-link.js'
import {ConfiguredBold} from "./elements/_tiptap-configured-bold.js";
import {ConfiguredStarterKit} from "./elements/_tiptap-configured-starter-kit.js";
import {ConfiguredTextStyle} from "./elements/_tiptap-configured-text-style.js";
import {ConfiguredTextAlign} from "./elements/_tiptap-configured-text-align.js";
import {CustomInlineImage} from "./elements/_tiptap-image-node.js";

export default function tiptap(content){
    let editor; // не делать полем объекта, т.к. оно проксируется и tiptap перестаёт работать

    return {
        content: content,
        updatedAt: Date.now(), // force Alpine to rerender on selection change

        init(element) {
            const _this = this;

            editor = new Editor({
                element: element,
                extensions: [
                    Color,
                    FontFamily,
                    Highlight,
                    Underline,
                    ConfiguredStarterKit,
                    ConfiguredBold,
                    ConfiguredTextStyle,
                    ConfiguredLink,
                    ConfiguredTextAlign,
                    ImageUploadPlaceholder,
                    CustomInlineImage,
                ],
                content: this.content,
                editorProps: {
                    attributes: {
                        class: 'format dark:format-invert focus:!outline-none format-blue max-w-none',
                    },
                },
                onCreate({ editor }) {
                    _this.updatedAt = Date.now()
                },
                onUpdate: ({editor}) => {
                    _this.updatedAt = Date.now()
                    this.content = editor.getHTML();

                },
                onSelectionUpdate({ editor }) {
                    _this.updatedAt = Date.now()
                },
            })

            this.$watch('content', (content) => {
                if (content === editor.getHTML()) {
                    return;
                }

                editor.commands.setContent(content, false)
            })
        },

        // Методы форматирования текста
        toggleBold() {
            editor?.chain().focus().toggleBold().run();
        },

        toggleItalic() {
            editor?.chain().focus().toggleItalic().run();
        },

        toggleUnderline() {
            editor?.chain().focus().toggleUnderline().run();
        },

        toggleStrike() {
            editor?.chain().focus().toggleStrike().run();
        },

        toggleCode() {
            editor?.chain().focus().toggleCode().run();
        },

        setParagraph() {
            editor?.chain().focus().setParagraph().run();
        },

        toggleHeading(level) {
            editor?.chain().focus().toggleHeading({level}).run();
        },

        isHeading(level) {
            return editor?.isActive('heading', { level }, this.updatedAt) || false;
        },

        isAnyHeading() {
            // || this.isHeading(1) // h1 не должен использоваться в редакторе
            return this.isHeading(2)
                || this.isHeading(3)
                || this.isHeading(4)
                || this.isHeading(5)
                || this.isHeading(6);
        },

        toggleBulletList() {
            editor?.chain().focus().toggleBulletList().run();
        },

        toggleOrderedList() {
            editor?.chain().focus().toggleOrderedList().run();
        },

        toggleBlockquote() {
            editor?.chain().focus().toggleBlockquote().run();
        },

        addLink() {
            const url = prompt('Enter the URL:');
            if (url) {
                editor?.chain().focus().setLink({href: url}).run();
            }
        },

        removeLink() {
            editor?.chain().focus().unsetLink().run();
        },

        setTextSize(fontSize) {
            if (fontSize === '16px') {
                editor?.chain().focus().unsetMark('textStyle').run();
            } else {
                editor?.chain().focus().setMark('textStyle', { fontSize }).run();
            }
        },

        isTextSize(fontSize) {
            return editor?.isActive('textStyle', { fontSize }, this.updatedAt) || false;
        },

        isTextSized() {
            return this.isTextSize('12px')
                // || this.isTextSize('16px') // это размер по умолчанию, если выбран он - игнорируем
                || this.isTextSize('14px')
                || this.isTextSize('18px')
                || this.isTextSize('24px')
                || this.isTextSize('32px');
        },

        setTextAlignment(align) {
            editor.chain().focus().setTextAlign(align).run();
        },

        isTextAligned(alignment) {
            return editor?.isActive({ textAlign: alignment }, this.updatedAt) || false;
        },

        addImage() {
            return editor?.chain().focus().insertContent({ type: 'imageUploadPlaceholder' }).run()
        },

        // Проверка состояний
        isActive(type, options = {}) {
            return editor?.isActive(type, options) || false;
        },

        // Очистка при уничтожении компонента
        destroy() {
            if (editor) {
                editor.destroy();
                editor = null;
            }
        }
    }
}


