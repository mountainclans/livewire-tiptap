import Bold from "@tiptap/extension-bold";

export const ConfiguredBold = Bold.extend({
    // Override the renderHTML method
    renderHTML({mark, HTMLAttributes}) {
        const {style, ...rest} = HTMLAttributes;

        // Merge existing styles with font-weight
        const newStyle = 'font-weight: bold;' + (style ? ' ' + style : '');

        return ['span', {...rest, style: newStyle.trim()}, 0];
    },
    // Ensure it doesn't exclude other marks
    addOptions() {
        return {
            ...this.parent?.(),
            HTMLAttributes: {},
        };
    },
});
