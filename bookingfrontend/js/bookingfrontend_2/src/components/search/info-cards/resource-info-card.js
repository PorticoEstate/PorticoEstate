import '../../accordion-item'


ko.bindingHandlers.slidedownTarget = {
    init: function (element, valueAccessor) {
        var callback = valueAccessor();
        if (typeof callback === 'function') {
            callback(element);
        }
    }
};


function ResourceInfoCardViewModel(params) {
    this.resource = params.resource;
    this.buildings = ko.isObservable(params.buildings) ? params.buildings() : params.buildings;
    this.towns = params.towns;
    this.date = params.date;
    this.lang = params.lang;
    this.disableText = params.disableText || false;
    this.expanded = ko.observable(false);
    this.static = params.static || false;

    this.filterGroups = params.filterGroups || undefined


    this.cleanTownName = function (townName) {
        return townName.split('\n').map(line => {
            // Check if 'Bydel' is in the line
            if (line.toLowerCase().includes('bydel')) {
                // Remove 'Bydel'
                line = line.replace(/bydel/gi, '').trim();
            }
            // Capitalize first letter of each word
            return line.charAt(0).toUpperCase() + line.slice(1).toLowerCase();
        }).join('\n');
    };

    this.description_text = ko.computed(() => {
        if (this.disableText) {
            return ''
        }
        const description_json = JSON.parse(this.resource.description_json);
        return new DOMParser()
            .parseFromString(this.lang && description_json[this.lang] || description_json['no'], "text/html")
            .documentElement.textContent;
    })
    this.getHtml = (html) => {
        if (this.disableText) {
            return ''
        }
        const t = new DOMParser()
            .parseFromString(html, "text/html");
        return new DOMParser()
            .parseFromString(html, "text/html")
            .documentElement.innerHTML;
    }



    this.url = ko.computed(() => {
        if (!this.buildings || this.buildings.length === 0) {
            return '';
        }
        if (this.resource.simple_booking === 1) {
            return phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiresource.show',
                building_id: this.buildings?.[0].id,
                id: this.resource.id
            }, false);
        } else {
            return phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uiapplication.add',
                building_id: this.buildings?.[0].id,
                resource_id: this.resource.id
            }, false);
        }
    });

    this.locationUrl = ko.computed(() => {
        if (!this.buildings || this.buildings.length === 0) {
            return '';
        }
        return phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibuilding.show',
            id: this.buildings?.[0].id
        });
    });

    this.resourceUrl = ko.computed(() => {
        return phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uiresource.show',
            id: this.resource.original_id !== undefined ? this.resource.original_id : this.resource.id
        }, false, this.resource.remoteInstance?.webservicehost || undefined);
    });

    this.calendarId = `calendar-${this.resource.id}`;

    this.placeText = ko.computed(() => {
        const towns = this.towns().map(t => this.cleanTownName(t.name)).join(' • ');


        const remoteInstance = this.resource.remoteInstance?.name ? `<span class="text-overline">${this.resource.remoteInstance?.name}</span>` : '';

        return [remoteInstance, `<span class="text-overline">${towns}</span>`].filter(Boolean).join(' • ');
    });

    this.building = ko.computed(() => {
        return this.buildings?.map(b => {
            const buildingUrl = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uibuilding.show',
                id: b.original_id !== undefined ? b.original_id : b.id
            }, false, b.remoteInstance?.webservicehost || undefined);
            return `<a href="${buildingUrl}" class="link-text link-text-primary"><i class="fa-solid fa-location-dot"></i>${b.name}</a>`;
        }).join(' • ');
    });


    this.toggle = (data, clickEvent) => {
        if (clickEvent.target?.tagName === 'A' || clickEvent.target?.classList.contains('no-op')) {
            return true;
        }
        const target = clickEvent.target.closest('.js-slidedown.slidedown').querySelector('.js-slidedown-content.slidedown__content');
        const expanded = !this.expanded();

        expanded ? $(target).slideDown() : $(target).slideUp();

        this.expanded(expanded);
        return false;
    }
}

ko.components.register('resource-info-card', {
    viewModel: ResourceInfoCardViewModel,
    // language=HTML
    template: `
        <div class="col-12 mb-4">

            <div class="js-slidedown slidedown">
                <button class="js-slidedown-toggler slidedown__toggler" type="button"
                        data-bind="click: toggle, attr:{'aria-expanded': expanded}">
                    <div class="slider-header">
                         <span><div class="fa-solid fa-layer-group"></div> <span
                             data-bind="text: resource.name"></span></span>
                        <span class="slidedown__toggler__info" data-bind="html: placeText"></span>
                        <span class="slidedown__toggler__info" data-bind="html: building"></span>
                    </div>
                    <div class="slidedown-actions">
                        <a data-bind="attr: {'href': resourceUrl}" class="link-text link-text-primary"><i
                            class="fa-solid fa-expand no-op"></i></a>
                        <i class="fa-solid fa-chevron-down dropdown-icon text-primary"
                           data-bind="css: { open: expanded }"></i>
                    </div>
                </button>

                <div class="js-slidedown-content slidedown__content">
                    <!-- ko ifnot: disableText -->

                    <div class="accordion" data-bind="attr: {id: 'accordion' + resource.id}">
                        <accordion-item
                            params="title_tag: 'description', parentID: 'accordion' + resource.id, content: description_text">
                            <!--                            <p data-bind="html: description_text"></p>-->
                        </accordion-item>
                        <!-- ko if: !!resource.opening_hours -->

                        <accordion-item
                            params="title_tag: 'opening hours', title_group: 'booking', parentID: 'accordion' + resource.id, content: getHtml(resource.opening_hours)">

                        </accordion-item>
                        <!-- /ko -->
                        <!-- ko if: !!resource.contact_info -->

                        <accordion-item params=" title_tag: 'contact information', parentID: 'accordion' + resource.id, content: getHtml(resource.contact_info)">
                        </accordion-item>
                        <!-- /ko -->

                    </div>
                    <!--                    <div>-->
                    <!--                        -->
                    <!--                    </div>-->
                    <!-- /ko -->

                    <!-- ko if: expanded -->
                    <!-- ko if: date -->
                    <!-- ko component: {
                        name: 'pe-calendar',
                        params: {
                            building_id: buildings[0].original_id || buildings[0].id,
                            resource_id: resource.original_id || resource.id,
                            instance: resource.remoteInstance?.webservicehost || '',
                            dateString: date,
                            nointeraction: static,
                            filterGroups: filterGroups
                        }
                    } -->
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- /ko -->
                </div>
            </div>
        </div>

    `
});