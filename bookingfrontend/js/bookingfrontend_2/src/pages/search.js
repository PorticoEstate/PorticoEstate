import '../helpers/util';
import {phpGWLink} from "../helpers/util";
import "../components/application-cart";
import {ModifyIds} from "../helpers/modifyIds";

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
        resources_with_available_time: ko.observableArray([]),
        easy_booking_available_ids: ko.observableArray([])
    }

    activity_cache = {};
    allocation_cache = {};
    easy_booking_available_cache = {};
    easy_booking_not_available_cache = {};
    lang = 'no';

    constructor() {
        this.lang = getCookie("selected_lang") || 'no';

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

    fetchEasyBooking(finished) {
        const start_date = `${this.data.date()}`;
        const end_date = `${this.data.date()}`;
        if (!(start_date in this.easy_booking_available_cache)) {
            this.easy_booking_available_cache[start_date] = [];
            this.easy_booking_not_available_cache[start_date] = [];
        }
        const resource_ids = [...new Set(this.data.result_all()
            .filter(r => !this.easy_booking_not_available_cache[start_date].includes(r.id) &&
                !this.easy_booking_available_cache[start_date].includes(r.id))
            .map(r => r.id))];
        if (resource_ids.length === 0) {
            finished()
            return;
        }

        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibooking.get_freetime_limit',
            start_date,
            end_date,
            resource_ids: resource_ids.join(","),
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                // console.log("Res", response);
                this.populate_easy_booking_available_cache(response, start_date, resource_ids);
                finished();
            },
            error: error => {
                console.log(error);
                finished();
            }
        })
    }

    fetchAvailableResources() {
        let fetched_easy_booking = false;
        let fetched_available_resource = false;
        const from_date = `${this.data.date()} 00:00:00`;
        const to_date = `${this.data.date()} 23:59:59`;
        const onSuccess = () => {
            if (fetched_easy_booking && fetched_available_resource) {
                this.data.available_resources([]);
                this.data.taken_allocations(this.data.result_all().filter(r => r.simple_booking !== 1).map(r => this.allocation_cache[from_date].allocations[r.id]).flat());
                this.data.seasons(this.data.result_all().filter(r => r.simple_booking !== 1).map(r => this.allocation_cache[from_date].seasons[r.id]).flat());
                this.calculateAvailableResources();
                this.search();
                // console.log("Easy Cache", this.easy_booking_available_cache)
            }
        }

        this.fetchEasyBooking(() => {
            fetched_easy_booking = true;
            onSuccess()
        })


        if (!(from_date in this.allocation_cache)) {
            this.allocation_cache[from_date] = {};
            this.allocation_cache[from_date].allocations = {};
            this.allocation_cache[from_date].seasons = {};
        }
        const resource_ids = [...new Set(this.data.result_all()
            .filter(r => r.simple_booking !== 1)
            .filter(r => !(r.id in this.allocation_cache[from_date].allocations)).map(r => r.id))];
        if (resource_ids.length === 0) {
            fetched_available_resource = true;
            onSuccess();
            return;
        }
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.search_available_resources',
            from_date,
            to_date,
            resource_ids: resource_ids.join(","),
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                // console.log("Res", response);
                this.populate_allocation_cache(response, from_date, resource_ids);
                fetched_available_resource = true;
                onSuccess();
            },
            error: error => {
                console.log(error);
            }
        })
    }

    populate_allocation_cache = (response, from_date, resource_ids) => {
        const cache = this.allocation_cache[from_date];
        resource_ids.map(id => {
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

    populate_easy_booking_available_cache = (response, start_date, resource_ids) => {
        const cache = this.easy_booking_available_cache[start_date];
        Object.keys(response).map(resource_id => {
            response[resource_id].map(resource => {
                if (!resource.overlap) {
                    cache.push(resource.applicationLink.resource_id);
                }
            })
        })
        this.easy_booking_available_cache[start_date] = [...new Set(cache)];
        this.easy_booking_not_available_cache[start_date] = [...new Set(resource_ids.filter(id => !this.easy_booking_available_cache[start_date].includes(id)))];
    }

    reset() {
        this.data.selected_town(null);
        $('#search-booking-activities').val([]).trigger('change')
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
                resources = resources.filter(resource => this.data.selected_facilities().map(f => f.id).every(id => this.data.resource_facilities().some(rf => rf.resource_id === resource.id && rf.facility_id === id)));
            }
            if (this.data.selected_activities().length > 0) {
                resources = resources.filter(resource => this.data.selected_activities().map(f => f.id).every(id => this.data.resource_activities().some(ra => this.activity_cache[id].includes(ra.activity_id) && resource.id === ra.resource_id)));
            }
            if (this.data.selected_resource_categories().length > 0) {
                resources = (resources.filter(res => this.data.selected_resource_categories().some(cat => cat.id === res.rescategory_id)));
            }

            hasSearch = true;
        }

        if (this.data.text() !== "") {
            if (!hasSearch)
                resources = this.data.resources();
            const re = new RegExp(this.data.text(), 'i');
            let buildings_resources = [];
            let activity_resources = [];
            // Find all buildings matching so we can filter on later
            if (this.data.selected_buildings().length === 0) {
                const buildings = this.data.buildings().filter(building => building.name.match(re));
                buildings_resources = this.getResourcesFromBuildings(buildings);
            }
            if (this.data.selected_activities().length === 0) {
                const matchingActivities = this.data.activities().filter(activity => activity.name.match(re));
                const activitySets = matchingActivities.map(activity => this.activity_cache[activity.id]);
                const resourceActivities = this.data.resource_activities();
                const resourceIdsSet = new Set(
                    activitySets.flatMap(
                        actIds => actIds.flatMap(
                            a => resourceActivities.filter(ref => ref.activity_id === a)
                        )
                    ).map(act => act.resource_id)
                );
                activity_resources = this.data.resources().filter(resource => resourceIdsSet.has(resource.id));

            }
            resources = [...resources.filter(resource => resource.name.match(re) || buildings_resources.some(r => r.id === resource.id)), ...activity_resources]
            hasSearch = true;
        }
        const el = emptySearch();

        if (hasSearch) {
            // Remove duplicates
            resources = resources.reduce((accumulator, current) => {
                if (accumulator.findIndex(item => item.id === current.id) === -1) {
                    accumulator.push(current);
                }
                return accumulator;
            }, []);
            this.data.result_all(resources);
            if (this.data.show_only_available())
                resources = resources.filter(r => this.data.resources_with_available_time().includes(r.id));
            this.addInfoCards(el, resources.sort((a, b) => a.name.localeCompare(b.name)));
        } else {
            fillSearchCount(null);
            this.addInfoCards(el, [])
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

        const getApiDate = (dateString) => {
            const [day, month, year] = this.data.date().split(".");
            return new Date(`${year}-${month}-${day}`);
        }

        const isSameDate = (fromDateString, toDateString) => {
            const from = new Date(fromDateString.split(" ")[0]);
            const to = new Date(toDateString.split(" ")[0]);
            const same = from.getDate() === to.getDate() && from.getMonth() === to.getMonth() && from.getFullYear() === to.getFullYear();
            return same;
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

            return totalAllocatedSeconds >= totalAllowedSeconds;
        }

        const date = getApiDate(this.data.date());
        const wday = date.getDay();
        const resource_allocation = this.data.taken_allocations().reduce((acc, cur) => {
            acc[cur.resource_id] = acc[cur.resource_id] || [];
            acc[cur.resource_id].push([
                timeToNumber(cur.from_.split(" ")[1]),
                !isSameDate(cur.from_, cur.to_) ? 86400 : timeToNumber(cur.to_.split(" ")[1])
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
        this.data.resources_with_available_time([...new Set([...available_ids, ...this.easy_booking_available_cache[this.data.date()]])]);
        // console.log("Available ids", available_ids);
    }

    updateBuildings = (town = null) => {
        this.data.buildings(
            this.data.towns_data().filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: htmlDecode(item.b_name), original_id: item.original_b_id, remoteInstance: item.remoteInstance}))
                .sort((a, b) => a.name?.localeCompare(b.name, "no"))
        )
    }

    getResourcesFromBuildings = (buildings) => {
        const building_ids = buildings.map(building => building.id);
        return this.data.building_resources()
            .filter(resource => building_ids.includes(resource.building_id))
            .map(resource => this.data.resources().find(res => res.id === resource.resource_id))
            .filter(r => !!r);
    }

    getBuildingsFromResource = (resource_id) => {
        const building_resources = this.data.building_resources().filter(br => br.resource_id === resource_id);
        const ids = building_resources.map(br => br.building_id);
        return this.data.buildings().filter(b => ids.includes(b.id));
    }

    getTownFromBuilding = (buildings) => {
        const ids = buildings.map(b => b.id)
        return this.data.towns_data().filter(t => ids.includes(t.b_id));
    }

    cleanTownName = (townName) => {
        return townName.split('\n').map(line => {
            // Check if 'Bydel' is in the line
            if (line.toLowerCase().includes('bydel')) {
                // Remove 'Bydel'
                line = line.replace(/bydel/gi, '').trim();
            }
            // Capitalize first letter of each word
            return line.charAt(0).toUpperCase() + line.slice(1).toLowerCase();
        }).join('\n');
    }

    addInfoCards(el, resources) {
        const append = [];
        const okResources = [];
        // const calendars = [];
        for (const resource of resources) {
            const buildings = this.getBuildingsFromResource(resource.id);
            const towns = this.getTownFromBuilding(buildings);
            if (towns.length > 0) {
                const calendarId = `calendar-${resource.id}`;
                okResources.push(resource);
                const description_json = JSON.parse(resource.description_json);
                const description_text = new DOMParser()
                    .parseFromString(description_json[this.lang] || description_json['no'], "text/html")
                    .documentElement.textContent;

                let url = "";
                if (resource.simple_booking === 1) {
                    url = phpGWLink('bookingfrontend/', {
                        menuaction: 'bookingfrontend.uiresource.show',
                        building_id: buildings[0].id,
                        id: resource.id
                    }, false);
                } else {
                    url = phpGWLink('bookingfrontend/', {
                        menuaction: 'bookingfrontend.uiapplication.add',
                        building_id: buildings[0].id,
                        resource_id: resource.id
                    }, false);
                }
                const locationUrl = phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uibuilding.show',
                    id: buildings[0].id
                })
                console.log(resource.name, buildings);

                // language=HTML
                append.push(`
                            <div class="col-12 mb-4">
                                <div class="js-slidedown slidedown">
                                    <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
                                        <span><div class="fa-solid fa-layer-group"></div> ${resource.name}</span>
                                        <span class="slidedown__toggler__info">
                ${joinWithDot([resource.remoteInstance?.name ? `<span class="text-overline">${resource.remoteInstance?.name}</span>`: '',`<span class="text-overline">${joinWithDot(towns.map(t => this.cleanTownName(t.name)))}</span>`, ...buildings.map(b => {
                    const buildingUrl = phpGWLink('bookingfrontend/', {
                        menuaction: 'bookingfrontend.uibuilding.show',
                        id: 'original_id' in b && b.original_id !== undefined ? b.original_id : b.id
                    }, false, b.remoteInstance?.webservicehost  || undefined)
                    return `<a href="${buildingUrl}" class="link-text link-text-primary"><i class="fa-solid fa-location-dot"></i>${b.name}</a>`
                })])}
            </span>
                                    </button>
                                    <div class="js-slidedown-content slidedown__content">
                                        <div>
                                            <!--                                            <div class="d-flex">-->
                                                <!--<button class="pe-btn pe-btn-primary" style="margin-right: 8px;" onclick="location.href=\'${url}
                                                    \'">Søknad</button>-->
                                                <!--<button class="pe-btn pe-btn-secondary"
                                                        onclick="location.href=\'${locationUrl}\'">${buildings[0].name}
                                                </button>-->
                                            <!--                                            </div>-->
                                            <p>
                                                ${description_text}
                                            </p>
                                        </div>
                                        <div id="${calendarId}" class="calendar" data-building-id="${buildings[0].original_id || buildings[0].id}"
                                             data-resource-id="${resource.original_id || resource.id}"
                                             data-instance="${resource.remoteInstance?.webservicehost || ''}"
                                             data-date="${getDateFromSearch(this.data.date())}"></div>
                                    </div>
                                </div>
                            </div>
                    `
                )
            }
        }
        this.data.result(okResources.slice(0, 50));
        el.append(append.join(""));
        el.find('a.link-text').on('click', function (event) {
            event.stopPropagation();
            // If you need to follow the link, uncomment the following line
            // window.location.href = $(this).attr('href');
        });
        fillSearchCount(okResources.slice(0, 50), okResources.length);
        // calendars.map(calendar => calendar.createCalendarDom())
    }

    adjustMobilePositionOnSearch() {
        let searchBox = document.getElementById("search-booking");
        setTimeout(function () {
            // Scroll the search box to the top of the screen
            window.scrollTo(0, searchBox.getBoundingClientRect().top);
        }, 200);
    }
}

class EventSearch {
    data = {
        text: ko.observable(""),
        from_date: ko.observable(getSearchDateString(new Date())),
        to_date: ko.observable(getSearchDateString(new Date(new Date().getTime() + (7 * 86400 * 1000)))),
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
        header_text_kword: ko.observable({}),
        header_sub_kword: ko.observable({})
    }

    constructor() {
        const searchEl = document.getElementById("search-header");
        console.log(remote_search)

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

        AbortSignal.timeout ??= function timeout(ms) {
            const ctrl = new AbortController()
            setTimeout(() => ctrl.abort(), ms)
            return ctrl.signal
        }


        const promises = [
            fetch(phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uisearch.get_search_data_all',
                length: -1,

            }, true)) // Fetch local data
                .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch local data');
                    return response.json();
                }),
            ...remote_search.map(remote => {
                const remoteUrl = phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uisearch.get_search_data_all',
                    length: -1,
                }, true, remote.webservicehost);
                return fetch(remoteUrl, {
                    signal: AbortSignal.timeout(2500)

                }) // Fetch remote data
                    .then(response => {
                        if (!response.ok) throw new Error(`Failed to fetch data from ${remote.webservicehost}`);
                        return response.json();
                    })
                    .then(data => ModifyIds(data, remote))
                    .catch(error => {
                        console.error(`Error fetching or processing data from ${remote.webservicehost}: ${error}`);
                        return null; // Return null or a default object to handle failed remote fetches
                    })
            })
        ];

        Promise.all(promises).then(responses => {
            // console.log(responses);
            // Filter out null responses from failed fetches
            responses = responses.filter(response => response !== null);
            if (responses.length === 0) throw new Error('No data retrieved from any source.');

            // Combine local and remote data
            const combinedData = responses.reduce((acc, data, indx) => {
                if(indx === 0 ){
                    return acc;
                }
                return {
                    ...acc,
                    activities: [...acc.activities, ...data.activities],
                    buildings: [...acc.buildings, ...data.buildings],
                    building_resources: [...acc.building_resources, ...data.building_resources],
                    facilities: [...acc.facilities, ...data.facilities],
                    resources: [...acc.resources, ...data.resources],
                    resource_activities: [...acc.resource_activities, ...data.resource_activities],
                    resource_facilities: [...acc.resource_facilities, ...data.resource_facilities],
                    resource_categories: [...acc.resource_categories, ...data.resource_categories],
                    resource_category_activity: [...acc.resource_category_activity, ...data.resource_category_activity],
                    towns: [...acc.towns, ...data.towns],
                    organizations: [...acc.organizations, ...data.organizations],
                };

            }, responses[0]); // Use first response as base

            // Update data and observables
            self.data = {...self.data, ...combinedData};

            self.booking.data.building_resources(combinedData.building_resources);
            self.booking.data.towns_data(sortOnName(combinedData.towns));
            self.booking.data.activities(sortOnName(self.data.activities));
            self.booking.data.resources(sortOnName(self.data.resources.map(resource => ({
                ...resource,
                name: htmlDecode(resource.name)
            }))));
            self.booking.data.facilities(sortOnName(self.data.facilities.map(facility => ({
                ...facility,
                name: htmlDecode(facility.name)
            }))));
            self.booking.data.resource_categories(sortOnName(self.data.resource_categories));
            self.booking.data.resource_facilities(self.data.resource_facilities);
            self.booking.data.resource_activities(self.data.resource_activities);
            self.booking.data.resource_category_activity(self.data.resource_category_activity);

            self.event.data.events(self.data.events);

            self.organization.data.activities(sortOnName(self.data.activities.map(activity => ({
                ...activity,
                name: htmlDecode(activity.name)
            }))));
            self.organization.data.organizations(self.data.organizations.map(organization => ({
                ...organization,
                name: htmlDecode(organization.name)
            })));
        }).catch(error => {
            console.log(error);
        });
    }


    updateHeaderTexts = (type) => {
        switch (type) {
            case "booking":
                if (!landing_sections.booking) {
                    this.updateHeaderTexts();
                    break;
                }
                this.ko_search.header_text_kword({tag: 'rent_premises_facilities_equipment', group: 'bookingfrontend'});
                this.ko_search.header_sub_kword({tag: 'use_filters_to_find_rental_objects', group: 'bookingfrontend'});
                $("#search-booking").show();
                $("#search-event").hide();
                $("#search-organization").hide();
                this.booking.search();
                window.location.hash = '#booking';
                break;
            case "event":
                if (!landing_sections.event) {
                    this.updateHeaderTexts();
                    break;
                }
                this.ko_search.header_text_kword({tag: 'find_event_or_activity', group: 'bookingfrontend'});
                this.ko_search.header_sub_kword({tag: 'use_filters_to_find_todays_events', group: 'bookingfrontend'});
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
                if (!landing_sections.organization) {
                    this.updateHeaderTexts();
                    break;
                }
                this.ko_search.header_text_kword({tag: 'find_team_or_organization', group: 'bookingfrontend'});
                this.ko_search.header_sub_kword({tag: 'search_for_like_minded_people', group: 'bookingfrontend'});
                $("#search-organization").show();
                $("#search-booking").hide();
                $("#search-event").hide();
                this.organization.search();
                window.location.hash = '#organization';
                break;
            default:
                if (landing_sections.booking) {
                    this.ko_search.type_group("booking")
                    break;
                }
                if (landing_sections.event) {
                    this.ko_search.type_group("event")
                    break;
                }
                this.ko_search.type_group("organization")

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

function getDateFromSearch(dateString) {
    // Normalize the divider to a hyphen
    const normalizedDateStr = dateString.replace(/[.\/]/g, '-');

    // Split the date into its components
    const [day, month, year] = normalizedDateStr.split('-').map(num => parseInt(num, 10));

    // Create a DateTime object
    const dt = luxon.DateTime.local(year, month, day);

    return dt.toJSDate();
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
    return texts.map(t => t && t.length > 0 ? `<span class="d-flex align-items-center">${t}</span>` : null).filter(t => t).join(`<span class="slidedown__toggler__info__separator"><i class="fa-solid fa-circle"></i></span>`)
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

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

