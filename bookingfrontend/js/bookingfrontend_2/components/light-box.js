
allowedEmbedTypes = ['embed', 'youtube', 'vimeo', 'instagram', 'url'];
allowedMediaTypes = [...allowedEmbedTypes, 'image', 'html'];

ko.components.register('light-box', {
    viewModel: class LightboxViewModel {

        /**
         * A mapping of resource IDs to their corresponding building resource details.
         * @type {KnockoutObservableArray<{src: string, alt: string}>}
         */
        images = null;
        currentIndex = ko.observable(0);

        constructor(params) {
            this.images = params.images; // This is an observable array
            this.hash = this.randomHash();
            this.settings = Object.assign(Object.assign(Object.assign({}, bootstrap.Modal.Default), bootstrap.Carousel.Default), {
                interval: false,
                target: '[data-toggle="lightbox"]',
                gallery: '',
                size: 'xl',
                constrain: true
            });
            this.settings = Object.assign(Object.assign({}, this.settings), params.options);

            this.modalOptions = (() => this.setOptionsFromSettings(bootstrap.Modal.Default))();
            this.carouselOptions = (() => this.setOptionsFromSettings(bootstrap.Carousel.Default))();

            this.hasImages = ko.computed(() => {
                return this.images().length > 0;
            });
            this.additionalImageCount = ko.computed(() => {
                var count = this.images().length - 4;
                return count > 0 ? count : 0;
            })
            this.currentImage = ko.computed(() => {
                return this.hasImages() ? this.images()[this.currentIndex()] : {};
            });
            this.carouselClasses = ko.computed(() => {
                return `lightbox-carousel carousel slide ${this.settings.size === 'fullscreen' ? 'position-absolute w-100 translate-middle top-50 start-50' : ''}`
            })


            this.processedImages = ko.pureComputed(() => {
                return this.images().map((image, i) => {
                    // Define regex patterns for Youtube and Instagram URLs
                    const youtubeRegex = /.*youtu(.be\/|v\/|embed\/|watch\?v=)([^#&?]*).*/;
                    const instagramRegex = /.*instagram.com.*/;

                    // Get src from image
                    let src = image.src.replace(/\/$/, '');
                    const regex = new RegExp(`^(${allowedMediaTypes.join('|')})`, 'i');
                    const isHtml = /^html/.test(src);
                    const isForcedImage = /^image/.test(src);

                    // Fetch Instagram and YouTube URLs
                    const youtubeMatch = src.match(youtubeRegex);
                    const instagramMatch = src.match(instagramRegex);
                    const youtubeLink = youtubeMatch && youtubeMatch[2].length === 11 ? `https://www.youtube.com/embed/${youtubeMatch[2]}` : null;
                    const instagramEmbed = instagramMatch ? src + '/embed' : null;

                    let inner;
                    if (youtubeLink) {
                        inner = youtubeLink;
                    } else if (instagramEmbed) {
                        inner = instagramEmbed;
                    } else {
                        inner = src;
                    }

                    // Determine whether the src link is an image
                    const isImg = !youtubeLink && !instagramEmbed;

                    if (regex.test(src)) {
                        src = src.replace(regex, '');
                    }
                    const imgClasses = this.settings.constrain ? 'mw-100 mh-100 h-auto w-auto m-auto top-0 end-0 bottom-0 start-0' : 'h-100 w-100';

                    const params = new URLSearchParams(src.split('?')[1]);
                    let caption = params.get('caption') ? `<p class="lightbox-caption m-0 p-2 text-center text-white small"><em>${params.get('caption')}</em></p>` : '';
                    let url = src;
                    if (caption) {
                        try {
                            url = new URL(src);
                            url.searchParams.delete('caption');
                            url = url.toString();
                        } catch (e) {
                            url = src;
                        }
                    }

                    // Returning data to use in template
                    return {
                        imgClasses: imgClasses,
                        url: url,
                        caption: caption,
                        isHtml: isHtml,
                        isForcedImage: isForcedImage,
                        isImg: isImg,
                        inner: inner
                    };
                });
            });

        }

        openModal(index) {
            if (this.hasImages()) {
                this.currentIndex(index);
                $("#lightboxModal").modal('show');
                this.attachArrowKeyHandlers();
                $('#lightboxModal').on('hidden.bs.modal', function () {
                    this.detachArrowKeyHandlers();
                });
            }
        };

        next = function() {
            if (this.hasImages()) {
                var nextIndex = this.currentIndex() < this.images().length - 1 ? this.currentIndex() + 1 : 0;
                this.currentIndex(nextIndex);
            }
        };

        prev = function() {
            if (this.hasImages()) {
                var prevIndex = this.currentIndex() > 0 ? this.currentIndex() - 1 : this.images().length - 1;
                this.currentIndex(prevIndex);
            }
        };

        attachArrowKeyHandlers() {
            $(document).on('keydown', (e) => {
                if (e.keyCode === 37) { // Left arrow key
                    this.prev();
                }
                if (e.keyCode === 39) { // Right arrow key
                    this.next();
                }
            }).detach();
        };

        detachArrowKeyHandlers() {
            $(document).off('keydown');
        };

        closeModal() {
            $("#lightboxModal").modal('hide');
        };

        setOptionsFromSettings(obj) {
            return Object.keys(obj).reduce((p, c) => Object.assign(p, {[c]: this.settings[c]}), {});
        }

        randomHash(length = 8) {
            return Array.from({length}, () => Math.floor(Math.random() * 36).toString(36)).join('');
        }
    },
    // language=HTML
    template: `
        <!-- ko if: hasImages -->
        <div class="modal lightbox fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 bg-transparent">
                    <div class="modal-body p-0">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 p-3"
                                data-bs-dismiss="modal" aria-label="Close" style="font-size: 2rem;z-index: 2; background: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <!-- Carousel code here -->
                        <div class="lightbox-carousel carousel slide"
                             data-bind="css: carouselClasses, carousel: carouselOptions">
                            <div class="carousel-inner" data-bind="foreach: {data: processedImages, as: 'image'}">
                                <div class="carousel-item"
                                     data-bind="css: {'active': $index() === $parent.currentIndex()}, style: { minHeight: '100px' }">
                                    <div class="position-absolute top-50 start-50 translate-middle text-white">
                                        <div class="spinner-border" role="status"></div>
                                    </div>
                                    <div class="ratio ratio-16x9" style="background-color: #000;">
                                        <!-- ko if: image.isImg -->
                                        <img data-bind="attr: { src: image.url, class: 'd-block ' + image.imgClasses + ' img-fluid', style: { 'z-index': 1, 'object-fit': 'contain' } }"/>
                                        <!-- /ko -->

                                        <!-- ko ifnot: image.isImg -->
                                        <iframe data-bind="attr: { src: image.inner, title: image.isYoutube ? 'YouTube video player' : 'Instagram Embed', frameborder: '0', allow: image.isYoutube ? 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' : '', allowfullscreen: true }"></iframe>
                                        <!-- /ko -->
                                    </div>
                                    <!-- ko if: image.caption -->
                                    <p class="lightbox-caption m-0 p-2 text-center text-white small"
                                       data-bind="html: image.caption"></p>
                                    <!-- /ko -->
                                </div>
                            </div>
                            <!-- Carousel Controls -->
                            <!-- ko if: images().length > 0 -->
                            <button class="carousel-control carousel-control-prev h-75 m-auto" type="button"
                                    data-bs-target="#lightboxCarousel" data-bs-slide="prev" data-bind="click: prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control carousel-control-next h-75 m-auto" type="button"
                                    data-bs-target="#lightboxCarousel" data-bs-slide="next" data-bind="click: next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            <!-- /ko -->
                        </div>
                        <!-- You might replace it with the carousel component in bootstrap -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /ko -->
        <!-- Only display if there are images -->
        <!-- ko if: hasImages -->
        <div class="row">
            <!-- Iterate over the first four images or all if less than four -->
            <!-- ko foreach: images.slice(0, 4) -->
            <div class="col-md-3">
                <div class="img-container-building">
                    <img data-bind="attr: { src: src, alt: alt }, click: function() { $parent.openModal($index()) }"
                         class="img-thumbnail-building cursor-pointer"/>

                    <!-- If it's the fourth image and there are additional images, show overlay -->
                    <!-- ko if: $index() === 3 && $parent.additionalImageCount() > 0 -->
                    <div class="overlay" data-bind="click: function() { $parent.openModal($index()) }">
                        <span class="additional-count">+<!-- ko text: $parent.additionalImageCount -->
                            <!-- /ko --></span>
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