ko.bindingHandlers.withAfterRender = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        var value = ko.unwrap(valueAccessor());  // Ensure unwrapped value if observable

        ko.applyBindingsToNode(element, { visible: true }, bindingContext);
        if (value && value.afterRender) {
            value.afterRender.call(viewModel, element);
        }
    }
};
