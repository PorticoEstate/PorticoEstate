<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:call-template name="msgbox"/>
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="datetime_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		<xsl:text> H:i</xsl:text>
	</xsl:variable>

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
						<xsl:value-of select="php:function('lang', 'Opening hours')" />
					</label>
					<div class="custom-container">
						<xsl:value-of select="resource/opening_hours" disable-output-escaping="yes"/>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Contact information')" />
					</label>
					<div class="custom-container">
						<xsl:value-of select="resource/contact_info" disable-output-escaping="yes"/>
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
									<xsl:with-param name="class" select="'table table-striped table-bordered'" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
				<div class="pure-control-group">
					<label for="for_field_deactivate_application">
						<xsl:value-of select="php:function('lang', 'Deactivate application')"/>
					</label>
					<xsl:if test="resource/deactivate_application=1">
						<xsl:value-of select="php:function('lang', 'Yes')"/>
					</xsl:if>
					<xsl:if test="resource/deactivate_application=0">
						<xsl:value-of select="php:function('lang', 'No')"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label for="for_field_hidden_in_frontend">
						<xsl:value-of select="php:function('lang', 'hidden in frontend')"/>
					</label>
					<xsl:if test="resource/hidden_in_frontend=1">
						<xsl:value-of select="php:function('lang', 'Yes')"/>
					</xsl:if>
					<xsl:if test="resource/hidden_in_frontend=0">
						<xsl:value-of select="php:function('lang', 'No')"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label for="for_field_activate_prepayment">
						<xsl:value-of select="php:function('lang', 'activate prepayment')"/>
					</label>
					<xsl:if test="resource/activate_prepayment=1">
						<xsl:value-of select="php:function('lang', 'Yes')"/>
					</xsl:if>
					<xsl:if test="resource/activate_prepayment=0">
						<xsl:value-of select="php:function('lang', 'No')"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label for="for_field_deny_application_if_booked">
						<xsl:value-of select="php:function('lang', 'deny application if booked')"/>
					</label>
					<xsl:if test="resource/deny_application_if_booked=1">
						<xsl:value-of select="php:function('lang', 'Yes')"/>
					</xsl:if>
					<xsl:if test="resource/deny_application_if_booked=0">
						<xsl:value-of select="php:function('lang', 'No')"/>
					</xsl:if>
				</div>

				<div class="pure-control-group custom-container">
					<label>
						<xsl:value-of select="php:function('lang', 'seasons')"/>
					</label>

					<div class="pure-u-md-1-2">
						<table class="table table-striped table-bordered dataTable" style="white-space: nowrap;">
							<thead>
								<tr>
									<th>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'name')"/>
									</th>
								</tr>
							</thead>
							<xsl:for-each select="seasons">
								<tr>
									<td>
										<xsl:value-of select="id" />
									</td>
									<td>
										<xsl:value-of select="name" />
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resource category')"/>
					</label>
					<span>
						<xsl:value-of select="resource/rescategory_name"/>
					</span>
				</div>
				<input type= "hidden" id="field_activity_id" value="{resource/activity_id}"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Main activity')" />
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
						<xsl:value-of select="php:function('lang', 'Activities')"/>
					</label>
					<span>
						<xsl:value-of select="resource/activities_names"/>
					</span>
				</div>
				<xsl:if test="resource/permission/write">
					<div class="pure-control-group">
						<label></label>
						<span>
							<a class='button'>
								<xsl:attribute name="href">
									<xsl:value-of select="resource/edit_activities_link"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'Edit activities')" />
							</a>
						</span>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Facilities')"/>
					</label>
					<span>
						<xsl:value-of select="resource/facilities_names"/>
					</span>
				</div>
				<xsl:if test="resource/permission/write">
					<div class="pure-control-group">
						<label></label>
						<span>
							<a class='button'>
								<xsl:attribute name="href">
									<xsl:value-of select="resource/edit_facilities_link"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'Edit facilities')" />
							</a>
						</span>
					</div>
				</xsl:if>
				<xsl:if test="resource/rescategory_capacity = 1">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'capacity')"/>
						</label>
						<xsl:value-of select="resource/capacity"/>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Direct booking')"/>
					</label>
					<xsl:if test="not(resource/direct_booking = '')">
						<xsl:value-of select="php:function('date', $date_format, number(resource/direct_booking))"/>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Simple booking')"/>
					</label>
					<xsl:if test="number(resource/simple_booking) = 1">
						<xsl:text>X</xsl:text>
					</xsl:if>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'start date')"/>
					</label>
					<xsl:if test="not(resource/simple_booking_start_date = '')">
						<xsl:value-of select="php:function('date', $datetime_format, number(resource/simple_booking_start_date))"/>
					</xsl:if>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'end date')"/>
					</label>
					<xsl:if test="not(resource/simple_booking_end_date = '')">
						<xsl:value-of select="php:function('date', $date_format, number(resource/simple_booking_end_date))"/>
					</xsl:if>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'day horizon')"/>
					</label>
					<xsl:value-of select="resource/booking_day_horizon"/>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'month horizon')"/>
					</label>
					<xsl:value-of select="resource/booking_month_horizon"/>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'day default lenght')"/>
					</label>
					<xsl:value-of select="resource/booking_day_default_lenght"/>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'dow default start')"/>
					</label>
					<xsl:value-of select="resource/booking_dow_default_start"/>
				</div>

				<!--				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'dow default end')"/>
					</label>
					<xsl:value-of select="resource/booking_dow_default_end"/>
				</div>-->

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'time default start')"/>
					</label>
					<xsl:value-of select="resource/booking_time_default_start"/>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'time default end')"/>
					</label>
					<xsl:value-of select="resource/booking_time_default_end"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'minutes')"/>
					</label>
					<xsl:value-of select="resource/booking_time_minutes"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'buffer deadline')"/>
							&nbsp;
						(<xsl:value-of select="php:function('lang', 'minutes')"/>)
					</label>
					<xsl:value-of select="resource/booking_buffer_deadline"/>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'limit number')"/>
					</label>
					<xsl:value-of select="resource/booking_limit_number"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'limit number horizont')"/>
					</label>
					<xsl:value-of select="resource/booking_limit_number_horizont"/>
				</div>

				<xsl:if test="resource/rescategory_e_lock = 1">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Electronic lock')"/>
						</label>
						<div class = 'pure-u-md-1-2'>
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_1'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl'/>
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
										<xsl:with-param name="data" select ='data'/>
										<xsl:with-param name="config" select ='config'/>
										<xsl:with-param name="class" select="'table table-striped table-bordered'" />
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>

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

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'participant limit')"/>
					</label>
					<div class = 'pure-u-md-1-2'>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_2'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl'/>
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
									<xsl:with-param name="data" select ='data'/>
									<xsl:with-param name="config" select ='config'/>
									<xsl:with-param name="class" select="'table table-striped table-bordered'" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
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

		var documentsURL = phpGWLink('/index.php', {menuaction:'booking.uidocument_resource.index', sort:'name', filter_owner_id:resource_id, length:-1}, true);
		var permissionsURL = phpGWLink('/index.php', {menuaction: 'booking.uipermission_resource.index', sort:'name', filter_object_id :resource_id, length:-1}, true);

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

		createTable('documents_container',documentsURL,colDefsDocuments, '', 'pure-table pure-table-bordered');
		createTable('permissions_container',permissionsURL,colDefsPermissions, '', 'pure-table pure-table-bordered');
	</script>
</xsl:template>
