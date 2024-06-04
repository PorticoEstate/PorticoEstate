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
                    <button type="button" class="pe-btn pe-btn-secondary align-self-end" id="id-reset-filter"
                            data-bind="click: () => resetSearchFilter()">
                        <xsl:value-of select="php:function('lang', 'Reset filter')"/>
                        <i class="fas fa-undo ms-2"></i>
                    </button>
                </div>
            </xsl:if>
        </div>
<!--        <div data-bind="text: type_group"></div>-->
        <div data-bind="if: type_group() == 'booking'">
            <booking-search params="
                    instance: booking,
                    building_resources: data.building_resources,
                    towns_data: data.towns,
                    activities: data.activities,
                    resources: data.resources,
                    facilities: data.facilities,
                    resource_categories: data.resource_categories,
                    resource_facilities: data.resource_facilities,
                    resource_activities: data.resource_activities,
                    resource_category_activity: data.resource_category_activity,
            ">
            </booking-search>
        </div>
        <div data-bind="if: type_group() == 'organization'">

            <organization-search
                    params="
                    instance: organization,
                    activities: data.activities,
                    organizations: data.organizations
            ">
            </organization-search>
        </div>

        <div data-bind="if: type_group() == 'event'">

            <event-search
                    params="
                    instance: event,
            ">
            </event-search>
        </div>




    </div>
    <script>
        const landing_sections = JSON.parse(`<xsl:value-of select="landing_sections_json"/>`)
        const remote_search = JSON.parse(`<xsl:value-of select="multi_domain_list"/>`)
    </script>
</xsl:template>

