<xsl:template match="data" xmlns:php="http://php.net/xsl">
   <div id="organization-page-content">  
    <div class="info-content pb-5">
        <div class="container my-container-top-fix wrapper">
            <div class="location">
                <span><a>
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                        </xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Home')" />
                    </a>
                </span>
                <span><xsl:value-of select="php:function('lang', 'organization')" /></span>
            </div>

            <div class="row">
            <div class="col-lg-6">
                
                <div class="row">
                    <div class="col-12 px-2 p-3">
                        <h3 id="main-item-header"><xsl:value-of select="organization/name"/></h3>
                        <xsl:if test="organization/street and normalize-space(organization/street)">
                            <i class="fas fa-map-marker d-inline"> </i>
                            <div class="building-place-adr">
                                <span id="item-street"><xsl:value-of select="organization/street"/></span>
                                <span id="item-zip-city"><xsl:value-of select="organization/zip_code"/>&#160;<xsl:value-of select="organization/city"/></span>
                        </div>
                        </xsl:if>
                        <a id="item-web-href" class="d-block mt-2" href="">
                            <span id="item-web-url"><xsl:value-of select="organization/homepage"/></span>
                        </a>
                        
                    </div>
                    <p class="col-12">
                        <xsl:value-of select="organization/description"/>
                    </p>
                    <div class="col-12 mb-4">
                        <xsl:if test="organization/permission/write">
                            <button class="btn btn-light" onclick="window.location.href='{organization/edit_link}'">
                                <xsl:value-of select="php:function('lang', 'edit')" />
                            </button>
                        </xsl:if>
                    </div>
                    <div class="building-accordion">
                        <div class="building-card">
                            <xsl:if test="organization/description and normalize-space(organization/description)">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'description')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseOne" class="btn fas fa-plus float-right"></button>
                                        
                                    </h5>
                                    
                                </div>

                                <div id="collapseOne" class="collapse">
                                    <div class="card-body">
                                        <p id="item-about"><xsl:value-of select="organization/description"/></p>
                                    </div>
                                </div>
                            </xsl:if>
                        </div>
                        <div class="building-card">
                            <xsl:if test="organization/email and normalize-space(organization/email) or
                            organization/phone and normalize-space(organization/phone)">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'contact person')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
                                    </h5>
                                </div>
                                <div id="collapseThree" class="collapse">
                                    <div class="card-body" >
                                        <div>
                                            <div class="d-block">
                                                <xsl:if test="organization/email and normalize-space(organization/email)">
                                                    <dt>
                                                        <xsl:value-of select="php:function('lang', 'Email')" />
                                                    </dt>
                                                    <dd>
                                                        <a href="mailto:{organization/email}">
                                                            <xsl:value-of select="organization/email"/>
                                                        </a>
                                                    </dd>
                                                </xsl:if>
                                            </div>
                                            <div class="d-block mb-2">
                                                <xsl:if test="organization/phone and normalize-space(organization/phone)">
                                                        <dt>
                                                            <xsl:value-of select="php:function('lang', 'Phone')" />
                                                        </dt>
                                                        <dd>
                                                            <xsl:value-of select="organization/phone"/>
                                                        </dd>
                                                    </xsl:if>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </xsl:if>    
                        </div>
                    </div>
                </div>
                    

            </div>
            
            <div class="col-lg-6 building-bookable">
                <h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'group')" /></h5>
                <div class="custom-card p-0 m-0" data-bind="visible: groups().length > 0">
                    <div data-bind="foreach: groups">
                        <div class="custom-subcard mb-0">
                            <span data-bind="text: name"></span>
                            <!--<div class="d-block">
                                <label class="font-weight-bold" data-bind="if: group_contact_person_name">Navn:&#160;</label>
                                <span data-bind="text: group_contact_person_name"></span>
                            </div>
                            <div class="d-block">
                                <label class="font-weight-bold" data-bind="if: group_contact_person_email">Email:&#160;</label>
                                <span data-bind="text: group_contact_person_email"></span>
                            </div>
                            <div class="d-block mb-2">
                                <label class="font-weight-bold" data-bind="if: group_contact_person_phone">Tel:&#160;</label>
                                <span data-bind="text: group_contact_person_phone" class="d-inline-block"></span>
                            </div>-->
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        </div>
        </div>
        <div class="push"></div>
    </div>

    <script type="text/javascript">
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/organization.js";
            document.head.appendChild(script);			
        </script>
</xsl:template>
