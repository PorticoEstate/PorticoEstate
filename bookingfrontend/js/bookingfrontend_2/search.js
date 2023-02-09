const Search = () => {
    // All data from server
    let data = {
        location: [],
        activities: [],
        resource_categories: [],
        resources: [],
        facilities: [],
        buildings: [],
        building_resources: [],
        organizations: [],
    }

    const ko_search = {
        type_group: ko.observable("booking"),
        header_text: ko.observable("Lei lokale til det du trenger")
    }

    // All data for ko
    const ko_booking = {
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

    const ko_event = {
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

    const ko_organization = {
        activities: ko.observableArray([]),
        selected_activities: ko.observableArray([]),
        organizations: ko.observableArray([]),
        selected_organizations: ko.observableArray([]),
        text: ko.observable("")
    }

    const fetchData = () => {
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uisearch.get_search_data_all',
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                console.log(response);
                data = response;
                ko_booking.towns(
                    [...new Set(data.towns.map(item => item.name))]
                        .sort()
                        .map(name => ({name, id: data.towns.find(i => i.name === name).id}))
                )
                ko_booking.activities(data.activities)
                ko_booking.resources(data.resources)
                ko_booking.facilities(data.facilities)
                ko_booking.resource_categories(data.resource_categories)
                updateLocations(null);

                ko_event.activities(data.activities)
                ko_event.resources(data.resources)
                ko_event.facilities(data.facilities)
                ko_event.resource_categories(data.resource_categories)

                ko_organization.activities(data.activities)
                ko_organization.organizations(data.organizations)
            },
            error: error => {
                console.log(error);
            }
        })
    }

    const updateLocations = (town = null) => {
        ko_booking.locations(
            data.towns.filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: item.b_name}))
        )
    }

    const searchEl = document.getElementById("search-header");
    ko.cleanNode(searchEl);
    ko.applyBindings(ko_search, searchEl);
    const bookingEl = document.getElementById("search-booking");
    ko.cleanNode(bookingEl);
    ko.applyBindings(ko_booking, bookingEl);
    const eventEl = document.getElementById("search-event");
    ko.cleanNode(eventEl);
    ko.applyBindings(ko_event, eventEl);
    const organizationEl = document.getElementById("search-organization");
    ko.cleanNode(organizationEl);
    ko.applyBindings(ko_organization, organizationEl);

    // Setting up update of location from town
    ko_booking.selected_town.subscribe(town => {
        updateLocations(town);
    });

    ko_booking.selected_facilities.subscribe(facilitites => {
        console.log(facilitites);
    })

    ko_organization.selected_organizations.subscribe(organizations => {
        organizationSearch();
    })

    ko_organization.text.subscribe(text => {
        organizationSearch();
    })

    ko_organization.selected_activities.subscribe(activities => {
        organizationSearch();
    })

    const organizationSearch = () => {
        let organizations = [];
        if (ko_organization.text()!=="" || ko_organization.selected_organizations().length>0 ||ko_organization.selected_activities().length>0) {
            const re = new RegExp(ko_organization.text(), 'i');
            organizations = data.organizations.filter(o => o.name.match(re))
            if (ko_organization.selected_organizations().length > 0) {
                organizations = organizations.filter(o => ko_organization.selected_organizations().some(ko => ko.id === o.id))
            }
            if (ko_organization.selected_activities().length > 0) {
                let ids = [];
                for (const activity of ko_organization.selected_activities()) {
                    ids.push(...getAllSubRowsIds(data.activities, activity.id))
                }
                // Unique
                ids = [...new Set(ids)];
                organizations = organizations.filter(o => ids.some(id => id === o.activity_id))
            }
        }
        const el = $("#search-result");
        el.empty();
        addInfoCardOrganizations(el, organizations);
        createJsSlidedowns();
    }

    ko_search.type_group.subscribe(type => {
        switch (type) {
            case "booking":
                ko_search.header_text("Lei lokale til det du trenger");
                $("#search-booking").show();
                $("#search-event").hide();
                $("#search-organization").hide();
                break;
            case "event":
                ko_search.header_text("Finn arrangement");
                $("#search-event").show();
                $("#search-booking").hide();
                $("#search-organization").hide();
                break;
            case "organization":
                ko_search.header_text("Finn organisasjon");
                $("#search-organization").show();
                $("#search-booking").hide();
                $("#search-event").hide();
                break;
            default:
                ko_search.header_text("Lei lokale til det du trenger");
        }
    })

    // Traverse data and give you parent and childs with parent_id in dataset
    const getAllSubRowsIds = (rows, id) => {
        let result = [id];
        rows.filter(a => a.parent_id === id).map(a => {
            result.push(...getAllSubRowsIds(rows, a.id));
        });
        return result;
    }

    const addInfoCardOrganizations = (el, organizations) => {
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

    fetchData();
}

Search();

$(document).ready(function () {
    $("#search-event").hide();
    $("#search-organization").hide();
    // $('#js-select-activities').select2({
    //     theme: 'select-v2',
    //     width: '100%',
    //     closeOnSelect: false
    // });
    // $('#js-select-resource-categories').select2({
    //     theme: 'select-v2',
    //     width: '100%',
    //     closeOnSelect: false
    // });
    // $('#js-select-resources').select2({
    //     theme: 'select-v2',
    //     width: '100%',
    //     closeOnSelect: false
    // });
    // $('#js-select-facilities').select2({
    //     theme: 'select-v2',
    //     width: '100%',
    //     closeOnSelect: false
    // });
})

