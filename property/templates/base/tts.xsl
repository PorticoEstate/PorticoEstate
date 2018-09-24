
<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="add">
			<xsl:apply-templates select="add"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- add -->
<xsl:template xmlns:php="http://php.net/xsl" match="add">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
		var my_groups = <xsl:value-of select="my_groups"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Please select a person or a group to handle the ticket !')"/>;
	</script>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned"  ENCTYPE="multipart/form-data" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="add">
				<fieldset>
					<xsl:for-each select="value_origin">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="descr"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="data">
									<div>
										<xsl:variable name="link_request">
											<xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/>
										</xsl:variable>
										<a href="{link}" title="{//lang_origin_statustext}">
											<xsl:value-of select="id"/>
										</a>
										<xsl:text> </xsl:text>
									</div>
								</xsl:for-each>
							</div>
						</div>
					</xsl:for-each>
					<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
					<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
					<xsl:call-template name="location_form2"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:call-template name="categories"/>
					</div>
					<xsl:choose>
						<xsl:when test="simple !='1'">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Group')"/>
								</label>

								<xsl:call-template name="group_select"/>
							</div>
							<xsl:choose>
								<xsl:when test="disable_userassign_on_add !='1'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Assign to')"/>
										</label>

										<xsl:call-template name="user_id_select"/>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:call-template name="contact_form"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Send e-mail')"/>
								</label>
								<input type="checkbox" name="values[send_mail]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')"/>
									</xsl:attribute>
									<xsl:if test="pref_send_mail = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Priority')"/>
								</label>

								<xsl:variable name="lang_priority_statustext">
									<xsl:value-of select="lang_priority_statustext"/>
								</xsl:variable>
								<xsl:variable name="select_priority_name">
									<xsl:value-of select="select_priority_name"/>
								</xsl:variable>
								<select name="{$select_priority_name}" class="pure-input-1-2" >
									<xsl:apply-templates select="priority_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>

								<select id="status_id" name="values[status]" class="pure-input-1-2" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"/>
									</xsl:attribute>
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</div>
							<xsl:choose>
								<xsl:when test="show_finnish_date ='1'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'finnish date')"/>
										</label>
										<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_finnish_date_statustext"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
						</xsl:when>
					</xsl:choose>
					<xsl:apply-templates select="custom_attributes/attributes"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'subject')"/>
						</label>

						<input type="text" id="subject" name="values[subject]" value="{value_subject}" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter the subject of this ticket')"/>
							</xsl:attribute>
							<xsl:if test="tts_mandatory_title != ''">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a title !')"/>
								</xsl:attribute>
							</xsl:if>

						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Details')"/>
						</label>

						<textarea class="pure-input-1-2" rows="10" name="values[details]" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter the details of this ticket')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please give som details !')"/>
							</xsl:attribute>
							<xsl:value-of select="value_details"/>
						</textarea>
					</div>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_upload_file"/>
								</label>

								<input  class="pure-input-1-2" type="file" name="file">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_file_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
				</fieldset>
			</div>
		</div>
		<div class="proplist-col">
			<input type="hidden" id="save" name="values[save]" value=""/>
			<input type="hidden" id="apply" name="values[apply]" value=""/>
			<input type="hidden" id="cancel" name="values[cancel]" value=""/>
			<input class="pure-button pure-button-primary" type="button" name="save" value="{lang_send}" onClick="confirm_session('save');">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Save the entry and return to list')"/>
				</xsl:attribute>
			</input>
			<input class="pure-button pure-button-primary" type="button" name="apply" value="{lang_save}" onClick="confirm_session('apply');">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Save the ticket')"/>
				</xsl:attribute>
			</input>
			<input class="pure-button pure-button-primary" type="button" name="cancel" value="{lang_cancel}" onClick="confirm_session('cancel');">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>


<!-- view -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<style type="text/css">
		#floating-box {
		position: relative;
		z-index: 10;
		}
		#submitbox {
		display: none;
		} 	</style>
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
		function generate_order()
		{
		Window1=window.open('<xsl:value-of select="order_link"/>','','left=50,top=100');
		}

		function generate_request()
		{
		Window1=window.open('<xsl:value-of select="request_link"/>','','left=50,top=100');
		}

		function template_lookup()
		{
		var oArgs = {menuaction:'property.uilookup.order_template',type:'order_template'};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

		function response_lookup()
		{
		var oArgs = {menuaction:'property.uilookup.response_template',type:'response_template'};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

		function preview_html(id)
		{

		var on_behalf_of_assigned = document.getElementById("on_behalf_of_assigned").checked ? 1 : 0;

		var oArgs = {menuaction:'property.uitts.view',id:id, preview_html:true, on_behalf_of_assigned: on_behalf_of_assigned};
		var strURL = phpGWLink('index.php', oArgs);
		Window1=window.open(strURL,'Search',"left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");

		}

		function preview_pdf(id)
		{
		var on_behalf_of_assigned = document.getElementById("on_behalf_of_assigned").checked ? 1 : 0;

		var oArgs = {menuaction:'property.uitts.view',id:id, preview_pdf:true, on_behalf_of_assigned: on_behalf_of_assigned};
		var strURL = phpGWLink('index.php', oArgs);
		Window1=window.open(strURL,'Search',"left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
		}

		var my_groups = <xsl:value-of select="my_groups"/>;

		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var location_item_id = '<xsl:value-of select="location_item_id"/>';
		var order_id = '<xsl:value-of select="value_order_id"/>';
		var location_code = '<xsl:value-of select="value_location_code"/>';

		//	var initialSelection = <xsl:value-of select="resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>


	</script>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" ENCTYPE="multipart/form-data" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="message" class='message'/>
			<div id="floating-box">
				<div id="submitbox">
					<table width="200px">
						<tbody>
							<tr>
								<td width="200px">
									<input type="button" class="pure-button pure-button-primary" name="save" onClick="confirm_session('save');">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('lang', 'save')"/>
										</xsl:attribute>
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'save the ticket')"/>
										</xsl:attribute>
									</input>
								</td>
								<xsl:choose>
									<xsl:when test="access_order = 1">
										<xsl:choose>
											<xsl:when test="value_order_id!=''">
												<td>
													<xsl:variable name="lang_send_order">
														<xsl:value-of select="php:function('lang', 'send order')"/>
													</xsl:variable>
													<input type="button" class="pure-button pure-button-primary" id="send_order_button" name="send_order" value="{$lang_send_order}" onClick="confirm_session('send_order');">
														<xsl:attribute name="title">
															<xsl:value-of select="$lang_send_order"/>
														</xsl:attribute>
													</input>
												</td>
											</xsl:when>
										</xsl:choose>
									</xsl:when>
								</xsl:choose>
								<td>
									<xsl:variable name="lang_done">
										<xsl:value-of select="php:function('lang', 'done')"/>
									</xsl:variable>
									<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.cancel_form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="general">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Ticket')"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="value_id"/>
						</label>
						<input class="pure-input-1-2" type="text" id="subject" name="values[subject]" value="{value_subject}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'update subject')"/>
							</xsl:attribute>
							<xsl:if test="simple ='1'">
								<xsl:attribute name="readonly">
									<xsl:text>readonly</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
					<xsl:for-each select="value_origin">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="descr"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="data">
									<div>
										<a href="{link}" title="{statustext}">
											<xsl:value-of select="id"/>
										</a>
										<xsl:text> </xsl:text>
									</div>
								</xsl:for-each>
							</div>
						</div>
					</xsl:for-each>
					<xsl:choose>
						<xsl:when test="lookup_type ='view2'">
							<xsl:call-template name="location_view2"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_form2"/>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="contact_phone !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Contact phone')"/>
								</label>
								<xsl:value-of select="contact_phone"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:call-template name="contact_form"/>
					<xsl:for-each select="value_target">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="descr"/>
							</label>
							<xsl:for-each select="data">
								<a href="{link}" title="{statustext}">
									<xsl:value-of select="id"/>
								</a>
								<xsl:text> </xsl:text>
							</xsl:for-each>
						</div>
					</xsl:for-each>
					<xsl:if test="simple !='1'">
						<div class="pure-control-group">
							<xsl:variable name="lang_make_relation">
								<xsl:value-of select="php:function('lang', 'make relation')"/>
							</xsl:variable>

							<label>
								<a href="#" onClick="make_relation({location_item_id});">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_make_relation"/>
									</xsl:attribute>
									<xsl:value-of select="$lang_make_relation"/>
								</a>
							</label>
							<select name="make_relation" id="make_relation" class="pure-input-1-2" >
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_make_relation"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="relation_type_list/options"/>
							</select>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'details')"/>
						</label>
						<xsl:choose>
							<xsl:when test="additional_notes=''">
								<xsl:value-of select="php:function('lang', 'no additional notes')"/>
							</xsl:when>
							<xsl:otherwise>
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
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<xsl:choose>
						<xsl:when test="simple !='1'">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'group')"/>
								</label>
								<xsl:call-template name="group_select"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'assigned to')"/>
								</label>
								<xsl:call-template name="user_id_select"/>
							</div>
							<xsl:choose>
								<xsl:when test="lang_takeover != ''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="lang_takeover"/>
										</label>
										<input type="checkbox" name="values[takeover]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Take over the assignment for this ticket')"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Send e-mail')"/>
								</label>
								<input type="checkbox" name="values[send_mail]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')"/>
									</xsl:attribute>
									<xsl:if test="pref_send_mail = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Priority')"/>
						</label>
						<xsl:variable name="lang_priority_statustext">
							<xsl:value-of select="php:function('lang', 'Select the priority the selection belongs to')"/>
						</xsl:variable>
						<xsl:variable name="select_priority_name">
							<xsl:value-of select="select_priority_name"/>
						</xsl:variable>
						<select name="{$select_priority_name}" title="{$lang_priority_statustext}" class="pure-input-1-2" >
							<xsl:if test="simple ='1'">
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:apply-templates select="priority_list/options"/>
						</select>
					</div>
					<xsl:choose>
						<xsl:when test="value_order_id=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>
								<select id="status_id" name="values[status]" class="pure-input-1-2" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"/>
									</xsl:attribute>
									<xsl:if test="simple ='1'">
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:call-template name="categories"/>
					</div>
					<xsl:choose>
						<xsl:when test="simple !='1'">
							<xsl:choose>
								<xsl:when test="show_finnish_date ='1'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'finnish date')"/>
										</label>

										<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'select the estimated date for closing the task')"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="show_billable_hours ='1'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'billable hours')"/>
										</label>
										<input class="pure-input-1-4" type="text" id="values_billable_hour" name="values[billable_hours]" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')"/>
											</xsl:attribute>
										</input>
										<input  class="pure-input-1-4" type="text" id="values_billable_hour_orig" name="values[billable_hours_orig]" value="{value_billable_hours}" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<input type="hidden" name="values[status]" value="{value_status}"/>
							<input type="hidden" name="values[assignedto]" value="{value_assignedto_id}"/>
							<input type="hidden" name="values[group_id]" value="{value_group_id}"/>
							<input type="hidden" name="values[priority]" value="{value_priority}"/>
							<input type="hidden" name="values[cat_id]" value="{value_cat_id}"/>
							<input type="hidden" name="values[finnish_date]" value="{value_finnish_date}"/>
							<input type="hidden" name="values[billable_hour]" value="{value_billable_hours}"/>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:apply-templates select="custom_attributes/attributes"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'new note')"/>
						</label>
						<textarea class="pure-input-1-2" rows="{textarearows}" name="values[note]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'add new comments')"/>
							</xsl:attribute>
						</textarea>
					</div>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-u-md-1-2" >
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_2'">
											<xsl:call-template name="table_setup">
												<xsl:with-param name="container" select ='container'/>
												<xsl:with-param name="requestUrl" select ='requestUrl'/>
												<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
												<xsl:with-param name="data" select ='data'/>
												<xsl:with-param name="tabletools" select ='tabletools' />
												<xsl:with-param name="config" select ='config'/>
											</xsl:call-template>
										</xsl:if>
									</xsl:for-each>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<script type="text/javascript">
								var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
							</script>
							<xsl:call-template name="file_upload"/>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="send_response = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'notify client by sms')"/>
								</label>
								<input type="checkbox" name="notify_client_by_sms" value="true">
									<xsl:attribute name="title">
										<xsl:value-of select="value_sms_client_order_notice"/>
									</xsl:attribute>
								</input>
								<input  class="pure-input-1-8" type="text" name="to_sms_phone" value="{value_sms_phone}">
									<xsl:attribute name="title">
										<xsl:value-of select="value_sms_client_order_notice"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_sms_text">
									<xsl:value-of select="php:function('lang', 'sms text')"/>
								</xsl:variable>

								<label>
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="$lang_sms_text"/>
										</xsl:attribute>
										<xsl:value-of select="$lang_sms_text"/>
									</a>
								</label>
								<textarea class="pure-input-1-2" rows="{textarearows}" id="response_text" name="values[response_text]" onKeyUp="javascript: SmsCountKeyUp(804);" onKeyDown="javascript: SmsCountKeyDown(804);" wrap="virtual">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_sms_text"/>
									</xsl:attribute>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'character left')"/>
								</label>
								<input type="text" readonly="readonly" size="3" maxlength="3" name="charNumberLeftOutput" id="charNumberLeftOutput" value="804">
								</input>
							</div>
						</xsl:when>
					</xsl:choose>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external communication')"/>
						</label>
						<input type="hidden" id="external_communication" name="external_communication" value=""/>

						<input type="button" class="pure-button pure-button-primary" name="init_external_communication" onClick="confirm_session('external_communication');">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'new')"/>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'external communication')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'messages')"/>
						</label>
						<div class="pure-u-md-1-2" >
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_9'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl'/>
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
										<xsl:with-param name="data" select ='data'/>
										<xsl:with-param name="tabletools" select ='tabletools' />
										<xsl:with-param name="config" select ='config'/>
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>
						</div>
					</div>

						<xsl:choose>
							<xsl:when test="access_order = 1">
								<xsl:choose>
									<xsl:when test="value_order_id=''">
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'make order')"/>
											</label>

											<input type="checkbox" name="values[make_order]" value="True">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'make order')"/>
												</xsl:attribute>
											</input>
										</div>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="value_order_id!=''">
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'order id')"/>
											</label>
											<xsl:value-of select="value_order_id"/>
											<input type="hidden" name="values[order_id]" value="{value_order_id}"/>
											<xsl:text> | </xsl:text>
											<xsl:variable name="lang_preview_html">
												<xsl:value-of select="php:function('lang', 'preview html')"/>
											</xsl:variable>
											<a href="{preview_html}">
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_preview_html"/>
												</xsl:attribute>
												<xsl:value-of select="$lang_preview_html"/>
											</a>
											<xsl:text> | </xsl:text>
											<xsl:variable name="lang_preview_pdf">
												<xsl:value-of select="php:function('lang', 'preview pdf')"/>
											</xsl:variable>
											<a href="{preview_pdf}">
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_preview_pdf"/>
												</xsl:attribute>
												<xsl:value-of select="$lang_preview_pdf"/>
											</a>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'on behalf of assigned')"/>
											</label>
											<input type="checkbox" id = "on_behalf_of_assigned" name="on_behalf_of_assigned" value="True">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'on behalf of assigned - vacation mode')"/>
												</xsl:attribute>
											</input>
										</div>
										<div class="pure-control-group">
											<xsl:variable name="lang_continuous">
												<xsl:value-of select="php:function('lang', 'continuous')"/>
											</xsl:variable>
											<label for="name">
												<xsl:value-of select="$lang_continuous"/>
											</label>
											<input type="checkbox" name="values[continuous]" value="1">
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_continuous"/>
												</xsl:attribute>
												<xsl:if test="value_continuous = '1'">
													<xsl:attribute name="checked">
														<xsl:text>checked</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'external project')"/>
											</label>
											<input type="hidden" id="external_project_id" name="values[external_project_id]"  value="{value_external_project_id}"/>
											<input class="pure-input-1-2" type="text" id="external_project_name" name="values[external_project_name]" value="{value_external_project_name}"/>
											<div id="external_project_container"/>
										</div>

										<xsl:call-template name="vendor_form"/>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'contract')"/>
											</label>
											<select id="vendor_contract_id" name="values[contract_id]" class="pure-input-1-2">
												<xsl:if test="count(contract_list/options) &gt; 0">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:if>
												<option value="">
													<xsl:value-of select="php:function('lang', 'select')"/>
												</option>
												<xsl:apply-templates select="contract_list/options"/>
											</select>
										</div>
										<xsl:if test="enable_order_service_id = 1">
											<div class="pure-control-group">
												<xsl:variable name="lang_service">
													<xsl:value-of select="php:function('lang', 'service')"/>
												</xsl:variable>
												<label>
													<xsl:value-of select="$lang_service"/>
												</label>
												<input type="hidden" id="service_id" name="values[service_id]"  value="{value_service_id}"/>
												<input class="pure-input-1-2" type="text" id="service_name" name="values[service_name]" value="{value_service_name}">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
													<xsl:attribute name="data-validation-error-msg">
														<xsl:value-of select="$lang_service"/>
													</xsl:attribute>
												</input>

												<div id="service_container"/>
											</div>
										</xsl:if>
										<div class="pure-control-group">
											<xsl:variable name="lang_dimb">
												<xsl:value-of select="php:function('lang', 'dimb')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="$lang_dimb"/>
											</label>
											<input type="hidden" id="ecodimb" name="values[ecodimb]"  value="{ecodimb_data/value_ecodimb}"/>
											<input class="pure-input-1-2" type="text" id="ecodimb_name" name="values[ecodimb_name]" value="{ecodimb_data/value_ecodimb} {ecodimb_data/value_ecodimb_descr}">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="$lang_dimb"/>
												</xsl:attribute>
											</input>
											<div id="ecodimb_container"/>
										</div>
										<div class="pure-control-group">
											<xsl:variable name="lang_budget_account">
												<xsl:value-of select="php:function('lang', 'budget account')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="$lang_budget_account"/>
											</label>
											<input type="hidden" id="b_account_id" name="values[b_account_id]"  value="{b_account_data/value_b_account_id}"/>
											<input class="pure-input-1-2" type="text" id="b_account_name" name="values[b_account_name]" value="{b_account_data/value_b_account_id} {b_account_data/value_b_account_name}">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="$lang_budget_account"/>
												</xsl:attribute>
											</input>
											<div id="b_account_container"/>
										</div>
										<xsl:if test="enable_unspsc = 1">
											<div class="pure-control-group">
												<xsl:variable name="lang_unspsc_code">
													<xsl:value-of select="php:function('lang', 'unspsc code')"/>
												</xsl:variable>
												<label>
													<xsl:value-of select="$lang_unspsc_code"/>
												</label>
												<input type="hidden" id="unspsc_code" name="values[unspsc_code]"  value="{value_unspsc_code}"/>
												<input class="pure-input-1-2" type="text" id="unspsc_code_name" name="values[unspsc_code_name]" value="{value_unspsc_code} {value_unspsc_code_name}">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
													<xsl:attribute name="data-validation-error-msg">
														<xsl:value-of select="$lang_unspsc_code"/>
													</xsl:attribute>
												</input>
												<div id="unspsc_code_container"/>
											</div>
										</xsl:if>

										<div class="pure-control-group">
											<xsl:variable name="lang_building_part">
												<xsl:value-of select="php:function('lang', 'building part')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="$lang_building_part"/>
											</label>

											<select name="values[building_part]" class="pure-input-1-2" >
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_building_part"/>
												</xsl:attribute>
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="$lang_building_part"/>
												</xsl:attribute>
												<option value="">
													<xsl:value-of select="$lang_building_part"/>
												</option>
												<xsl:apply-templates select="building_part_list/options"/>
											</select>
										</div>
										<xsl:choose>
											<xsl:when test="branch_list!=''">
												<div class="pure-control-group">
													<label>
														<xsl:value-of select="php:function('lang', 'branch')"/>
													</label>
													<select name="values[branch_id]" class="pure-input-1-2" >
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'select branch')"/>
														</xsl:attribute>
														<option value="0">
															<xsl:value-of select="php:function('lang', 'select branch')"/>
														</option>
														<xsl:apply-templates select="branch_list/options"/>
													</select>
												</div>
											</xsl:when>
										</xsl:choose>
										<div class="pure-control-group">
											<xsl:variable name="lang_order_dim1">
												<xsl:value-of select="php:function('lang', 'order_dim1')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="$lang_order_dim1"/>
											</label>
											<select name="values[order_dim1]" class="pure-input-1-2" >
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'order_dim1')"/>
												</xsl:attribute>
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="$lang_order_dim1"/>
												</xsl:attribute>
												<option value="">
													<xsl:value-of select="php:function('lang', 'order_dim1')"/>
												</option>
												<xsl:apply-templates select="order_dim1_list/options"/>
											</select>
										</div>
										<xsl:if test="enable_unspsc = 1">
											<div class="pure-control-group">
												<xsl:variable name="lang_tax_code">
													<xsl:value-of select="php:function('lang', 'tax code')"/>
												</xsl:variable>
												<label>
													<xsl:value-of select="$lang_tax_code"/>
												</label>
												<select name="values[tax_code]" class="pure-input-1-2" >
													<xsl:attribute name="title">
														<xsl:value-of select="$lang_tax_code"/>
													</xsl:attribute>
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
													<xsl:attribute name="data-validation-error-msg">
														<xsl:value-of select="$lang_tax_code"/>
													</xsl:attribute>
													<option value="">
														<xsl:value-of select="$lang_tax_code"/>
													</option>
													<xsl:apply-templates select="tax_code_list/options"/>
												</select>
											</div>
										</xsl:if>
										<div class="pure-control-group">
											<label>
												<a href="javascript:template_lookup()">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'lookup template')"/>
													</xsl:attribute>
													<xsl:value-of select="php:function('lang', 'description')"/>
												</a>
											</label>

											<textarea class="pure-input-1-2" rows="{textarearows}" id="order_descr" name="values[order_descr]" wrap="virtual">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'description order')"/>
												</xsl:attribute>
												<xsl:value-of select="value_order_descr"/>
											</textarea>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'invoice remark')"/>
											</label>
											<xsl:choose>
												<xsl:when test="value_order_sent != 1">
													<textarea class="pure-input-1-2" rows="{textarearows}" id="invoice_remark" name="values[invoice_remark]" wrap="virtual">
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'invoice remark')"/>
														</xsl:attribute>
														<xsl:value-of select="value_invoice_remark"/>
													</textarea>
												</xsl:when>
												<xsl:otherwise>
													<xsl:value-of select="value_invoice_remark"/>

												</xsl:otherwise>
											</xsl:choose>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'deadline')"/>
											</label>
											<table class="pure-table pure-u-md-1-2">
												<thead>
													<tr>
														<th>
															<xsl:value-of select="php:function('lang', 'deadline for start')"/>
														</th>
														<th>
															<xsl:value-of select="php:function('lang', 'deadline for execution')"/>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>

															<input type="text" id="order_deadline" name="values[order_deadline]" size="10" value="{value_order_deadline}" readonly="readonly">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'deadline for start')"/>
																</xsl:attribute>
															</input>
														</td>
														<td>

															<input type="text" id="order_deadline2" name="values[order_deadline2]" size="10" value="{value_order_deadline2}" readonly="readonly">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'deadline for execution')"/>
																</xsl:attribute>
															</input>

														</td>
													</tr>
												</tbody>
											</table>

										</div>

										<div class="pure-control-group">
											<xsl:variable name="lang_period">
												<xsl:value-of select="php:function('lang', 'period')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="php:function('lang', 'cost estimate')"/>
											</label>
											<table class="pure-table pure-u-md-1-2">
												<thead>
													<tr>
														<th>
															<xsl:value-of select="php:function('lang', 'Enter the budget')"/>
															<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
														</th>
														<th>
															<xsl:value-of select='$lang_period'/>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>

															<input  id="field_budget" type="text" name="values[budget]">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'Enter the budget')"/>
																</xsl:attribute>
															</input>
														</td>
														<td>
															<select name="values[budget_period]" style="width: 14em;">
																<xsl:attribute name="title">
																	<xsl:value-of select='$lang_period'/>
																</xsl:attribute>
																<xsl:apply-templates select="year_list/options"/>
															</select>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'budget')"/>
											</label>
											<div class = 'pure-u-md-1-2'>
												<!--div  id="paging_4"> </div>
												<div class="pure-table" id="datatable-container_4"/-->
												<xsl:for-each select="datatable_def">
													<xsl:if test="container = 'datatable-container_4'">
														<xsl:call-template name="table_setup">
															<xsl:with-param name="container" select ='container'/>
															<xsl:with-param name="requestUrl" select ='requestUrl'/>
															<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
															<xsl:with-param name="data" select ='data'/>
															<xsl:with-param name="tabletools" select ='tabletools' />
															<xsl:with-param name="config" select ='config'/>
														</xsl:call-template>
													</xsl:if>
												</xsl:for-each>

											</div>
										</div>

										<div class="pure-control-group">
											<xsl:variable name="lang_period">
												<xsl:value-of select="php:function('lang', 'period')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="php:function('lang', 'payment')"/>
											</label>
											<table class="pure-table pure-u-md-1-2">
												<thead>
													<tr>
														<th>
															<xsl:value-of select="php:function('lang', 'Enter actual cost')"/>
															<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]

														</th>
														<th>
															<xsl:value-of select='$lang_period'/>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<input type="text" name="values[actual_cost]">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'Enter actual cost')"/>
																</xsl:attribute>
															</input>
														</td>
														<td>
															<select name="values[actual_cost_period]" style="width: 14em;">
																<xsl:attribute name="title">
																	<xsl:value-of select='$lang_period'/>
																</xsl:attribute>
																<xsl:apply-templates select="period_list/options"/>
															</select>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'actual cost')"/>
											</label>

											<div class = 'pure-u-md-1-2'>
												<!--div  id="paging_4"> </div>
												<div class="pure-table" id="datatable-container_4"/-->
												<xsl:for-each select="datatable_def">
													<xsl:if test="container = 'datatable-container_5'">
														<xsl:call-template name="table_setup">
															<xsl:with-param name="container" select ='container'/>
															<xsl:with-param name="requestUrl" select ='requestUrl'/>
															<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
															<xsl:with-param name="data" select ='data'/>
															<xsl:with-param name="tabletools" select ='tabletools' />
															<xsl:with-param name="config" select ='config'/>
														</xsl:call-template>
													</xsl:if>
												</xsl:for-each>

											</div>
										</div>
										<xsl:choose>
											<xsl:when test="need_approval='1'">
												<div class="pure-control-group">
													<label>
														<xsl:value-of select="php:function('lang', 'approval')"/>
													</label>
													<div id="approval_container" class="pure-u-md-1-2">
													</div>
												</div>
											</xsl:when>
										</xsl:choose>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'send order')"/>
											</label>
											<table class="pure-table pure-u-md-1-2">
												<thead>
													<tr>
														<th>
															<select name="values[send_order_format]">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'format')"/>
																</xsl:attribute>
																<option value="html">
																	<xsl:text>HTML</xsl:text>
																</option>
																<option value="pdf">
																	<xsl:text>PDF</xsl:text>
																</option>
															</select>
														</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<xsl:for-each select="datatable_def">
																<xsl:if test="container = 'datatable-container_3'">
																	<xsl:call-template name="table_setup">
																		<xsl:with-param name="container" select ='container'/>
																		<xsl:with-param name="requestUrl" select ='requestUrl'/>
																		<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
																		<xsl:with-param name="data" select ='data'/>
																		<xsl:with-param name="tabletools" select ='tabletools' />
																		<xsl:with-param name="config" select ='config'/>
																	</xsl:call-template>
																</xsl:if>
															</xsl:for-each>
														</td>
													</tr>
													<tr>
														<td>
															<label>
																<xsl:value-of select="php:function('lang', 'extra mail address')"/>
															</label>
															<input type="text" name="values[vendor_email][]" value="{value_extra_mail_address}">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'The order will also be sent to this one')"/>
																</xsl:attribute>
															</input>
														</td>
													</tr>
												</tbody>
											</table>

										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'status')"/>
											</label>
											<select id="status_id" name="values[status]" class="pure-input-1-2">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"/>
												</xsl:attribute>
												<xsl:apply-templates select="status_list/options"/>
											</select>
										</div>
										<div class="pure-control-group">
											<label for="name">
												<xsl:value-of select="php:function('lang', 'order received')"/>
											</label>
											<xsl:variable name="lang_receive_order">
												<xsl:value-of select="php:function('lang', 'receive order')"/>
											</xsl:variable>
											<input type="button" class="pure-button pure-button-primary" name="edit" value="{$lang_receive_order}" onClick="receive_order({location_item_id});">
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_receive_order"/>
												</xsl:attribute>
												<xsl:if test="value_order_sent != 1">
													<xsl:attribute name="disabled">
														<xsl:text>disabled</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
											<div  class="pure-custom">
												<table>
													<tr>
														<td id="order_received_time">
															<xsl:value-of select="value_order_received"/>
														</td>
													</tr>
													<tr>
														<td align="right" id ="current_received_amount">
															<xsl:value-of select="value_order_received_amount"/>
														</td>
													</tr>
												</table>
											</div>
											<input  class="pure-custom" type="text" id="order_received_amount" size="6"/>
										</div>

										<div class="pure-control-group">
											<div class="pure-custom">
												<xsl:for-each select="datatable_def">
													<xsl:if test="container = 'datatable-container_7'">
														<xsl:call-template name="table_setup">
															<xsl:with-param name="container" select ='container'/>
															<xsl:with-param name="requestUrl" select ='requestUrl' />
															<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
															<xsl:with-param name="tabletools" select ='tabletools' />
															<xsl:with-param name="data" select ='data' />
															<xsl:with-param name="config" select ='config' />
														</xsl:call-template>
													</xsl:if>
												</xsl:for-each>
											</div>
										</div>

										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'attachments')"/>
											</label>
											<div class="pure-custom">
												<xsl:for-each select="datatable_def">
													<xsl:if test="container = 'datatable-container_8'">
														<xsl:call-template name="table_setup">
															<xsl:with-param name="container" select ='container'/>
															<xsl:with-param name="requestUrl" select ='requestUrl' />
															<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
															<xsl:with-param name="tabletools" select ='tabletools' />
															<xsl:with-param name="data" select ='data' />
															<xsl:with-param name="config" select ='config' />
														</xsl:call-template>
													</xsl:if>
												</xsl:for-each>
											</div>
										</div>

									</xsl:when>
								</xsl:choose>
							</xsl:when>
							<xsl:otherwise>
								<xsl:choose>
									<xsl:when test="value_order_id!=''">
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'order id')"/>
											</label>

											<xsl:value-of select="value_order_id"/>
										</div>
										<xsl:call-template name="vendor_view"/>
									</xsl:when>
								</xsl:choose>
							</xsl:otherwise>
						</xsl:choose>
				</fieldset>
			</div>
			<div id="notify">
				<fieldset>

					<xsl:variable name="lang_contact_statustext">
						<xsl:value-of select="php:function('lang', 'click this link to select')"/>
					</xsl:variable>
					<div class="pure-control-group">
						<label>
							<a href="javascript:notify_contact_lookup()" title="{$lang_contact_statustext}">
								<xsl:value-of select="php:function('lang', 'contact')"/>
							</a>
						</label>
						<input type="hidden" id="notify_contact" name="notify_contact" value="">
						</input>
						<input type="hidden" name="notify_contact_name" value="" onClick="notify_contact_lookup();" readonly="readonly"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'notify')"/>
						</label>

						<!--div id="paging_5"> </div>
						<div class="pure-table" id="datatable-container_5"/>
						<div id="datatable-buttons_5"/-->
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_6'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl'/>
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
									<xsl:with-param name="data" select ='data'/>
									<xsl:with-param name="tabletools" select ='tabletools'/>
									<xsl:with-param name="config" select ='config'/>
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</fieldset>
			</div>
			<div id="history">
				<!--div id="paging_1"/>
				<div class="pure-table" id="datatable-container_1"/-->
				<xsl:for-each select="datatable_def">
					<xsl:if test="container = 'datatable-container_1'">
						<xsl:call-template name="table_setup">
							<xsl:with-param name="container" select ='container'/>
							<xsl:with-param name="requestUrl" select ='requestUrl'/>
							<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
							<xsl:with-param name="data" select ='data'/>
							<xsl:with-param name="tabletools" select ='tabletools' />
							<xsl:with-param name="config" select ='config'/>
						</xsl:call-template>
					</xsl:if>
				</xsl:for-each>

			</div>
		</div>
		<div class="proplist-col">
			<input type="hidden" id="save" name="values[save]" value=""/>
			<input type="button" class="pure-button pure-button-primary" name="save" onClick="confirm_session('save');">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'save')"/>
				</xsl:attribute>
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'save the ticket')"/>
				</xsl:attribute>
			</input>
			<input type="hidden" id="send_order" name="values[send_order]" value=""/>
			<xsl:choose>
				<xsl:when test="access_order = 1">
					<xsl:choose>
						<xsl:when test="value_order_id!=''">
							<xsl:variable name="lang_send_order">
								<xsl:value-of select="php:function('lang', 'send order')"/>
							</xsl:variable>
							<input type="button" class="pure-button pure-button-primary" name="send_order" onClick="confirm_session('send_order');">
								<xsl:attribute name="value">
									<xsl:value-of select="$lang_send_order"/>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_send_order"/>
								</xsl:attribute>
							</input>
						</xsl:when>
					</xsl:choose>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="lang_done">
				<xsl:value-of select="php:function('lang', 'done')"/>
			</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.cancel_form.submit();">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>

	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form name="cancel_form" id="cancel_form" action="{$done_action}" method="post"></form>

	<xsl:if test="simple !='1'">

		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<div class="pure-g">
			<xsl:choose>
				<xsl:when test="request_link != ''">
					<xsl:variable name="request_link">
						<xsl:value-of select="request_link"/>
					</xsl:variable>
					<form method="post" action="{$request_link}" class="pure-u-1-1 pure-u-md-1-2">
						<xsl:variable name="lang_generate_request">
							<xsl:value-of select="php:function('lang', 'Generate Request')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary pure-u-24-24" name="location" value="{$lang_generate_request}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'click this to generate a request with this information')"/>
							</xsl:attribute>
						</input>
					</form>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="order_link != ''">
					<xsl:variable name="order_link">
						<xsl:value-of select="order_link"/>
					</xsl:variable>
					<form method="post" action="{$order_link}" class="pure-u-1-1 pure-u-md-1-2">
						<xsl:variable name="lang_generate_project">
							<xsl:value-of select="php:function('lang', 'generate new project')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary pure-u-24-24" name="location" value="{$lang_generate_project}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')"/>
							</xsl:attribute>
						</input>
					</form>

					<xsl:variable name="add_to_project_link">
						<xsl:value-of select="add_to_project_link"/>
					</xsl:variable>
					<form method="post" action="{$add_to_project_link}" class="pure-u-1-1 pure-u-md-1-2">
						<xsl:variable name="lang_add_to_project">
							<xsl:value-of select="php:function('lang', 'add to project')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary pure-u-24-24" name="location" value="{$lang_add_to_project}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')"/>
							</xsl:attribute>
						</input>
					</form>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="link_entity!=''">
					<xsl:for-each select="link_entity">
						<xsl:variable name="link">
							<xsl:value-of select="link"/>
						</xsl:variable>
						<form method="post" action="{$link}" class="pure-u-1-1 pure-u-md-1-2">
							<xsl:variable name="name">
								<xsl:value-of select="name"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary pure-u-24-24" name="location" value="{$name}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_start_statustext"/>
								</xsl:attribute>
							</input>
						</form>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:if>
</xsl:template>


<!-- New template-->
<xsl:template match="table_header_additional_notes">
	<tr class="th">
		<td class="th_text" width="4%" align="right">
			<xsl:value-of select="lang_count"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_date"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_user"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_note"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="additional_notes">
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class"/>
				</xsl:when>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<td align="right">
			<xsl:value-of select="value_count"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_date"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_user"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_note"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_history">
	<tr class="th">
		<td class="th_text" width="20%" align="left">
			<xsl:value-of select="lang_date"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_user"/>
		</td>
		<td class="th_text" width="30%" align="left">
			<xsl:value-of select="lang_action"/>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_new_value"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="record_history">
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class"/>
				</xsl:when>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<td align="left">
			<xsl:value-of select="value_date"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_user"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_action"/>
		</td>
		<td align="left">
			<xsl:value-of select="value_new_value"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="vendor_email">
	<tr>
		<td>
			<input type="checkbox" name="values[vendor_email][]" value="{email}">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'The address to which this order will be sendt')"/>
				</xsl:attribute>
			</input>
		</td>
		<td>
			<xsl:value-of select="email"/>
		</td>
	</tr>
</xsl:template>