import TextStyle from "@tiptap/extension-text-style";

export const ConfiguredTextStyle = TextStyle.extend({
    addAttributes() {
        return {
            fontSize: {
                default: null,
                parseHTML: element => element.style.fontSize,
                renderHTML: attributes => {
                    if (!attributes.fontSize) {
                        return {};
                    }
                    return {style: 'font-size: ' + attributes.fontSize};
                },
            },
        };
    },
});
