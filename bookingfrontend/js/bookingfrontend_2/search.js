class OrganizationSearch {
    data = {
        activities: ko.observableArray([]),
        selected_activities: ko.observableArray([]),
        organizations: ko.observableArray([]),
        selected_organizations: ko.observableArray([]),
        text: ko.observable("")
    }

    constructor() {
        const organizationEl = document.getElementById("search-organization");
        ko.cleanNode(organizationEl);
        ko.applyBindings(this.data, organizationEl);

        this.data.text.subscribe(text => {
            this.search();
        })

        this.data.selected_activities.subscribe(activities => {
            this.search();
        })
    }

    reset() {
        $('#search-organization-activities').val([]).trigger('change');
        this.data.text("");
    }

    search() {
        let organizations = [];
        const el = emptySearch();
        if (this.data.text() !== "" || this.data.selected_organizations().length > 0 || this.data.selected_activities().length > 0) {
            const re = new RegExp(this.data.text(), 'i');
            organizations = this.data.organizations().filter(o => o.name.match(re))
            if (this.data.selected_organizations().length > 0) {
                organizations = organizations.filter(o => this.data.selected_organizations().some(ko => ko.id === o.id))
            }
            if (this.data.selected_activities().length > 0) {
                let ids = [];
                for (const activity of this.data.selected_activities()) {
                    ids.push(...getAllSubRowsIds(this.data.activities(), activity.id))
                }
                // Unique
                ids = [...new Set(ids)];
                organizations = organizations.filter(o => ids.some(id => id === o.activity_id))
            }
            this.addInfoCards(el, organizations);
        } else {
            fillSearchCount(null);
        }

        createJsSlidedowns();
    }

    addInfoCards = (el, organizations) => {
        const append = [];
        for (const organization of organizations) {
            append.push(`
    <div class="col-12 mb-4">
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
          <span>${organization.name}</span>
          <span class="slidedown__toggler__info">
          ${joinWithDot([organization.email, organization.street])}
          </span>
        </button>
        <div class="js-slidedown-content slidedown__content">
          <p>
            ${organization.description}
            <ul>
                <li>Hjemmeside: ${organization.homepage}</li>
                <li>Tlf: ${organization.phone}</li>
                <li>E-post: ${organization.email}</li>
                <li>Adresse: ${organization.street}</li>
                <li>Postnr: ${organization.zip_code}</li>
                <li>Poststed: ${organization.city}</li>
                <li>Distrikt: ${organization.district}</li>
                <li><a href="${phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uiorganization.show',
                    id: organization.id
                }, false)}">Mer info</a></li>
            </ul>
          </p> 
        </div>
      </div>
    </div>
`
            )
        }
        el.append(append.join(""));
        fillSearchCount(organizations);
    }
}

class BookingSearch {
    data = {
        towns_data: ko.observableArray([]),
        towns: ko.observableArray([]),
        selected_town: ko.observable(),
        buildings: ko.observableArray([]),
        selected_buildings: ko.observableArray([]),
        building_resources: ko.observableArray([]),
        activities: ko.observableArray([]),
        selected_activities: ko.observableArray([]),
        resource_activities: ko.observableArray([]),
        resource_categories: ko.observableArray([]),
        selected_resource_categories: ko.observableArray([]),
        resource_category_activity: ko.observableArray([]),
        resources: ko.observableArray([]),
        selected_resources: ko.observableArray([]),
        facilities: ko.observableArray([]),
        selected_facilities: ko.observableArray([]),
        resource_facilities: ko.observableArray([]),
        text: ko.observable(""),
        date: ko.observable("")
    }

    activity_cache = {};

    constructor() {
        const bookingEl = document.getElementById("search-booking");
        ko.cleanNode(bookingEl);
        ko.applyBindings(this.data, bookingEl);
        this.updateBuildings(null);

        this.data.text.subscribe(_ => this.search())
        this.data.selected_buildings.subscribe(_ => this.search())
        this.data.selected_resource_categories.subscribe(_ => this.search())
        this.data.selected_facilities.subscribe(_ => this.search())
        this.data.selected_activities.subscribe(_ => this.search())
        this.data.towns.subscribe(_ => this.updateBuildings(null));

        this.data.selected_town.subscribe(town => {
            this.updateBuildings(town);
            this.search();
        })

        this.data.towns_data.subscribe(towns => {
            this.data.towns(
                [...new Set(towns.map(item => item.name))]
                    .sort()
                    .map(name => ({name: htmlDecode(name), id: towns.find(i => i.name === name).id}))
            )
        })

        this.data.activities.subscribe(activities => {
            for (const activity of activities) {
                this.activity_cache[activity.id] = getAllSubRowsIds(activities, activity.id);
            }
        })
    }

    reset() {
        this.data.selected_town(null);
        $('#search-booking-activities').val([]).trigger('change');
        $('#search-booking-building').val([]).trigger('change');
        $('#search-booking-resource_categories').val([]).trigger('change');
        $('#search-booking-facilities').val([]).trigger('change');
        this.data.text("");
        this.data.date("");
    }

    search() {
        let resources = [];
        const el = emptySearch();
        let hasSearch = false;
        if (this.data.selected_town() !== undefined ||
            this.data.selected_buildings().length > 0 ||
            this.data.selected_facilities().length > 0 ||
            this.data.selected_activities().length > 0 ||
            this.data.selected_resource_categories().length > 0
        ) {
            resources = this.data.resources();
            if (this.data.selected_town() !== undefined)
                resources = resources.filter(resource => this.data.building_resources().some(br => this.data.buildings().some(b => b.id === br.building_id && resource.id === br.resource_id)));
            if (this.data.selected_buildings().length > 0) {
                resources = resources.filter(resource => this.data.building_resources().some(br => this.data.selected_buildings().some(sb => sb.id === br.building_id && resource.id === br.resource_id)));
            }
            if (this.data.selected_facilities().length > 0) {
                resources = resources.filter(resource => this.data.resource_facilities().some(rf => this.data.selected_facilities().some(sf => sf.id === rf.facility_id && resource.id === rf.resource_id)));
            }
            if (this.data.selected_activities().length > 0) {
                resources = resources.filter(resource => this.data.resource_activities().some(ra => this.data.selected_activities().some(sa => this.activity_cache[sa.id].includes(ra.activity_id) && resource.id === ra.resource_id)));
            }
            if (this.data.selected_resource_categories().length > 0) {
                const activities = [...new Set(this.data.resource_category_activity().filter(activity => this.data.selected_resource_categories().some(rc => rc.id === activity.rescategory_id)).map(a => a.activity_id))];
                resources = resources.filter(resource => this.data.resource_activities().some(ra => activities.some(sa => this.activity_cache[sa].includes(ra.activity_id) && resource.id === ra.resource_id)));
            }

            hasSearch = true;
        }
        if (this.data.text() !== "") {
            if (!hasSearch)
                resources = this.data.resources();
            const re = new RegExp(this.data.text(), 'i');
            resources = resources.filter(resource => resource.name.match(re))
            hasSearch = true;
        }

        if (hasSearch) {
            this.addInfoCards(el, resources);
        } else {
            fillSearchCount(null);
        }
        createJsSlidedowns();
    }

    updateBuildings = (town = null) => {
        this.data.buildings(
            this.data.towns_data().filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: htmlDecode(item.b_name)}))
        )
    }

    getBuildingsFromResource = (resource_id) => {
        const building_resources = this.data.building_resources().filter(br => br.resource_id===resource_id);
        const ids = building_resources.map(br => br.building_id);
        return this.data.buildings().filter(b => ids.includes(b.id));
    }

    getTownFromBuilding = (buildings) => {
        const ids = buildings.map(b => +b.id)
        return this.data.towns_data().filter(t => ids.includes(+t.b_id));
    }

    addInfoCards(el, resources) {
        const append = [];
        for (const resource of resources) {
            const buildings = this.getBuildingsFromResource(resource.id);
            const towns = this.getTownFromBuilding(buildings);
            if (towns.length>0) {
                append.push(`
    <div class="col-12 mb-4">
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
          <span>${resource.name}</span>
            <span class="slidedown__toggler__info">
                ${joinWithDot([...towns.map(t => t.name), ...buildings.map(b => b.name)])}
            </span>
        </button>
        <div class="js-slidedown-content slidedown__content">
          <p>
            ${resource.description}
            <ul>
                <li></li>
                <li></li>
            </ul>
          </p> 
        </div>
      </div>
    </div>
`
                )
            }
        }
        el.append(append.join(""));
        fillSearchCount(resources);
    }
}

class EventSearch {
    data = {
        text: ko.observable(""),
        from_date: ko.observable(getSearchDateString(new Date())),
        to_date: ko.observable(),
        events: ko.observableArray([])
    }

    constructor() {
        const eventEl = document.getElementById("search-event");
        ko.cleanNode(eventEl);
        ko.applyBindings(this.data, eventEl);
        this.data.text.subscribe(text => {
            this.search();
        })

        this.data.from_date.subscribe(from => {
            this.fetchEventOnDates();
        })

        this.data.to_date.subscribe(to => {
            this.fetchEventOnDates();
        })
    }

    fetchEventOnDates() {
        const from = this.data.from_date()?.split(".");
        const to = this.data.to_date()?.split(".");
        const fromDate = from && from.length > 1 ? `${from[2]}-${from[1]}-${from[0]}` : getIsoDateString(new Date()); // year-month-day
        const toDate = to && to.length > 1 ? `${to[2]}-${to[1]}-${to[0]}` : "";
        const buildingID = "";
        const facilityTypeID = "";
        const start = 0;
        const end = 1000;
        const loggedInOrgs = "";
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uieventsearch.upcoming_events',
            fromDate,
            toDate,
            buildingID,
            facilityTypeID,
            loggedInOrgs,
            start,
            end,
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                this.data.events = response;
                this.search();
            },
            error: error => {
                console.log(error);
            }
        })
    }

    reset() {
        this.data.text("");
        this.data.from_date(getSearchDateString(new Date()))
        this.data.to_date("");
    }

    search() {
        let events = this.data.events.slice(0, 5);
        const el = emptySearch();
        if (this.data.text() !== "") {
            const re = new RegExp(this.data.text(), 'i');
            events = this.data.events.filter(o => o.event_name.match(re) || o.location_name.match(re))
        }
        this.addInfoCards(el, events);
        createJsSlidedowns();
    }

    addInfoCards(el, events) {
        const append = [];
        for (const event of events) {
            append.push(`
    <div class="col-12 mb-4">
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
          <span>${event.event_name}</span>
          <span class="slidedown__toggler__info">
          ${joinWithDot([
                event.location_name, 
                getSearchDatetimeString(new Date(event.from))+" - "+((new Date(event.from)).getDate()===(new Date(event.to)).getDate() ? getSearchTimeString(new Date(event.to)) : getSearchDatetimeString(new Date(event.to)))])}
          </span>
        </button>
        <div class="js-slidedown-content slidedown__content">
          <p>
            ${event.location_name}
            <ul>
                <li>Fra: ${event.from}</li>
                <li>Til: ${event.to}</li>
            </ul>
          </p> 
        </div>
      </div>
    </div>
`
            )
        }
        el.append(append.join(""));
        fillSearchCount(events);
    }
}

class Search {
    // All data from server
    data = {
        location: [],
        activities: [],
        resource_categories: [],
        resources: [],
        facilities: [],
        buildings: [],
        building_resources: [],
        organizations: [],
        events: []
    }
    booking = new BookingSearch();
    event = new EventSearch();
    organization = new OrganizationSearch();

    ko_search = {
        type_group: ko.observable(null),
        header_text: ko.observable(""),
        header_sub: ko.observable("")
    }

    constructor() {
        const searchEl = document.getElementById("search-header");
        ko.cleanNode(searchEl);
        ko.applyBindings(this.ko_search, searchEl);

        this.ko_search.type_group.subscribe(type => {
            this.updateHeaderTexts(type);
        })

        this.fetchData();
        const self = this;
        $(document).ready(function () {
            self.ko_search.type_group(location.hash.substring(1))
        });

        $('#id-reset-filter').click(() => {
            switch (self.ko_search.type_group()) {
                case "booking":
                    self.booking.reset();
                    break;
                case "event":
                    self.event.reset();
                    break;
                case "organization":
                    self.organization.reset();
                    break;
                default:

            }
        })
    }

    fetchData = () => {
        const self = this;
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.get_search_data_all',
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                console.log(response);
                self.data = {...self.data, ...response};

                self.booking.data.building_resources(response.building_resources);
                self.booking.data.towns_data(response.towns);
                self.booking.data.activities(self.data.activities)
                self.booking.data.resources(self.data.resources.map(r => ({...r, name: htmlDecode(r.name)})))
                self.booking.data.facilities(self.data.facilities.map(f => ({...f, name: htmlDecode(f.name)})))
                self.booking.data.resource_categories(self.data.resource_categories)
                self.booking.data.resource_facilities(self.data.resource_facilities)
                self.booking.data.resource_activities(self.data.resource_activities)
                self.booking.data.resource_category_activity(self.data.resource_category_activity);

                self.event.data.events(self.data.events);

                self.organization.data.activities(self.data.activities.map(a => ({...a, name: htmlDecode(a.name)})))
                self.organization.data.organizations(self.data.organizations.map(o => ({
                    ...o,
                    name: htmlDecode(o.name)
                })));
            },
            error: error => {
                console.log(error);
            }
        })
    }


    updateHeaderTexts = (type) => {
        switch (type) {
            case "booking":
                this.ko_search.header_text("Lei lokale, anlegg eller utstyr");
                this.ko_search.header_sub("Bruk filtrene til å finne de leieobjekter som du ønsker å leie");
                $("#search-booking").show();
                $("#search-event").hide();
                $("#search-organization").hide();
                this.booking.search();
                window.location.hash = '#booking';
                break;
            case "event":
                this.ko_search.header_text("Finn arrangement eller aktivitet");
                this.ko_search.header_sub("Bruk filtrene til å finne ut hva som skjer i dag, eller til helgen");
                $("#search-event").show();
                $("#search-booking").hide();
                $("#search-organization").hide();
                if (this.data.events.length === 0) {
                    this.event.fetchEventOnDates();
                }
                this.event.search();
                window.location.hash = '#event';
                break;
            case "organization":
                this.ko_search.header_text("Finn lag eller organisasjon");
                this.ko_search.header_sub("Er du på jakt etter noen som er interessert i det samme som deg? Søk på navn til lag eller organisasjon, eller filtrer på aktivitet eller område");
                $("#search-organization").show();
                $("#search-booking").hide();
                $("#search-event").hide();
                this.organization.search();
                window.location.hash = '#organization';
                break;
            default:
                this.ko_search.type_group("booking")
        }
    }

}

const search = new Search();

function getSearchDateString(date) {
    return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()}`;
}

function getSearchTimeString(date) {
    return `${("0" + date.getHours()).slice(-2)}:${("0" + date.getMinutes()).slice(-2)}`
}

function getSearchDatetimeString(date) {
    return `${getSearchDateString(date)} ${getSearchTimeString(date)}`;
}

function getIsoDateString(date) {
    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
}

function getAllSubRowsIds(rows, id) {
    let result = [id];
    rows.filter(a => a.parent_id === id).map(a => {
        result.push(...getAllSubRowsIds(rows, a.id));
    });
    return result;
}

function emptySearch() {
    const el = $("#search-result");
    el.empty();
    return el;
}

function fillSearchCount(data) {
    const el = $("#search-count");
    el.empty();
    if (data) {
        el.append(`Antall treff: ${data.length}`);
    }
}

function htmlDecode(input) {
    const doc = new DOMParser().parseFromString(input, "text/html");
    const newInput = doc.documentElement.textContent;
    // Some texts are double encoded
    const newDoc = new DOMParser().parseFromString(newInput, "text/html");
    return newDoc.documentElement.textContent;
}

function joinWithDot(texts) {
    return texts.map(t => t && t.length>0 ? `<span>${t}</span>` : null).filter(t => t).join(` <span className="slidedown__toggler__info__separator">&#8226;</span> `)
}