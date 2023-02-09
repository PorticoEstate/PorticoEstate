<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="search-page-content">
        <div id="search-header">
            <H1 class="text-primary text-md-start text-center" data-bind="text: header_text"></H1>
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
                    <div class="multisearch__inner">
                        <div class="multisearch__inner__item">
                            <label for="search-booking-text">Søk</label>
                            <input id="search-booking-text" type="text" placeholder="Søk"></input>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-booking-area">Område</label>
                            <select class="js-select-multisearch" id="search-booking-area" aria-label="Bydel"
                                    data-bind="options: towns,
						   optionsText: 'name',
						   value: selected_town,
						   optionsCaption: 'Område/bydel'"/>

                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-booking-location">Lokale</label>
                            <select class="js-select-multisearch" id="search-booking-location" aria-label="Lokale"
                                    data-bind="options: locations,
                            optionsText: 'name',
                            value: selected_location,
                            optionsCaption: 'Velg'
                            "/>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-booking-datepicker">Dato</label>
                            <input type="text" id="search-booking-datepicker" placeholder="Velg"/>
                        </div>
                        <button type="button" class="pe-btn pe-btn-primary pe-btn--large w-100 d-md-none">Søk</button>
                        <button type="button" id="search-booking-button"
                                class="pe-btn pe-btn-primary pe-btn--circle d-none d-md-flex multisearch__inner__icon-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-booking-activities">Aktivitet</label>
                    <select class="js-select-multisearch" id="search-booking-activities" aria-label="Aktiviteter"
                            multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-booking-resource_categories">Ressurskategori
                    </label>
                    <select class="js-select-multisearch" id="search-booking-resource_categories"
                            aria-label="Ressurskategori"
                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-booking-resources">Ressurser</label>
                    <select class="js-select-multisearch" id="search-booking-resources" aria-label="Ressurser"
                            multiple="true" data-bind="options: resources,
            optionsText: 'name',
            selectedOptions: selected_resources
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-booking-facilities">Fasiliteter</label>
                    <select class="js-select-multisearch" id="search-booking-facilities" aria-label="Fasiliteter"
                            multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selected_facilities
            "/>

                </div>
            </div>
        </div>

        <div id="search-event">
            <div class="bodySection">
                <div class="multisearch w-100">
                    <div class="multisearch__inner">
                        <div class="multisearch__inner__item">
                            <label for="search-event-text">Søk</label>
                            <input id="search-event--text" type="text" placeholder="Søk"></input>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-event-area">Område</label>
                            <select class="js-select-multisearch" id="search-event-area" aria-label="Bydel" data-bind="options: towns,
						   optionsText: 'name',
						   value: selected_town,
						   optionsCaption: 'Område/bydel'"/>

                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-event-location">Lokale</label>
                            <select class="js-select-multisearch" id="search-event-location" aria-label="Lokale"
                                    data-bind="options: locations,
                            optionsText: 'name',
                            value: selected_location,
                            optionsCaption: 'Velg'
                            "/>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label for="search-event-datepicker">Dato</label>
                            <input type="text" id="search-event-datepicker" placeholder="Velg"/>
                        </div>
                        <button type="button" class="pe-btn pe-btn-primary pe-btn--large w-100 d-md-none">Søk</button>
                        <button type="button"
                                class="pe-btn pe-btn-primary pe-btn--circle d-none d-md-flex multisearch__inner__icon-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-event--activities">Aktivitet</label>
                    <select class="js-select-multisearch" id="search-event-activities" aria-label="Aktiviteter"
                            multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-event-resource_categories">Ressurskategori</label>
                    <select class="js-select-multisearch" id="search-event-resource_categories"
                            aria-label="Ressurskategori"
                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-event-resources">Ressurser</label>
                    <select class="js-select-multisearch" id="search-event-resources" aria-label="Ressurser"
                            multiple="true" data-bind="options: resources,
            optionsText: 'name',
            selectedOptions: selected_resources
            "/>
                </div>
                <div class="col-3 mb-4">
                    <label class="text-bold text-primary" for="search-event--facilities">Fasiliteter</label>
                    <select class="js-select-multisearch" id="search-event-facilities" aria-label="Fasiliteter"
                            multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selected_facilities
            "/>

                </div>
            </div>
        </div>

        <div id="search-organization">
            <div class="bodySection">
                <div class="multisearch w-100">
                    <div class="multisearch__inner">
                        <div class="multisearch__inner__item">
                            <label for="search-organization-text">Søk</label>
                            <input id="search-organization-text" type="text" placeholder="Søk" data-bind="textInput: text"></input>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label class="text-bold text-primary" for="search-organization-activities">Aktivitet</label>
                            <select class="js-select-multisearch" id="search-organization-activities"
                                    aria-label="Aktiviteter"
                                    multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities
            "/>
                        </div>
                        <div class="multisearch__inner__item multisearch__inner__item--border">
                            <label class="text-bold text-primary" for="search-organization-organization">Organisasjon</label>
                            <select class="js-select-multisearch" id="search-organization-organization"
                                    aria-label="Organisasjoner"
                                    multiple="true" data-bind="options: organizations,
            optionsText: 'name',
            selectedOptions: selected_organizations
            "/>
                        </div>
                        <button type="button" class="pe-btn pe-btn-primary pe-btn--large w-100 d-md-none">Søk</button>
                        <button type="button"
                                class="pe-btn pe-btn-primary pe-btn--circle d-none d-md-flex multisearch__inner__icon-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="search-result" class="pt-3"></div>
    </div>
</xsl:template>

