import {phpGWLink} from "../../helpers/util";
import {
    arraysAreEqual,
    fillSearchCount, getAllSubRowsIds,
    getFullTimeString,
    getSearchDateString,
    htmlDecode,
} from "./search-util";

import './info-cards/resource-info-card'
import '../../helpers/jqueryBindings'

export class BookingSearch {
    /** @type {ko.observableArray} */
    building_resources;

    /** @type {ko.observableArray} */
    towns_data;

    /** @type {ko.observableArray} */
    activities;

    /** @type {ko.observableArray} */
    resources;

    /** @type {ko.observableArray} */
    facilities;

    /** @type {ko.observableArray} */
    resource_categories;

    /** @type {ko.observableArray} */
    resource_activities;

    /** @type {ko.observableArray} */
    resource_category_activity;

    subscriptions = [];

    towns = ko.observableArray([]);
    selected_town = ko.observable();
    selected_buildings = ko.observableArray([]);
    selected_activities = ko.observableArray([]);
    selected_resource_categories = ko.observableArray([]);
    selected_resources = ko.observableArray([]);
    selected_facilities = ko.observableArray([]);
    text = ko.observable("");
    date = ko.observable(getSearchDateString(new Date()));
    show_only_available = ko.observable(false);
    result_all = ko.observableArray([]);
    available_resources = ko.observableArray([]);
    taken_allocations = ko.observableArray([]);
    seasons = ko.observableArray([]);
    resources_with_available_time = ko.observableArray([]);
    easy_booking_available_ids = ko.observableArray([])

    show_more_filters = ko.observable(false);

    result = ko.observableArray([]);  // This observable array will hold the search results
    result_shown = ko.observable(25);  // This observable tracks the number of results shown

    activity_cache = {};
    allocation_cache = {};
    easy_booking_available_cache = {};
    easy_booking_not_available_cache = {};
    lang = 'no';
    translationObservable = globalThis['translations'];

    strings = {
        // Assuming `trans` is a global function available for translation
        search: ko.computed(function () {
            if (globalThis['translations'] && globalThis['translations']()) {
            }
            return trans('common', 'search');
        }),

        transCallable: ko.computed(function () {
            if (globalThis['translations'] && globalThis['translations']()) {
            }
            return trans;
        })
    }

    constructor(params) {
        params.instance(this)

        this.native_buildings = params.native_buildings;
        this.building_resources = params.building_resources;
        this.towns_data = params.towns_data;
        this.activities = params.activities;
        this.resources = params.resources;
        this.facilities = params.facilities;
        this.resource_categories = params.resource_categories;
        this.resource_facilities = params.resource_facilities;
        this.resource_activities = params.resource_activities;
        this.resource_category_activity = params.resource_category_activity;

        this.lang = getCookie("selected_lang") || 'no';



        this.show_only_available.subscribe(show => {
            if (!show)
                this.resources_with_available_time([])

            this.searchFetch();
        })


        this.towns_data.subscribe(towns => {
            this.towns(
                [...new Set(towns.map(item => item.name))]
                    .sort((a, b) => a.localeCompare(b, "no"))
                    .map(name => ({name: htmlDecode(name), id: towns.find(i => i.name === name).id}))
            )
        })

        this.activities.subscribe(activities => {
            for (const activity of activities) {
                this.activity_cache[activity.id] = getAllSubRowsIds(activities, activity.id);
            }
        })

        this.result_all.subscribeChanged((result, old) => {
            // Only download new available resources if old data is different from new
            if (arraysAreEqual(result.map(r => r.id).sort(), old.map(r => r.id).sort()))
                return;
            if (this.show_only_available())
                this.fetchAvailableResources();
        })

        window.addEventListener('scroll', this.handleScroll.bind(this));


        this.initializeComputed();
        this.initializeSubscriptions();

    }

    initializeSubscriptions() {
        this.subscriptions = [this.text.subscribe(_ => this.searchFetch()),
            this.selected_buildings.subscribe(_ => this.searchFetch()),
            this.selected_resource_categories.subscribe(_ => this.searchFetch()),
            this.selected_facilities.subscribe(_ => this.searchFetch()),
            this.selected_activities.subscribe((data) => {
                this.searchFetch()
            }),
            this.date.subscribe(date => {
                if (date === "") {
                    this.date(getSearchDateString(new Date()));
                    return;
                }
                this.searchFetch()
            }),

            this.selected_town.subscribe(town => {
                this.searchFetch();
            }),
        ]
    }


    initializeComputed() {

        this.buidling_towns = ko.computed(() => {
            if (!this.towns_data || !this.towns_data()) {
                return {};
            }


            const mappedData = this.towns_data().reduce((acc, item) => {
                acc[item.b_id] = item;
                return acc;
            }, {});

            // console.log(mappedData);

            return mappedData;

        })
        this.buildings = ko.computed(() => {
            if (!this.buidling_towns || !this.buidling_towns()) {
                return [];
            }
            const buildingTowns = this.buidling_towns();
            const town = this.selected_town();
            const filtered =  this.native_buildings()
                .filter(item => town ? (buildingTowns[item.id] && town.id === buildingTowns[item.id].id) : true)
                .map(item => ({
                    id: item.id,
                    name: htmlDecode(item.name),
                    original_id: item.original_id,
                    remoteInstance: item.remoteInstance,
                }))
                .sort((a, b) => a.name?.localeCompare(b.name, "no"));

            return filtered;

        });
    }

    handleScroll() {
        const bottomOfWindow = window.scrollY + window.innerHeight >= document.documentElement.scrollHeight;
        if (bottomOfWindow && this.result_shown() < this.result().length) {
            this.result_shown(this.result_shown() + 25);
        }
    }

    fetchEasyBooking(finished) {
        const start_date = `${this.date()}`;
        const end_date = `${this.date()}`;
        if (!(start_date in this.easy_booking_available_cache)) {
            this.easy_booking_available_cache[start_date] = [];
            this.easy_booking_not_available_cache[start_date] = [];
        }
        const resource_ids = [...new Set(this.result_all()
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
        const from_date = `${this.date()} 00:00:00`;
        const to_date = `${this.date()} 23:59:59`;
        const onSuccess = () => {
            if (fetched_easy_booking && fetched_available_resource) {
                this.available_resources([]);
                this.taken_allocations(this.result_all().filter(r => r.simple_booking !== 1).map(r => this.allocation_cache[from_date].allocations[r.id]).flat());
                this.seasons(this.result_all().filter(r => r.simple_booking !== 1).map(r => this.allocation_cache[from_date].seasons[r.id]).flat());
                this.calculateAvailableResources();
                // this.search();
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
        const resource_ids = [...new Set(this.result_all()
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
        // Temporarily disable subscriptions
        for (const subscription of this.subscriptions) {
            subscription.dispose();

        }

        // Reset values
        this.selected_town(null);
        this.selected_activities([]);
        this.selected_buildings([]);
        this.selected_resource_categories([]);
        this.selected_facilities([]);
        this.text("");
        this.date(getSearchDateString(new Date()));

        // Re-enable subscriptions
        this.initializeSubscriptions();
        this.searchFetch();

    }

    searchFetch() {
        // console.log("SEARCHING")
        this.result_shown(25); // Reset the number of shown results to 25 when search is performed
        if (this.show_only_available())
            this.fetchAvailableResources();
        else
            this.search();
    }


    search() {
        let resources = [];
        let hasSearch = false;
        if (this.selected_town() !== undefined ||
            this.selected_buildings().length > 0 ||
            this.selected_facilities().length > 0 ||
            this.selected_activities().length > 0 ||
            this.selected_resource_categories().length > 0
        ) {
            resources = this.resources();
            if (this.selected_town() !== undefined)
                resources = resources.filter(resource => this.building_resources().some(br => this.buildings().some(b => b.id === br.building_id && resource.id === br.resource_id)));
            if (this.selected_buildings().length > 0) {
                resources = resources.filter(resource => this.building_resources().some(br => this.selected_buildings().some(sb => sb.id === br.building_id && resource.id === br.resource_id)));
            }
            if (this.selected_facilities().length > 0) {
                resources = resources.filter(resource => this.selected_facilities().map(f => f.id).every(id => this.resource_facilities().some(rf => rf.resource_id === resource.id && rf.facility_id === id)));
            }
            if (this.selected_activities().length > 0) {
                resources = resources.filter(resource => this.selected_activities().map(f => f.id).every(id => this.resource_activities().some(ra => this.activity_cache[id].includes(ra.activity_id) && resource.id === ra.resource_id)));
            }
            if (this.selected_resource_categories().length > 0) {
                resources = (resources.filter(res => this.selected_resource_categories().some(cat => cat.id === res.rescategory_id)));
            }

            hasSearch = true;
        }

        if (this.text() !== "") {
            if (!hasSearch)
                resources = this.resources();
            const re = new RegExp(this.text(), 'i');
            let buildings_resources = [];
            let activity_resources = [];
            // Find all buildings matching so we can filter on later
            let buildings;
            if (this.selected_buildings().length === 0) {
                buildings = this.buildings().filter(building => building.name.match(re));
                buildings_resources = this.getResourcesFromBuildings(buildings);
            }

            // console.log('Building filter', buildings, buildings_resources)
            if (this.selected_activities().length === 0) {
                const matchingActivities = this.activities().filter(activity => activity.name.match(re));
                const activitySets = matchingActivities.map(activity => this.activity_cache[activity.id]);
                const resourceActivities = this.resource_activities();
                const resourceIdsSet = new Set(
                    activitySets.flatMap(
                        actIds => actIds.flatMap(
                            a => resourceActivities.filter(ref => ref.activity_id === a)
                        )
                    ).map(act => act.resource_id)
                );
                activity_resources = this.resources().filter(resource => resourceIdsSet.has(resource.id));

            }
            resources = [...resources.filter(resource => resource.name.match(re) || buildings_resources.some(r => r.id === resource.id)), ...activity_resources]
            hasSearch = true;
        }
        if (hasSearch) {
            // Remove duplicates
            resources = resources.reduce((accumulator, current) => {
                if (accumulator.findIndex(item => item.id === current.id) === -1) {
                    accumulator.push(current);
                }
                return accumulator;
            }, []);

            // Filter resources to ensure they have at least one building and one town
            // resources = resources.filter(resource => {
            //     const buildings = this.getBuildingsFromResource(resource.id);
            //     const towns = this.getTownFromBuilding(buildings);
            //     return buildings.length > 0 && towns.length > 0;
            // });

            this.result_all(resources);
            if (this.show_only_available())
                resources = resources.filter(r => this.resources_with_available_time().includes(r.id));
            // console.log(resources.length, "res")
            this.result(resources);
        } else {
            fillSearchCount(null);
            this.result([]);
        }
        // createJsSlidedowns();
    }

    showMoreResults() {
        this.result_shown(this.result_shown() + 25);
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
            const [day, month, year] = this.date().split(".");
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

        const date = getApiDate(this.date());
        const wday = date.getDay();
        const resource_allocation = this.taken_allocations().reduce((acc, cur) => {
            acc[cur.resource_id] = acc[cur.resource_id] || [];
            acc[cur.resource_id].push([
                timeToNumber(cur.from_.split(" ")[1]),
                !isSameDate(cur.from_, cur.to_) ? 86400 : timeToNumber(cur.to_.split(" ")[1])
            ]);
            return acc;
        }, {})
        const resource_season = this.seasons().reduce((acc, cur) => {
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
        this.resources_with_available_time([...new Set([...available_ids, ...this.easy_booking_available_cache[this.date()]])]);
    }


    getResourcesFromBuildings = (buildings) => {
        const building_ids = buildings.map(building => building.id);
        return this.building_resources()
            .filter(resource => building_ids.includes(resource.building_id))
            .map(resource => this.resources().find(res => res.id === resource.resource_id))
            .filter(r => !!r);
    }

    getBuildingsFromResource = (resource_id) => {
        const building_resources = this.building_resources().filter(br => br.resource_id === resource_id);
        const ids = building_resources.map(br => br.building_id);
        const res = this.buildings().filter(b => ids.includes(b.id));
        return res
    }

    getTownFromBuilding = (buildings) => {
        const ids = buildings.map(b => b.id)
        return this.towns_data().filter(t => ids.includes(t.b_id));
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

    resLength = ko.computed(() => {
        const maxCount = this.result().length;
        const currentResults = this.result_shown() > maxCount ? maxCount : this.result_shown();
        // return `Antall treff: ${currentResults} av ${maxCount}`
        return `Antall treff: ${maxCount}`
    })


    adjustMobilePositionOnSearch() {
        let searchBox = document.getElementById("search-booking");
        setTimeout(function () {
            window.scrollTo(0, searchBox.getBoundingClientRect().top);
        }, 200);
    }

    onTextFocus(data,event) {
        if(!isMobile()) {
            return true;
        }
        // console.log(event)
        let target = event.target;

        while (target) {
            if (target.className === 'bodySection') {
                // console.log('Parent found:', target);
                break;
            }
            target = target.parentElement;
        }
        if(target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        // event.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}


ko.components.register('booking-search', {
    viewModel: {
        createViewModel: (params, componentInfo) => new BookingSearch(params, componentInfo)
    },
    // language=HTML
    template: `
        <div id="search-booking">
            <div class="bodySection">
                <div class="multisearch w-100">
                    <div class="multisearch__inner multisearch__inner--no-button w-100">
                        <div class="row flex-column flex-md-row mb-lg-4">
                            <div class="col col-md-6 col-lg-6 mb-3 mb-lg-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-text">
                                        <trans>common:search</trans>
                                    </label>
                                    <input id="search-booking-text" type="text"
                                           data-bind="textInput: text, attr: {placeholder: strings.search}, event: { focus: onTextFocus }"/>

                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-datepicker">
                                        <trans>common:date</trans>
                                    </label>
                                    <input type="text" id="search-booking-datepicker" placeholder="dd.mm.yyyy"
                                           class="js-basic-datepicker" data-bind="textInput: date, datepicker"/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border"
                                 data-bind="css: {'filter-element': !show_more_filters()}">
                                <div class="multisearch__inner__item ">
                                    <label class="text-bold text-primary" for="search-booking-activities">
                                        <trans>bookingfrontend:activity</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-activities"
                                            multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities,
            attr: {'aria-label': strings.transCallable()('booking','activities')},
select2: {theme: 'select-v2 select-v2--main-search'}
            ">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row flex-column flex-md-row">
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0"
                                 data-bind="css: {'filter-element': !show_more_filters()}">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-area">
                                        <trans>bookingfrontend:where</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-area"
                                            data-bind="
                                          options: towns,
                                          optionsText: 'name',
                                          value: selected_town,
                                          optionsCaption: strings.transCallable()('bookingfrontend','district'),
                                          attr: {'aria-label': strings.transCallable()('bookingfrontend','district')},
                                          select2: {theme: 'select-v2 select-v2--main-search'}
                                    ">
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border"
                                 data-bind="css: {'filter-element': !show_more_filters()}">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-building">
                                        <trans>booking:what</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-building"
                                            multiple="true"
                                            data-bind="options: buildings,
                            optionsText: 'name',
                            selectedOptions: selected_buildings,
                            attr: {'aria-label': strings.transCallable()('bookingfrontend','location')},
select2: {theme: 'select-v2 select-v2--main-search'}
                            ">
                                    </select>
                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border"
                                 data-bind="css: {'filter-element': !show_more_filters()}">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-resource_categories">
                                        <trans>booking:type</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-resource_categories"
                                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories,
            attr: {'aria-label': strings.transCallable()('booking','resource_category')},
select2: {theme: 'select-v2 select-v2--main-search'}
            ">
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border"
                                 data-bind="css: {'filter-element': !show_more_filters()}">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-facilities">
                                        <trans>bookingfrontend:facilities</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-facilities"
                                            multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selected_facilities,
            attr: {'aria-label': strings.transCallable()('bookingfrontend','facilities')},
select2: {theme: 'select-v2 select-v2--main-search'}

            ">
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex d-md-none justify-content-end">
                                <button id="js-toggle-filter"
                                        class="pe-btn pe-btn-secondary align-self-end gap-2 d-flex"
                                        data-bind="click: () => show_more_filters(!show_more_filters())">
                                    <!-- ko if: !show_more_filters() -->
                                    <trans>bookingfrontend:see_more_filters</trans>
                                    <!-- /ko -->
                                    <!-- ko if: show_more_filters() -->
                                    <trans>bookingfrontend:see_less_filters</trans>
                                    <!-- /ko -->
                                    <i class="fa"
                                       data-bind="css: {'fa-chevron-up': show_more_filters(), 'fa-chevron-down': !show_more_filters()}"></i>
                                    
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="search-count" class="pt-3" data-bind="text: resLength"></div>

            <div class="col-12 d-flex justify-content-start my-4 mb-md-0">
                <input type="checkbox" id="show_only_available" class="checkbox-fa"
                       data-bind="checked: show_only_available"/>
                <label class="choice text-purple text-label" for="show_only_available">
                    <i class="far fa-square unchecked-icon"></i>
                    <i class="far fa-check-square checked-icon"></i>
                    <trans>bookingfrontend:show_only_available</trans>
                </label>
            </div>

            <div id="search-result" class="pt-3">
                <div data-bind="foreach: { data: result.slice(0, result_shown()), as: 'resource' }">
                    <resource-info-card
                            params="{ resource: resource, buildings: $parent.getBuildingsFromResource(resource.id), towns: $parent.getTownFromBuilding($parent.getBuildingsFromResource(resource.id)), lang: $parent.lang, towns_data: $parent.towns_data, date: $parent.date }"></resource-info-card>
                </div>
                <!--                <button data-bind="visible: result_shown() < result().length, click: showMoreResults"-->
                <!--                        class="btn btn-primary mt-3">Show More-->
                <!--                </button>-->
            </div>
        </div>
    `
});

