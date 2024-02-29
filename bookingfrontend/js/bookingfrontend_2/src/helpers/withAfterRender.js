ko.bindingHandlers.withAfterRender = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        var value = valueAccessor();

        ko.applyBindingsToNode(element, {visible: true}, bindingContext);
        if (value.afterRender) {
            value.afterRender(element);
        }

    }
};