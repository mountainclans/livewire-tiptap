import { Node, mergeAttributes } from '@tiptap/core'

const uploadFile = async function (editor, file) {
    const formData = new FormData();

    formData.append('image', file)

    const response = await fetch('/tiptap/upload-image', {
        method: 'POST',
        body: formData
    })

    const { url } = await response.json()

    // Заменяем placeholder на изображение
    editor.chain().focus().deleteNode('imageUploadPlaceholder')
        .insertInlineImage({
            src: url,
            float: 'left',
        })
        .run()
}

export const ImageUploadPlaceholder = Node.create({
    name: 'imageUploadPlaceholder',

    group: 'block',
    atom: true,

    addAttributes() {
        return {
            id: { default: null },
        }
    },

    parseHTML() {
        return [{ tag: 'div[data-type="image-upload-placeholder"]' }]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(HTMLAttributes, { 'data-type': 'image-upload-placeholder' })]
    },

    addNodeView() {
        return ({ node, editor }) => {
            const dom = document.createElement('div')
            dom.classList.add('relative', 'border', 'border-dashed', 'p-4', 'text-center', 'text-gray-500')
            dom.innerText = 'Drop image here or click to upload'

            dom.addEventListener('click', () => {
                const input = document.createElement('input')
                input.type = 'file'
                input.accept = 'image/*'
                input.click()

                input.onchange = async () => {
                    const file = input.files[0]
                    if (file) {
                        await uploadFile(editor, file)
                    }
                }
            })

            dom.addEventListener('dragover', e => e.preventDefault())
            dom.addEventListener('drop', async e => {
                e.preventDefault()
                const file = e.dataTransfer?.files[0]
                if (file && file.type.startsWith('image/')) {
                    await uploadFile(editor, file)
                }
            })

            return {
                dom
            }
        }
    }
})
