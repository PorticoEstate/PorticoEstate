/* global date_format, lang, initialSelection */


/**
 * ArticleTableViewModel class to manage article table state and behavior.
 */
class ArticleTableViewModel {
    /**
     * Initializes a new instance of the ArticleTableViewModel class.
     * @param {Object} params - The parameters passed to the component.
     */
    constructor(params) {
        /** @type {Observable<Date[]>} */
        this.dates = params.date;

        /** @type {Observable<any[]>} */
        this.selectedResources = params.selectedResources;

        /** @type {ObservableArray<Object>} */
        this.articles = ko.observableArray([]);

        // Subscriptions to observables
        this.dateSubscription = this.dates.subscribe(this.updateMandatoryQuantities.bind(this));
        this.selectedResourcesSubscription = this.selectedResources.subscribe(this.handleResourceChange.bind(this));

        // Initialize an observable to store the loading state
        this.isLoading = ko.observable(false);

        // Perform the data fetch
        this.fetchArticles();
    }

    /**
     * Handles changes in selected resources.
     * @param {any} newValue - The new value of the selectedResources observable.
     */
    handleResourceChange(newValue) {
        console.log('Selected resources changed:', newValue);
        this.fetchArticles();
    }

    /**
     * Updates the quantities of mandatory items based on the date ranges.
     * @param {Array} dates - An array of date ranges to calculate the total times.
     */
    updateMandatoryQuantities(dates) {
        let totalMilliseconds = 0;
        dates.forEach((dateRange) => {
            const from = DateTime.fromFormat(dateRange.from_, "dd/MM/yyyy HH:mm");
            const to = DateTime.fromFormat(dateRange.to_, "dd/MM/yyyy HH:mm");

            // Check if 'from' and 'to' are valid Luxon DateTime objects
            if (from.isValid && to.isValid) {
                totalMilliseconds += to - from; // Luxon DateTime objects can be subtracted directly
            } else {
                console.error('Invalid date range:', dateRange);
                // Handle invalid dates appropriately
            }
        });

        const totalMinutes = Math.floor(totalMilliseconds / 60000);
        const totalHours = Math.floor(totalMinutes / 60);
        const totalDays = Math.floor(totalHours / 24);

        // Update quantities for mandatory items.
        this.articles().forEach((resource) => {
            // Logic for updating mandatory items...
            // See the previous implementation for details.
            if (resource.info.mandatory) {
                var quantity = 0;
                switch (resource.info.unit) {
                    case 'minute':
                        quantity = totalMinutes;
                        break;
                    case 'hour':
                        quantity = totalHours;
                        break;
                    case 'day':
                        quantity = totalDays;
                        break;
                    default:
                        // If the unit doesn't match minute, hour, or day, log an error or set a default
                        console.error('Unknown unit type for mandatory item:', resource.info.unit);
                        quantity = 0; // Default quantity
                        break;
                }
                // Update the selected quantity observable for the resource
                resource.info.selected_quantity(quantity);
            }

            // If the resource has groups with mandatory items, update those as well
            Object.values(resource.groups).forEach(function(group) {
                group.forEach(function(item) {
                    if (item.mandatory) {
                        var quantity = 0;
                        switch (item.unit) {
                            case 'minute':
                                quantity = totalMinutes;
                                break;
                            case 'hour':
                                quantity = totalHours;
                                break;
                            case 'day':
                                quantity = totalDays;
                                break;
                            default:
                                console.error('Unknown unit type for mandatory item:', item.unit);
                                quantity = 0; // Default quantity
                                break;
                        }
                        // Update the selected quantity observable for the item
                        item.selected_quantity(quantity);
                    }
                });
            });
        });


        // Optionally, log the total times.
        console.log(totalMinutes, totalHours, totalDays);
    }

    /**
     * Calculates the total price for a given resource.
     * @param {Object} resource - The resource for which to calculate the total price.
     * @returns {string} The total price, formatted as a string with two decimal places.
     */
    calculateTotal(resource) {
        // Start with the resource's base price times its quantity
        var total = parseFloat(resource.info.price) * (resource.info.selected_quantity() || 0);

        // Add the price of each subitem
        Object.values(resource.groups).forEach(function(group) {
            group.forEach(function(item) {
                total += parseFloat(item.price) * (item.selected_quantity() || 0);
            });
        });

        return total.toFixed(2); // Return the total as a string with two decimal places
    }

    /**
     * Structures the raw table data into a more manageable format.
     * @param {Array<Object>} items - The raw table data items.
     * @returns {Object} The structured table data.
     */
    structureTableData(items) {
        let resources = {};

        // First, create entries for top-level resources
        items.forEach(function(item) {
            if (!item.parent_mapping_id) {
                // item.selected_quantity = ko.observable(Math.max(item.selected_quantity || 0, 0));
                resources[item.id] = {
                    info: item,
                    groups: {}
                };
            }
        });
        // Then, assign children to their respective parents
        items.forEach(function(item) {
            // Ensure selected_quantity is at least 0
            item.selected_quantity = ko.observable(Math.max(item.selected_quantity || 0, 0));

            // Add a computed observable for selected_sum
            item.selected_sum = ko.pureComputed(function() {
                return (item.selected_quantity() * parseFloat(item.price)).toFixed(2);
            });                if (item.parent_mapping_id) {
                // Check if the parent actually exists
                if (resources[item.parent_mapping_id]) {
                    if (!resources[item.parent_mapping_id].groups[item.article_group_name]) {
                        resources[item.parent_mapping_id].groups[item.article_group_name] = [];
                    }
                    resources[item.parent_mapping_id].groups[item.article_group_name].push(item);
                } else {
                    // Handle the case where the parent does not exist
                    console.error('Parent resource with ID ' + item.parent_mapping_id + ' does not exist.');
                    // You might want to handle this situation differently, depending on your needs.
                }
            }
        });

        return resources;
    }

    /**
     * Gets static JSON data for the articles.
     * @returns {Object} The parsed JSON data.
     */
    getStaticTableData() {
        return  JSON.parse(`{"data":[{"id":700,"parent_mapping_id":null,"resource_id":482,"article_id":"1_482","name":"Anretning","unit":"hour","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"136.99","tax":"34.25","price":"171.24","unit_price":"0.00","mandatory":1,"lang_unit":"Time","selected_quantity":1,"selected_article_quantity":"700_1_9_136.99_","selected_sum":"171.24"},{"id":860,"parent_mapping_id":700,"article_id":"2_3","name":"- Gulrotkake","unit":"kg","tax_code":9,"tax_percent":25,"group_id":2,"article_group_name":"Kake","ex_tax_price":"996.99","tax":"249.25","price":"1246.24","unit_price":"0.00","mandatory":"","lang_unit":"Kg","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":859,"parent_mapping_id":700,"article_id":"2_2","name":"- Utvask","unit":"each","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"125.00","tax":"31.25","price":"156.25","unit_price":"0.00","mandatory":"","lang_unit":"Stk","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":129,"parent_mapping_id":null,"resource_id":106,"article_id":"1_106","name":"Småsalen","unit":"hour","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"0.00","tax":"0.00","price":"0.00","unit_price":"0.00","mandatory":1,"lang_unit":"Time","selected_quantity":1,"selected_article_quantity":"129_1_9_0.00_","selected_sum":"0.00"},{"id":860,"parent_mapping_id":129,"article_id":"2_3","name":"- Gulrotkake","unit":"kg","tax_code":9,"tax_percent":25,"group_id":2,"article_group_name":"Kake","ex_tax_price":"996.99","tax":"249.25","price":"1246.24","unit_price":"0.00","mandatory":"","lang_unit":"Kg","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":859,"parent_mapping_id":129,"article_id":"2_2","name":"- Utvask","unit":"each","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"125.00","tax":"31.25","price":"156.25","unit_price":"0.00","mandatory":"","lang_unit":"Stk","selected_quantity":"","selected_article_quantity":"","selected_sum":""}]}`);

    }

    /**
     * Fetches article data from the server and updates the articles observable.
     */
    async fetchArticles() {
        window.application_id = typeof (window.application_id) === 'undefined' ? '' : window.application_id;
        window.reservation_type = typeof (window.reservation_type) === 'undefined' ? '' : window.reservation_type;
        window.reservation_id = typeof (window.reservation_id) === 'undefined' ? '' : window.reservation_id;
        const alloc_template_id = null
        const oArgs = {
            menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
            sort: 'name',
            application_id: application_id,
            reservation_type: reservation_type,
            reservation_id: reservation_id,
            alloc_template_id: alloc_template_id
        };

        // Generate the endpoint URL
        let url = phpGWLink('bookingfrontend/', oArgs, true);

        // Append each resource to the URL
        for (const resource of this.selectedResources()) {
            url += '&resources[]=' + resource;
        }
        console.log(url);
        // const structuredData = this.structureTableData(this.getStaticTableData()['data']);
        // this.articles(Object.values(structuredData));
        // return
        this.isLoading(true); // Set loading state to true
        try {
            const response = await fetch(url); // Replace with your actual API endpoint
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = (await response.json())['data'];
            console.log(data);
            const structuredData = this.structureTableData(data);
            this.articles(Object.values(structuredData));
        } catch (error) {
            console.error('Fetching articles failed:', error);
            // Handle the error state appropriately, perhaps by setting an error message observable
        } finally {
            this.updateMandatoryQuantities(this.dates())
            this.isLoading(false); // Set loading state to false
        }
    }

    /**
     * Disposes subscriptions to prevent memory leaks when the component is removed.
     */
    dispose() {
        this.dateSubscription.dispose();
        this.selectedResourcesSubscription.dispose();
    }
}





ko.components.register('article-table', {
    viewModel: {
        createViewModel: (params) => new ArticleTableViewModel(params)
    },
    // language=HTML
    template: `

        <!-- ko foreach: { data: articles, as: 'resource' } -->
        <table class="table">
            <thead>
            <tr>
                <th data-bind="text: resource.info.name"></th>
                <th></th>
                <th data-bind="text: 'Price/Hour: ' + resource.info.price"></th>
                <th data-bind="text: 'Hours: ' + resource.info.selected_quantity()"></th>
                <th data-bind="text: 'Total: ' + (resource.info.selected_quantity() * resource.info.price).toFixed(2)"></th>
                <th>
                    <button class="btn btn-link" type="button" data-toggle="collapse"
                            data-bind="attr: {'data-target': '#resource' + resource.info.resource_id}"
                            aria-expanded="true">
                        Toggle
                    </button>
                </th>
            </tr>
            <tr style="display: none;">
                <td colspan="5">
                    
                    <input type="hidden"
                           name="selected_articles[]"
                           data-bind="value: resource.info.id + '_' + (resource.info.selected_quantity() || '0') + '_' + resource.info.tax_code + '_' + resource.info.ex_tax_price + '_' + (resource.info.parent_mapping_id || '')" />
                    <input type="hidden" data-bind="value: resource.info.id" name="resource_ids[]">
                    <input type="hidden" data-bind="value: resource.info.selected_quantity"
                           name="resource_quantities[]">
                    <input type="hidden" data-bind="value: resource.info.mandatory" name="resource_mandatory[]">
                    <!-- Add other hidden fields as needed -->
                </td>
            </tr>
            </thead>
            <tbody data-bind="attr: {id: 'resource' + resource.info.resource_id}, collapse: true">
            <!-- ko foreach: { data: Object.keys(resource.groups), as: 'groupName' } -->
            <tr>
                <td colspan="5" class="font-weight-bold" data-bind="text: groupName"></td>
            </tr>
            <!-- ko foreach: { data: resource.groups[groupName], as: 'item' } -->
            <tr>
                <td></td>
                <td data-bind="text: item.name"></td>
                <td data-bind="text: item.price"></td> <!-- Updated from item.unit_price to item.price -->
                <td>
                    <input type="number" min="0"
                           data-bind="value: item.selected_quantity, event: { change: $parent.updateQuantity }">
                </td>
                <td style="display: none;">
                    <!-- selected_articles -->
                    <input type="hidden"
                           name="selected_articles[]"
                           data-bind="value: item.id + '_' + (item.selected_quantity() || '0') + '_' + item.tax_code + '_' + item.ex_tax_price + '_' + (item.parent_mapping_id || '')" />

                </td>
                <td data-bind="text: item.selected_sum"></td>
                <td>


                <td>
                    <!-- Action buttons or inputs would go here -->
                </td>
                <td style="display: none;">
                    <!-- Item id -->
                    <input type="hidden" data-bind="value: item.id">
                </td>

                <td style="display: none;">
                    <!-- mandatory -->
                    <input type="hidden" data-bind="value: item.mandatory" name="mandatory_items[]">
                </td>
                <td style="display: none;">
                    <!-- selected_quantity -->
                    <input type="hidden" data-bind="value: item.selected_quantity" name="selected_quantities[]">
                </td>
                <td style="display: none;">
                    <!-- parent_mapping_id -->
                    <input type="hidden" data-bind="value: item.parent_mapping_id" name="parent_mapping_ids[]">
                </td>
            </tr>
            <!-- /ko -->
            <!-- /ko -->
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">Total Price:</td>
                <td data-bind="text: $parent.calculateTotal(resource)"></td>
                <td></td>
            </tr>
            </tfoot>
        </table>
        <!-- /ko -->
    `
});

