/* global organization_id,organization_write_permission,organization_login */

import '../components/map-modal'
import '../components/collapsable-text'
import '../components/search/booking_search'
import '../helpers/withAfterRender';
import '../helpers/util';
import {joinWithDot} from "../components/search/search-util";

ko.bindingHandlers.groupsDisplay = {
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        var groups = ko.unwrap(valueAccessor());
        var hasWritePermission = organization_write_permission;

        var groupTexts = groups.map(function (group) {
            if (hasWritePermission) {
                // language=HTML
                return `<a href="${phpGWLink('bookingfrontend/', {
                    menuaction: 'bookingfrontend.uigroup.show',
                    id: group.id
                })}"
                           class="pe-btn  pe-btn--transparent pe-btn-text-primary pe-btn-text-overline p-0">${group.name}</a>`;
            } else {
                return group.name;
            }
        });

        if (hasWritePermission) {
            // language=HTML
            groupTexts.push(`
                <a href="${phpGWLink('bookingfrontend/', {
                menuaction: 'bookingfrontend.uigroup.edit',
                organization_id: organization_id
            })}" class="pe-btn  pe-btn--transparent pe-btn-text-secondary pe-btn-text-overline  p-0">
                    + ${trans('booking', 'new group')}
                </a>
            `);
        }

        element.innerHTML = `<span class="">${trans('booking', 'groups (2018)')}:
                        </span>` + joinWithDot(groupTexts);
    }
};

class OrganizationModel {
    organization_id = organization_id;
    buildings = ko.observableArray([]);
    groups = ko.observableArray();
    delegates = ko.observableArray();
    resources = ko.observableArray();
    towns = ko.observableArray();
    bookableResource = ko.observableArray();
    resourcesExpanded = ko.observable(false);
    buildingsExpanded = ko.observable(false);
    bookedByGroup = ko.observableArray();
    selectedResourceIds = ko.observableArray([]);
    selectedGroupIds = ko.observableArray([]);
    isBuildingCollapseActive = ko.observable(true);
    isBuildingRendered = ko.observable(false);
    isLoaded = globalThis['translationsLoaded'];
    searchDate = ko.observable(luxon.DateTime.now().toFormat("dd.MM.yyyy"));
    // searchDate = ko.observable('06.06.2024');


    constructor() {
        this.fetchBuildings();
        this.fetchGroups();
        if (organization_login) {
            this.fetchDelegates();
        }
        this.fetchCalendarEvents();
        this.searchDate.subscribe(() => this.fetchCalendarEvents())

    }

    groupIds = ko.computed(() => {
        return this.groups().map(group => group.id);
    })

    toggleBuildingsExpanded() {
        this.buildingsExpanded(!this.buildingsExpanded())
    }


    buildingUrl(data) {
        return phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibuilding.show',
            id: data.id
        });
    }



    getTownFromBuilding = (buildings) => {
        // console.log(this.towns())
        const ids = buildings.map(b => b.id)
        return this.towns().filter(t => ids.includes(t.b_id));
    }
    getBuildingsFromResource = (resource_id) => {
        const buildingRes = this.resources().filter(r => r.id === resource_id);
        return buildingRes.map(r => ({id: r.building_id, name: r.building_name}))
        // const building_resources = this.building_resources().filter(br => br.resource_id === resource_id);
        // const ids = building_resources.map(br => br.building_id);
        // const res = this.buildings().filter(b => ids.includes(b.id));
        // return []
    }

    delegateUrl(data) {
        return phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uidelegate.show',
            id: data.id
        });
    }

    fetchDelegates() {
        const delegateURL = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uidelegate.index',
            sort: 'name',
            filter_organization_id: organization_id,
            length: -1
        }, true);

        fetch(delegateURL)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if ('data' in data) {
                    this.delegates(data.data);
                    // console.log("Delegates:", data.data);
                }
            })
            .catch(error => {
                console.error("Error fetching delegates:", error);
            });
    }

    fetchGroups() {
        const groupURL = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uigroup.index',
            sort: 'name',
            filter_organization_id: organization_id,
            length: -1
        }, true);

        fetch(groupURL)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if ('data' in data) {
                    this.groups(data.data);
                    // console.log("Groups:", data.data);
                }
            })
            .catch(error => {
                console.error("Error fetching groups:", error);
            });
    }

    fetchBuildings() {
        const buildingURL = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uibuilding.find_buildings_used_by',
            sort: 'name',
            organization_id: organization_id,
            length: -1
        }, true);

        fetch(buildingURL)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if ('ResultSet' in data && 'Result' in data.ResultSet) {
                    // Assuming the data is returned as an array of building objects
                    this.buildings(data.ResultSet.Result);
                    // console.log(data.ResultSet.Result);
                }

            })
            .catch(error => {
                console.error("Error fetching buildings:", error);
            });
    }


    async fetchCalendarEvents() {
        const date =luxon.DateTime.fromFormat( this.searchDate(), "dd.MM.yyyy");
        // console.log(date);
        const url = phpGWLink('bookingfrontend/', {
            menuaction: "bookingfrontend.uibooking.organization_schedule",
            length: -1,
            date: `${date.year}-${date.month}-${date.day}`,
            organization_id: organization_id
        }, true);
        const eventsArray = [];

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'text/plain'
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            // console.log('res', result)

            const resources = result.resources

            this.resources(result.resources);
            this.towns(result.towns);


            // console.log("Die greate resulten!", resources);

        } catch (error) {
            console.error(error);
        }
    }


    resourcesContainerAfterRender(changes) {
        const elem = changes[0];
        // console.log("YOLO", elem[0].parentElement)
        // console.log(elem[0].parentElement, elem[0].parentElement.scrollHeight, elem[0].parentElement.clientHeight)
        this.isBuildingCollapseActive(elem.parentElement.scrollHeight > elem.parentElement.clientHeight || elem.parentElement.scrollWidth > elem.parentElement.clientWidth)
        this.isBuildingRendered(true)
        // this.resourcesContainer(elem);
    }


}

const organizationModel = new OrganizationModel();
ko.applyBindings(organizationModel, document.getElementById('organization-page-content'));
