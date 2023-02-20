<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="search-page-content">
        <div id="search-header">
            <H1 class="text-primary text-md-start text-center mb-0" data-bind="text: header_text"></H1>
            <p class="mb-4 text-primary" data-bind="text: header_sub"></p>
            <div class="col-12 mb-4">
                <label class="filter">
                    <input type="radio" name="type_group" value="booking" data-bind="checked: type_group"/>
                    <span class="filter__radio">Leie</span>
                </label>
                <label class="filter">
                    <input type="radio" name="type_group" value="event" data-bind="checked: type_group"/>
                    <span class="filter__radio">Arrangement</span>
                </label>
                <label class="filter">
                    <input type="radio" name="type_group" value="organization" data-bind="checked: type_group"/>
                    <span class="filter__radio">Organisasjon</span>
                </label>
            </div>
        </div>

        <div id="search-booking">
            <div class="bodySection">
                <div class="multisearch w-100">
                    <div class="multisearch__inner multisearch__inner--no-button w-100">
                        <div class="row flex-column flex-md-row mb-lg-4">
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-text">Søk</label>
                                    <input id="search-booking-text" type="text" placeholder="Søk"></input>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-area">Område</label>
                                    <select class="js-select-multisearch" id="search-booking-area" aria-label="Bydel"
                                            data-bind="options: towns,
						   optionsText: 'name',
						   value: selected_town,
						   optionsCaption: 'Område/bydel'"/>

                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-location">Lokale</label>
                                    <select class="js-select-multisearch" id="search-booking-location"
                                            aria-label="Lokale"
                                            data-bind="options: locations,
                            optionsText: 'name',
                            value: selected_location,
                            optionsCaption: 'Velg'
                            "/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-datepicker">Dato</label>
                                    <input type="text" id="search-booking-datepicker" placeholder="Velg"/>
                                </div>
                            </div>
                        </div>
                        <div class="row flex-column flex-md-row">
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0">
                                <div class="multisearch__inner__item ">
                                    <label class="text-bold text-primary" for="search-booking-activities">Aktivitet
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-activities"
                                            aria-label="Aktiviteter"
                                            multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities
            "/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-resource_categories">
                                        Ressurskategori
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-resource_categories"
                                            aria-label="Ressurskategori"
                                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories
            "/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-resources">Ressurser
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-resources"
                                            aria-label="Ressurser"
                                            multiple="true" data-bind="options: resources,
            optionsText: 'name',
            selectedOptions: selected_resources
            "/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-facilities">Fasiliteter
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-facilities"
                                            aria-label="Fasiliteter"
                                            multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selected_facilities
            "/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="search-event">
            <div class="bodySection">
                <div class="multisearch w-100 mb-5">
                    <div class="multisearch__inner w-100">
                        <div class="row flex-column flex-md-row">
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-event-text">Søk</label>
                                    <input id="search-event--text" type="text" placeholder="Arrangement / bygning"
                                           data-bind="textInput: text"></input>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-event-datepicker-from">Fra dato</label>
                                    <input type="text" id="search-event-datepicker-from" class="js-basic-datepicker"
                                           placeholder="dd.mm.yyyy" data-bind="textInput: from_date"/>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-event-datepicker-to">Til dato</label>
                                    <input type="text" id="search-event-datepicker-to" class="js-basic-datepicker"
                                           placeholder="dd.mm.yyyy" data-bind="textInput: to_date"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="search-organization">
            <div class="bodySection">
                <div class="multisearch w-100 mb-5">
                    <div class="multisearch__inner w-100">
                        <div class="row flex-column flex-md-row">
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-organization-text">Søk</label>
                                    <input id="search-organization-text" type="text"
                                           placeholder="Skriv navn på lag/organisasjon"
                                           data-bind="textInput: text"></input>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-organization-activities">
                                        Aktivitet
                                    </label>
                                    <select class="js-select-multisearch" id="search-organization-activities"
                                            aria-label="Aktiviteter"
                                            multiple="true" data-bind="
                                                options: activities,
                                                optionsText: 'name',
                                                selectedOptions: selected_activities
            "/>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-organization-organization">
                                        Organisasjon
                                    </label>
                                    <select class="js-select-multisearch" id="search-organization-organization"
                                            aria-label="Organisasjoner"
                                            multiple="true" data-bind="
                                                options: organizations,
                                                optionsText: 'name',
                                                selectedOptions: selected_organizations
            "/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="search-result" class="pt-3"></div>
    </div>
</xsl:template>

