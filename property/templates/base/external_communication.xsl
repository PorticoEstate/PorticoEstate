
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="add_deviation">
			<xsl:apply-templates select="add_deviation" />
		</xsl:when>
		<xsl:when test="send_sms">
			<xsl:apply-templates select="send_sms" />
		</xsl:when>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="send_sms">
	<script type="text/javascript">
		self.name="first_Window";
		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address', 'select user')"/>
	</script>

	<div class="content">
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

					<div id="main">

						<div class="pure-control-group">
							<label for='location_name'>
								<xsl:value-of select="php:function('lang', 'all')"/>
							</label>
							<input type="checkbox" id="send_sms_to_all" name="send_sms_to_all" value="__get_all__"/>
						</div>
						<div id="location_selector" class="pure-control-group">
							<label for='location_name'>
								<xsl:value-of select="php:function('lang', 'location')"/>
							</label>
							<input type="hidden" id="location_code" name="location_code" />
							<input type="text" id="location_name" name="location_name" class="pure-input-3-4"/>
							<div id="location_container"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'recipients')"/>
							</label>
							<select id="sms_recipients" name="sms_recipients[]" multiple="true" class="pure-input-3-4">
								<xsl:attribute name="data-validation">
									<xsl:text>sms_recipients</xsl:text>
								</xsl:attribute>
								<xsl:apply-templates select="recipient_list/options"/>
							</select>
						</div>


						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'extra sms address')"/>
							</label>

							<input type="text" name="extra_sms_recipients" value="{value_extra_sms_address}" class="pure-input-3-4" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'comma separated list')"/>
								</xsl:attribute>
							</input>
						</div>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'sms')"/>
							</legend>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'message')"/>
								</label>
								<textarea id ="sms_content" class="pure-input-3-4" rows="10" name="sms_content" onKeyUp="javascript: SmsCountKeyUp(804);" onKeyDown="javascript: SmsCountKeyDown(804);">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'message')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:value-of disable-output-escaping="yes" select="default_message"/>
								</textarea>
							</div>

						</fieldset>
					</div>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<xsl:variable name="lang_send">
						<xsl:value-of select="php:function('lang', 'send')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="send">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_send"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_send"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="add_deviation">
	<script type="text/javascript">
		self.name="first_Window";
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>
	</script>

	<div class="content">
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<input type="hidden" id="do_preview" name="do_preview" value="{value_do_preview}"/>

					<div id="main">

						<xsl:if test="value_id !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<xsl:value-of select="value_id"/>
							</div>

						</xsl:if>
						<xsl:choose>
							<xsl:when test="count(type_list/options/*) =0">
								<input type="hidden" id="type_id" name="type_id" value="1"/>
							</xsl:when>
							<xsl:otherwise>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'type')"/>
									</label>
									<select id="type_id" name="type_id" class="pure-input-3-4">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:apply-templates select="type_list/options"/>
									</select>
								</div>
							</xsl:otherwise>
						</xsl:choose>
						<div class="pure-control-group">
							<label for='location_name'>
								<xsl:value-of select="php:function('lang', 'location')"/>
							</label>
							<input type="hidden" id="location_code" name="location_code" />
							<input type="text" id="location_name" name="location_name" class="pure-input-3-4"/>
							<div id="location_container"/>
						</div>

						<xsl:call-template name="vendor_form"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<div class="pure-u-md-3-4" >
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
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contract')"/>
							</label>
							<select id="vendor_contract_id" name="contract_id" class="pure-input-3-4">
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
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'order')"/>
							</label>
							<div class="pure-custom pure-u-md-3-4">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_2'">
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
								<xsl:value-of select="php:function('lang', 'deviation')"/>
							</label>
							<div class="pure-custom pure-u-md-3-4">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_3'">
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
								<xsl:value-of select="php:function('lang', 'extra mail address')"/>
							</label>

							<input type="text" name="mail_recipients[]" value="{value_extra_mail_address}" class="pure-input-3-4" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'comma separated list')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'upload file')"/>
							</label>

							<input  class="pure-input-3-4" type="file" name="file">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_file_statustext"/>
								</xsl:attribute>
							</input>
						</div>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</legend>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'subject')"/>
								</label>
								<input type="text" name="subject" value="{value_subject}" class="pure-input-3-4" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'subject')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'message')"/>
								</label>
								<textarea id ="new_note" class="pure-input-3-4" rows="10" name="message">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'message')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</textarea>
							</div>

						</fieldset>
					</div>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="save">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_send">
						<xsl:value-of select="php:function('lang', 'send')"/>
					</xsl:variable>
					<xsl:variable name="lang_preview_html">
						<xsl:value-of select="php:function('lang', 'preview html')"/>
					</xsl:variable>
					<input type="hidden" id="preview_html" name="preview_html" value=""/>
					<input type="submit" class="pure-button pure-button-primary" name="init_preview">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_preview_html"/>
						</xsl:attribute>
					</input>
					<input type="submit" class="pure-button pure-button-primary" name="send">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_send"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_send"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var order_id = '<xsl:value-of select="value_order_id"/>';

		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>

		function contact_lookup()
		{
		var oArgs = {menuaction:'property.uilookup.contact', column:'mail_recipients'};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:Math.round($(window).width()*0.9),height:Math.round($(window).height()*0.9),fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}

	</script>

	<div class="content">
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<input type="hidden" id="ticket_id" name="ticket_id" value="{value_ticket_id}"/>
					<input type="hidden" id="id" name="id" value="{value_id}"/>
					<input type="hidden" id="do_preview" name="do_preview" value="{value_do_preview}"/>

					<div id="main">
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'internal communication')"/>
							</legend>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'ticket')"/>
								</label>
								<a href="{cancel_url}" class="pure-button pure-button-primary">
									<xsl:value-of select="value_ticket_id"/>
								</a>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'subject')"/>
								</label>
								<xsl:value-of select="value_ticket_subject"/>

							</div>

							<xsl:apply-templates select="contact_data"/>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'details')"/>
								</label>
								<xsl:choose>
									<xsl:when test="additional_notes=''">
										<xsl:value-of select="php:function('lang', 'no additional notes')"/>
									</xsl:when>
									<xsl:otherwise>
										<div class = 'pure-u-md-3-4'>
											<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_0'">
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
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</fieldset>

						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'external communication')"/>
							</legend>

							<xsl:if test="value_id !=''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<xsl:value-of select="value_id"/>
								</div>

							</xsl:if>
							<xsl:choose>
								<xsl:when test="count(type_list/options/*) =0">
									<input type="hidden" id="type_id" name="type_id" value="1"/>
								</xsl:when>
								<xsl:otherwise>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'type')"/>
										</label>
										<select id="type_id" name="type_id" class="pure-input-3-4">
											<xsl:attribute name="data-validation">
												<xsl:text>required</xsl:text>
											</xsl:attribute>
											<xsl:apply-templates select="type_list/options"/>
										</select>
									</div>
								</xsl:otherwise>
							</xsl:choose>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'subject')"/>
								</label>
								<xsl:choose>
									<xsl:when test="mode = 'edit'">
										<input type="text" name="subject" value="{value_subject}" class="pure-input-3-4" >
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'subject')"/>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="value_subject"/>
									</xsl:otherwise>
								</xsl:choose>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'details')"/>
								</label>
								<xsl:choose>
									<xsl:when test="additional_notes=''">
										<xsl:value-of select="php:function('lang', 'no additional notes')"/>
									</xsl:when>
									<xsl:otherwise>
										<div class = 'pure-u-md-3-4'>
											<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_3'">
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
									</xsl:otherwise>
								</xsl:choose>
							</div>
							<xsl:choose>
								<xsl:when test="mode = 'edit'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'descr')"/>
										</label>
										<div class="pure-custom pure-input-3-4">
											<textarea id ="new_note" rows="10" name="message">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'message')"/>
												</xsl:attribute>
												<xsl:if test="value_id = ''">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</textarea>
										</div>
									</div>
									<xsl:call-template name="vendor_form"/>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'email')"/>
								</label>
								<div class="pure-u-md-3-4" >
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

							<div class="pure-control-group">
								<label>
									<a href="javascript:contact_lookup()">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'extra mail address')"/>
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'extra mail address')"/>
									</a>

								</label>
								<xsl:choose>
									<xsl:when test="mode = 'edit'">
										<input type="text" id="mail_recipients" name="mail_recipients[]" value="{value_extra_mail_address}" class="pure-input-3-4" >
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'comma separated list')"/>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="value_extra_mail_address"/>
									</xsl:otherwise>
								</xsl:choose>
							</div>
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
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'upload file')"/>
								</label>

								<xsl:call-template name="multi_upload_file_inline">
									<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
									<xsl:with-param name="multi_upload_action">
										<xsl:value-of select="multi_upload_action"/>
									</xsl:with-param>
									<xsl:with-param name="capture">camera</xsl:with-param>
								</xsl:call-template>
							</div>

						</fieldset>
					</div>
					<div id="history">
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
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:choose>
						<xsl:when test="mode = 'edit'">
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="save">
								<xsl:attribute name="value">
									<xsl:value-of select="$lang_save"/>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_save"/>
								</xsl:attribute>
							</input>
							<xsl:if test="value_id !=''">
								<xsl:variable name="lang_send">
									<xsl:value-of select="php:function('lang', 'send')"/>
								</xsl:variable>
								<xsl:variable name="lang_preview_html">
									<xsl:value-of select="php:function('lang', 'preview html')"/>
								</xsl:variable>
								<input type="hidden" id="preview_html" name="preview_html" value=""/>
								<!--input type="button" class="pure-button pure-button-primary" name="preview_html" onClick="preview({value_id});"-->
								<input type="submit" class="pure-button pure-button-primary" name="init_preview">
									<xsl:attribute name="value">
										<xsl:value-of select="$lang_preview_html"/>
									</xsl:attribute>
								</input>
								<input type="submit" class="pure-button pure-button-primary" name="send">
									<xsl:attribute name="value">
										<xsl:value-of select="$lang_send"/>
									</xsl:attribute>
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_send"/>
									</xsl:attribute>
								</input>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<input type="button" class="pure-button pure-button-primary" name="edit" onClick="window.location = '{edit_action}';">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'edit')"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<xsl:template match="contact_data" xmlns:php="http://php.net/xsl">
	<div class="pure-control-group">

		<div class="pure-u-1 pure-u-md-1-3">
			<label>
				<xsl:value-of select="php:function('lang', 'contact')"/>
			</label>
			<div class="pure-u-md-1-3">
				<table>
					<tr>
						<th>
							<xsl:value-of select="contact_name"/>
						</th>
					</tr>
					<xsl:choose>
						<xsl:when test="contact_phone!=''">
							<tr>
								<td>
									<xsl:value-of select="contact_phone"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="contact_email!=''">
							<tr>
								<td>
									<a href="mailto:{contact_email}">
										<xsl:value-of select="contact_email"/>
									</a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<xsl:choose>
							<xsl:when test="contact_name2!=''">
								<tr>
									<th>
										<xsl:value-of select="contact_name2"/>
									</th>
								</tr>
							</xsl:when>
						</xsl:choose>
					</tr>
					<xsl:choose>
						<xsl:when test="contact_phone2!=''">
							<tr>
								<td>
									<xsl:value-of select="contact_phone2"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="contact_email2!=''">
							<tr>
								<td>
									<a href="mailto:{contact_email2}">
										<xsl:value-of select="contact_email2"/>
									</a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
