<xsl:template match="data" xmlns:php="http://php.net/xsl">


<<<<<<< HEAD
    <div class="container my-container-top-fix wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a>
                    <xsl:attribute name="href">
                        <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                    </xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Home')" />
                </a>
            </li>
            <li class="breadcrumb-item active">
                <xsl:value-of select="php:function('lang', 'building')" />
            </li>       
        </ol>
        <div class="row p-3">
            <div class="col-lg-6">
                
                <div class="row">
                    <div class="col-sm-4 d-none d-sm-block col-item-img">
                        <img class="img-fluid rounded" id="item-main-picture" src=""/>
                    </div>
                    <div class="col-sm-8 col-xs-12 building-place-info">
                        <h3 id="main-item-header"></h3>
                        <i class="fas fa-map-marker d-inline"> </i>
                        <div class="building-place-adr">
                            <span id="item-street"></span>
                            <span id="item-zip-city"></span>
                        </div>
                    </div>
                    
                    <p class="px-2 p-3" id="item-description">
                    </p>
                    
                    <div class="building-accordion">
                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">
                                        Anlegget inneholder
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseOne" class="btn fas fa-plus float-right"></button>
                                    
                                </h5>
                                
                            </div>

                            <div id="collapseOne" class="collapse">
                                <div class="card-body">
                                    <p id="item-contains"></p>
                                </div>
                            </div>
                        </div>
                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false">
                                        Bilder
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseTwo" class="btn fas fa-plus float-right"></button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse">
                                <div class="card-body building-images" id="list-img-thumbs">
                                </div>
                            </div>
                        </div>
                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
                                        Åpningstider
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse">
                                <div class="card-body">
                                    <span id="opening_hours"></span>
                                </div>
                            </div>
                        </div>

                        <div class="building-card">
                            <div class="building-card-header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false">
                                        Kontaktinformasjon
                                    </button>
                                    <button data-toggle="collapse" data-target="#collapseFour" class="btn fas fa-plus float-right"></button>
                                </h5>
                            </div>
                            <div id="collapseFour" class="collapse">
                                <div class="card-body">
                                    <div id="contact_info"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    

            </div>
            
            <div class="col-lg-6 building-bookable">
                <h3 class="">
                    <xsl:value-of select="php:function('lang', 'Bookable resources')" />
                </h3>
                <div data-bind="foreach: bookableResource">
                    <div class="custom-card">
                        <a class="bookable-resource-link-href" href="" data-bind=""><h2 data-bind="text: name"></h2></a>
                        <span class="font-weight-bold">Fasiliteter: </span>
                        <span>Bla bla, </span>
                        <span>Bla bla</span>
                    </div>
                </div>
                
            </div>
        </div>
        
        
        <div class="row margin-top-and-bottom">
            
            <div class="button-group dropdown calendar-tool invisible">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Velg lokaler 
                    <span class="caret"></span>
                </button>
                
                <ul class="dropdown-menu px-2" data-bind="foreach: bookableResource">
                    <li>
                        <div class="form-check checkbox checkbox-primary">
                            
                            <label class="check-box-label">
                                <input class="form-check-input choosenResource" type="checkbox"  checked="checked" data-bind="text: name"/>
                                <span class="label-text" data-bind="text: name"></span>
                            </label>
                        </div>
                    </li>
                </ul>
                
                <button class="btn btn-default datepicker-btn mr-1 mt-1 mb-1">
                    <i class="far fa-calendar-alt"></i> Velg dato</button>
                
                <button class="btn btn-default" id="newApplicationBtn">
                    <i class="fas fa-plus"></i>
                    <xsl:value-of select="php:function('lang', 'new booking application')" />
                </button>
            </div>
            
            
                
            <!--<div class="input-group date" id="datepicker" data-provide="datepicker">
                <input type="text" class="form-control" />
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>-->
                

            
            <div id="myScheduler" class="d-none d-lg-block margin-top-and-bottom"></div>

            <div id="mySchedulerSmallDeviceView" class="d-lg-none margin-top-and-bottom"></div>

        </div>
        
        
        <div class="push"></div>
    </div>
        
        
    <div id="lightbox" class="modal hide" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-body lightbox-body">
                <a href="#" class="close">&#215;</a>
                <img src="" alt="" />
            </div>
        </div>
    </div>
        

    <script type="text/javascript">
        var script = document.createElement("script"); 
		script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/building.js";

        document.head.appendChild(script);			
    </script>
=======
		<xsl:for-each select="building">
			<xsl:if test="deactivate_calendar=0">
				<div>
					<button onclick="window.location.href='{schedule_link}'">
						<xsl:value-of select="php:function('lang', 'Building schedule')" />
					</button>
					- Søk ledig tid/informasjon om hva som skjer
				</div>
			</xsl:if>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2">
					<dl class="proplist-col main">
						<xsl:if test="normalize-space(description)">
							<dl class="proplist description">
								<dt>
									<xsl:value-of select="php:function('lang', 'Description')" />
								</dt>
								<dd>
									<xsl:value-of select="description" disable-output-escaping="yes"/>
								</dd>
							</dl>
						</xsl:if>

						<xsl:if test="normalize-space(homepage) or normalize-space(email) or normalize-space(phone) or normalize-space(street)">
							<h3>
								<xsl:value-of select="php:function('lang', 'Contact information')" />
							</h3>
							<xsl:if test="deactivate_sendmessage=0">
								<div>
									<button onclick="window.location.href='{message_link}'">
										<xsl:value-of select="php:function('lang', 'Send message')" />
									</button>
									- Melding til saksbehandler for bygg
								</div>
							</xsl:if>

							<dl class="contactinfo">
								<xsl:if test="homepage and normalize-space(homepage)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Homepage')" />
									</dt>
									<dd>
										<a href="{homepage}">
											<xsl:value-of select="homepage"/>
										</a>
									</dd>
								</xsl:if>

								<xsl:if test="email and normalize-space(email)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Email')" />
									</dt>
									<dd>
										<a href='mailto:{email}'>
											<xsl:value-of select="email"/>
										</a>
									</dd>
								</xsl:if>

								<xsl:if test="phone and normalize-space(phone)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Telephone')" />
									</dt>
									<dd>
										<xsl:value-of select="phone"/>
									</dd>
								</xsl:if>

								<xsl:if test="street and normalize-space(street)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Address')" />
									</dt>
									<dd>
										<xsl:value-of select="street"/>
										<br/>
										<xsl:value-of select="zip_code"/>
										<span>&nbsp; </span>
										<xsl:value-of select="city"/>
										<br/>
										<xsl:value-of select="district"/>
									</dd>
								</xsl:if>
								<xsl:if test="tilsyn_name and normalize-space(tilsyn_name)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
									</dt>
									<dd>
										<xsl:value-of select="tilsyn_name"/>
									</dd>
								</xsl:if>

								<xsl:if test="tilsyn_email and normalize-space(tilsyn_email)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
									</dt>
									<dd>
										<a href='mailto:{tilsyn_email}'>
											<xsl:value-of select="tilsyn_email"/>
										</a>
									</dd>
								</xsl:if>

								<xsl:if test="tilsyn_phone and normalize-space(tilsyn_phone)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
									</dt>
									<dd>
										<xsl:value-of select="tilsyn_phone"/>
									</dd>
								</xsl:if>

								<xsl:if test="tilsyn_name and normalize-space(tilsyn_name2)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
									</dt>
									<dd>
										<xsl:value-of select="tilsyn_name2"/>
									</dd>
								</xsl:if>

								<xsl:if test="tilsyn_email and normalize-space(tilsyn_email2)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
									</dt>
									<dd>
										<a href='mailto:{tilsyn_email2}'>
											<xsl:value-of select="tilsyn_email2"/>
										</a>
									</dd>
								</xsl:if>

								<xsl:if test="tilsyn_phone and normalize-space(tilsyn_phone2)">
									<dt>
										<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
									</dt>
									<dd>
										<xsl:value-of select="tilsyn_phone2"/>
									</dd>
								</xsl:if>

							</dl>
						</xsl:if>

						<h3>
							<xsl:value-of select="php:function('lang', 'Bookable resources')" />
						</h3>
						<div id="resources_container"/>

						<h3>
							<xsl:value-of select="php:function('lang', 'Building users')" />
						</h3>
						<div id="building_users_container"/>

						<h3>
							<xsl:value-of select="php:function('lang', 'Documents')" />
						</h3>
						<div id="documents_container"/>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-lg-1-2">
					<dl class="proplist-col images">
						<div id="images_container"></div>
					</dl>
					<dl class="proplist-col images map">
						<!--div id="images_container"></div-->
						<xsl:if test="street and normalize-space(street)">
							<div class="gmap-container">
								<iframe width="500" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="googlemapiframe" src=""></iframe>
							</div>
							<small>
								<a href="" id="googlemaplink" style="color:#0000FF;text-align:left" target="_new">Vis større kart</a>
							</small>
						</xsl:if>
					</dl>
				</div>
			</div>
			<script type="text/javascript">
				var building_id = <xsl:value-of select="id"/>;
				var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Activity', 'Resource Type')"/>;
				var address = '<xsl:value-of select="street"/>, <xsl:value-of select="zip_code"/>, <xsl:value-of select="city"/>';
				<![CDATA[
                var resourcesURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uiresource.index_json',sort:'name', filter_building_id:building_id}, true);
                var documentURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_building.index', sort:'name', no_images:1, filter_owner_id:building_id}, true);
                var building_usersURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uiorganization.building_users', sort:'name', building_id:building_id}, true);
                var document_buildingURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_building.index_images', sort:'name', filter_owner_id:building_id}, true);
				var iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + address;
				var linkurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&geocode=&q=' + address;
                ]]>

				var rResources = 'results';
				var rBuilding_users = [{n: 'ResultSet'},{n: 'Result'}];

				var colDefsResources = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}, {key: 'activity_name', label: lang['Activity']}];
				var colDefsDocument = [{key: 'description', label: lang['Name'], formatter: genericLink}];
				var colDefsBuilding_users = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'activity_name', label: lang['Activity']}];

				var paginatorTableBuilding_users = new Array();
				paginatorTableBuilding_users.limit = 10;
				createPaginatorTable('building_users_container', paginatorTableBuilding_users);

				createTable('resources_container', resourcesURL, colDefsResources, rResources);
				createTable('documents_container', documentURL, colDefsDocument);
				createTable('building_users_container', building_usersURL, colDefsBuilding_users, rBuilding_users, '', paginatorTableBuilding_users);

				$(window).on('load', function(){
				// Load image
				JqueryPortico.booking.inlineImages('images_container', document_buildingURL);

				// Load Google map
				if( iurl.length > 0 ) {
				$("#googlemapiframe").attr("src", iurl);
				$("#googlemaplink").attr("href", linkurl);
				}
				});

			</script>
		</xsl:for-each>
	</div>
>>>>>>> master
</xsl:template>
