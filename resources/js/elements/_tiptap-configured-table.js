import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableHeader from '@tiptap/extension-table-header'
import TableCell from '@tiptap/extension-table-cell'

export const ConfiguredTable = Table.configure({
    resizable: false,
    HTMLAttributes: {},
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
