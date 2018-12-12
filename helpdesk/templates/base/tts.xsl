
<!-- $Id: tts.xsl 15283 2016-06-14 09:21:39Z sigurdne $ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="add">
			<xsl:apply-templates select="add"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- add -->
<xsl:template xmlns:php="http://php.net/xsl" match="add">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
		var my_groups = <xsl:value-of select="my_groups"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Please select a person or a group to handle the ticket !')"/>;

		function response_lookup()
		{
		var oArgs = {menuaction:'helpdesk.uilookup.response_template',type:'response_template'};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
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
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:call-template name="categories"/>
					</div>
					<xsl:choose>
						<xsl:when test="simple !='1'">
							<xsl:if test="disable_groupassign_on_add !='1'">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'Group')"/>
									</label>

									<xsl:call-template name="group_select"/>
								</div>
							</xsl:if>
							<xsl:if test="disable_userassign_on_add !='1'">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'Assign to')"/>
									</label>

									<xsl:call-template name="user_id_select"/>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<xsl:variable name="lang_reverse">
									<xsl:value-of select="php:function('lang', 'reverse')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_reverse"/>
								</label>
								<input type="hidden" id="set_user_id" name="values[set_user_id]"  value="{value_set_user}"/>
								<input type="text" id="set_user_name" name="values[set_user_name]" value="{value_set_user_name}" class="pure-input-1-2">
								</input>
								<div id="set_user_container"/>
							</div>
							<!--xsl:call-template name="contact_form"/-->
							<!--div class="pure-control-group">
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
							</div-->
							<xsl:if test="disable_priority !='1'">
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
									<select name="{$select_priority_name}" title="{$lang_priority_statustext}" class="pure-input-1-2" >
										<xsl:apply-templates select="priority_list/options"/>
									</select>
								</div>
							</xsl:if>
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
										<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;">
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

						<input type="text" name="values[subject]" value="{value_subject}" size="60"  class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Enter the subject of this ticket')"/>
							</xsl:attribute>
							<xsl:if test="tts_mandatory_title != ''">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a title!')"/>
								</xsl:attribute>
							</xsl:if>

						</input>
					</div>
					<div class="pure-control-group">
						<xsl:choose>
							<xsl:when test="simple !='1'">
								<label>
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'standard text')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'standard text')"/>
									</a>
								</label>
							</xsl:when>
							<xsl:otherwise>
								<label>
									<xsl:value-of select="php:function('lang', 'new note')"/>
								</label>
							</xsl:otherwise>
						</xsl:choose>

						<textarea cols="60" rows="10" name="values[details]" id="new_note" class="pure-input-1-2" >
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

								<input type="file" name="file" size="40">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_file_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'paste image data')"/>
							<br/>
							<xsl:text>Ctrl + V</xsl:text>
						</label>
						<canvas title="Copy image data into clipboard and press Ctrl+V" style="border:1px solid grey;" id="my_canvas" width="100" height="10" class="pure-input-1-2" >
						</canvas>
						<input type="hidden" id="pasted_image" name="pasted_image"></input>
						<input type="hidden" id="pasted_image_is_blank" name="pasted_image_is_blank" value="1"></input>
					</div>

				</fieldset>
			</div>
		</div>
		<div class="proplist-col">
			<input type="hidden" id="save" name="values[save]" value=""/>
			<input type="hidden" id="apply" name="values[apply]" value=""/>
			<input type="hidden" id="cancel" name="values[cancel]" value=""/>
			<!--input class="pure-button pure-button-primary" type="button" name="save" value="{lang_send}" onClick="confirm_session('save');">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Save the entry and return to list')"/>
				</xsl:attribute>
			</input-->
			<input class="pure-button pure-button-primary" type="button" name="apply" value="{lang_send}" onClick="confirm_session('apply');">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_send"/>
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
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>

		function response_lookup()
		{
		var oArgs = {menuaction:'helpdesk.uilookup.response_template',type:'response_template'};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

		var my_groups = <xsl:value-of select="my_groups"/>;

		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var location_item_id = '<xsl:value-of select="location_item_id"/>';

		//	var initialSelection = <xsl:value-of select="resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>

		var parent_cat_id = <xsl:value-of select="parent_cat_id"/>;

		function open_print_view()
		{
		var oArgs = {menuaction:'helpdesk.uitts._print',id: $('#id').val()};
		var strURL = phpGWLink('index.php', oArgs);
		var win = window.open(strURL, '_blank');
		win.focus();
		}


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
			<div id="general">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Ticket')"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="value_id"/>
						</label>
						<input type="text" name="values[subject]" value="{value_subject}" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'update subject')"/>
							</xsl:attribute>
						</input>
						<input type="hidden" id="id" name="id" value="{id}">
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'owned by')"/>
						</label>
						<xsl:value-of select="value_owned_by"/>
					</div>
					<xsl:if test="set_user ='1'">
						<div class="pure-control-group">
							<xsl:variable name="lang_forward">
								<xsl:value-of select="php:function('lang', 'forward')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_forward"/>
							</label>
							<input type="hidden" id="set_user_id" name="values[set_user_id]"  value="{value_set_user}"/>
							<input type="text" id="set_user_name" name="values[set_user_name]" value="{value_set_user_name}" class="pure-input-1-2">
							</input>
							<div id="set_user_container"/>
						</div>
					</xsl:if>
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
						<xsl:when test="contact_phone !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Contact phone')"/>
								</label>
								<xsl:value-of select="contact_phone"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<!--xsl:call-template name="contact_form"/-->
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
							<xsl:if test="disable_priority !='1'">
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
										<xsl:apply-templates select="priority_list/options"/>
									</select>
								</div>
							</xsl:if>
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
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'category')"/>
								</label>
								<xsl:call-template name="categories"/>
							</div>
							<xsl:choose>
								<xsl:when test="show_finnish_date ='1'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'finnish date')"/>
										</label>

										<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;">
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
										<input type="text" id="values_billable_hour" name="values[billable_hours]" size="10" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')"/>
											</xsl:attribute>
										</input>
										<input type="text" id="values_billable_hour_orig" name="values[billable_hours_orig]" size="10" value="{value_billable_hours}" readonly="readonly">
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
						<xsl:choose>
							<xsl:when test="simple !='1'">
								<label>
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'standard text')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'standard text')"/>
									</a>
								</label>
							</xsl:when>
							<xsl:otherwise>
								<label>
									<xsl:value-of select="php:function('lang', 'new note')"/>
								</label>
							</xsl:otherwise>
						</xsl:choose>
						<textarea cols="{textareacols}" rows="{textarearows}" id="new_note" name="values[note]" class="pure-input-1-2" >
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


					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'paste image data')"/>
							<br/>
							<xsl:text>Ctrl + V</xsl:text>
						</label>
						<canvas title="Copy image data into clipboard and press Ctrl+V" style="border:1px solid grey;" id="my_canvas" width="100" height="10" class="pure-input-1-2" >
						</canvas>
						<input type="hidden" id="pasted_image" name="pasted_image"></input>
					</div>

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
								<input type="text" name="to_sms_phone" value="{value_sms_phone}">
									<xsl:attribute name="title">
										<xsl:value-of select="value_sms_client_order_notice"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'response')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'response')"/>
									</a>
								</label>
								<textarea cols="{textareacols}" rows="{textarearows}" id="response_text" name="values[response_text]" onKeyUp="javascript: SmsCountKeyUp(160);" onKeyDown="javascript: SmsCountKeyDown(160);" wrap="virtual">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'response')"/>
									</xsl:attribute>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'character left')"/>
								</label>
								<input type="text" readonly="readonly" size="3" maxlength="3" name="charNumberLeftOutput" id="charNumberLeftOutput" value="160">
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:if test="simple !='1'">
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
									<xsl:if test="container = 'datatable-container_7'">
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
					</xsl:if>

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
								<xsl:value-of select="php:function('lang', 'add')"/>
							</a>
						</label>
						<input type="hidden" id="notify_contact" name="notify_contact" value="">
						</input>
						<input type="hidden" name="notify_contact_name" value="" onClick="notify_contact_lookup();" readonly="readonly"/>
						<div class="pure-u-md-1-2" >
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
					<xsl:value-of select="php:function('lang', 'send')"/>
				</xsl:attribute>
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'send')"/>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_done">
				<xsl:value-of select="php:function('lang', 'done')"/>
			</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.cancel_form.submit();">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="print_view" onClick="open_print_view();">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'print view')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>

	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form name="cancel_form" id="cancel_form" action="{$done_action}" method="post"></form>

	<hr noshade="noshade" width="100%" align="center" size="1"/>
	<div class="proplist-col">
		<xsl:choose>
			<xsl:when test="request_link != ''">
				<xsl:variable name="request_link">
					<xsl:value-of select="request_link"/>
				</xsl:variable>
				<form method="post" action="{$request_link}">
					<xsl:variable name="lang_generate_request">
						<xsl:value-of select="php:function('lang', 'Generate Request')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="location" value="{$lang_generate_request}">
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
				<form method="post" action="{$order_link}">
					<xsl:variable name="lang_generate_project">
						<xsl:value-of select="php:function('lang', 'generate new project')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="location" value="{$lang_generate_project}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')"/>
						</xsl:attribute>
					</input>
				</form>

				<xsl:variable name="add_to_project_link">
					<xsl:value-of select="add_to_project_link"/>
				</xsl:variable>
				<form method="post" action="{$add_to_project_link}">
					<xsl:variable name="lang_add_to_project">
						<xsl:value-of select="php:function('lang', 'add to project')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="location" value="{$lang_add_to_project}">
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
					<form method="post" action="{$link}">
						<xsl:variable name="name">
							<xsl:value-of select="name"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="location" value="{$name}">
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
