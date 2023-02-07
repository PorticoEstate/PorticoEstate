const Search = () => {
    // All data from server
    const data = {
        location: []
    }

    // All data for ko
    const ko_data = {
        towns: ko.observableArray([]),
        selectedTown: ko.observable(),
        locations: ko.observableArray([]),
        selectedLocation: ko.observable()
    }

    // Area
    const fetchTowns = () => {
        const url = phpGWLink('bookingfrontend/', {
            menuaction: "bookingfrontend.uisearch.get_search_data_location",
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                // { b_id, b_name, id, name } b=building, id/name is part_of_town
                data.location = response;
                ko_data.towns(
                    [...new Set(response.map(item => item.name))]
                        .sort()
                        .map(name => ({name, id: response.find(i => i.name === name).id}))
                )
            },
            error: error => {
                console.log(error);
            }
        })
    }

    const searchEl = document.getElementById("search-page-content");
    ko.cleanNode(searchEl);
    ko.applyBindings(ko_data, searchEl);

    ko_data.selectedTown.subscribe(town => {
        ko_data.locations(
            data.location.filter(item => town.id === item.id)
                .map(item => ({id: item.b_id, name: item.b_name}))
        )
    });

    fetchTowns();
}

Search();
