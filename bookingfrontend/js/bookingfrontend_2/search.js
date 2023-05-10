// Easier way of getting old value from KO on change
ko.subscribable.fn.subscribeChanged = function (callback) {
    let previousValue;
    this.subscribe(function (_previousValue) {
        previousValue = _previousValue;
    }, undefined, 'beforeChange');
    this.subscribe(function (latestValue) {
        callback(latestValue, previousValue);
    });
};

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
            this.addInfoCards(el, organizations.slice(0, 10), organizations.length);
        } else {
            fillSearchCount(null);
        }

        createJsSlidedowns();
    }

    addInfoCards = (el, organizations, count) => {
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
        fillSearchCount(organizations, count);
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
        date: ko.observable(getSearchDateString(new Date())),
        show_only_available: ko.observable(false),
        result: ko.observableArray([]),
        result_all: ko.observableArray([]),
        available_resources: ko.observableArray([]),
        taken_allocations: ko.observableArray([]),
        seasons: ko.observableArray([]),
        resources_with_available_time: ko.observableArray([])
    }

    activity_cache = {};
    allocation_cache = {}

    constructor() {
        const bookingEl = document.getElementById("search-booking");
        ko.cleanNode(bookingEl);
        ko.applyBindings(this.data, bookingEl);
        this.updateBuildings(null);

        this.data.text.subscribe(_ => this.searchFetch())
        this.data.selected_buildings.subscribe(_ => this.searchFetch())
        this.data.selected_resource_categories.subscribe(_ => this.searchFetch())
        this.data.selected_facilities.subscribe(_ => this.searchFetch())
        this.data.selected_activities.subscribe(_ => this.searchFetch())
        this.data.show_only_available.subscribe(show => {
            if (!show)
                this.data.resources_with_available_time([])
            this.searchFetch();
        })
        this.data.towns.subscribe(_ => this.updateBuildings(null));
        this.data.date.subscribe(date => {
            if (date === "") {
                this.data.date(getSearchDateString(new Date()));
                return;
            }
            this.searchFetch()
        })

        this.data.selected_town.subscribe(town => {
            this.updateBuildings(town);
            this.searchFetch();
        })

        this.data.towns_data.subscribe(towns => {
            this.data.towns(
                [...new Set(towns.map(item => item.name))]
                    .sort((a, b) => a.localeCompare(b, "no"))
                    .map(name => ({name: htmlDecode(name), id: towns.find(i => i.name === name).id}))
            )
        })

        this.data.activities.subscribe(activities => {
            for (const activity of activities) {
                this.activity_cache[activity.id] = getAllSubRowsIds(activities, activity.id);
            }
        })

        this.data.result_all.subscribeChanged((result, old) => {
            // Only download new available resources if old data is different from new
            if (arraysAreEqual(result.map(r => r.id).sort(), old.map(r => r.id).sort()))
                return;
            if (this.data.show_only_available())
                this.fetchAvailableResources();
        })
    }

    fetchAvailableResources() {
        const onSuccess = (from_date) => {
            this.data.available_resources([]);
            this.data.taken_allocations(this.data.result_all().map(r => this.allocation_cache[from_date].allocations[r.id]).flat());
            this.data.seasons(this.data.result_all().map(r => this.allocation_cache[from_date].seasons[r.id]).flat());
            this.calculateAvailableResources();
            this.search();

        }
        const from_date = `${this.data.date()} 00:00:00`;
        const to_date = `${this.data.date()} 23:59:59`;
        if (!(from_date in this.allocation_cache)) {
            this.allocation_cache[from_date] = {};
            this.allocation_cache[from_date].allocations = {};
            this.allocation_cache[from_date].seasons = {};
        }
        const resource_ids = this.data.result_all().filter(r => !(r.id in this.allocation_cache[from_date].allocations)).map(r => r.id).join(",");
        if (resource_ids.length === 0) {
            onSuccess(from_date);
            return;
        }
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.search_available_resources',
            from_date,
            to_date,
            resource_ids,
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                // console.log("Res", response);
                this.populate_allocation_cache(response, from_date, resource_ids);
                onSuccess(from_date);
            },
            error: error => {
                console.log(error);
            }
        })
    }

    populate_allocation_cache = (response, from_date, resource_ids) => {
        const cache = this.allocation_cache[from_date];
        resource_ids.split(",").map(id => {
            if (!(id in cache.allocations))
                cache.allocations[id] = [];
            if (!(id in cache.seasons))
                cache.seasons[id] = [];
        })

        response.allocations.map(allocation => {
            cache.allocations[allocation.resource_id].push(allocation);
        })
        response.seasons.map(season => {
            cache.seasons[season.resource_id].push(season);
        })
    }

    reset() {
        this.data.selected_town(null);
        $('#search-booking-activities').val([]).trigger('change');
        $('#search-booking-building').val([]).trigger('change');
        $('#search-booking-resource_categories').val([]).trigger('change');
        $('#search-booking-facilities').val([]).trigger('change');
        this.data.text("");
        this.data.date(getSearchDateString(new Date()));
    }

    searchFetch() {
        if (this.data.show_only_available())
            this.fetchAvailableResources();
        else this.search();
    }
    search() {
        let resources = [];
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
            this.data.result_all(resources);
            if (this.data.show_only_available())
                resources = resources.filter(r => this.data.resources_with_available_time().includes(r.id));
            const el = emptySearch();
            this.addInfoCards(el, resources);
        } else {
            fillSearchCount(null);
        }
        createJsSlidedowns();
    }

    calculateAvailableResources = () => {
        const timeToNumber = (time) => {
            const [hour, min, sec] = time.split(":");
            return +sec + (+min * 60) + (+hour * 3600);
        }
        const numberToTime = (num) => {
            const date = new Date(num * 1000);
            return getFullTimeString(date);
        }

        const allocationFillsAllowed = (allocations, allowed) => {
            allocations.sort((a, b) => a[0] - b[0]);
            const mergedAllocations = [allocations[0]];

            for (let i = 1; i < allocations.length; i++) {
                const lastMerged = mergedAllocations[mergedAllocations.length - 1];
                if (allocations[i][0] <= lastMerged[1]) {
                    lastMerged[1] = Math.max(lastMerged[1], allocations[i][1]);
                } else {
                    mergedAllocations.push(allocations[i]);
                }
            }

            const totalAllocatedSeconds = mergedAllocations.reduce((sum, allocation) => {
                return sum + (allocation[1] - allocation[0]);
            }, 0);

            const totalAllowedSeconds = allowed.reduce((sum, range) => {
                return sum + (range[1] - range[0]);
            }, 0);

            return totalAllocatedSeconds === totalAllowedSeconds;
        }

        const [day, month, year] = this.data.date().split(".");
        const date = new Date(`${year}-${month}-${day}`);
        const wday = date.getDay();
        const resource_allocation = this.data.taken_allocations().reduce((acc, cur) => {
            acc[cur.resource_id] = acc[cur.resource_id] || [];
            acc[cur.resource_id].push([
                timeToNumber(cur.from_.split(" ")[1]),
                timeToNumber(cur.to_.split(" ")[1])
            ]);
            return acc;
        }, {})
        const resource_season = this.data.seasons().reduce((acc, cur) => {
            if (cur.wday === wday) {
                if (!(cur.resource_id in acc))
                    acc[cur.resource_id] = [];
                acc[cur.resource_id].push([timeToNumber(cur.from_time), timeToNumber(cur.to_time)]);
            }
            return acc;
        }, {})
        const available_ids = Object.keys(resource_season).filter(id => {
            if (!(id in resource_allocation)) return true;
            return !allocationFillsAllowed(resource_allocation[id], resource_season[id])
        }).map(id => +id)
        this.data.resources_with_available_time(available_ids);
    }

    updateBuildings = (town = null) => {
        this.data.buildings(
            this.data.towns_data().filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: htmlDecode(item.b_name)}))
                .sort((a, b) => a.name?.localeCompare(b.name, "no"))
        )
    }

    getBuildingsFromResource = (resource_id) => {
        const building_resources = this.data.building_resources().filter(br => br.resource_id === resource_id);
        const ids = building_resources.map(br => br.building_id);
        return this.data.buildings().filter(b => ids.includes(b.id));
    }

    getTownFromBuilding = (buildings) => {
        const ids = buildings.map(b => +b.id)
        return this.data.towns_data().filter(t => ids.includes(+t.b_id));
    }

    addInfoCards(el, resources) {
        const append = [];
        const okResources = [];
        for (const resource of resources) {
            const buildings = this.getBuildingsFromResource(resource.id);
            const towns = this.getTownFromBuilding(buildings);
            if (towns.length > 0) {
                okResources.push(resource);
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
        this.data.result(okResources.slice(0, 50));
        el.append(append.join(""));
        fillSearchCount(okResources.slice(0, 50), okResources.length);
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
        const fromDate = from && from.length > 1 ? `${from[2]}-${from[1]}-${from[0]}T00:00:00` : getIsoDateString(new Date()); // year-month-day
        const toDate = to && to.length > 1 ? `${to[2]}-${to[1]}-${to[0]}T23:59:59` : `${from[2]}-${from[1]}-${from[0]}T23:59:59`;
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
        let count = this.data.events.length;
        const el = emptySearch();
        if (this.data.text() !== "") {
            const re = new RegExp(this.data.text(), 'i');
            events = this.data.events.filter(o => o.event_name.match(re) || o.location_name.match(re))
            count = events.length;
        }
        this.addInfoCards(el, events, count);
        createJsSlidedowns();
    }

    addInfoCards(el, events, count) {
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
                getSearchDatetimeString(new Date(event.from)) + " - " + ((new Date(event.from)).getDate() === (new Date(event.to)).getDate() ? getSearchTimeString(new Date(event.to)) : getSearchDatetimeString(new Date(event.to)))])}
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
        fillSearchCount(events, count);
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
                self.booking.data.towns_data(sortOnName(response.towns));
                self.booking.data.activities(sortOnName(self.data.activities));
                self.booking.data.resources(sortOnName(self.data.resources.map(resource => ({
                    ...resource,
                    name: htmlDecode(resource.name)
                }))));
                self.booking.data.facilities(sortOnName(self.data.facilities.map(facility => ({
                    ...facility,
                    name: htmlDecode(facility.name)
                }))));
                self.booking.data.resource_categories(sortOnName(self.data.resource_categories))
                self.booking.data.resource_facilities(self.data.resource_facilities)
                self.booking.data.resource_activities(self.data.resource_activities)
                self.booking.data.resource_category_activity(self.data.resource_category_activity);

                self.event.data.events(self.data.events);

                self.organization.data.activities(sortOnName(self.data.activities.map(activity => ({
                    ...activity,
                    name: htmlDecode(activity.name)
                }))))
                self.organization.data.organizations(self.data.organizations.map(organization => ({
                    ...organization,
                    name: htmlDecode(organization.name)
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

function getFullTimeString(date) {
    return `${("0" + date.getHours()).slice(-2)}:${("0" + date.getMinutes()).slice(-2)}:${("0" + date.getSeconds()).slice(-2)}`
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

function fillSearchCount(data, count = 0) {
    const el = $("#search-count");
    el.empty();
    if (data) {
        el.append(`Antall treff: ${data.length}${count > 0 ? " av " + count : ""}`);
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
    return texts.map(t => t && t.length > 0 ? `<span>${t}</span>` : null).filter(t => t).join(` <span className="slidedown__toggler__info__separator">&#8226;</span> `)
}

function sortOnField(data, field) {
    return data.sort((a, b) => a[field]?.localeCompare(b[field], "no"))
}

function sortOnName(data) {
    return sortOnField(data, 'name')
}

function arraysAreEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) {
        return false;
    }
    for (let i = 0; i < arr1.length; i++) {
        if (arr1[i] !== arr2[i]) {
            return false;
        }
    }
    return true;
}