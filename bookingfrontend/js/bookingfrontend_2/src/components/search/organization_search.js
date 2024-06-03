import {emptySearch, getAllSubRowsIds, joinWithDot} from "./search-util";
import './info-cards/organization-info-card'

class OrganizationSearchViewModel {
    result = ko.observableArray([]);  // This observable array will hold the search results
    result_shown = ko.observable(25);  // This observable tracks the number of results shown

    show_only_available = ko.observable(false);
    transCallable = ko.computed(function () {
        if (globalThis['translations'] && globalThis['translations']()) {
        }
        return trans;
    });

    constructor(params) {
        params.instance(this)
        console.log(params);
        this.activities = params.activities;
        this.selected_activities = ko.observableArray([]);
        this.organizations = params.organizations;
        this.selected_organizations = ko.observableArray([]);
        this.text = ko.observable("");

        this.text.subscribe(() => this.search());
        this.selected_activities.subscribe(() => this.search());
        window.addEventListener('scroll', this.handleScroll.bind(this));
    }

    reset() {
        $('#search-organization-activities').val([]).trigger('change');
        this.text("");
    }

    search() {
        this.result_shown(25)
        let organizations = [];
        if (this.text() !== "" || this.selected_organizations().length > 0 || this.selected_activities().length > 0) {
            const re = new RegExp(this.text(), 'i');
            organizations = this.organizations().filter(o => o.name.match(re));
            if (this.selected_organizations().length > 0) {
                organizations = organizations.filter(o => this.selected_organizations().some(ko => ko.id === o.id));
            }
            if (this.selected_activities().length > 0) {
                let ids = [];
                for (const activity of this.selected_activities()) {
                    ids.push(...getAllSubRowsIds(this.activities(), activity.id));
                }
                ids = [...new Set(ids)];
                organizations = organizations.filter(o => ids.some(id => id === o.activity_id));
            }
            console.log(organizations);
            this.result(organizations);
            // this.addInfoCards(el, organizations.slice(0, 10), organizations.length);
        } else {
            this.result([]);

            // fillSearchCount(null);
        }

        // createJsSlidedowns();
    }

//     addInfoCards(el, organizations, count) {
//         const append = [];
//         for (const organization of organizations) {
//             append.push(`
//     <div class="col-12 mb-4">
//       <div class="js-slidedown slidedown">
//         <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
//           <span>${organization.name}</span>
//           <span class="slidedown__toggler__info">
//           ${joinWithDot([organization.email, organization.street])}
//           </span>
//         </button>
//         <div class="js-slidedown-content slidedown__content">
//           <p>
//             ${organization.description}
//             <ul>
//                 <li>Hjemmeside: ${organization.homepage}</li>
//                 <li>Tlf: ${organization.phone}</li>
//                 <li>E-post: ${organization.email}</li>
//                 <li>Adresse: ${organization.street}</li>
//                 <li>Postnr: ${organization.zip_code}</li>
//                 <li>Poststed: ${organization.city}</li>
//                 <li>Distrikt: ${organization.district}</li>
//                 <li><a href="${phpGWLink('bookingfrontend/', {
//                     menuaction: 'bookingfrontend.uiorganization.show',
//                     id: organization.id
//                 }, false)}">Mer info</a></li>
//             </ul>
//           </p>
//         </div>
//       </div>
//     </div>
// `
//             );
//         }
//         el.append(append.join(""));
//         // fillSearchCount(organizations, count);
//     }

    handleScroll() {
        const bottomOfWindow = window.scrollY + window.innerHeight >= document.documentElement.scrollHeight;
        if (bottomOfWindow && this.result_shown() < this.result().length) {
            this.result_shown(this.result_shown() + 25);
        }
    }

    resLength = ko.computed(() => {
        const maxCount = this.result().length;
        const currentResults = this.result_shown() > maxCount ? maxCount : this.result_shown();
        // return `Antall treff: ${currentResults} av ${maxCount}`
        return `Antall treff: ${maxCount}`
    })
}

ko.components.register('organization-search', {
    viewModel: OrganizationSearchViewModel,
    // language=HTML
    template: `
        <div id="search-organization">
            <div class="bodySection">
                <div class="multisearch w-100 mb-5">
                    <div class="multisearch__inner w-100">
                        <div class="row flex-column flex-md-row">
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-organization-text">
                                        <trans>common:search</trans>
                                    </label>
                                    <input id="search-organization-text" type="text"
                                           data-bind="textInput: text, attr: {'placeholder': transCallable()('bookingfrontend','enter_team_organization_name')}"/>

                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-organization-activities">
                                        <trans>bookingfrontend:activity</trans>
                                    </label>
                                    <select class="js-select-multisearch" id="search-organization-activities"
                                            multiple="true" data-bind="
                                                options: activities,
                                                optionsText: 'name',
                                                selectedOptions: selected_activities,
                                                attr: {'aria-label': transCallable()('booking','activities')},
                                                select2: {theme: 'select-v2 select-v2--main-search'}
                                                ">
                                    </select>
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
                <div data-bind="foreach: { data: result.slice(0, result_shown()), as: 'org' }">
                    <organization-info-card
                            params="{ organization: org }"></organization-info-card>
                </div>
            </div>
        </div>
    `
});

