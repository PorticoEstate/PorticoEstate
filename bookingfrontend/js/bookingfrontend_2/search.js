const Search = () => {
    // All data from server
    let data = {
        location: [],
        activities: [],
        resources: [],
        facilities: [],
        buildings: [],
        building_resources: [],

    }

    // All data for ko
    const ko_data = {
        towns: ko.observableArray([]),
        selectedTown: ko.observable(),
        locations: ko.observableArray([]),
        selectedLocation: ko.observable(),
        activities: ko.observableArray([]),
        selectedActivities: ko.observableArray([]),
        resources: ko.observableArray([]),
        selectedResources: ko.observableArray([]),
        facilities: ko.observableArray([]),
        selectedFacilities: ko.observableArray([])
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
    ko_data.selectedTown.subscribe(town => {
        updateLocations(town);
    });

    ko_data.selectedFacilities.subscribe(facilitites => {
        console.log(facilitites);
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