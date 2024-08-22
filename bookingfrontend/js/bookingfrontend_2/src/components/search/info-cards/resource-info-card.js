// resource-info-card.js



function ResourceInfoCardViewModel(params) {
    this.resource = params.resource;
    this.buildings = ko.isObservable(params.buildings) ? params.buildings() : params.buildings;
    this.towns = params.towns;
    this.date = params.date;
    this.lang = params.lang;
    this.disableText = params.disableText || false;
    this.expanded = ko.observable(false);
    this.slideDownTarget = ko.observable();
    this.static = params.static || false;

    this.filterGroups = params.filterGroups ||undefined


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
        if(this.disableText) {
            return ''
        }
        const description_json = JSON.parse(this.resource.description_json);
        return new DOMParser()
            .parseFromString(this.lang && description_json[this.lang] || description_json['no'], "text/html")
            .documentElement.textContent;
    })

    this.url = ko.computed(() => {
        if(!this.buildings || this.buildings.length === 0) {
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
        if(!this.buildings || this.buildings.length === 0) {
            return '';
        }
        return phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibuilding.show',
            id: this.buildings?.[0].id
        });
    });

    this.calendarId = `calendar-${this.resource.id}`;

    this.infoText = ko.computed(() => {
        const towns = this.towns().map(t => this.cleanTownName(t.name)).join(' • ');
        const buildings = this.buildings?.map(b => {
            const buildingUrl = phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uibuilding.show',
                id: b.original_id !== undefined ? b.original_id : b.id
            }, false, b.remoteInstance?.webservicehost || undefined);
            return `<a href="${buildingUrl}" class="link-text link-text-primary"><i class="fa-solid fa-location-dot"></i>${b.name}</a>`;
        }).join(' • ');

        const remoteInstance = this.resource.remoteInstance?.name ? `<span class="text-overline">${this.resource.remoteInstance?.name}</span>` : '';

        return [remoteInstance, `<span class="text-overline">${towns}</span>`, buildings].filter(Boolean).join(' • ');
    });

    this.addSlideDown = (target)  => {
        this.slideDownTarget(target)
    }
    this.toggle = (data,clickEvent)  => {
        if(clickEvent.target?.tagName === 'A') {
            return true;
        }
        const expanded = !this.expanded();

        expanded ? $(this.slideDownTarget()).slideDown(): $(this.slideDownTarget()).slideUp();

        this.expanded(expanded);
        return false;
    }
}

ko.components.register('resource-info-card', {
    viewModel: ResourceInfoCardViewModel,
    template: `
        <div class="col-12 mb-4">
            <div class="js-slidedown slidedown">
                <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false" data-bind="click: toggle">
                    <span><div class="fa-solid fa-layer-group"></div> <span data-bind="text: resource.name"></span></span>
                    <span class="slidedown__toggler__info" data-bind="html: infoText"></span>
                </button>
                <div class="js-slidedown-content slidedown__content" data-bind="withAfterRender: { afterRender: addSlideDown}">
                    <!-- ko ifnot: disableText -->

                    <div>
                        <p data-bind="html: description_text"></p>
                    </div>
                    <!-- /ko -->

                    <!-- ko if: expanded && date -->
                    <pe-calendar params="building_id: buildings[0].original_id || buildings[0].id, resource_id: resource.original_id || resource.id, instance: resource.remoteInstance?.webservicehost || '', dateString: date, nointeraction: static, filterGroups: filterGroups"></pe-calendar>
                    <!-- /ko -->
                </div>
            </div>
        </div>
    `
});
