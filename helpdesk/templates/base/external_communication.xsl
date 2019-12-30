
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var order_id = '<xsl:value-of select="value_order_id"/>';

		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>
		function response_lookup()
		{
		var oArgs = {menuaction:'helpdesk.uilookup.response_template',type:'response_template', category:2};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
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
										<label for="new_note">
											<xsl:value-of select="php:function('lang', 'descr')"/>
											<br/>
											<a href="javascript:response_lookup()">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'standard text')"/>
												</xsl:attribute>
												<xsl:value-of select="php:function('lang', 'standard text')"/>
											</a>
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
												<xsl:value-of select="value_initial_message"/>
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
									<xsl:value-of select="php:function('lang', 'extra mail address')"/>
								</label>
								<xsl:choose>
									<xsl:when test="mode = 'edit'">
										<input type="text" name="mail_recipients[]" value="{value_extra_mail_address}" class="pure-input-3-4" >
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

							<xsl:if test="mode = 'edit'">
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

								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'status')"/>
									</label>

									<select id="status_id" name="ticket_status" class="pure-input-3-4" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Set the status of the ticket')"/>
										</xsl:attribute>
										<xsl:apply-templates select="status_list/options"/>
									</select>
								</div>
							</xsl:if>

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

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
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
