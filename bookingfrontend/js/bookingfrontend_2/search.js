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

    }

    // All data for ko
    const ko_data = {
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
        type_group: ko.observable("booking"),
        header_text: ko.observable("Lei lokale til det du trenger")
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
                ko_data.towns(
                    [...new Set(data.towns.map(item => item.name))]
                        .sort()
                        .map(name => ({name, id: data.towns.find(i => i.name === name).id}))
                )
                ko_data.activities(data.activities)
                ko_data.resources(data.resources)
                ko_data.facilities(data.facilities)
                ko_data.resource_categories(data.resource_categories)
                updateLocations(null);
            },
            error: error => {
                console.log(error);
            }
        })
    }

    const updateLocations = (town=null) => {
        ko_data.locations(
            data.towns.filter(item => town ? town.id === item.id : true)
                .map(item => ({id: item.b_id, name: item.b_name}))
        )
    }

    const searchEl = document.getElementById("search-page-content");
    ko.cleanNode(searchEl);
    ko.applyBindings(ko_data, searchEl);

    // Setting up update of location from town
    ko_data.selected_town.subscribe(town => {
        updateLocations(town);
    });

    ko_data.selected_facilities.subscribe(facilitites => {
        console.log(facilitites);
    })

    ko_data.type_group.subscribe(type => {
        console.log("Type", type);
        switch(type) {
            case "booking":
                ko_data.header_text("Lei lokale til det du trenger");
                break;
            case "event":
                ko_data.header_text("Finn arrangement");
                break;
            case "organization":
                ko_data.header_text("Finn organisasjon");
                break;
            default:
                ko_data.header_text("Lei lokale til det du trenger");
        }
    })

    fetchData();
}

Search();

$(document).ready(function() {
    $('#js-select-activities').select2({
        theme: 'select-v2',
        width: '100%',
        closeOnSelect: false
    });
    $('#js-select-resource-categories').select2({
        theme: 'select-v2',
        width: '100%',
        closeOnSelect: false
    });
    $('#js-select-resources').select2({
        theme: 'select-v2',
        width: '100%',
        closeOnSelect: false
    });
    $('#js-select-facilities').select2({
        theme: 'select-v2',
        width: '100%',
        closeOnSelect: false
    });
})