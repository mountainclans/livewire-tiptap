import Link from "@tiptap/extension-link";

export const ConfiguredLink =  Link.configure({
    openOnClick: false,
    autolink: true,
    defaultProtocol: 'https',
    HTMLAttributes: {
        class: 'text-blue-500 underline',
        target: '_blank',
        rel: 'noopener noreferrer',
        title: null,
    },
})
    .extend({
        renderHTML({ HTMLAttributes }) {
            return ['a', {
                ...HTMLAttributes,
                title: HTMLAttributes.href, // отображаем адрес ссылки при наведении
            }, 0]
        }
    });
