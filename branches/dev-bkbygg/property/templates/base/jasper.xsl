
<!-- $Id$ -->
<xsl:template match="data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="user_input"/>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<div id="tab-content">
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<div id="general">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
			<dl>
						<xsl:choose>
							<xsl:when test="msgbox_data != ''">
						<dt>
										<xsl:call-template name="msgbox"/>
						</dt>
							</xsl:when>
						</xsl:choose>
			</dl>
                                                            
			<form name="form_app" class="pure-form pure-form-aligned" method="post" action="{$form_action}">
						<xsl:choose>
							<xsl:when test="value_id != ''">
						<div class="pure-control-group">
							<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
							</label>
										<xsl:value-of select="value_id"/>
						</div>
							</xsl:when>
						</xsl:choose>
				<div class="pure-control-group">
					<label>
									<xsl:value-of select="php:function('lang', 'application')"/>
					</label>
									<select name="app" onChange="this.form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'application')"/>
										</xsl:attribute>
										<xsl:apply-templates select="apps_list"/>
									</select>
				</div>
						</form>
			<div class="pure-control-group">
				<form ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned" name="form" id="form" method="post" action="{$form_action}">
					<div class="pure-control-group">
											<input type="hidden" name="values[app]" value="{value_app}"/>
						<label>
											<xsl:value-of select="php:function('lang', 'location')"/>
						</label>
						<select name="values[location]" data-validation="required">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Select submodule')"/>
												</xsl:attribute>
												<option value="">
													<xsl:value-of select="php:function('lang', 'No location')"/>
												</option>
												<xsl:apply-templates select="location_list"/>
											</select>
					</div>
									<xsl:choose>
										<xsl:when test="value_file_name != ''">
							<div class="pure-control-group">
								<label>
													<xsl:value-of select="php:function('lang', 'filename')"/>
								</label>
													<xsl:value-of select="value_file_name"/>
							</div>
										</xsl:when>
									</xsl:choose>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'file')"/>
						</label>
											<input type="file" size="50" name="file">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'upload file')"/>
												</xsl:attribute>
											</input>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'title')"/>
						</label>
						<input type="text" data-validation="required" name="values[title]" value="{value_title}" size="60">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'title')"/>
												</xsl:attribute>
											</input>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'descr')"/>
						</label>
											<textarea cols="60" rows="10" name="values[descr]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'descr')"/>
												</xsl:attribute>
												<xsl:value-of select="value_descr"/>
											</textarea>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'format type')"/>
						</label>
						<div style="display: inline-block; vertical-align: top;">
												<xsl:apply-templates select="format_type_list"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'details')"/>
						</label>
						<div class="pure-custom">
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
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
											<xsl:value-of select="php:function('lang', 'input type')"/>
						</label>
											<select name="values[input_type]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'input type')"/>
												</xsl:attribute>
												<option value="">
													<xsl:value-of select="php:function('lang', 'input type')"/>
												</option>
												<xsl:apply-templates select="input_type_list"/>
											</select>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'input name')"/>
						</label>
											<input type="text" name="values[input_name]" value="{value_input_name}" size="12">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'input name')"/>
												</xsl:attribute>
											</input>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'is id')"/>
						</label>
											<input type="checkbox" name="values[is_id]" value="1">
											</input>
					</div>
					<div class="pure-control-group">
						<label>
											<xsl:value-of select="php:function('lang', 'private')"/>
						</label>
											<input type="checkbox" name="values[access]" value="True">
												<xsl:if test="value_access = 'private'">
													<xsl:attribute name="checked">
														<xsl:text>checked</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
					</div>
					<div class="pure-control-group">
												<xsl:variable name="lang_save">
													<xsl:value-of select="php:function('lang', 'save')"/>
												</xsl:variable>
												<xsl:variable name="lang_apply">
													<xsl:value-of select="php:function('lang', 'apply')"/>
												</xsl:variable>
												<xsl:variable name="lang_cancel">
													<xsl:value-of select="php:function('lang', 'cancel')"/>
												</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'save')"/>
															</xsl:attribute>
														</input>
						<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'apply')"/>
															</xsl:attribute>
														</input>
						<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onClick="document.cancel_form.submit();">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'cancel')"/>
															</xsl:attribute>
														</input>
                                                                                                    
					</div>
							</form>
                                            
				<xsl:variable name="form_cancel">
					<xsl:value-of select="form_cancel"/>
				</xsl:variable>
				<form name="cancel_form" id="cancel_form" method="post" action="{$form_cancel}"></form>
			</div>
		<!--  DATATABLE DEFINITIONS-->
		</div>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="format_type_list">
	<div class="pure-control-group">
		<label style="text-align: left;">
				<xsl:value-of select="name"/>
		</label>
				<input type="checkbox" name="values[formats][]" value="{id}">
					<xsl:if test="selected != 0">
						<xsl:attribute name="checked">
							<xsl:text>checked</xsl:text>
						</xsl:attribute>
					</xsl:if>
				</input>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="apps_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
</xsl:template>

<!-- New template-->
<xsl:template match="input_type_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="descr"/>
		</option>
</xsl:template>

<!-- New template-->
<xsl:template match="location_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
</xsl:template>

<!-- New template-->
<!-- user_input  -->
<xsl:template xmlns:php="http://php.net/xsl" match="user_input">
		<xsl:choose>
			<xsl:when test="lookup_functions != ''">
				<script type="text/javascript">
					self.name="first_Window";
					<xsl:value-of select="lookup_functions"/>
				</script>
			</xsl:when>
		</xsl:choose>
		<div class="yui-content">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form name="form" method="post" action="{$form_action}">
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="3">
									<xsl:call-template name="msgbox"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:for-each select="attributes">
						<tr>
							<td align="left" valign="top">
								<xsl:value-of select="input_name"/>
							</td>
							<td align="left">
								<xsl:choose>
									<xsl:when test="input_name!=''">
										<input type="hidden" name="values_attribute[{counter}][input_name]" value="{input_name}"/>
										<input type="hidden" name="values_attribute[{counter}][datatype]" value="{datatype}"/>
										<input type="hidden" name="values_attribute[{counter}][counter]" value="{counter}"/>
										<input type="hidden" name="values_attribute[{counter}][is_id]" value="{is_id}"/>
										<input type="hidden" name="values_attribute[{counter}][nullable]" value="{nullable}"/>
										<xsl:choose>
											<xsl:when test="datatype='R'">
												<xsl:call-template name="choice"/>
											</xsl:when>
											<xsl:when test="datatype='CH'">
												<xsl:call-template name="choice"/>
											</xsl:when>
											<xsl:when test="datatype='LB'">
												<select name="values_attribute[{counter}][value]" class="forms">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
													<option value="">
														<xsl:value-of select="//lang_none"/>
													</option>
													<xsl:for-each select="choice">
														<xsl:variable name="id">
															<xsl:value-of select="id"/>
														</xsl:variable>
														<xsl:choose>
															<xsl:when test="checked='checked'">
																<option value="{$id}" selected="selected">
																	<xsl:value-of disable-output-escaping="yes" select="value"/>
																</option>
															</xsl:when>
															<xsl:otherwise>
																<option value="{$id}">
																	<xsl:value-of disable-output-escaping="yes" select="value"/>
																</option>
															</xsl:otherwise>
														</xsl:choose>
													</xsl:for-each>
												</select>
											</xsl:when>
											<xsl:when test="datatype='AB'">
												<table>
													<tr>
														<td>
															<xsl:variable name="contact_name">
																<xsl:value-of select="name"/>
																<xsl:text>_name</xsl:text>
															</xsl:variable>
															<xsl:variable name="lookup_function">
																<xsl:text>lookup_</xsl:text>
																<xsl:value-of select="name"/>
																<xsl:text>();</xsl:text>
															</xsl:variable>
															<xsl:variable name="clear_function">
																<xsl:text>clear_</xsl:text>
																<xsl:value-of select="name"/>
																<xsl:text>();</xsl:text>
															</xsl:variable>
															<input type="hidden" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="5">
																<xsl:choose>
																	<xsl:when test="disabled!=''">
																		<xsl:attribute name="disabled">
																		  <xsl:text> disabled</xsl:text>
																		</xsl:attribute>
																	</xsl:when>
																</xsl:choose>
															</input>
															<input size="30" type="text" name="{$contact_name}" value="{contact_name}" onClick="{$lookup_function}" readonly="readonly">
																<xsl:choose>
																	<xsl:when test="disabled!=''">
																		<xsl:attribute name="disabled">
																		  <xsl:text> disabled</xsl:text>
																		</xsl:attribute>
																	</xsl:when>
																</xsl:choose>
															</input>
															<input type="checkbox" name="clear_{name}_box" onClick="{$clear_function}">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'delete')"/>
																</xsl:attribute>
																<xsl:attribute name="readonly">
																	<xsl:text>readonly</xsl:text>
																</xsl:attribute>
															</input>
														</td>
													</tr>
													<xsl:choose>
														<xsl:when test="contact_tel!=''">
															<tr>
																<td>
																	<xsl:value-of select="contact_tel"/>
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
												</table>
											</xsl:when>
											<xsl:when test="datatype='ABO'">
												<table>
													<tr>
														<td>
															<xsl:variable name="org_name">
																<xsl:value-of select="name"/>
																<xsl:text>_name</xsl:text>
															</xsl:variable>
															<xsl:variable name="lookup_function">
																<xsl:text>lookup_</xsl:text>
																<xsl:value-of select="name"/>
																<xsl:text>();</xsl:text>
															</xsl:variable>
															<input type="hidden" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="5">
																<xsl:choose>
																	<xsl:when test="disabled!=''">
																		<xsl:attribute name="disabled">
																		  <xsl:text> disabled</xsl:text>
																		</xsl:attribute>
																	</xsl:when>
																</xsl:choose>
															</input>
															<input size="30" type="text" name="{$org_name}" value="{org_name}" onClick="{$lookup_function}" readonly="readonly">
																<xsl:choose>
																	<xsl:when test="disabled!=''">
																		<xsl:attribute name="disabled">
																		  <xsl:text> disabled</xsl:text>
																		</xsl:attribute>
																	</xsl:when>
																</xsl:choose>
															</input>
														</td>
													</tr>
												</table>
											</xsl:when>
											<xsl:when test="datatype='VENDOR'">
												<xsl:variable name="vendor_name">
													<xsl:value-of select="name"/>
													<xsl:text>_org_name</xsl:text>
												</xsl:variable>
												<xsl:variable name="lookup_function">
													<xsl:text>lookup_</xsl:text>
													<xsl:value-of select="name"/>
													<xsl:text>();</xsl:text>
												</xsl:variable>
												<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
												<input size="30" type="text" name="{$vendor_name}" value="{vendor_name}" onClick="{$lookup_function}" readonly="readonly">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
											</xsl:when>
											<xsl:when test="datatype='user'">
												<xsl:variable name="user_name">
													<xsl:value-of select="name"/>
													<xsl:text>_user_name</xsl:text>
												</xsl:variable>
												<xsl:variable name="lookup_function">
													<xsl:text>lookup_</xsl:text>
													<xsl:value-of select="name"/>
													<xsl:text>();</xsl:text>
												</xsl:variable>
												<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
												<input size="30" type="text" name="{$user_name}" value="{user_name}" onClick="{$lookup_function}" readonly="readonly">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
											</xsl:when>
											<xsl:when test="datatype='date'">
												<input type="text" id="values_attribute_{counter}" name="values_attribute[{counter}][value]" value="{value}" size="12" maxlength="12">
													<xsl:attribute name="readonly">
														<xsl:text> readonly</xsl:text>
													</xsl:attribute>
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
											</xsl:when>
											<xsl:when test="datatype='timestamp'">
												<input type="text" id="values_attribute_{counter}" name="values_attribute[{counter}][value]" value="{value}" size="12" maxlength="12">
													<xsl:attribute name="readonly">
														<xsl:text> readonly</xsl:text>
													</xsl:attribute>
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
											</xsl:when>
											<xsl:otherwise>
												<input type="text" name="values_attribute[{counter}][value]" value="{value}" size="30">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:when>
								</xsl:choose>
							</td>
						</tr>
					</xsl:for-each>
					<tr>
						<td align="left" valign="top">
							<xsl:value-of select="php:function('lang', 'format type')"/>
						</td>
						<td>
							<select name="sel_format">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select format')"/>
								</xsl:attribute>
								<xsl:apply-templates select="formats"/>
							</select>
						</td>
					</tr>
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<tr height="50">
						<td align="left">
							<input type="submit" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'save')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</table>
			</form>
		</div>
</xsl:template>

<!-- New template-->
<xsl:template match="formats">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
</xsl:template>
