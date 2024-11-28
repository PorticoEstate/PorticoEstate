/* global landing_sections */

import '../helpers/util';
import {phpGWLink} from "../helpers/util";
import "../components/application-cart";
import {ModifyIds} from "../helpers/modifyIds";
import "../components/search/booking_search";
import "../components/search/organization_search";
import "../components/search/event_search";
import {htmlDecode, sortOnName} from "../components/search/search-util";

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


class Search {
    // All data from server
    data = {
        location: ko.observableArray([]),
        activities: ko.observableArray([]),
        resource_categories: ko.observableArray([]),
        resources: ko.observableArray([]),
        facilities: ko.observableArray([]),
        buildings: ko.observableArray([]),
        building_resources: ko.observableArray([]),
        organizations: ko.observableArray([]),
        events: ko.observableArray([]),
        towns: ko.observableArray([]),
        resource_activities: ko.observableArray([]),
        resource_facilities: ko.observableArray([]),
        resource_category_activity: ko.observableArray([])
    }

    // booking = new BookingSearch();
    booking = ko.observable();
    organization = ko.observable();
    event = ko.observable();
    // event = new EventSearch();
    // organization = new OrganizationSearch();
    ready = globalThis['translationsLoaded'];

    // ko_search = {
    type_group = ko.observable(null);
    header_text_kword = ko.observable({});
    header_sub_kword = ko.observable({});

    // }

    constructor() {
        const searchEl = document.getElementById("search-page-content");
        // console.log(remote_search)

        ko.cleanNode(searchEl);
        ko.applyBindings(this, searchEl);

        this.type_group.subscribe(type => {
            this.updateHeaderTexts(type);
        })
        // this.booking.subscribe((init) => {
            this.fetchData();
        // })

        const self = this;
        $(document).ready(function () {
            self.type_group(location.hash.substring(1))
        });
    }

    /*
     *  Knockout callback to reset search data
     *
     */
    resetSearchFilter() {
        switch (this.type_group()) {
            case "booking":
                this.booking().reset();
                break;
            case "event":
                this.event.reset();
                break;
            case "organization":
                this.organization().reset();
                break;
            default:

        }
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
                if (indx === 0) {
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
            // self.data = {...self.data, ...combinedData};
            // console.log(combinedData);

            for (const key in combinedData) {
                if (self.data[key] && ko.isObservable(self.data[key])) {
                    let toSave = combinedData[key];
                    if (['resources', 'facilities', 'towns', 'activities', 'resource_categories', 'organizations'].some(entry => entry === key)) {
                        toSave = sortOnName(sortOnName(toSave.map(entry => ({
                            ...entry,
                            name: htmlDecode(entry.name)
                        }))))
                    }
                    self.data[key](toSave);
                }
            }

            // self.booking().building_resources(combinedData.building_resources);
            // self.booking().towns_data(sortOnName(combinedData.towns));
            // self.booking().activities(sortOnName(self.data.activities));
            // self.booking().resources(sortOnName(self.data.resources.map(resource => ({
            //     ...resource,
            //     name: htmlDecode(resource.name)
            // }))));
            // self.booking().facilities(sortOnName(self.data.facilities.map(facility => ({
            //     ...facility,
            //     name: htmlDecode(facility.name)
            // }))));
            // self.booking().resource_categories(sortOnName(self.data.resource_categories));
            // self.booking().resource_facilities(self.data.resource_facilities);
            // self.booking().resource_activities(self.data.resource_activities);
            // self.booking().resource_category_activity(self.data.resource_category_activity);

            // self.event.data.events(self.data.events);
            //
            // self.organization().activities(sortOnName(self.data.activities.map(activity => ({
            //     ...activity,
            //     name: htmlDecode(activity.name)
            // }))));
            // self.organization().organizations(self.data.organizations.map(organization => ({
            //     ...organization,
            //     name: htmlDecode(organization.name)
            // })));
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
                this.header_text_kword({tag: 'rent_premises_facilities_equipment', group: 'bookingfrontend'});
                this.header_sub_kword({tag: 'use_filters_to_find_rental_objects', group: 'bookingfrontend'});
                // $("#search-booking").show();
                // $("#search-event").hide();
                // $("#search-organization").hide();
                this.booking()?.search();
                window.location.hash = '#booking';
                break;
            case "event":
                if (!landing_sections.event) {
                    this.updateHeaderTexts();
                    break;
                }
                this.header_text_kword({tag: 'find_event_or_activity', group: 'bookingfrontend'});
                this.header_sub_kword({tag: 'use_filters_to_find_todays_events', group: 'bookingfrontend'});
                // $("#search-event").show();
                // $("#search-booking").hide();
                // $("#search-organization").hide();
                // if (this.data.events.length === 0) {
                //     this.event.fetchEventOnDates();
                // }
                this.event()?.search();
                window.location.hash = '#event';
                break;
            case "organization":
                if (!landing_sections.organization) {
                    this.updateHeaderTexts();
                    break;
                }
                this.header_text_kword({tag: 'find_team_or_organization', group: 'bookingfrontend'});
                this.header_sub_kword({tag: 'search_for_like_minded_people', group: 'bookingfrontend'});
                // $("#search-organization").show();
                // $("#search-booking").hide();
                // $("#search-event").hide();
                this.organization()?.search();
                window.location.hash = '#organization';
                break;
            default:
                if (landing_sections.booking) {
                    this.type_group("booking")
                    break;
                }
                if (landing_sections.event) {
                    this.type_group("event")
                    break;
                }
                this.type_group("organization")

        }
    }

}

const search = new Search();
