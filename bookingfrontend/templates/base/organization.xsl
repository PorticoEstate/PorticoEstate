<xsl:template match="data" xmlns:php="http://php.net/xsl">
   <div id="organization-page-content">  
    <div class="info-content pb-5">
        <div class="container wrapper">
            <div class="location">
                <span><a>
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                        </xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Home')" />
                    </a>
                </span>
            </div>

            <div class="row">
            <div class="col-lg-6">
                
                <div class="row">
                    
                        <div class="col-xl-4 col-lg-5 col-md-4 col-sm-4 mb-4 col-item-img">
                            <img class="img-fluid image-circle" id="item-main-picture" src=""/>
                        </div>
                        <div class="col-xl-6 col-lg-7 col-md-8 col-sm-8 col-xs-12 mb-4">
                            <h3 id="main-item-header"><xsl:value-of select="organization/name"/></h3>
                            <xsl:if test="organization/street and normalize-space(organization/street)">
                                <i class="fas fa-map-marker d-inline"> </i>
                                <div class="building-place-adr">
                                    <span id="item-street"><xsl:value-of select="organization/street"/></span>
                                    <span class="d-block" id="item-zip-city"><xsl:value-of select="organization/zip_code"/>&#160;<xsl:value-of select="organization/city"/></span>
                            </div>
                            </xsl:if>
                            <xsl:if test="organization/permission/write">
                                <button class="btn btn-light" onclick="window.location.href='{organization/edit_link}'">
                                    <xsl:value-of select="php:function('lang', 'edit')" />
                                </button>
                            </xsl:if>
                        </div>

                        <div class="col-12 ml-1">
                            <xsl:if test="organization/description and normalize-space(organization/description)">
                                <xsl:value-of disable-output-escaping="yes" select="organization/description"/>
                            </xsl:if>
                        </div>
                                           
                    <div class="building-accordion mt-4">
						<xsl:if test="organization/logged_on">
                        <div class="building-card">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseDelegates" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'delegates')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseDelegates" class="btn fas fa-plus float-right"></button>
                                        
                                    </h5>
                                    
                                </div>

                                <div id="collapseDelegates" class="collapse">
                                    <div class="card-body">
                                        <div id="delegates_container"/>                                        
                                        <xsl:if test="organization/permission/write">
                                            <a href="{organization/new_delegate_link}">
                                                <xsl:value-of select="php:function('lang', 'new delegate')" />
                                            </a>
                                        </xsl:if>
                                    </div>
                                </div>
                        </div>
								</xsl:if>

                        <div class="building-card">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseBuildingsUsedBy" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'Used buildings (2018)')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseBuildingsUsedBy" class="btn fas fa-plus float-right"></button>
                                        
                                    </h5>
                                    
                                </div>

                                <div id="collapseBuildingsUsedBy" class="collapse">
                                    <div class="card-body">
                                        <div id="buildings_used_by_container"/>
                                    </div>
                                </div>
                        </div>

							<div class="building-card card-img-thumbs">
							<div class="building-card-header">
								<h5 class="mb-0">
									<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsePicture" aria-expanded="false">
										<xsl:value-of select="php:function('lang', 'picture')" />
									</button>
									<button data-toggle="collapse" data-target="#collapsePicture" class="btn fas fa-plus float-right"></button>
								</h5>
							</div>
							<div id="collapsePicture" class="collapse">
								<div class="card-body organization-images" id="list-img-thumbs">
								</div>
							</div>
							</div>

                        <div class="building-card">
									<xsl:if test="organization/contact_info and normalize-space(organization/contact_info)">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseContacts" aria-expanded="false">
													<xsl:value-of select="php:function('lang', 'Contact information')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseContacts" class="btn fas fa-plus float-right"></button>
                                    </h5>
                                </div>
                                <div id="collapseContacts" class="collapse">
                                    <div class="card-body" >
												<xsl:value-of disable-output-escaping="yes" select="organization/contact_info"/>
                                    </div>
                                </div>
									</xsl:if>
                        </div>
                    </div>
                </div>
                    

            </div>
            
            <div class="col-lg-6 building-bookable">
						<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Groups (2018)')" /></h5>
                <div class="custom-card p-0 m-0 mb-2" data-bind="visible: groups().length > 0">
                    <div data-bind="foreach: groups">
                        <div class="custom-subcard mb-0">
									<xsl:if test="organization/permission/write">
									    <a class="group_link"><span data-bind="text: name"></span></a>
									</xsl:if>
									<xsl:if test="not(organization/permission/write)">
										<span data-bind="text: name"></span>
									</xsl:if>
                        </div>
                    </div>
                </div>
                <xsl:if test="organization/permission/write">
                    <a href="{organization/new_group_link}">
                        <xsl:value-of select="php:function('lang', 'new group')" />
                    </a>
							<xsl:if test="config_data/help_group_edit and normalize-space(config_data/help_group_edit)">
								<div class="margin-top-and-bottom">
									<xsl:value-of select="config_data/help_group_edit"/>
								</div>
							</xsl:if>
                </xsl:if>
            </div>
        </div>
        </div>
        </div>
        <div class="push"></div>

        <div id="lightbox" class="modal hide" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-body lightbox-body">
                    <a href="#" class="close">&#215;</a>
                    <img src="" alt="" />
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
            JqueryPortico.booking = {};
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/organization.js";
            document.head.appendChild(script);

            var organization_id = <xsl:value-of select="organization/id"/>;
            var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Activity', 'Contact 1', 'Contact 2', 'email','phone', 'active')"/>;
        
            <![CDATA[
            var groupURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uigroup.index', sort:'name', filter_organization_id: organization_id}, true);
            var delegateURL =  phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidelegate.index', sort: 'name', filter_organization_id: organization_id, filter_active:'-1'},true);
            var buildingURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uibuilding.find_buildings_used_by', sort:'name', organization_id: organization_id}, true);
            var document_organizationURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_organization.index_images', sort:'name', filter_owner_id:organization_id}, true);
            ]]>
                    
            var rBuilding = [{n: 'ResultSet'},{n: 'Result'}];

            var colDefsDelegate = [
            {key: 'name', label: lang['Name'], formatter: genericLink},
            {key: 'email', label: lang['email']}
            ];

            var colDefsBuilding = [{key: 'name', label: lang['Name'], formatter: genericLink}];
                
            createTable('delegates_container', delegateURL, colDefsDelegate, '', 'table table-hover');
            createTable('buildings_used_by_container', buildingURL, colDefsBuilding, rBuilding, 'table table-hover');
    
            $(window).on('load', function(){
            // Load image
            //JqueryPortico.booking.inlineImages('images_container', document_organizationURL);
            });			
        </script>
</xsl:template>
