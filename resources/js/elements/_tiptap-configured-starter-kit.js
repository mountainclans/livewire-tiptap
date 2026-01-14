import StarterKit from "@tiptap/starter-kit";

export const ConfiguredStarterKit = StarterKit.configure({
    textStyle: false,
    bold: false,
    marks: {
        bold: false,
    },
    // не удаляем пустые параграфы
    hardBreak: {
        keepMarks: true,
    },
});
