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
                <span><xsl:value-of select="php:function('lang', 'organization')" /></span>
            </div>

            <div class="row">
            <div class="col-lg-6">
                
                <div class="row">
                    
                        <div class="col-sm-4 d-none d-sm-block col-item-img">
                            <img class="img-fluid rounded" id="item-main-picture" src=""/>
                        </div>
                        <div class="col-sm-8 mb-5">
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
                                        <xsl:value-of select="organization/description"/>
                                    </div>
                                </div>
                            </xsl:if>
                        </div>                

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

                        <div class="building-card">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseBuildingsUsedBy" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'Used buildings')" />
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

                        <div class="building-card">
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
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseDocuments" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'documents')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseDocuments" class="btn fas fa-plus float-right"></button>
                                        
                                    </h5>
                                    
                                </div>

                                <div id="collapseDocuments" class="collapse">
                                    <div class="card-body">
                                        <div id="documents_container"/>
                                    </div>
                                </div>
                        </div>                        
                        

                        <div class="building-card">
                            <xsl:if test="organization/email and normalize-space(organization/email) or
                            organization/phone and normalize-space(organization/phone)">
                                <div class="building-card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseContacts" aria-expanded="false">
                                            <xsl:value-of select="php:function('lang', 'contact person')" />
                                        </button>
                                        <button data-toggle="collapse" data-target="#collapseContacts" class="btn fas fa-plus float-right"></button>
                                    </h5>
                                </div>
                                <div id="collapseContacts" class="collapse">
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
                <div class="custom-card p-0 m-0 mb-2" data-bind="visible: groups().length > 0">
                    <div data-bind="foreach: groups">
                        <div class="custom-subcard mb-0">
                            <a class="group_link"><span data-bind="text: name"></span></a>
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
                <xsl:if test="organization/permission/write">
                    <a href="{organization/new_group_link}">
                        <xsl:value-of select="php:function('lang', 'new group')" />
                    </a>
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
            var documentURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_organization.index', sort:'name', no_images:1, filter_owner_id:organization_id}, true);
            var document_organizationURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_organization.index_images', sort:'name', filter_owner_id:organization_id}, true);
            ]]>
                    
            var rBuilding = [{n: 'ResultSet'},{n: 'Result'}];

            var colDefsDelegate = [
            {key: 'name', label: lang['Name'], formatter: genericLink},
            {key: 'email', label: lang['email']}
            ];

            var colDefsBuilding = [{key: 'name', label: lang['Name'], formatter: genericLink}];
            var colDefsDocument = [{key: 'description', label: lang['Name'], formatter: genericLink}];
                
            createTable('delegates_container', delegateURL, colDefsDelegate, '', 'table table-hover');
            createTable('buildings_used_by_container', buildingURL, colDefsBuilding, rBuilding, 'table table-hover');
    
            createTable('documents_container', documentURL, colDefsDocument, '', 'table table-hover');
            $(window).on('load', function(){
            // Load image
            //JqueryPortico.booking.inlineImages('images_container', document_organizationURL);
            });			
        </script>
</xsl:template>
