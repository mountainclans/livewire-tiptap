import StarterKit from "@tiptap/starter-kit";

const baseOptions = {
    textStyle: false,
    bold: false,
    marks: {
        bold: false,
    },
    // не удаляем пустые параграфы
    hardBreak: {
        keepMarks: true,
    },
};

/**
 * Стартовый набор под конкретный редактор: overrides гасит узлы, которые
 * не разрешены его набором инструментов (bulletList: false и т.п.).
 */
export function configuredStarterKit(overrides = {}) {
    return StarterKit.configure({...baseOptions, ...overrides});
}

export const ConfiguredStarterKit = configuredStarterKit();
