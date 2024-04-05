
ko.components.register('collapsable-text', {
    viewModel: function (params) {
        self.content = params.content
        self.descriptionExpanded = ko.observable(false);

        self.toggleDescription = () => {
            self.descriptionExpanded(!self.descriptionExpanded())
        }
        // // computed observable to check if the button should be visible
        // self.isContentOverflowing = ko.observable(false);
        //
        // // Placeholder for content element reference
        // self.contentElement = null;
        //

        // // Function to check content height
        // self.checkContentHeight = () => {
        //     console.log("CHECK HEIGHT ")
        //     if (!self.contentElement) return;
        //
        //     // Convert em to pixels - assumes 16px base font size, adjust as needed
        //     const maxHeightEm = 9.55;
        //     const emSize = parseFloat(getComputedStyle(document.body).fontSize);
        //     const maxHeightPx = maxHeightEm * emSize;
        //
        //     // Update observable based on content height
        //     self.isContentOverflowing(self.contentElement.scrollHeight > maxHeightPx);
        // };
        // // Update the check whenever the content or its expansion state changes
        // self.descriptionExpanded.subscribe(self.checkContentHeight);
        // ko.computed(() => {
        //     if(self.content && self.content()) {
        //         // do nothing
        //     }
        //     ko.tasks.schedule(self.checkContentHeight); // Ensure check runs after DOM updates
        // });

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
