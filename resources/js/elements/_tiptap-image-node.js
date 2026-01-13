import { Node, mergeAttributes } from '@tiptap/core'

export const CustomInlineImage = Node.create({
    name: 'inlineImage',

    inline: false,
    group: 'block',

    addAttributes() {
        return {
            src: { default: null },
            alt: { default: null },
        }
    },

    parseHTML() {
        return [
            {
                tag: 'span[data-inline-image]',
                getAttrs: (element) => {
                    const img = element.querySelector('img')
                    if (!img) return false

                    return {
                        src: img.getAttribute('src') || img.src,
                        alt: img.getAttribute('alt') || '',
                    }
                },
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        const { src, alt } = HTMLAttributes

        return [
            'span',
            {
                'data-inline-image': '',
                class: 'block rounded my-1 text-center w-fit mx-auto',
                contenteditable: 'false',
            },
            [
                'img',
                {
                    src,
                    alt,
                    class: 'max-w-full h-auto rounded',
                }
            ],
        ]
    },

    addNodeView() {
        return ({ node, getPos, editor }) => {
            const container = document.createElement('span')
            container.setAttribute('data-inline-image', '')
            container.className = 'block rounded my-1 text-center w-fit mx-auto relative'

            // Картинка
            const img = document.createElement('img')
            img.src = node.attrs.src
            img.alt = node.attrs.alt || ''
            img.className = 'max-w-full h-auto rounded my-0'

            container.appendChild(img)

            // Кнопка удаления
            const btn = document.createElement('button')
            btn.type = 'button'
            btn.innerHTML = `
                <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            `
            btn.className = 'absolute top-1 right-1 bg-gray-600 hover:bg-gray-400 text-white text-xs w-6 h-6 rounded-lg flex items-center justify-center cursor-pointer border-none select-none'

            btn.addEventListener('click', event => {
                event.preventDefault()
                const pos = getPos()
                if (pos !== null) {
                    editor.chain().focus().deleteRange({ from: pos, to: pos + node.nodeSize }).run()
                }
            })

            container.appendChild(btn)

            return {
                dom: container,
                update(updatedNode) {
                    if (updatedNode.type !== node.type) {
                        return false
                    }
                    if (updatedNode.attrs.src !== node.attrs.src) {
                        img.src = updatedNode.attrs.src
                    }
                    if (updatedNode.attrs.alt !== node.attrs.alt) {
                        img.alt = updatedNode.attrs.alt || ''
                    }
                    return true
                },
            }
        }
    },

    addCommands() {
        return {
            insertInlineImage:
                attrs =>
                    ({ chain }) =>
                        chain().insertContent({
                            type: this.name,
                            attrs,
                        }).run(),
        }
    },
})
