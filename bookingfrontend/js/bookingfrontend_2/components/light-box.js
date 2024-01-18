ko.components.register('light-box', {
    viewModel: function(params) {
        var self = this;
        self.images = params.images; // This is an observable array
        self.currentIndex = ko.observable(0);

        self.hasImages = ko.computed(function() {
            return self.images().length > 0;
        });

        self.currentImage = ko.computed(function() {
            return self.hasImages() ? self.images()[self.currentIndex()] : {};
        });

        self.openModal = function(index) {
            if (self.hasImages()) {
                self.currentIndex(index);
                $("#lightboxModal").modal('show');
                self.attachArrowKeyHandlers();
                $('#lightboxModal').on('hidden.bs.modal', function () {
                    self.closeModal()
                });
            }
        };

        self.closeModal = function() {
            // $("#lightboxModal").modal('hide');
            self.detachArrowKeyHandlers();
        };

        self.next = function() {
            if (self.hasImages()) {
                var nextIndex = self.currentIndex() < self.images().length - 1 ? self.currentIndex() + 1 : 0;
                self.currentIndex(nextIndex);
            }
        };

        self.prev = function() {
            if (self.hasImages()) {
                var prevIndex = self.currentIndex() > 0 ? self.currentIndex() - 1 : self.images().length - 1;
                self.currentIndex(prevIndex);
            }
        };

        self.attachArrowKeyHandlers = function() {
            $(document).on('keydown', function(e) {
                if (e.keyCode === 37) { // Left arrow key
                    self.prev();
                }
                if (e.keyCode === 39) { // Right arrow key
                    self.next();
                }
            }).detach();
        };
        self.additionalImageCount = ko.computed(function() {
            var count = self.images().length - 4;
            return count > 0 ? count : 0;
        });

        self.detachArrowKeyHandlers = function() {
            $(document).off('keydown');
        };
    },
    template: `
        <div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-labelledby="lightboxModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Conditional content based on whether images are available -->
                    <!-- ko if: hasImages -->
                    <div class="modal-header">
                        <h5 class="modal-title" data-bind="text: currentImage().alt"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img data-bind="attr: { src: currentImage().src, alt: currentImage().alt }" class="img-fluid" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bind="click: prev">Previous</button>
                        <button type="button" class="btn btn-secondary" data-bind="click: next">Next</button>
                    </div>
                    <!-- /ko -->
                    <!-- ko ifnot: hasImages -->
                    <div class="modal-body">
                        <p>No images available.</p>
                    </div>
                    <!-- /ko -->
                </div>
            </div>
        </div>

         <!-- Only display if there are images -->
        <!-- ko if: hasImages -->
        <div class="row">
            <!-- Iterate over the first four images or all if less than four -->
            <!-- ko foreach: images.slice(0, 4) -->
            <div class="col-md-3">
                <div class="img-container-building">
					<img data-bind="attr: { src: src, alt: alt }, click: function() { $parent.openModal($index()) }" class="img-thumbnail-building cursor-pointer" />
	
					<!-- If it's the fourth image and there are additional images, show overlay -->
					<!-- ko if: $index() === 3 && $parent.additionalImageCount() > 0 -->
					<div class="overlay" data-bind="click: function() { $parent.openModal($index()) }">
						<span class="additional-count">+<!-- ko text: $parent.additionalImageCount --><!-- /ko --></span>
					</div>
					<!-- /ko -->
                </div>
            </div>
            <!-- /ko -->
        </div>
        <!-- /ko -->

        <!-- Display message if there are no images -->
        <!-- ko ifnot: hasImages -->
        <div class="col-12">
            <p class="text-center">No images available.</p>
        </div>
        <!-- /ko -->
    `
});