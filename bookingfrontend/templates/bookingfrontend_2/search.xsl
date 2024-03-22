<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="search-page-content">
        <div id="search-header">
            <H1 class="text-primary text-md-start text-center mb-0">
                <trans params="tag: header_text_kword().tag, group: header_text_kword().group"></trans>
            </H1>
            <p class="mb-4 text-primary">
                <trans params="tag: header_sub_kword().tag, group: header_sub_kword().group"></trans>
            </p>
            <xsl:variable name="enabledCount"
                          select="sum(landing_sections/booking | landing_sections/event | landing_sections/organization)"/>
            <xsl:if test="$enabledCount != 1">
                <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
                    <div class="filter-group align-self-start mb-4 mb-md-0">
                        <xsl:if test="landing_sections/booking = 1">
                            <label class="filter-group__item" id="type_group-booking">
                                <input type="radio" name="type_group" value="booking" data-bind="checked: type_group"/>
                                <span class="filter-group__item__radio">
                                    <xsl:value-of select="php:function('lang', 'rent')"/>
                                </span>
                            </label>
                        </xsl:if>
                        <!-- Event radio button -->
                        <xsl:if test="landing_sections/event = 1">
                            <label class="filter-group__item" id="type_group-event">
                                <input type="radio" name="type_group" value="event" data-bind="checked: type_group"/>
                                <span class="filter-group__item__radio">
                                    <xsl:value-of select="php:function('lang', 'event')"/>
                                </span>
                            </label>
                        </xsl:if>

                        <!-- Organization radio button -->
                        <xsl:if test="landing_sections/organization = 1">
                            <label class="filter-group__item" id="type_group-organization">
                                <input type="radio" name="type_group" value="organization"
                                       data-bind="checked: type_group"/>
                                <span class="filter-group__item__radio">
                                    <xsl:value-of select="php:function('lang', '_organization')"/>
                                </span>
                            </label>
                        </xsl:if>
                    </div>
                    <button type="button" class="pe-btn pe-btn-secondary align-self-end" id="id-reset-filter">
                        <xsl:value-of select="php:function('lang', 'Reset filter')"/>
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
                                    <label for="search-booking-text">
                                        <xsl:value-of select="php:function('lang', 'search')"/>
                                    </label>
                                    <input id="search-booking-text" type="text"
                                           data-bind="textInput: text">
                                        <xsl:attribute name="placeholder">
                                            <xsl:value-of select="php:function('lang', 'search')"/>
                                        </xsl:attribute>
                                    </input>
                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-datepicker">
                                        <xsl:value-of select="php:function('lang', 'date')"/>
                                    </label>
                                    <input type="text" id="search-booking-datepicker" placeholder="dd.mm.yyyy"
                                           class="js-basic-datepicker" data-bind="textInput: date"/>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item ">
                                    <label class="text-bold text-primary" for="search-booking-activities">
                                        <xsl:value-of select="php:function('lang', 'activity')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-activities"
                                            multiple="true" data-bind="options: activities,
            optionsText: 'name',
            selectedOptions: selected_activities
            ">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'activities')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row flex-column flex-md-row">
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 filter-element">
                                <div class="multisearch__inner__item">
                                    <xsl:variable name="districtText" select="php:function('lang', 'district')"/>
                                    <label for="search-booking-area">
                                        <xsl:value-of select="php:function('lang', 'where')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-area"
                                            data-bind="options: towns,
						   optionsText: 'name',
						   value: selected_town,
                           optionsCaption: '{$districtText}' ">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'district')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item">
                                    <label for="search-booking-building">
                                        <xsl:value-of select="php:function('lang', 'what')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-building"
                                            multiple="true"
                                            data-bind="options: buildings,
                            optionsText: 'name',
                            selectedOptions: selected_buildings
                            ">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'location')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>

                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-resource_categories">
                                        <xsl:value-of select="php:function('lang', 'type')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-resource_categories"
                                            multiple="true" data-bind="options: resource_categories,
            optionsText: 'name',
            selectedOptions: selected_resource_categories
            ">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'Resource category')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border filter-element">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-booking-facilities">
                                        <xsl:value-of select="php:function('lang', 'Facilities')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-booking-facilities"
                                            aria-label="Fasiliteter"
                                            multiple="true" data-bind="options: facilities,
            optionsText: 'name',
            selectedOptions: selected_facilities
            ">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'facilities')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex d-md-none justify-content-end">
                                <button id="js-toggle-filter"
                                        class="pe-btn pe-btn-secondary align-self-end toggle-filter">
                                    <xsl:value-of select="php:function('lang', 'see_more_filters')"/>
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
                                           placeholder="dd.mm.yyyy" data-bind="textInput: from_date"/>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0 multisearch__inner--border">
                                <div class="multisearch__inner__item">
                                    <label for="search-event-datepicker-to">
                                        <xsl:value-of select="php:function('lang', 'To date')"/>
                                    </label>
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
                                    <label for="search-organization-text">
                                        <xsl:value-of select="php:function('lang', 'search')"/>
                                    </label>
                                    <input id="search-organization-text" type="text"
                                           data-bind="textInput: text">
                                        <xsl:attribute name="placeholder">
                                            <xsl:value-of
                                                    select="php:function('lang', 'enter_team_organization_name')"/>
                                        </xsl:attribute>
                                    </input>
                                </div>
                            </div>
                            <div class="col mb-3 mb-md-0">
                                <div class="multisearch__inner__item">
                                    <label class="text-bold text-primary" for="search-organization-activities">
                                        <xsl:value-of select="php:function('lang', 'activity')"/>
                                    </label>
                                    <select class="js-select-multisearch" id="search-organization-activities"
                                            multiple="true" data-bind="
                                                options: activities,
                                                optionsText: 'name',
                                                selectedOptions: selected_activities">
                                        <xsl:attribute name="aria-label">
                                            <xsl:value-of select="php:function('lang', 'activities')"/>
                                        </xsl:attribute>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="search-count" class="pt-3"></div>
        <div class="col-12 d-flex justify-content-start my-4 mb-md-0">
            <input type="checkbox" id="show_only_available" class="checkbox-fa"
                   data-bind="checked: show_only_available"/>
            <label class="choice text-purple text-label" for="show_only_available">
                <i class="far fa-square unchecked-icon"></i>
                <i class="far fa-check-square checked-icon"></i>
                <xsl:value-of select="php:function('lang', 'show_only_available')"/>
            </label>
        </div>
        <div id="search-result" class="pt-3"></div>
    </div>
    <script>
        const landing_sections = JSON.parse(`<xsl:value-of select="landing_sections_json"/>`)
    </script>
</xsl:template>

