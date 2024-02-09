<xsl:template match="data" xmlns:php="http://php.net/xsl" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <style>
        .modal-dialog,
        .modal-content {
        /* 80% of window height */
        height: 80%;
        }

        .modal-body {
        /* 100% = dialog height, 120px = header + footer */
        max-height: calc(100vh - 210px);
        overflow-y: auto;
        }
        /*
        .modal {
        max-height: 100vh;
        .modal-dialog {
        .modal-content {
        .modal-body {
        max-height: calc(80vh - 140px);
        overflow-y: auto;
        }
        }
        }
        }

        */
    </style>
    <div id="building-page-content">
        <div class="container mx-3">
            <div class="row">
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
                        <h1 class="m-0 fa-solid fa-location-dot" style="font-size:32px"></h1>
                        <h1 class="m-0">
                            <xsl:value-of select="building/name"/>
                        </h1>
                    </div>
                    <span>
                        <span id="buildingStreet"><xsl:value-of select="building/street"/></span>,
                        <span id="buildingZipCode"><xsl:value-of select="building/zip_code"/></span>
                        <span id="buildingCity" style="display:none"><xsl:value-of select="building/city"/></span>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 d-flex gap-3">
                    <span class="d-flex gap-1">
                        <span class="">Type:</span>
                        Bygg
                    </span>
                    <span class="slidedown__toggler__info__separator">
                        <i class="fa-solid fa-circle"></i>
                    </span>
                    <span class="d-flex gap-1">
                        <span class="">Bydel:</span>
                        <xsl:value-of select="building/part_of_town"/>
                    </span>
                    <span class="slidedown__toggler__info__separator">
                        <i class="fa-solid fa-circle"></i>
                    </span>
                    <span class="d-flex gap-1">
                        <span class="">Utleieressurser:</span>
                        <span data-bind="text: bookableResource().length"/>
                    </span>
                </div>
            </div>
            <hr class="divider divider-primary my-4"/>


            <div class="row">
                <div class="col-sm-12">
                    <h3 class="">
                        Utleieressurser
                    </h3>
                </div>
                <div class="col-md-10 col-sm-12 d-flex gap-2 flex-wrap bookable-resources-container"
                     data-bind="foreach: bookableResource,  css: {{ 'expanded': resourcesExpanded }}">
                    <div data-bind="if: resourceItemLink != false">
                        <a class="pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3 pe-btn--small"
                           href="" data-bind="attr: {{'href': resourceItemLink }}">
                            <i class="fa-solid fa-layer-group"></i>
                            <span data-bind="html: name"></span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div data-bind="if: resourceItemLink == false">
                        <span class="pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3 pe-btn--small">
                            <i class="fa-solid fa-layer-group"></i>
                            <span data-bind="html: name"></span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </div>

                <div class="col-md-2 col-sm-12 d-flex justify-content-end align-items-start">
                    <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"
                            data-bind="click: toggleResources">
                        <span data-bind="text: resourcesExpanded() ? 'Vis mindre' : 'Vis mer'"></span>
                        <i class="fa"
                           data-bind="css: {{'fa-chevron-up': resourcesExpanded(), 'fa-chevron-down': !resourcesExpanded()}}"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="">
                        Beskrivelse
                    </h3>
                </div>
                <div class="col-sm-12 d-flex flex-column collapsible-content collapsed-description"
                     data-bind="css: {{'collapsed-description': !descriptionExpanded()}}">
                    <p><xsl:value-of disable-output-escaping="yes"
                                  select="building/description"/></p>

                </div>
                <div class="col-sm-12">
                    <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3" data-bind="click: toggleDescription">
                        <span data-bind="text: descriptionExpanded() ? 'Vis mindre' : 'Vis mer'"></span>
                        <i class="fa"
                           data-bind="css: {{'fa-chevron-up': descriptionExpanded(), 'fa-chevron-down': !descriptionExpanded()}}"></i>
                    </button>
                </div>
            </div>
            <hr class="divider divider-primary my-4"/>

            <div class="row mb-4">
                <light-box params="images: imageArray"></light-box>
            </div>
            <div class="row">
                <div class="col-sm-12">
<!--                                [phone] => 55561106
            [email] => fana-ytrebygda.kulturkontor@bergen.kommune.no-->
                    <h3 class="">
                        <xsl:value-of select="php:function('lang', 'contact information')"/>
                    </h3>
                </div>
                <div class="col-sm-12 d-flex flex-column">
                        <xsl:if test="building/phone">
                            <div>Telefon: <xsl:value-of select="building/phone"/></div>
                        </xsl:if>
                        <xsl:if test="building/email">
                            <div>Epost: <xsl:value-of select="building/email"/></div>
                        </xsl:if>


                </div>
            </div>
        </div>


    </div>
    <script>
        var lang =<xsl:value-of select="php:function('js_lang', 'new application', 'Resource (2018)')"/>;
        var deactivate_application =<xsl:value-of select="building/deactivate_application"/>;
        /**
        * Hardcoded: Disable calendar at this level
        **/
        var deactivate_calendar = '<xsl:value-of select="building/deactivate_calendar"/>';
        var active_building = Number(<xsl:value-of select="building/active"/>);
    </script>
</xsl:template>
