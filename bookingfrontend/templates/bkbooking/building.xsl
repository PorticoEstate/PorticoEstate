<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

	<div class="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Home')" />
				</a>
			</li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="building/name"/>
				</a>
			</li>
		</ul>

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
									- Meldig til saksbehandler for bygg
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

				createTable('resources_container', resourcesURL, colDefsResources, rResources, 'pure-table pure-table-bordered');
				createTable('documents_container', documentURL, colDefsDocument, '', 'pure-table pure-table-bordered');
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
</xsl:template>
