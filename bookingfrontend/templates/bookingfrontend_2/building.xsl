<xsl:template match="data" xmlns:php="http://php.net/xsl" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div id="building-page-content">
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
                        <h2 class="m-0 fa-solid fa-location-dot" style="font-size:22px"></h2>
                        <h2 class="m-0">
                            <xsl:value-of select="building/name"/>
                        </h2>
                    </div>
                    <div>
                        <map-modal>
                            <span>
                                <xsl:value-of select="building/street"/>,
                            </span>
                            <span>
                                <xsl:value-of select="building/zip_code"/>
                            </span>
                            <span style="display: none">
                                <xsl:value-of select="building/city"/>
                            </span>
                        </map-modal>
                    </div>
                </div>
            </div>




            <div class="row">
                <div class="col-md d-flex gap-3">
<!--                    <span class="d-flex gap-1">-->
<!--                        <span class=""><xsl:value-of select="php:function('lang', 'type')"/>:</span>-->
<!--                        <xsl:value-of select="php:function('lang', 'building')"/>-->
<!--                    </span>-->
<!--                    <span class="slidedown__toggler__info__separator">-->
<!--                        <i class="fa-solid fa-circle"></i>-->
<!--                    </span>-->
                    <span class="d-flex gap-1 text-overline">
                        <span class=""><xsl:value-of select="php:function('lang', 'district')"/>:</span>
                        <xsl:value-of select="building/part_of_town"/>
                    </span>
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
                        <xsl:value-of select="php:function('lang', 'rental_resources')"/>
                    </h3>
                </div>
                <div class="col-md-10 col-sm-12 d-flex gap-2 flex-wrap bookable-resources-container"
                     data-bind="foreach: {{data: bookableResource, afterRender: (e) => resourcesContainerAfterRender(e)}},  css: {{ 'expanded': resourcesExpanded }}">
                    <div data-bind="if: resourceItemLink != false">
                        <a class="pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3 pe-btn--small"
                           href="" data-bind="attr: {{'href': resourceItemLink }}">
                            <i class="fa-solid fa-layer-group"></i>
                            <span data-bind="html: name"></span>
                        </a>
                    </div>
                    <div data-bind="if: resourceItemLink == false">
                        <span class="pe-btn pe-btn-colour-secondary link-text link-text-secondary d-flex gap-3 pe-btn--small">
                            <i class="fa-solid fa-layer-group"></i>
                            <span data-bind="html: name"></span>
                        </span>
                    </div>
                </div>

                <div class="col-md-2 col-sm-12 justify-content-end align-items-start" data-bind="style: {{ display: isCollapseActive() &amp;&amp; isRendered() ? 'flex': 'none' }}">
                    <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"
                            data-bind="click: toggleResources">
                        <span data-bind="if: resourcesExpanded()"><xsl:value-of select="php:function('lang', 'show_less')"/></span>
                        <span  data-bind="ifnot: resourcesExpanded()"><xsl:value-of select="php:function('lang', 'show_more')"/></span>

                        <i class="fa"
                           data-bind="css: {{'fa-chevron-up': resourcesExpanded(), 'fa-chevron-down': !resourcesExpanded()}}"></i>
                    </button>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-12 mb-2">
                    <h3 class="m-0">
                        <xsl:value-of select="php:function('lang', 'description')"/>
                    </h3>
                </div>
                <collapsable-text params="{{ context: $context }}">
                    <xsl:value-of disable-output-escaping="yes"
                                  select="building/description"/>
                </collapsable-text>
            </div>
            <hr class="divider divider-primary mb-4"/>

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
                        <div><xsl:value-of select="php:function('lang', 'phone')"/>:
                            <xsl:value-of select="building/phone"/>
                        </div>
                    </xsl:if>
                    <xsl:if test="building/email">
                        <div><xsl:value-of select="php:function('lang', 'email')"/>:
                            <xsl:value-of select="building/email"/>
                        </div>
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
