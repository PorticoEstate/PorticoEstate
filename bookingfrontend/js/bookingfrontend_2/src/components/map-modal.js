import {GenerateRandomHash} from "../helpers/hash";

const removeTrailingComma = (str) => {
    if (str.endsWith(',')) {
        str = str.slice(0, -1);
    }
    return str;
}

ko.components.register('map-modal', {
    viewModel: {
        createViewModel: function (params, componentInfo) {
            const ViewModel = (params) => {
                let mapParts = componentInfo.templateNodes.map(a => a.textContent)
                if (mapParts.length === 1) {
                    if (mapParts[0].includes('\n')) {
                        mapParts = mapParts[0].split('\n')
                    }
                }

                mapParts = mapParts.map(text => removeTrailingComma(text.trim()))
                self.mapParts = ko.observableArray(mapParts);

                self.randomHash = ko.observable(GenerateRandomHash())

                // self.content = params.content
                self.descriptionExpanded = ko.observable(false);
                self.toggleDescription = () => {
                    self.descriptionExpanded(!self.descriptionExpanded())
                }
                self.shortPath = ko.computed(() => {
                    return self.mapParts().slice(0, 2).join(', ');
                })
                self.longPath = ko.computed(() => {
                    return `https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=${self.mapParts().join(',')}`;
                })
                self.openModal = () => {
                    $('#mapModal' + randomHash()).modal('show');
                };
                self.closeModal = () => {
                    $('#mapModal' + randomHash()).modal('hide');
                };
            }
            return ViewModel(params);
        },

    },
    // language=HTML
    template: `
        <button type="button" class="pe-btn pe-btn--transparent link-text link-text-secondary p-0"
                data-bind="click: function() { openModal() }">
            <div class="text-primary d-flex gap-1 align-items-center">
                <i class="fa-solid fa-map-pin" style="font-size: 1rem"></i>
                <span class="text-label" data-bind="text: shortPath"></span>
            </div>
        </button>

        <div class="modal fade" data-bind="attr: {id: 'mapModal' + randomHash()}" tabindex="-1"
             aria-labelledby="mapModal"
             aria-hidden="true">
            <div class="modal-dialog modal-xl" style="height: 100%">
                <div class="modal-content" style="height: 80%;">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center pt-0 pb-4">
                        <div style="display: flex; justify-content: center; align-items: stretch; width: 100%;">
                            <iframe id="iframeMap" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                    style="width: 100%;" data-bind="attr:{src: longPath()}">
                            </iframe>
                        </div>
                        <br/>

                    </div>
                </div>
            </div>
        </div>
    `
});
