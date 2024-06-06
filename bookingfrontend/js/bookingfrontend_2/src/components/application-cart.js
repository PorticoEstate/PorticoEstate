import {onDocumentReady, phpGWLink} from '../helpers/util.js';
import './shopping-basket';

class ApplicationsCartModel {
    constructor() {
        this.applicationCartItems = ko.observableArray([]);
        this.applicationCartItemsEmpty = ko.observable(false);
        this.visible = ko.observable(true);
        this.initializeCart();
    }

    initializeCart() {
        this.getApplicationsCartItems();
    }



    deleteItem(item) {
        console.log("got delete request", this, item)
        const requestUrl = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.delete_partial"}, true);
        if (confirm('Do you want to delete this application?')) {
            fetch(requestUrl, {
                method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: item.id})
            })
                .then(response => response.json())
                .then(() => this.getApplicationsCartItems())
                .catch(error => console.error('Error:', error));
        }
    }

    getApplicationsCartItems() {

        const getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.get_partials"}, true);
        fetch(getJsonURL)
            .then(response => response.json())
            .then(data => this.updateCartItems(data))
            .catch(error => console.error('Error fetching cart items:', error));
    }

    updateCartItems(data) {
        const {list, total_sum} = data;
        if (total_sum) {
            // document.getElementById("total_sum").textContent = total_sum.toFixed(2);
            // document.getElementById("total_sum_block").style.display = 'block';
        }

        this.applicationCartItemsEmpty(list.length < 1);
        this.applicationCartItems(list.map(item => {

            return {
                ...item,
                dates: item.dates.map((dateObj) => {
                    const currentStartDate = new Date((dateObj.from_).replace(" ", "T"));
                    currentStartDate.setHours((dateObj.from_).substring(11, 13));
                    currentStartDate.setMinutes((dateObj.from_).substring(14, 16));

                    const currentEndDate = new Date((dateObj.to_).replace(" ", "T"));
                    currentEndDate.setHours((dateObj.to_).substring(11, 13));
                    currentEndDate.setMinutes((dateObj.to_).substring(14, 16));

                    return {
                        date: formatSingleDateWithoutHours(currentStartDate),
                        from_: dateObj.from_,
                        to_: dateObj.to_,
                        periode: formatPeriodeHours(currentStartDate, currentEndDate)
                    }
                }),
                resources: ko.observableArray(item.resources.map(res => ({...res}))),
                joinedResources: item.resources.map(res => res.name).join(", ")
            }
        }));
    }
}

let ApplicationCart = new ApplicationsCartModel();
onDocumentReady(() => {
    const elem = document.getElementById('application-cart-container') || document.getElementById("applications-cart-content");
    if (elem) {
        console.log("INITIALISING CART")
        ko.applyBindings(ApplicationCart, elem);
    }
})

export {ApplicationCart};


