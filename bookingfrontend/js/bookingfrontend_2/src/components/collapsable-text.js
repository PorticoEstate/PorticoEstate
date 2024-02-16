
ko.components.register('collapsable-text', {
    viewModel: function (params) {
        self.content = params.content
        self.descriptionExpanded = ko.observable(false);
        self.toggleDescription = () => {
            self.descriptionExpanded(!self.descriptionExpanded())
        }
    },
    // language=HTML
    template: `
        <div class="col-sm-12 d-flex flex-column collapsible-content collapsed-description"
             data-bind="css: {'collapsed-description': !descriptionExpanded()}">
            <!-- ko if: content -->
            <p data-bind="html: content"></p>
            <!-- /ko -->
            <!-- ko ifnot: content -->
            <p data-bind="template: { nodes: $componentTemplateNodes }"></p>
            <!-- /ko -->

        </div>
        <div class="col-sm-12">
            <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"
                    data-bind="click: toggleDescription">
                <span data-bind="text: descriptionExpanded() ? 'Vis mindre' : 'Vis mer'"></span>
                <i class="fa"
                   data-bind="css: {'fa-chevron-up': descriptionExpanded(), 'fa-chevron-down': !descriptionExpanded()}"></i>
            </button>
        </div>
    `
});
