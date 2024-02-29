<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="search-page-content">
        <div id="search-header">
            <H1 class="text-primary text-md-start text-center mb-0" data-bind="text: header_text"></H1>
            <p class="mb-4 text-primary" data-bind="text: header_sub"></p>
            <xsl:variable name="enabledCount" select="sum(landing_sections/booking | landing_sections/event | landing_sections/organization)" />
            <xsl:if test="$enabledCount != 1">
            <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
                <div class="filter-group align-self-start mb-4 mb-md-0">
                    <xsl:if test="landing_sections/booking = 1">
                        <label class="filter-group__item" id="type_group-booking">
                            <input type="radio" name="type_group" value="booking" data-bind="checked: type_group"/>
                            <span class="filter-group__item__radio">Leie</span>
                        </label>
                    </xsl:if>
                    <!-- Event radio button -->
                    <xsl:if test="landing_sections/event = 1">
                        <label class="filter-group__item" id="type_group-event">
                            <input type="radio" name="type_group" value="event"  data-bind="checked: type_group"/>
                            <span class="filter-group__item__radio">Arrangement</span>
                        </label>
                    </xsl:if>

                    <!-- Organization radio button -->
                    <xsl:if test="landing_sections/organization = 1">
                        <label class="filter-group__item" id="type_group-organization">
                            <input type="radio" name="type_group" value="organization"  data-bind="checked: type_group"/>
                            <span class="filter-group__item__radio">Organisasjon</span>
                        </label>
                    </xsl:if>
                </div>
                <button type="button" class="pe-btn pe-btn-secondary align-self-end" id="id-reset-filter">
                    Nullstill søk
                    <i class="fas fa-undo ms-2"></i>
                </button>
            </div>
            </xsl:if>
        </div>

        <div id="search-booking">
            <div class="bodySection">
                <div class="multisearch w-100">
                    <div class="multisearch__inner multisearch__inner--no-button w-100">
                        <div class="row flex-column flex-md-row mb-lg-4">
                            <div class="col col-md-6 col-lg-6 mb-3 mb-lg-0">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-text">Søk</label>
                                    <input id="search-booking-text" type="text" placeholder="Søk"
                                           data-bind="textInput: text"></input>
                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-datepicker">Dato</label>
                                    <input type="text" id="search-booking-datepicker" placeholder="dd.mm.yyyy"
                                           class="js-basic-datepicker" data-bind="textInput: date"/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
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
                        </div>
                        <div class="row flex-column flex-md-row">
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 filter-element">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-area">Hvor</label>
                                    <select class="js-select-multisearch" id="search-booking-area" aria-label="Bydel"
                                            data-bind="options: towns,
						   optionsText: 'name',
						   value: selected_town,
						   optionsCaption: 'Område/bydel'"/>

                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-building">Hva</label>
                                    <select class="js-select-multisearch" id="search-booking-building"
                                            aria-label="Lokale"
                                            multiple="true"
                                            data-bind="options: buildings,
                            optionsText: 'name',
                            selectedOptions: selected_buildings
                            "/>
                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-resource_categories">
                                        Type
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-resource_categories"
                                            aria-label="Ressurskategori"
                                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories
            "/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
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
                            <div class="d-flex d-md-none justify-content-end">
                                <button id="js-toggle-filter"
                                        class="pe-btn pe-btn-secondary align-self-end toggle-filter">
                                    Se flere filter
                                </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="search-count" class="pt-3"></div>
        <div class="col-12 d-flex justify-content-start my-4 mb-md-0">
            <input type="checkbox" id="show_only_available" class="checkbox-fa" data-bind="checked: show_only_available"/>
            <label class="choice text-purple text-label" for="show_only_available">
                <i class="far fa-square unchecked-icon"></i>
                <i class="far fa-check-square checked-icon"></i>
                Vis kun tilgjengelige
            </label>
        </div>
        <div id="search-result" class="pt-3"></div>
    </div>
    <script>
        const landing_sections = JSON.parse(`<xsl:value-of select="landing_sections_json" />`)
    </script>
</xsl:template>

