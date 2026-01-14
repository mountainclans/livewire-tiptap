import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableHeader from '@tiptap/extension-table-header'
import TableCell from '@tiptap/extension-table-cell'
import { Node, mergeAttributes } from '@tiptap/core'

// Обёртка для таблицы
export const TableWrapper = Node.create({
    name: 'tableWrapper',

    group: 'block',
    content: 'table',

    parseHTML() {
        return [
            {
                tag: 'div[data-table-wrapper]',
            },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            mergeAttributes(HTMLAttributes, {
                'data-table-wrapper': '',
                'class': 'table-wrapper'
            }),
            0
        ]
    },
})

export const ConfiguredTable = Table.configure({
    resizable: false,
    HTMLAttributes: {
        class: 'tiptap-table-editor',
    },
})

export const ConfiguredTableRow = TableRow.configure({
    HTMLAttributes: {},
})

export const ConfiguredTableHeader = TableHeader.configure({
    HTMLAttributes: {},
})

export const ConfiguredTableCell = TableCell.configure({
    HTMLAttributes: {},
})
