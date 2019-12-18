1) pasteHTML works if you set the isNotSplitEdgePoint in the function splitPoint()

//         isNotSplitEdgePoint: isInline
           isNotSplitEdgePoint : true

But I don't know what else it will break...



2) Double insert of image from clipboard;
https://github.com/summernote/summernote/issues/2717

var Clipboard = /** @class */ (function () {
    function Clipboard(context) {
        this.context = context;
        this.$editable = context.layoutInfo.editable;
    }
    Clipboard.prototype.initialize = function () {
        this.$editable.on('paste', this.pasteByEvent.bind(this));
    };
    /**
     * paste by clipboard event
     *
     * @param {Event} event
     */
    Clipboard.prototype.pasteByEvent = function (event) {
        var clipboardData = event.originalEvent.clipboardData;
        if (clipboardData && clipboardData.items && clipboardData.items.length) {
            var item = lists.head(clipboardData.items);
            if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
                this.context.invoke('editor.insertImagesOrCallback', [item.getAsFile()]);
		event.preventDefault(); // <<< ADD THIS LINE
            }
            this.context.invoke('editor.afterCommand');
        }
    };
    return Clipboard;
}());