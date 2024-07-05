<xsl:template match="data" xmlns:php="http://php.net/xsl" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:variable name="writePermission">
        <xsl:choose>
            <xsl:when test="organization/permission/write">true</xsl:when>
            <xsl:otherwise>false</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="organizationLoggedIn">
        <xsl:choose>
            <xsl:when test="organization/logged_on">true</xsl:when>
            <xsl:otherwise>false</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <div id="organization-page-content">


        <div class="container mx-3">
            <div class="row  pb-3">
                <div class="col-md-2">
                    <a class="link-text link-text-primary">
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/', '')"/>
                        </xsl:attribute>
                        <i class="fa-solid fa-arrow-left"></i>
                        <xsl:value-of select="php:function('lang', 'home_page')"/>
                    </a>
                </div>
            </div>


            <div class="row gx-3">
                <div class="col d-flex flex-column">
                    <div class="font-weight-bold gap-2 d-flex align-items-center mb-1">
                        <h2 class="m-0 fa-solid fa-futbol" style="font-size:22px"></h2>
                        <h2 class="m-0">
                            <xsl:value-of select="organization/name"/>
                        </h2>
                        <!-- Edit Button -->
                        <xsl:if test="$writePermission = 'true'">
                            <a class="pe-btn  pe-btn--transparent pe-btn-text-secondary"
                               onclick="window.location.href='{organization/edit_link}'"
                               title="{php:function('lang', 'edit')}">
                                <xsl:attribute name="href">
                                    <xsl:value-of select="organization/edit_link"/>
                                </xsl:attribute>
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </xsl:if>
                    </div>
                    <div>
                        <map-modal>
                            <span>
                                <xsl:value-of select="organization/street"/>,
                            </span>
                            <span>
                                <xsl:variable name="zipCode" select="organization/zip_code"/>
                                <xsl:variable name="city" select="organization/city"/>

                                <!-- Check the length of the zip code -->
                                <xsl:choose>
                                    <xsl:when test="string-length($zipCode) = 4">
                                        <!-- Apply modifications if the length is 4 -->
                                        <xsl:value-of select="concat($zipCode,' ', $city)"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <!-- Otherwise, output the zip code as is -->
                                        <xsl:value-of select="$zipCode"/>
                                    </xsl:otherwise>
                                </xsl:choose>

                            </span>
                            <span style="display: none">
                                <xsl:value-of select="organization/city"/>
                            </span>
                        </map-modal>
                    </div>
                </div>
            </div>
            <div class="row" data-bind="visible: isLoaded">
                <div class="col-md d-flex gap-3">
                    <!--                    <span class="d-flex gap-1">-->
                    <!--                        <span class=""><xsl:value-of select="php:function('lang', 'type')"/>:</span>-->
                    <!--                        <xsl:value-of select="php:function('lang', 'building')"/>-->
                    <!--                    </span>-->
                    <!--                    <span class="slidedown__toggler__info__separator">-->
                    <!--                        <i class="fa-solid fa-circle"></i>-->
                    <!--                    </span>-->
                    <span class="d-flex gap-1 flex-wrap align-items-center text-overline" data-bind="groupsDisplay: groups"></span>
                    <!--                    <span class="slidedown__toggler__info__separator">-->
                    <!--                        <i class="fa-solid fa-circle"></i>-->
                    <!--                    </span>-->
                    <!--                    <span class="d-flex gap-1">-->
                    <!--                        <span class=""><xsl:value-of select="php:function('lang', 'rental_resources')"/>:</span>-->
                    <!--                        <span data-bind="text: bookableResource().length"/>-->
                    <!--                    </span>-->
                </div>
            </div>


            <hr class="divider divider-primary my-3"/>


            <div class="row  mb-4">
                <div class="col-sm-12 mb-2">
                    <h3 class="m-0">
                        <xsl:value-of select="php:function('lang', 'Used buildings (2018)')"/>
                    </h3>
                </div>
                <div class="col-md-10 col-sm-12 d-flex gap-2 flex-wrap bookable-resources-container"
                     data-bind="foreach: {{data: buildings, afterRender: (e) => resourcesContainerAfterRender(e)}},  css: {{ 'expanded': resourcesExpanded }}">
                    <a class="pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3 pe-btn--small"
                       href="" data-bind="attr: {{'href': $root.buildingUrl($data) }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <span data-bind="text: name"></span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div>
                    <xsl:attribute name="class">
                        <xsl:choose>
                            <xsl:when test="organization/logged_on">col-md-8</xsl:when>
                            <xsl:otherwise></xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <div class="col-md-2 col-sm-12 justify-content-end align-items-start"
                         data-bind="style: {{ display: isBuildingCollapseActive() &amp;&amp; isBuildingRendered() ? 'flex': 'none' }}">
                        <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"
                                data-bind="click: toggleBuildingsExpanded">
                            <span data-bind="if: buildingsExpanded()">
                                <xsl:value-of select="php:function('lang', 'show_less')"/>
                            </span>
                            <span data-bind="ifnot: buildingsExpanded()">
                                <xsl:value-of select="php:function('lang', 'show_more')"/>
                            </span>
                            <i class="fa"
                               data-bind="css: {{'fa-chevron-up': resourcesExpanded(), 'fa-chevron-down': !resourcesExpanded()}}"></i>
                        </button>
                    </div>

                    <div class="row">
                        <xsl:if test="organization/description and normalize-space(organization/description)">
                            <collapsable-text>
                                <xsl:value-of disable-output-escaping="yes" select="organization/description"/>
                            </collapsable-text>
                        </xsl:if>
                    </div>


                    <xsl:if test="(organization/phone and string-length(normalize-space(organization/phone)) > 0) or
              (organization/email and string-length(normalize-space(organization/email)) > 0) or
              (organization/homepage and string-length(normalize-space(organization/homepage)) > 0) or
              (organization/contact_info and string-length(normalize-space(organization/contact_info)) > 0)">

                        <div class="row py-3">
                            <div class="col-sm-12">
                                <!--                                [phone] => 55561106
                                            [email] => fana-ytrebygda.kulturkontor@bergen.kommune.no-->
                                <h3 class="">
                                    <xsl:value-of select="php:function('lang', 'contact information')"/>
                                </h3>
                            </div>
                            <div class="col-sm-12 d-flex flex-column">
                                <xsl:choose>
                                    <xsl:when test="(organization/phone and string-length(normalize-space(organization/phone)) > 0) or
                        (organization/email and string-length(normalize-space(organization/email)) > 0) or
                        (organization/homepage and string-length(normalize-space(organization/homepage)) > 0)">
                                        <xsl:if test="organization/phone and string-length(normalize-space(organization/phone)) > 0">
                                            <div>
                                                <xsl:value-of select="php:function('lang', 'phone')"/>:
                                                <xsl:value-of select="normalize-space(organization/phone)"/>
                                            </div>
                                        </xsl:if>
                                        <xsl:if test="organization/email and string-length(normalize-space(organization/email)) > 0">
                                            <div>
                                                <xsl:value-of select="php:function('lang', 'email')"/>:
                                                <xsl:value-of select="normalize-space(organization/email)"/>
                                            </div>
                                        </xsl:if>
                                        <xsl:if test="organization/homepage and string-length(normalize-space(organization/homepage)) > 0">
                                            <div>
                                                <xsl:value-of select="php:function('lang', 'homepage')"/>:
                                                <a href="{normalize-space(organization/homepage)}" target="_blank">
                                                    <xsl:value-of select="normalize-space(organization/homepage)"/>
                                                </a>
                                            </div>
                                        </xsl:if>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of disable-output-escaping="yes" select="organization/contact_info"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </div>
                        </div>
                    </xsl:if>
                </div>


                <xsl:if test="organization/logged_on">
                    <div class="col-md-4">

                        <div class="row">

                            <div class="accordion" id="organizationAccordion">
                                <!-- Delegates Section -->
                                <div class="accordion-item">
                                    <span class="accordion-header" id="headingDelegates">
                                        <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseDelegates"
                                                aria-expanded="false" aria-controls="collapseDelegates">
                                            <xsl:value-of select="php:function('lang', 'Delegates')"/>
                                        </button>
                                    </span>
                                    <div id="collapseDelegates" class="accordion-collapse collapse"
                                         aria-labelledby="headingDelegates" data-bs-parent="#organizationAccordion">
                                        <div class="accordion-body">
                                            <div data-bind="foreach: delegates">
                                                <div class="row pb-2">
                                                    <div class="col-12">
                                                        <a data-bind="attr: {{'href': $root.delegateUrl($data) }}, text: name"></a>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span data-bind="text: email"></span>
                                                    </div>

                                                </div>
                                            </div>
                                            <xsl:if test="$writePermission = 'true'">
                                                <a href="{organization/new_delegate_link}"
                                                   class="pe-btn pe-btn--transparent pe-btn-text-secondary pe-btn-text-overline p-0">
                                                    +
                                                    <xsl:value-of select="php:function('lang', 'new delegate')"/>
                                                </a>
                                            </xsl:if>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </xsl:if>
            </div>
            <div class="row gx-3">


            </div>
            <hr class="divider divider-primary my-3"/>




            <div class="row">
                <div class="col-12 mb-4">
                    <h3><xsl:value-of select="php:function('lang', 'organization_calendar')"/></h3>
                </div>
                <div class="col-12 col-md-4 mb-4 d-flex flex-column align-items-center">
                    <label class="input-icon w-100" aria-labelledby="input-text-icon">
                        <span class="far fa-calendar-alt icon" aria-hidden="true"></span>
                        <input type="text" onkeydown="return false"
                               class="js-basic-datepicker bookingDate"
                               id="standard-datepicker"
                               data-bind="textInput: searchDate">
                            <xsl:attribute name="placeholder">
                                <xsl:value-of select="php:function('lang', 'add_date')"/>
                            </xsl:attribute>
                        </input>
                    </label>
                </div>
            </div>

            <div id="search-result" class="pt-3">
                <!-- ko if: resources().length > 0 -->
                <div data-bind="foreach: {{ data: resources, as: 'resource' }}">
                    <resource-info-card
                            params="{{ resource: resource, buildings: $parent.getBuildingsFromResource(resource.id), towns: $parent.getTownFromBuilding($parent.getBuildingsFromResource(resource.id)), lang: null, towns_data: $parent.towns_data, date: $parent.searchDate, disableText: true, static: true, filterGroups: $parent.groupIds }}"></resource-info-card>
                </div>
                <!-- /ko -->
                <!-- ko if: resources().length === 0 -->
                <div class="alert alert-info col-md-4">
                    <xsl:value-of select="php:function('lang', 'no_resources_found')"/>
                </div>
                <!-- /ko -->
            </div>



            <!--			ERROR ABOVE-->


            <div class="row mb-4">
                <light-box params="images: imageArray"></light-box>
            </div>
            <hr class="divider divider-primary mb-4"/>

        </div>
        <div class="push"></div>
        <div id="lightbox" class="modal hide" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-body lightbox-body">
                    <a href="#" class="close">&#215;</a>
                    <img src="" alt=""/>
                </div>
            </div>
        </div>


    </div>

    <xsl:variable name="cacheRefreshToken">
        <xsl:value-of select="php:function('get_phpgw_info', 'server|cache_refresh_token')"/>
    </xsl:variable>
    <script>

        var cache_refresh_token = "<xsl:value-of select="cacheRefreshToken"/>";

        var organization_id =<xsl:value-of select="organization/id"/>;

        var organization_write_permission =<xsl:value-of select="$writePermission"/>;
        var organization_login =<xsl:value-of select="$organizationLoggedIn"/>;

    </script>
</xsl:template>
