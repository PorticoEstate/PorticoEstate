
ko.components.register('accordion-item', {
    viewModel: function (params) {
        const self = this;
        self.title = params.title || 'Default Title';
        self.title_tag = params.title_tag;
        self.title_group = params.title_group || 'bookingfrontend';
        self.parentID=params.parentID;
        self.uniqueID = self.parentID + '-' + (params.id || (self.title_tag || self.title).replace(/\s+/g, '-').toLowerCase());  // Unique ID for each item
        self.content = params.content


    },
    // language=HTML
    template: `
        <div class="accordion-item">
            <h3 class="accordion-header" data-bind="attr: { 'id': uniqueID + '-header' }">
                <button class="accordion-button collapsed text-xl" type="button" data-bs-toggle="collapse" aria-expanded="false"
                        data-bind="attr: { 'data-bs-target': '#' + uniqueID + '-collapse', 'aria-controls': uniqueID + '-collapse' }">
                    <!-- ko if: title_tag -->
                    <span><trans params="group: title_group,tag: title_tag"></trans></span>
                    <!-- /ko -->
                    <!-- ko ifnot: title -->
                    <span data-bind="text: title"></span>
                    <!-- /ko -->
                </button>
            </h3>
            <div class="accordion-collapse collapse" data-bind="attr: { 'id': uniqueID + '-collapse', 'aria-labelledby': uniqueID + '-header' }">
                <div class="accordion-body">
                    <!-- ko if: content -->
                    <p data-bind="html: content"></p>
                    <!-- /ko -->
                    <!-- ko ifnot: content -->
                    <p data-bind="template: { nodes: $componentTemplateNodes }"></p>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    `
});