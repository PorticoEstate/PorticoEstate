
<!-- $Id: tts.xsl 15283 2016-06-14 09:21:39Z sigurdne $ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="add">
			<xsl:apply-templates select="add"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
		<xsl:when test="navigate">
			<xsl:apply-templates select="navigate"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- navigate -->
<xsl:template xmlns:php="http://php.net/xsl" match="navigate">

	<style>
		.card .btn {
		z-index: 1;
		}
	</style>
	<div class="container">
		<div class="row mt-4">
			<xsl:for-each select="sub_menu">
				<div class="col-4 mb-3">
					<a href="{url}" class="stretched-link text-secondary">
						<div class="card h-100 mb-2">
							<div class="card-block text-center">
								<h1 class="p-3">
									<i class="{icon}"></i>
								</h1>
							</div>
							<div class="card-footer text-center">
								<xsl:value-of select="text"/>
							</div>
						</div>
					</a>
				</div>
			</xsl:for-each>

		</div>

	</div>
</xsl:template>
<!-- add -->
<xsl:template xmlns:php="http://php.net/xsl" match="add">
	<style>
		.file {
		position: relative;
		background: linear-gradient(to right, lightblue 50%, transparent 50%);
		background-size: 200% 100%;
		background-position: right bottom;
		transition:all 1s ease;
		background: lightgrey;
		}
		.file.done {
		background: lightgreen;
		}
	</style>


	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
		var my_groups = <xsl:value-of select="my_groups"/>;
		var simple = '<xsl:value-of select="simple"/>';
		var account_lid =  '<xsl:value-of select="account_lid"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'Please select a person or a group to handle the ticket !', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
		var html_editor = '<xsl:value-of select="html_editor"/>';

		function response_lookup()
		{
		var oArgs = {menuaction:'helpdesk.uilookup.response_template',type:'response_template', category:1};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

		var parent_cat_id = <xsl:value-of select="parent_cat_id"/>;

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
						<xsl:variable name="lang_origin_type">
							<xsl:value-of select="descr"/>
						</xsl:variable>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'started from')"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="data">
									<div>
										<a href="{link}">
											<xsl:value-of select="$lang_origin_type"/>
											<xsl:text>::</xsl:text>
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
						<xsl:call-template name="categories">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
						</xsl:call-template>
					</div>
					<xsl:choose>
						<xsl:when test="simple !='1'">
							<xsl:if test="disable_groupassign_on_add !='1'">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'Group')"/>
									</label>
									<xsl:call-template name="group_select">
										<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
									</xsl:call-template>

								</div>
							</xsl:if>
							<xsl:if test="disable_userassign_on_add !='1'">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'Assign to')"/>
									</label>
									<xsl:call-template name="user_id_select">
										<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
									</xsl:call-template>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<xsl:variable name="lang_on_behalf_of">
									<xsl:value-of select="php:function('lang', 'on behalf of')"/>
								</xsl:variable>
								<label for="set_on_behalf_of_name">
									<xsl:value-of select="$lang_on_behalf_of"/>
								</label>
								<input type="hidden" id="set_on_behalf_of_lid" name="values[set_on_behalf_of_lid]"  value="{value_set_on_behalf_of_lid}"/>
								<input type="text" id="set_on_behalf_of_name" name="values[set_on_behalf_of_name]" value="{value_set_on_behalf_of_name}" class="pure-input-3-4">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter username or ssn')"/>
									</xsl:attribute>
								</input>
								<div id="set_on_behalf_of_container"/>
							</div>
							<!-- Sigurd: 20191012: Midlertidig kommentert ut i påvente av ny integrasjon med HR-systemet for å finne nærmeste leder-->
							<!--							<div class="pure-control-group">
								<xsl:variable name="lang_reverse">
									<xsl:value-of select="php:function('lang', 'reverse')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_reverse"/>
								</label>
								<div class="pure-custom"  id="set_user_container"/>
							</div>-->
							<div class="pure-control-group">
								<xsl:variable name="lang_reverse_alternative">
									<xsl:value-of select="php:function('lang', 'reverse alternative')"/>
								</xsl:variable>
								<label for="set_user_alternative_name">
									<xsl:value-of select="$lang_reverse_alternative"/>
								</label>
								<input type="hidden" id="set_user_alternative_lid" name="values[set_user_alternative_lid]" />
								<input type="text" id="set_user_alternative_name" name="values[set_user_alternative_name]" class="pure-input-3-4">
								</input>
								<div class="pure-custom"  id="set_user_container_alternative"/>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Send e-mail')"/>
								</label>
								<input type="checkbox" id="send_email" name="values[send_mail]" value="1">
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
										<xsl:value-of select="lang_priority_statustext"/>
									</xsl:variable>
									<xsl:variable name="select_priority_name">
										<xsl:value-of select="select_priority_name"/>
									</xsl:variable>
									<select name="{$select_priority_name}" title="{$lang_priority_statustext}" class="pure-input-3-4" >
										<xsl:apply-templates select="priority_list/options"/>
									</select>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>

								<select id="status_id" name="values[status]" class="pure-input-3-4" >
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
					<xsl:apply-templates select="custom_attributes/attributes">
						<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
					</xsl:apply-templates>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'subject')"/>
						</label>

						<input type="text" name="values[subject]" value="{value_subject}" size="60"  class="pure-input-3-4" >
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
								<label for="new_note">
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'standard text')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'standard text')"/>
									</a>
								</label>
							</xsl:when>
							<xsl:otherwise>
								<label for="new_note">
									<xsl:value-of select="php:function('lang', 'new note')"/>
								</label>
							</xsl:otherwise>
						</xsl:choose>

						<div class="pure-custom pure-input-3-4">
							<textarea rows="10" name="details" id="new_note" class="pure-input-1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Enter the details of this ticket')"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>new_note</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please give som details !')"/>
								</xsl:attribute>
								<xsl:value-of select="value_details"/>
							</textarea>
						</div>
					</div>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'upload files')"/>
								</label>
								<!--								<div class="pure-input-3-4 pure-custom">
									<div id="drop-area" style="border: 2px dashed #ccc; padding: 20px;">
										<p>
											<xsl:value-of select="php:function('lang', 'Upload multiple files with the file dialog, or by dragging and dropping images onto the dashed region')"/>
										</p>

										<input id="file_input" type="file" name="file[]" class="pure-input-3-4" multiple="multiple" onchange="handleFiles(this.files)">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>-->
								<div id="drop-area" class="pure-input-3-4 pure-custom">
									<div style="border: 2px dashed #ccc; padding: 20px;">
										<p>
											<xsl:value-of select="php:function('lang', 'Upload multiple files with the file dialog, or by dragging and dropping images onto the dashed region')"/>
										</p>
										<div class="fileupload-buttonbar">
											<div  class="fileupload-buttons">
												<!-- The fileinput-button span is used to style the file input field as button -->
												<span class="fileinput-button pure-button">
													<span>
														<xsl:value-of select="php:function('lang', 'Add files')"/>
														<xsl:text>...</xsl:text>
													</span>
													<input id="fileupload" type="file" name="files[]" multiple="multiple">
														<xsl:attribute name="data-url">
															<xsl:value-of select="multi_upload_action"/>
														</xsl:attribute>
														<xsl:attribute name="capture">camera</xsl:attribute>
													</input>
												</span>
												<!--												<button type="button" id="start_file_upload" class="start pure-button">
													<xsl:value-of select="php:function('lang', 'Start upload')"/>
												</button>-->

												<!-- The global file processing state -->
												<span class="fileupload-process"></span>
											</div>
											<div class="fileupload-count">
												<xsl:value-of select="php:function('lang', 'Number files')"/>: <span id="files-count"></span>
											</div>
											<div class="fileupload-progress" style="display:none">
												<!-- The global progress bar -->
												<div id = 'progress' class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
												<!-- The extended global progress state -->
												<div class="progress-extended">&nbsp;</div>
											</div>
										</div>
										<!-- The table listing the files available for upload/download -->
										<div class="content_upload_download">
											<div class="presentation files" style="display: inline-table;"></div>
										</div>
									</div>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'paste image data')"/>
						</label>
						<div class="pure-input-3-4 pure-custom">
							<div id="paste_image_data"  style="border: 2px dashed #ccc; padding: 20px;">

							</div>
						</div>
						<input type="hidden" id="pasted_image_is_blank" name="pasted_image_is_blank" value="1"></input>
					</div>
				</fieldset>
			</div>
			<div id="notify">
				<div class="pure-control-group">
					<xsl:variable name="lang_notify">
						<xsl:value-of select="php:function('lang', 'notify')"/>
					</xsl:variable>
					<label for="set_notify_name">
						<xsl:value-of select="$lang_notify"/>
					</label>
					<input type="hidden" id="set_notify_lid" name="values[set_notify_lid]" />
					<input type="text" id="set_notify_name" name="values[set_notify_name]" class="pure-input-3-4">
					</input>
					<div class="pure-custom"  id="set_notify_container"/>
				</div>
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
		var oArgs = {menuaction:'helpdesk.uilookup.response_template',type:'response_template', category:1};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

		var my_groups = <xsl:value-of select="my_groups"/>;
		var simple = '<xsl:value-of select="simple"/>';

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
	<style>


		.wrapperForGlider {
		width: 500px;
		max-width: 80%;
		margin: 0 auto;
		margin-bottom: 50px;
		display: inline-block;
		}


	</style>
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
						<input type="text" name="values[subject]" value="{value_subject}" class="pure-input-3-4" >
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
					<xsl:if test="simple !='1' and reverse_assigned = 1">
						<div class="pure-control-group">
							<xsl:variable name="lang_forward">
								<xsl:value-of select="php:function('lang', 'forward')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_forward"/>
							</label>
							<xsl:choose>
								<xsl:when test="set_user ='1'">
									<input type="hidden" id="set_user_lid" name="values[set_user_lid]"  value="{value_set_user}"/>
									<input type="text" id="set_user_name" name="values[set_user_name]" value="{value_set_user_name}" class="pure-input-3-4">
									</input>
									<div id="set_user_container"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('lang', 'take over the ticket in order to be able to forward')"/>
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</xsl:if>
					<xsl:for-each select="value_origin">
						<xsl:variable name="lang_origin_type">
							<xsl:value-of select="descr"/>
						</xsl:variable>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'started from')"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="data">
									<div>
										<a href="{link}" title="{statustext}">
											<xsl:value-of select="$lang_origin_type"/>
											<xsl:text>::</xsl:text>
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
						<xsl:variable name="lang_target_type">
							<xsl:value-of select="descr"/>
						</xsl:variable>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Referred from')"/>
							</label>
							<xsl:for-each select="data">
								<a href="{link}" title="{statustext}">
									<xsl:value-of select="$lang_target_type"/>
									<xsl:text>::</xsl:text>
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
								<div class = 'pure-u-1 pure-u-md-3-4'>
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
								<xsl:call-template name="group_select">
									<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
								</xsl:call-template>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'assigned to')"/>
								</label>
								<xsl:call-template name="user_id_select">
									<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
								</xsl:call-template>
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
								<input type="checkbox" id="send_email" name="values[send_mail]" value="1">
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
									<select name="{$select_priority_name}" title="{$lang_priority_statustext}" class="pure-input-3-4" >
										<xsl:apply-templates select="priority_list/options"/>
									</select>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>
								<select id="status_id" name="values[status]" class="pure-input-3-4" >
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
								<xsl:call-template name="categories">
									<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
								</xsl:call-template>
							</div>
							<div class="pure-control-group">
								<label for="change_category_id">
									<xsl:value-of select="php:function('lang', 'change category')"/>
								</label>
								<select id="change_category_id" name="change_category" class="pure-input-3-4" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'change category')"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'change category')"/>
									</option>
									<xsl:for-each select="cat_change_list">
										<optgroup label="{label}">
											<xsl:for-each select="options">
												<option value="{id}" title="{title}">
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</option>
											</xsl:for-each>
										</optgroup>
									</xsl:for-each>
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
					<xsl:apply-templates select="custom_attributes/attributes">
						<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
					</xsl:apply-templates>

					<xsl:if  test="simple !='1'">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'publish text')"/>
							</label>
							<input type="checkbox" id="publish_text" name="values[publish_text]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Check to publish text')"/>
								</xsl:attribute>
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
							</input>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<xsl:choose>
							<xsl:when test="simple !='1'">
								<label for="new_note">
									<a href="javascript:response_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'standard text')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'standard text')"/>
									</a>
								</label>
							</xsl:when>
							<xsl:otherwise>
								<label for="new_note">
									<xsl:value-of select="php:function('lang', 'new note')"/>
								</label>
							</xsl:otherwise>
						</xsl:choose>
						<div class="pure-custom pure-input-3-4">
							<textarea cols="{textareacols}" rows="{textarearows}" id="new_note" name="note">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'add new comments')"/>
								</xsl:attribute>
							</textarea>
						</div>
					</div>
					<xsl:choose>
						<xsl:when test="fileupload = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-u-md-3-4" >
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
							<div class="pure-control-group ">
								<label for="name">
								</label>
								<div class="wrapperForGlider" style="display:none;">
									<div class="glider-contain">
										<div class="glider">
											<xsl:for-each select="content_files">
												<xsl:if test="img_url">
													<div>
														<img data-src="{img_url}" alt="{file_name}"/>
													</div>
												</xsl:if>
											</xsl:for-each>
										</div>
										<input type="button" role="button"  aria-label="Previous" class="glider-prev" value="«"></input>
										<input type="button" role="button" aria-label="Next" class="glider-next" value="»"></input>
										<div role="tablist" class="dots"></div>
									</div>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'upload files')"/>
								</label>

								<xsl:call-template name="multi_upload_file_inline">
									<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
									<xsl:with-param name="multi_upload_action">
										<xsl:value-of select="multi_upload_action"/>
									</xsl:with-param>
								</xsl:call-template>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'paste image data')"/>
						</label>
						<div class="pure-input-3-4 pure-custom">
							<div id="paste_image_data"  style="border: 2px dashed #ccc; padding: 20px;">
							</div>
							<input type="hidden" id="pasted_image" name="pasted_image"></input>
						</div>
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
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external communication')"/>
						</label>
						<input type="hidden" id="external_communication" name="external_communication" value=""/>

						<xsl:if test="simple !='1'">
							<input type="button" class="pure-button pure-button-primary" name="init_external_communication" onClick="confirm_session('external_communication');">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'new')"/>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'external communication')"/>
								</xsl:attribute>
							</input>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'messages')"/>
						</label>
						<div class="pure-u-md-3-4" >
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

				</fieldset>
			</div>
			<div id="notify">
				<fieldset class="pure-form-stacked">

					<xsl:variable name="lang_notify">
						<xsl:value-of select="php:function('lang', 'notify')"/>
					</xsl:variable>
					<label for="set_notify_name">
						<xsl:value-of select="$lang_notify"/>
					</label>
					<input type="hidden" id="set_notify_lid" name="set_notify_lid" />
					<input type="text" id="set_notify_name" name="set_notify_name" class="pure-input-3-4">
					</input>

					<div class="pure-u-md-3-4" >
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
