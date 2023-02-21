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

        this.data.selected_organizations.subscribe(organizations => {
            this.search();
        })

        this.data.text.subscribe(text => {
            this.search();
        })

        this.data.selected_activities.subscribe(activities => {
            this.search();
        })
    }

    search() {
        let organizations = [];
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
        }
        const el = emptySearch();
        this.addInfoCards(el, organizations);
        createJsSlidedowns();
    }

    addInfoCards = (el, organizations) => {
        const append = [];
        for (const organization of organizations) {
            append.push(`
    <div class="col-12 mb-4">
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
          ${organization.name}
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
    }
}

class BookingSearch {
    data = {
        towns: ko.observableArray([]),
        selected_town: ko.observable(),
        locations: ko.observableArray([]),
        selected_location: ko.observable(),
        activities: ko.observableArray([]),
        selected_activities: ko.observableArray([]),
        resource_categories: ko.observableArray([]),
        selected_resource_categories: ko.observableArray([]),
        resources: ko.observableArray([]),
        selected_resources: ko.observableArray([]),
        facilities: ko.observableArray([]),
        selected_facilities: ko.observableArray([]),
    }

    constructor() {
        const bookingEl = document.getElementById("search-booking");
        ko.cleanNode(bookingEl);
        ko.applyBindings(this.data, bookingEl);

    }

    search() {
        const el = emptySearch();
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

    search() {
        let events = this.data.events.slice(0, 5);
        if (this.data.text() !== "") {
            const re = new RegExp(this.data.text(), 'i');
            events = this.data.events.filter(o => o.event_name.match(re) || o.location_name.match(re))
        }
        const el = emptySearch();
        this.addInfoCards(el, events);
        createJsSlidedowns();
    }

    addInfoCards(el, events) {
        const append = [];
        for (const event of events) {
            append.push(`
    <div class="col-12 mb-4">
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="true">
          ${event.event_name}
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
                self.booking.data.towns(
                    [...new Set(self.data.towns.map(item => item.name))]
                        .sort()
                        .map(name => ({name, id: self.data.towns.find(i => i.name === name).id}))
                )
                self.booking.data.activities(self.data.activities)
                self.booking.data.resources(self.data.resources)
                self.booking.data.facilities(self.data.facilities)
                self.booking.data.resource_categories(self.data.resource_categories)
                self.updateLocations(null);

                self.event.data.events(self.data.events);

                self.organization.data.activities(self.data.activities)
                self.organization.data.organizations(self.data.organizations)
            },
            error: error => {
                console.log(error);
            }
        })
    }


    updateLocations = (town = null) => {
        this.booking.data.locations(
            this.data.towns.filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: item.b_name}))
        )
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