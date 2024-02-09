<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div class="info-content" id="resource-page-content">
        <div class="container">
            <div class="row pb-2">
                <div class="col-md-2">
                    <a class=" pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3  pe-btn--small">
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/', '')"/>
                        </xsl:attribute>
                        <i class="fa-solid fa-arrow-left"></i>
                        <xsl:value-of select="php:function('lang', 'Homepage')"/>
                    </a>
                </div>


            </div>
            <div class="row gx-3">
                <div class="col d-flex flex-column">
                    <div class="font-weight-bold gap-3 d-flex align-items-center mb-1">
                        <h1 class="m-0 fa-solid fa-layer-group" style="font-size:32px"></h1>
                        <h1 class="m-0">
                            <xsl:value-of select="resource/name"/>
                        </h1>
                    </div>
                    <span>
                        <xsl:value-of select="building/street"/>,
                        <xsl:value-of select="building/zip_code"/>
                    </span>
                </div>
            </div>

            <div class="row pb-3">
                <div class="col-md d-flex gap-3">
                    <span class="d-flex gap-1">
                        <span class="">Bydel:</span>
                        <xsl:value-of select="building/part_of_town"/>
                    </span>
                    <span class="slidedown__toggler__info__separator">
                        <i class="fa-solid fa-circle"></i>
                    </span>
                    <span class="d-flex gap-1">
                        <span class="">Bygg:</span>
                        <xsl:value-of select="building/name"/>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-sm-12 d-flex gap-2 flex-wrap bookable-resources-container">
                    <div>
                        <a class="pe-btn link-text link-text-primary d-flex gap-3 pe-btn--small"
                        >
                            <xsl:attribute name="href">
                                <xsl:value-of select="building/link"/>
                            </xsl:attribute>

                            <i class="fa-solid fa-location-dot"></i>
                            <span>
                                <xsl:value-of select="building/name"/>
                            </span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="divider divider-primary my-4"/>
            <div class="row mb-4">
                <div class="col-sm-12">
                    <h3 class="">
                        Beskrivelse
                    </h3>
                </div>
                <collapsable-text params="{{ content: selectedDescription }}">

                </collapsable-text>
            </div>
            <div class="row  mb-4">
                <div class="col-sm-12">
                    <h3 class="">
                        <xsl:value-of select="php:function('lang', 'Facilities')"/>
                    </h3>
                </div>
                <div class="col-sm-12 d-flex flex-row flex-wrap">
                    <xsl:for-each select="resource/facilities_list">
                        <div class="col-12 col-sm-6 col-md-3">
                            <xsl:value-of select="name"/>
                        </div>
                    </xsl:for-each>
                </div>

            </div>
            <!--            <hr class="divider divider-primary my-4"/>-->
            <div class="row mb-4 mycal">
                <div class="col-sm-12">
                    <h3 class="">
                        Bookingkalender -
                        <xsl:value-of select="resource/name"/>
                    </h3>
                </div>
<!--                <div id="calendar" class="calendar"></div>-->
                <pe-calendar>
                    <xsl:attribute name="params">
                        <xsl:text>building_id: </xsl:text>
                        <xsl:value-of select="building/id"/>
                        <xsl:text>, resource_id: </xsl:text>
                        <xsl:value-of select="resource/id"/>
<!--                        <xsl:text>, disableResourceSwap: true</xsl:text>-->
                    </xsl:attribute>
                </pe-calendar>
            </div>
            <div class="row mb-4">
                <light-box params="images: imageArray"></light-box>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="">
                        <xsl:value-of select="php:function('lang', 'contact information')"/>
                    </h3>
                </div>
                <div class="col-sm-12 d-flex flex-column">
                    <p>
                        <xsl:if test="resource/contact_info and normalize-space(resource/contact_info)">
                            <xsl:value-of disable-output-escaping="yes" select="resource/contact_info"/>
                        </xsl:if>
                    </p>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-12">
                    <h3 class="">
                        <xsl:value-of select="php:function('lang', 'Activities (2018)')"/>
                    </h3>
                </div>
                <div class="col-sm-12 d-flex flex-row flex-wrap">
                    <xsl:if test="count(resource/activities_list) &gt; 0">
                        <xsl:for-each select="resource/activities_list">
                            <div class="col-12 col-sm-6 col-md-3">
                                <xsl:value-of select="name"/>
                            </div>
                        </xsl:for-each>
                    </xsl:if>
                </div>
            </div>
        </div>
    </div>
    <script>
<!--        const calendar = new PEcalendar('calendar',<xsl:value-of select="building/id"/>,<xsl:value-of-->
<!--            select="resource/id"/>, null, true);-->

        var lang =<xsl:value-of select="php:function('js_lang', 'new application', 'Resource (2018)')"/>;
        var resourcename = '<xsl:value-of select="resource/name"/>';
        var deactivate_application =<xsl:value-of select="building/deactivate_application"/>+<xsl:value-of select="resource/deactivate_application"/>;
        var deactivate_calendar =<xsl:value-of select="building/deactivate_calendar"/>;
        var building_id = "<xsl:value-of select="building/id"/>";
        var simple_booking = "<xsl:value-of select="resource/simple_booking"/>";
        var initialDesc = `<xsl:value-of select="resource/description"/>`
    </script>
</xsl:template>
