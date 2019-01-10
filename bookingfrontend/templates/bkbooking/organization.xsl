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
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li>
				<xsl:value-of select="organization/name"/>
			</li>
		</ul>

		<xsl:if test="organization/permission/write">
			<span class="loggedin">
				<button onclick="window.location.href='{organization/edit_link}'">
					<xsl:value-of select="php:function('lang', 'edit')" />
				</button>
			</span>
		</xsl:if>
		
		<div class="pure-g">
			<div class="pure-u-1 pure-u-md-1-2">
				<xsl:if test="organization/description and normalize-space(organization/description)">
					<dl class="proplist description">
						<dt>
							<xsl:value-of select="php:function('lang', 'Description')" />
						</dt>
						<dd>
							<xsl:value-of select="organization/description" disable-output-escaping="yes"/>
						</dd>
					</dl>
				</xsl:if>

				<h3>
					<xsl:value-of select="php:function('lang', 'Contact information')" />
				</h3>
				<dl class="proplist contactinfo">
	
					<xsl:if test="organization/homepage and normalize-space(organization/homepage)">
						<dt>
							<xsl:value-of select="php:function('lang', 'Homepage')" />
						</dt>
						<dd>
							<a target="blank" href="{organization/homepage}">
								<xsl:value-of select="organization/homepage" />
							</a>
						</dd>
					</xsl:if>
			
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

					<xsl:if test="organization/phone and normalize-space(organization/phone)">
						<dt>
							<xsl:value-of select="php:function('lang', 'Phone')" />
						</dt>
						<dd>
							<xsl:value-of select="organization/phone"/>
						</dd>
					</xsl:if>

					<xsl:if test="organization/street and normalize-space(organization/street)">
						<dt>
							<xsl:value-of select="php:function('lang', 'Address')" />
						</dt>
						<dd>
							<xsl:value-of select="organization/street"/>
							<br/>
							<xsl:value-of select="organization/zip_code"/>
							<span>&nbsp; </span>
							<xsl:value-of select="organization/city"/>
							<br/>
							<xsl:value-of select="organization/district"/>
						</dd>
					</xsl:if>

				</dl>

				<h3>
					<xsl:value-of select="php:function('lang', 'Groups')" />
				</h3>
				<div id="groups_container"/>

				<a href="{organization/new_group_link}">
					<xsl:value-of select="php:function('lang', 'new group')" />
				</a>

				<h3>
					<xsl:value-of select="php:function('lang', 'delegates')" />
				</h3>
				<div id="delegates_container"/>

				<a href="{organization/new_delegate_link}">
					<xsl:value-of select="php:function('lang', 'new delegate')" />
				</a>

				<h3>
					<xsl:value-of select="php:function('lang', 'Used buildings')" />
				</h3>
				<div id="buildings_used_by_container"/>
				<h3>
					<xsl:value-of select="php:function('lang', 'Documents')" />
				</h3>
				<div id="documents_container"/>
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
	</div>
	<script type="text/javascript">
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
                
		var colDefsGroup = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'link', attrs: [{name: 'hidden'}]},
		{key: 'activity_name', label: lang['Activity']},
		{key: 'primary_contact_name', label: lang['Contact 1']},
		{key: 'secondary_contact_name', label: lang['Contact 2']}
		];

		var colDefsDelegate = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'email', label: lang['email']},
		{key: 'phone', label: lang['phone']},
		{key: 'active', label: lang['active']}
		];

		var colDefsBuilding = [{key: 'name', label: lang['Name'], formatter: genericLink}];
		var colDefsDocument = [{key: 'description', label: lang['Name'], formatter: genericLink}];
              
		createTable('groups_container', groupURL, colDefsGroup, '', 'pure-table pure-table-bordered');
		createTable('delegates_container', delegateURL, colDefsDelegate, '', 'pure-table pure-table-bordered');
		createTable('buildings_used_by_container', buildingURL, colDefsBuilding, rBuilding, 'pure-table pure-table-bordered');

		createTable('documents_container', documentURL, colDefsDocument, '', 'pure-table pure-table-bordered');
		$(window).on('load', function(){
		// Load image
		JqueryPortico.booking.inlineImages('images_container', document_organizationURL);
		});
			
	</script>

</xsl:template>



