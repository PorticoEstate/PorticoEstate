<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:call-template name="msgbox"/>
	<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
			<div id="building_show" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<div class="custom-container">
						<xsl:value-of select="building/description" disable-output-escaping="yes"/>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<span>
						<xsl:value-of select="building/activity_name"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Street')" />
					</label>
					<xsl:value-of select="building/street"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Zip code')" />
					</label>
					<xsl:value-of select="building/zip_code"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Postal City')" />
					</label>
					<xsl:value-of select="building/city"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'District')" />
					</label>
					<xsl:value-of select="building/district"/>
				</div>
				<div class="pure-control-group">
					<xsl:if test="building/location_code !=''">
						<label>
							<xsl:value-of select="php:function('lang', 'Location Code')" />
						</label>
						<a href="{building/location_link}">
							<xsl:value-of select="building/location_code"/>
						</a>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Homepage')" />
					</label>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="building/homepage"/>
						</xsl:attribute>
						<xsl:value-of select="building/homepage"/>
					</a>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Email')" />
					</label>
					<a>
						<xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
						<xsl:value-of select="building/email"/>
					</a>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Telephone')" />
					</label>
					<xsl:value-of select="building/phone"/>
				</div>
				<xsl:if test="building/tilsyn_name != ''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
						</label>
						<xsl:value-of select="building/tilsyn_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
						</label>
						<a>
							<xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email"/></xsl:attribute>
							<xsl:value-of select="building/tilsyn_email"/>
						</a>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
						</label>
						<xsl:value-of select="building/tilsyn_phone"/>
					</div>
				</xsl:if>
				<xsl:if test="building/tilsyn_name2 != ''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
						</label>
						<xsl:value-of select="building/tilsyn_name2"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
						</label>
						<a>
							<xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email2"/></xsl:attribute>
							<xsl:value-of select="building/tilsyn_email2"/>
						</a>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
						</label>
						<xsl:value-of select="building/tilsyn_phone2"/>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Bookable resources')" />
					</label>
					<div id="resources_container" class="custom-container"></div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Documents')" />
					</label>
					<div class="pure-custom">
						<div id="documents_container" class="custom-container"></div>
						<div>
							<a class='button'>
								<xsl:attribute name="href">
									<xsl:value-of select="building/add_document_link"/>
								</xsl:attribute>
								<xsl:if test="building/permission/write">
									<xsl:value-of select="php:function('lang', 'Add Document')" />
								</xsl:if>
							</a>
						</div>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Permissions')" />
					</label>
					<div id="permissions_container" class="custom-container"></div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="building/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="edit">
					<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/edit_link"/>"</xsl:attribute>
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Edit')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="schedule">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/schedule_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Building schedule')" />
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var building_id = <xsl:value-of select="building/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order')"/>;
	
    <![CDATA[
        var resourcesURL     = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
        var documentsURL     = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
        var permissionsURL   = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
        ]]>
		var colDefsResources = [{key: 'sort', label: lang['Sort order']},{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}];
		var colDefsDocuments = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'category', label: lang['Category']}, {key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label:lang['Edit']}, {name: 'delete', label:lang['Delete']})}];
		var colDefsPermissions = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label: 'Edit'}, {name: 'delete', label:lang['Delete']})}];

		createTable('resources_container',resourcesURL,colDefsResources);
		createTable('documents_container',documentsURL,colDefsDocuments);
		createTable('permissions_container',permissionsURL,colDefsPermissions);
	</script>
</xsl:template>
