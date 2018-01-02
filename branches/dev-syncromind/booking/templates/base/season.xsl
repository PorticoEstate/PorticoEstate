<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
		<div id="tab-content">
			<input type="hidden" name="tab" value=""/>
			<xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
			<div id="season_show" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Case officer')" />
					</label>
					<xsl:value-of select="season/officer_name"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'From')" />
					</label>
					<xsl:value-of select="php:function('pretty_timestamp', season/from_)"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'To')" />
					</label>
					<xsl:value-of select="php:function('pretty_timestamp', season/to_)"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<xsl:value-of select="season/building_name"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Status')" />
					</label>
					<xsl:value-of select="season/status"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resources')" />
					</label>
					<div id="resources_container" class="custom-container"></div>
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
			<xsl:if test="season/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="edit">
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/edit_link"/>"</xsl:attribute>
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Edit')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="boundaries">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/boundaries_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Boundaries')" />
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="week_template">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/wtemplate_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Week template')" />
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cencel">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var season_id = <xsl:value-of select="season/id"/>;
		var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Account', 'Role', 'Actions', 'Edit', 'Delete', 'Resource Type')"/>;
	    <![CDATA[
            var resourcesURL    = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
            var permissionsURL   = 'index.php?menuaction=booking.uipermission_season.index&sort=name&filter_object_id=' + season_id + '&phpgw_return_as=json&';
	        ]]>
		var colDefsRespurces = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'type', label: lang['Resource Type']}
		];
		var colDefsPermissions = [
		{key: 'subject_name', label: lang['Account']},
		{key: 'role', label: lang['Role']},
		{key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label:lang['Edit']}, {name: 'delete', label:lang['Delete']})}
		];
		createTable('resources_container', resourcesURL, colDefsRespurces);
		createTable('permissions_container', permissionsURL, colDefsPermissions);
	</script>
</xsl:template>
