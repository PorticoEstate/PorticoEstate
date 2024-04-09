import '../helpers/withAfterRender'

ko.components.register('collapsable-text', {
    viewModel: function (params) {
        self.content = params.content
        self.descriptionExpanded = ko.observable(false);

        self.contentSize = ko.observable(0);
        self.containerSize = ko.observable(0);
        self.contentElement = ko.observable(null);

        self.toggleDescription = () => {
            self.descriptionExpanded(!self.descriptionExpanded())
        }
        self.afterRenderContent = (element) => {
            console.log("after element set", element)
            self.contentElement(element);
        }

        self.isActive = ko.computed(() => {
            const elem = self.contentElement();
            if (!elem) {
                return true;
            }
            console.log("setting active", elem.parentElement.scrollHeight, elem.parentElement.clientHeight)
            return (elem.parentElement.scrollHeight > elem.parentElement.clientHeight || elem.parentElement.scrollWidth > elem.parentElement.clientWidth)
        })


    },
    // language=HTML
    template: `
        <div class="col-sm-12 d-flex flex-column collapsible-content collapsed-description"
             data-bind="css: {'collapsed-description-fade': !descriptionExpanded() && isActive, 'collapsed-description': !descriptionExpanded()}">
            <!-- ko if: content -->
            <p data-bind="html: content,withAfterRender: { afterRender: afterRenderContent}"></p>
            <!-- /ko -->
            <!-- ko ifnot: content -->
            <p data-bind="template: { nodes: $componentTemplateNodes }, withAfterRender: { afterRender: afterRenderContent}"></p>
            <!-- /ko -->
        </div>
        <div class="col-sm-12 " data-bind="visible: isActive">
            <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"
                    data-bind="click: toggleDescription">
                <!-- ko if: descriptionExpanded() -->
                <span><trans params="group: 'bookingfrontend',tag: 'show_less'"></span>
                <!-- /ko -->
                <!-- ko ifnot: descriptionExpanded() -->
                <span><trans params="group: 'bookingfrontend',tag: 'show_more'"></span>
                <!-- /ko -->
                <i class="fa"
                   data-bind="css: {'fa-chevron-up': descriptionExpanded(), 'fa-chevron-down': !descriptionExpanded()}"></i>
            </button>
        </div>
    `
});
