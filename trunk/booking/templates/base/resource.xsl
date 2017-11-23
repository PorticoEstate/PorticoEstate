<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:call-template name="msgbox"/>
	<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
		<input type="hidden" name="tab" value="" />
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
			<div id="resource" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<div class="custom-container">
						<xsl:value-of select="resource/name" disable-output-escaping="yes"/>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<div class="custom-container">
						<xsl:value-of select="resource/description" disable-output-escaping="yes"/>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Building')"/>
					</label>
					<div class = 'pure-u-md-1-2'>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl'/>
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
									<xsl:with-param name="data" select ='data'/>
									<xsl:with-param name="config" select ='config'/>
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<span>
						<xsl:value-of select="resource/activity_name"/>
					</span>
					<script type="text/javascript">
						var default_schema = "<xsl:value-of select="resource/activity_name"/>";
						var schema_type = "view";
					</script>

				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resource Type')" />
					</label>
					<span>
						<xsl:value-of select="php:function('lang', string(resource/type))"/>
					</span>
				</div>
				<input type= "hidden" id="field_activity_id" value="{resource/activity_id}"/>
				<div id="custom_fields"></div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Documents')" />
					</label>
					<div class="pure-custom">
						<div id="documents_container" class="custom-container"></div>
						<div>
							<a class='button'>
								<xsl:attribute name="href">
									<xsl:value-of select="resource/add_document_link"/>
								</xsl:attribute>
								<xsl:if test="resource/permission/write">
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
			<xsl:if test="resource/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="edit">
					<xsl:attribute name="onclick">window.location.href='<xsl:value-of select="resource/edit_link"/>'</xsl:attribute>
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Edit')" />
					</xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="resource_schedule">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="resource/schedule_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Resource schedule')" />
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="resource/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role', 'No records found')"/>;

    <![CDATA[
        var documentsURL = 'index.php?menuaction=booking.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
        var permissionsURL = 'index.php?menuaction=booking.uipermission_resource.index&sort=name&filter_object_id=' + resource_id + '&phpgw_return_as=json&';
        ]]>

		var colDefsDocuments = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'category', label: lang['Category']},
		{key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label:lang['Edit']}, {name: 'delete', label:lang['Delete']})}
		];
		var colDefsPermissions = [
		{key: 'subject_name', label: lang['Account']},
		{key: 'role', label: lang['Role']},
		{key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label:lang['Edit']}, {name: 'delete', label:lang['Delete']})}
		];

		createTable('documents_container',documentsURL,colDefsDocuments);
		createTable('permissions_container',permissionsURL,colDefsPermissions);
	</script>
</xsl:template>
