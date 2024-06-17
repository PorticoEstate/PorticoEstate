import {phpGWLink} from "../helpers/util";
import './time-slot-pill'

class ShoppingBasketModel {
    constructor(params) {
        this.applicationCartItems = params.applicationCartItems;
        this.cartElementExpanded = ko.observable(true);
        this.popperInstance = ko.observable();
        this.isVisible = ko.observable(false);
        this.deleteItem = params.deleteItem;
        this.applicationCartItems.subscribe(d => console.log(d))
    }

    initializePopper(element) {
        const button = element.previousElementSibling;

        this.popperInstance(new Popper(button, element, {
            placement: 'bottom-end',
            strategy: 'fixed'
        }));
    }

    destroyPopperInstance() {
        if (this.popperInstance()) {
            this.popperInstance().destroy();
            this.popperInstance(null);
        }
    }

    togglePopper(newSate = undefined) {
        let popperInfo = this.popperInstance();

        if (popperInfo) {
            this.isVisible(newSate || !this.isVisible());
            popperInfo.update()
        }
    }

    toggleCartExpansion() {
        this.cartElementExpanded(!this.cartElementExpanded());
    };

    popperAttr = ko.computed(() => {
        if(!this.isVisible) {
            return {}
        }
        return this.isVisible() ? {'data-show' : ''} : {};
    })
}


ko.components.register('shopping-basket', {
    viewModel: {
        createViewModel: (params, componentInfo) => new ShoppingBasketModel(params, componentInfo)
    },
    // language=HTML
    template: `
        <button class="pe-btn pe-btn--transparent menu__toggler" type="button" aria-expanded="false"
                data-bind="click: () => togglePopper()">
    <span class="text">
        <i class="fa-solid fa-basket-shopping"></i>
        <span class="badge badge-light" style="position: absolute;top: -5px;"
              data-bind="visible: applicationCartItems().length > 0, text: applicationCartItems().length">-1
        </span>
    </span>
        </button>
        <div class="poppercontent"
             style="z-index: 11"
             data-bind="withAfterRender: {afterRender: (d) => initializePopper(d)}, visible: applicationCartItems().length > 0 && isVisible, attr: popperAttr()">
            <div class="dialog-container">
                <div class="dialog-header">
                    <span class="dialog-title">Søknader klar for innsending</span>
                    <button type="button" class="btn-close" data-bind="click: () => togglePopper(false)"
                            aria-label="Close"></button>
                </div>
                <div class="dialog-ingress">
                    <p>Her er en oversikt over dine søknader som er klar for innsending og godkjennelse.</p>
                </div>
                <div class="dialog-content">
                    <!-- Content goes here -->
                    <div class="article-table-wrapper" data-bind="visible: cartElementExpanded">
                        <div data-bind="foreach: applicationCartItems">

                            <div class="article-table-header title-only">
                                <div class="resource-name d-flex gap-2 align-items-center">
                                    <i class="fa-solid fa-location-dot"></i><h3 class="p-0 m-0" data-bind="text: building_name"></h3>
                                </div>
                                <div class="resource-expand float-right">
                                    <span class="far fa-trash-alt mr-2" data-bind="click: () => $parent.deleteItem($data)"></span>
                                </div>
                            </div>
                            <div class="category-table">

                                    <div class="category-header category-article-row d-flex flex-wrap">
                                        <!-- ko foreach: { data: resources, as: 'item' } -->

                                        <div class="pill pill">
                                            <i class="fa-solid fa-layer-group"></i>
                                            <div class="pill-label" data-bind="text: item.name"></div>
                                        </div>
                                        <!-- /ko -->

                                    </div>

                                <div class="category-articles">
                                    <div class="category-article-row d-flex  flex-wrap">
                                        <!-- ko foreach: { data: dates, as: 's' } -->
                                        <time-slot-pill params="date: s"></time-slot-pill>
                                        <!-- /ko -->

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="dialog-actions">
                    <a class="pe-btn pe-btn-primary pe-btn-colour-primary link-text link-text-white d-flex gap-3"
                       data-bind="attr: { href: phpGWLink('bookingfrontend/', {menuaction:'bookingfrontend.uiapplication.add_contact' }, false) } ">
                        <div class="text-bold">
                            <trans params="group: 'bookingfrontend', tag:'complete_applications'"></trans>
                        </div>
                        <div class="text-bold d-flex align-items-center">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    `
});

