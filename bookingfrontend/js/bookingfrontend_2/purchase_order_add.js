/* global date_format, lang, initialSelection */

ko.bindingHandlers.collapse = {
    init: function (element, valueAccessor) {
        var value = valueAccessor();
        $(element).collapse(ko.unwrap(value) ? 'show' : 'hide');
    },
    update: function (element, valueAccessor) {
        var value = valueAccessor();
        $(element).collapse(ko.unwrap(value) ? 'show' : 'hide');
    }
};


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
        this.fetchArticles();
    }


    toggleCollapse(resource) {
        resource.isCollapsed(!resource.isCollapsed());
    }


    getPriceUnit(resource) {
        switch (resource.info.unit) {
            case 'minute':
                return "Minuttpris"
            case 'hour':
                return "Timepris"
            case 'day':
                return "Dagspris"
            default:
                // If the unit doesn't match minute, hour, or day, log an error or set a default
                console.error('Unknown unit type for mandatory item:', resource.info.unit);
                break;
        }
    }

    getPriceName(resource) {
        const plural = resource.info.selected_quantity() !== 1;
        switch (resource.info.unit) {
            case 'minute':
                return "Minutt"
            case 'hour':
                return "Timer"
            case 'day':
                return "Dager"
            default:
                // If the unit doesn't match minute, hour, or day, log an error or set a default
                console.error('Unknown unit type for mandatory item:', resource.info.unit);
                break;
        }
    }

    /**
     * Increments the quantity for a given item.
     * @param {Object} item - The item whose quantity is to be incremented.
     */
    incrementQuantity(item) {
        // Increase the item's quantity by 1
        item.selected_quantity(item.selected_quantity() + 1);
    }

    /**
     * Decrements the quantity for a given item.
     * @param {Object} item - The item whose quantity is to be decremented.
     */
    decrementQuantity(item) {
        // Decrease the item's quantity by 1, but not below 0
        const newQuantity = item.selected_quantity() - 1;
        item.selected_quantity(newQuantity < 0 ? 0 : newQuantity);
    }

    /**
     * Updates the quantities of mandatory items based on the date ranges.
     * @param {Array} dates - An array of date ranges to calculate the total times.
     */
    updateMandatoryQuantities(dates) {
        let totalMilliseconds = 0;
        dates.forEach((dateRange) => {
            const from = luxon.DateTime.fromFormat(dateRange.from_, "dd/MM/yyyy HH:mm");
            const to = luxon.DateTime.fromFormat(dateRange.to_, "dd/MM/yyyy HH:mm");

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
            Object.values(resource.groups).forEach(function (group) {
                group.forEach(function (item) {
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
        // console.log(totalMinutes, totalHours, totalDays);
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
        Object.values(resource.groups).forEach(function (group) {
            group.forEach(function (item) {
                total += parseFloat(item.price) * (item.selected_quantity() || 0);
            });
        });

        const hasDecimals = total % 1 !== 0;

        const options = {
            maximumFractionDigits: 2,
            minimumFractionDigits: hasDecimals ? 2 : 0,
        };

        const formattedTotal = total.toLocaleString('nb-NO', options);

        return hasDecimals ? formattedTotal : `${formattedTotal},-`;
    }


    /**
     * Returns number in nb-NO locale.
     * @param {number} value - input number.
     * @param {number} fractions - numbers after decimal.
     * @returns {string} Locale formatted number.
     */
    toLocale(value, fractions) {
        if (typeof value === "string") {
            value = +value;
        }

        const hasDecimals = value % 1 !== 0;

        const options = {
            minimumFractionDigits: hasDecimals ? (fractions !== undefined ? fractions : 2) : 0,
        };

        const formattedNumber = value.toLocaleString('nb-NO', options);

        return hasDecimals ? formattedNumber : `${formattedNumber},-`;
    }



    /**
     * Structures the raw table data into a more manageable format.
     * @param {Array<Object>} items - The raw table data items.
     * @returns {Object} The structured table data.
     */
    structureTableData(items) {
        let resources = {};

        // First, create entries for top-level resources
        items.forEach(function (item) {
            if (!item.parent_mapping_id) {
                // item.selected_quantity = ko.observable(Math.max(item.selected_quantity || 0, 0));
                resources[item.id] = {
                    info: item,
                    groups: {},
                    isCollapsed: ko.observable(false)  // Add the isCollapsed observable here
                };
            }
        });
        // Then, assign children to their respective parents
        items.forEach(function (item) {
            item.name = item.name.replace("- ", "")
            // Ensure selected_quantity is at least 0
            item.selected_quantity = ko.observable(Math.max(item.selected_quantity || 0, 0));

            // Add a computed observable for selected_sum
            item.selected_sum = ko.pureComputed(function () {
                return (item.selected_quantity() * parseFloat(item.price)).toFixed(2);
            });


            if (item.parent_mapping_id) {
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
            // Add a computed observable for computedString
            item.computed_selected_article = ko.pureComputed(function () {
                const val = `${item.id}_${item.selected_quantity()}_x_x_${item.parent_mapping_id || 'null'}`
                return `${item.id}_${item.selected_quantity()}_x_x_${item.parent_mapping_id || 'null'}`;
            });
        });

        return resources;
    }

    getRemark(itemArr) {
        const itemWithRemark = itemArr.find(a => a.article_group_remark);
        if(itemWithRemark) {
            return `<br/> <span class="remark">*${itemWithRemark.article_group_remark}</span>`
        }
        return ''
    }

    /**
     * Gets static JSON data for the articles.
     * @returns {Object} The parsed JSON data.
     */
    getStaticTableData() {
        return JSON.parse(`{"data":[{"id":700,"parent_mapping_id":null,"resource_id":482,"article_id":"1_482","name":"Anretning","unit":"hour","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"136.99","tax":"34.25","price":"171.24","unit_price":"0.00","mandatory":1,"lang_unit":"Time","selected_quantity":1,"selected_article_quantity":"700_1_9_136.99_","selected_sum":"171.24"},{"id":860,"parent_mapping_id":700,"article_id":"2_3","name":"- Gulrotkake","unit":"kg","tax_code":9,"tax_percent":25,"group_id":2,"article_group_name":"Kake","ex_tax_price":"996.99","tax":"249.25","price":"1246.24","unit_price":"0.00","mandatory":"","lang_unit":"Kg","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":859,"parent_mapping_id":700,"article_id":"2_2","name":"- Utvask","unit":"each","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"125.00","tax":"31.25","price":"156.25","unit_price":"0.00","mandatory":"","lang_unit":"Stk","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":129,"parent_mapping_id":null,"resource_id":106,"article_id":"1_106","name":"Småsalen","unit":"hour","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"0.00","tax":"0.00","price":"0.00","unit_price":"0.00","mandatory":1,"lang_unit":"Time","selected_quantity":1,"selected_article_quantity":"129_1_9_0.00_","selected_sum":"0.00"},{"id":860,"parent_mapping_id":129,"article_id":"2_3","name":"- Gulrotkake","unit":"kg","tax_code":9,"tax_percent":25,"group_id":2,"article_group_name":"Kake","ex_tax_price":"996.99","tax":"249.25","price":"1246.24","unit_price":"0.00","mandatory":"","lang_unit":"Kg","selected_quantity":"","selected_article_quantity":"","selected_sum":""},{"id":859,"parent_mapping_id":129,"article_id":"2_2","name":"- Utvask","unit":"each","tax_code":9,"tax_percent":25,"group_id":1,"article_group_name":"Andre","ex_tax_price":"125.00","tax":"31.25","price":"156.25","unit_price":"0.00","mandatory":"","lang_unit":"Stk","selected_quantity":"","selected_article_quantity":"","selected_sum":""}]}`);

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

    cleanText(str) {
        return str.replace(/<\/?[^>]+(>|$)/g, "");

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
        <div class="article-table-wrapper">
            <div class="article-table-header" data-bind="css: { 'collapsed-head': resource.isCollapsed() }">
                <!--                <div class="table article-table resource-table" data-bind="css: { 'collapsed-head': resource.isCollapsed() }">-->
                <div class="resource-name" data-bind="text: resource.info.name"></div>
                <div class="resource-price"
                     data-bind="text: $parent.getPriceUnit(resource)+': ' + $parent.toLocale(resource.info.price)"></div>
                <div class="resource-hours"
                     data-bind="text: $parent.getPriceName(resource)+': ' + resource.info.selected_quantity()"></div>
                <div class="resource-total"
                     data-bind="text: 'Total: ' + $parent.calculateTotal(resource)"></div>
                <div class="resource-expand"
                     data-bind="click: function() { $parent.toggleCollapse(resource) }">
                    <button class="btn btn-subtle" type="button" data-toggle="collapse"
                            data-bind="//click: function() { $parent.toggleCollapse(resource) }"
                            aria-expanded="true">
                        <!-- ko if: resource.isCollapsed() -->
                        <div><i class="fas fa-angle-down"></i></div>
                        <!-- /ko -->
                        <!-- ko ifnot: resource.isCollapsed() -->
                        <div><i class="fas fa-angle-up"></i></div>
                        <!-- /ko -->
                    </button>
                </div>
            </div>
            <div style="display: none;">
                <td colspan="8">
                    <!-- Hidden inputs for resource -->
                    <input type="hidden" data-bind="value: resource.info.id" name="resource_ids[]">
                    <input type="hidden" data-bind="value: resource.info.selected_quantity"
                           name="resource_quantities[]">
                    <input type="hidden" data-bind="value: resource.info.mandatory" name="resource_mandatory[]">
                    <input type="hidden" name="selected_articles[]"
                           data-bind="value: resource.info.computed_selected_article">
                    <!-- Add other hidden fields as needed -->
                </td>
            </div>
            <div data-bind="visible: !resource.isCollapsed(), attr: {id: 'resource' + resource.info.resource_id}"
                 class="collapsible-part">
                <!-- ko foreach: { data: Object.keys(resource.groups), as: 'groupName' } -->
                <div class="category-table ">
                    <div class="category-header">
                        <div class="category-name">
                            <span class="category-name-title" data-bind="text: groupName"></span>
                            <span data-bind="html: $parents[1].getRemark(resource.groups[groupName])"></span>
                        </div>
                        <div class="category-header-description">Beskrivelse</div>
                        <div class="category-header-unit-price">Pris pr enhet</div>
                        <div class="category-header-count">Antall enheter</div>
                        <div class="category-header-total">Total</div>
                    </div>
                    <div class="category-articles">
                        <!-- ko foreach: { data: resource.groups[groupName], as: 'item' } -->
                        <div class="category-article-row">
                            <div class="item-name" data-bind="text: item.name"></div>
                            <div class="desc-title">Beskrivelse</div>

                            <div class="item-description"
                                 data-bind="text: $parents[2].cleanText(item.article_remark)"></div>
                            <div class="price-title">Pris pr enhet</div>

                            <div class="item-price"
                                 data-bind="text: $parents[2].toLocale(item.price) + (item.unit === 'each' ? '/stk' : '/' + item.unit)"></div>
                            <!--                            <td class="item-quantity">-->
                            <!--                                <input type="number" class="form-control" min="0"-->
                            <!--                                       data-bind="value: item.selected_quantity, event: { change: $parent.updateQuantity }">-->
                            <!--                            </td>-->
                            <div class="item-quantity">
                                <button type="button" class=" pe-btn pe-btn-secondary pe-btn--small-circle "
                                        data-bind="click: function(data, event) { $parents[2].decrementQuantity(item)  }">
                                    <svg viewBox="0 0 48 48"
                                         xmlns="http://www.w3.org/2000/svg" ml-update="aware">
                                        <path class="horizontal" d="M32,26H16a2,2,0,0,1,0-4H32A2,2,0,0,1,32,26Z"/>
                                    </svg>
                                </button>
                                <span style="display: inline-block;min-width: 20px; text-align: center"
                                      data-bind="text: item.selected_quantity"></span>
                                <button type="button" class=" pe-btn pe-btn-secondary pe-btn--small-circle "
                                        data-bind="click: function() { $parents[2].incrementQuantity(item) }">
                                    <svg viewBox="0 0 48 48"
                                         xmlns="http://www.w3.org/2000/svg" ml-update="aware">
                                        <path class="horizontal" d="M32,26H16a2,2,0,0,1,0-4H32A2,2,0,0,1,32,26Z"/>
                                        <path class="vertical"
                                              d="M24,34a2,2,0,0,1-2-2V16a2,2,0,0,1,4,0V32A2,2,0,0,1,24,34Z"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <div class="sum-title">Total</div>
                            <div class="item-sum" data-bind="text: $parents[2].toLocale(item.selected_sum(), 2)"></div>
                            <div class="hidden-inputs" style="display: none;">
                                <!-- Hidden inputs for each item -->
                                <input type="hidden" data-bind="value: item.id">
                                <input type="hidden" data-bind="value: item.mandatory" name="mandatory_items[]">
                                <input type="hidden" data-bind="value: item.selected_quantity"
                                       name="selected_quantities[]">
                                <input type="hidden" data-bind="value: item.parent_mapping_id"
                                       name="parent_mapping_ids[]">
                                <input type="text" name="selected_articles[]"
                                       data-bind="value: item.computed_selected_article">

                            </div>
                        </div>
                        <!-- /ko -->
                    </div>
                </div>
                <!-- /ko -->
            </div>
        </div>
        <!-- /ko -->
    `
});
