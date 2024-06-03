import {phpGWLink} from "../../helpers/util";
import {
    getSearchDateString,
} from "./search-util";

import './info-cards/event-info-card'

class EventSearch {
    text = ko.observable("");
    // from_date = ko.observable(getSearchDateString(new Date()));
    from_date = ko.observable(getSearchDateString(new Date(2021, 1, 1)));
    to_date = ko.observable(getSearchDateString(new Date(new Date().getTime() + (7 * 86400 * 1000))));
    events = ko.observableArray([]);
    result_shown = ko.observable(25);

    constructor() {
        this.from_date.subscribe(from => {
            console.log("FROM", from);
            this.fetchEventOnDates();
        })

        this.to_date.subscribe(to => {
            console.log("TO", to);

            this.fetchEventOnDates();
        })

        this.fetchEventOnDates();
        window.addEventListener('scroll', this.handleScroll.bind(this));

    }

    handleScroll() {
        const bottomOfWindow = window.scrollY + window.innerHeight >= document.documentElement.scrollHeight;
        if (bottomOfWindow && this.result_shown() < this.result().length) {
            this.result_shown(this.result_shown() + 25);
        }
    }
    fetchEventOnDates() {
        const from = this.from_date()?.split(".");
        const to = this.to_date()?.split(".");
        const fromDate = from && from.length > 1 ? `${from[2]}-${from[1]}-${from[0]}T00:00:00` : getIsoDateString(new Date()); // year-month-day
        const toDate = to && to.length > 1 ? `${to[2]}-${to[1]}-${to[0]}T23:59:59` : `${from[2]}-${from[1]}-${from[0]}T23:59:59`;
        const buildingID = "";
        const facilityTypeID = "";
        const start = 0;
        const end = 1000;
        const loggedInOrgs = "";
        const url = phpGWLink('bookingfrontend/', {
            menuaction: 'bookingfrontend.uieventsearch.upcoming_events',
            fromDate,
            toDate,
            buildingID,
            facilityTypeID,
            loggedInOrgs,
            start,
            end,
            length: -1
        }, true);
        $.ajax({
            url,
            success: response => {
                this.events(response);
            },
            error: error => {
                console.log(error);
            }
        })
    }

    reset() {
        this.text("");
        this.from_date(getSearchDateString(new Date()))
        this.to_date("");
    }



//     addInfoCards(el, events, count) {
//         const append = [];
//         for (const event of events) {
//             append.push(`
//     <div class="col-12 mb-4">
//       <div class="js-slidedown slidedown">
//         <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
//           <span>${event.event_name}</span>
//           <span class="slidedown__toggler__info">
//           ${joinWithDot([
//                 event.location_name,
//                 getSearchDatetimeString(new Date(event.from)) + " - " + ((new Date(event.from)).getDate() === (new Date(event.to)).getDate() ? getSearchTimeString(new Date(event.to)) : getSearchDatetimeString(new Date(event.to)))])}
//           </span>
//         </button>
//         <div class="js-slidedown-content slidedown__content">
//           <p>
//             ${event.location_name}
//             <ul>
//                 <li>Fra: ${event.from}</li>
//                 <li>Til: ${event.to}</li>
//             </ul>
//           </p>
//         </div>
//       </div>
//     </div>
// `
//             )
//         }
//         el.append(append.join(""));
//         fillSearchCount(events, count);
//     }
    result = ko.computed(() => {
        console.log("rescall")

        if(!this.events()) {
            return []
        }
        this.result_shown(25)
        let events = this.events();
        if (this.text() !== "") {
            const re = new RegExp(this.text(), 'i');
            events = this.events().filter(o => o.event_name.match(re) || o.location_name.match(re))
        }
        console.log(events)
        return events;
        // this.addInfoCards(el, events, count);

    })

    resLength = ko.computed(() => {
        // const maxCount = 1337
        const maxCount = this.result().length;
        // const currentResults = this.result_shown() > maxCount ? maxCount : this.result_shown();
        // return `Antall treff: ${currentResults} av ${maxCount}`
        return `Antall treff: ${maxCount}`
    })
}


ko.components.register('event-search', {
    viewModel: EventSearch,
    template: `
       <div id="search-event">
        <div class="bodySection">
            <div class="multisearch w-100 mb-5">
                <div class="multisearch__inner w-100">
                    <div class="row flex-column flex-md-row">
                        <div class="col mb-3 mb-md-0">
                            <div class="multisearch__inner__item">
                                <label for="search-event-text">
                                    <xsl:value-of select="php:function('lang', 'search')"/>
                                </label>
                                <input id="search-event--text" type="text"
                                       data-bind="textInput: text">
                                    <xsl:attribute name="placeholder">
                                        <xsl:value-of select="php:function('lang', 'event_building')"/>
                                    </xsl:attribute>
                                </input>
                            </div>
                        </div>
                        <div class="col mb-3 mb-md-0 multisearch__inner--border">
                            <div class="multisearch__inner__item">
                                <label for="search-event-datepicker-from">
                                    <xsl:value-of select="php:function('lang', 'From date')"/>
                                </label>
                                <input type="text" id="search-event-datepicker-from" class="js-basic-datepicker"
                                       placeholder="dd.mm.yyyy" data-bind="textInput: from_date, datepicker"/>
                            </div>
                        </div>
                        <div class="col mb-3 mb-md-0 multisearch__inner--border">
                            <div class="multisearch__inner__item">
                                <label for="search-event-datepicker-to">
                                    <xsl:value-of select="php:function('lang', 'To date')"/>
                                </label>
                                <input type="text" id="search-event-datepicker-to" class="js-basic-datepicker"
                                       placeholder="dd.mm.yyyy" data-bind="textInput: to_date, datepicker"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
                   <div id="search-count" class="pt-3" data-bind="text: resLength"></div>

<!--            <div class="col-12 d-flex justify-content-start my-4 mb-md-0">-->
<!--                <input type="checkbox" id="show_only_available" class="checkbox-fa"-->
<!--                       data-bind="checked: show_only_available"/>-->
<!--                <label class="choice text-purple text-label" for="show_only_available">-->
<!--                    <i class="far fa-square unchecked-icon"></i>-->
<!--                    <i class="far fa-check-square checked-icon"></i>-->
<!--                    <trans>bookingfrontend:show_only_available</trans>-->
<!--                </label>-->
<!--            </div>-->

            <div id="search-result" class="pt-3">
                    <div data-bind="foreach: { data: result().slice(0, result_shown()), as: 'event' }">
                    <event-info-card
                            params="{ event: event }"></event-info-card>
                </div>
            </div>
        
    </div>
    `
});



