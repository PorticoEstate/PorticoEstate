
<!-- $Id$ -->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data">
	<xsl:apply-templates select="edit" />
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>

	<div id="generic_edit_tabview">
			
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
			
		<xsl:value-of select="validator"/>
	
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="generic">
					<fieldset>
						<xsl:choose>
							<xsl:when test="id_type != 'auto'">
								<div class="pure-control-group">
									<label for="name">
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<xsl:choose>
										<xsl:when test="value_id != ''">
											<xsl:value-of select="value_id"/>
										</xsl:when>
										<xsl:otherwise>
											<input type="text" name="values[{id_name}]" value="{value_id}">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Enter the ID')"/>
												</xsl:attribute>
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:if test="id_type != 'varchar'">
													<xsl:attribute name="data-validation">
														<xsl:text>number</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
										</xsl:otherwise>
									</xsl:choose>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:choose>
									<xsl:when test="value_id != ''">
										<div class="pure-control-group">
											<label for="name">
												<xsl:value-of select="php:function('lang', 'id')"/>
											</label>
											<xsl:value-of select="value_id"/>
										</div>
									</xsl:when>
								</xsl:choose>
							</xsl:otherwise>
						</xsl:choose>
							
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<input type="hidden" name="{id_name}" value="{value_id}">
								</input>
							</xsl:when>
						</xsl:choose>
							
						<xsl:for-each select="fields">
								
							<xsl:variable name="descr">
								<xsl:value-of select="descr"/>
							</xsl:variable>
																				
							<div class="pure-control-group">
								<label for="{name}">
									<xsl:value-of select="$descr"/>
								</label>
								<xsl:choose>
									<xsl:when test="type='text' or type='html'">
										<textarea cols="{//textareacols}" rows="{//textarearows}" name="values[{name}]" id="{name}" class="pure-input-1-2" >
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
											<xsl:value-of select="value"/>
										</textarea>
									</xsl:when>
									<xsl:when test="type='varchar'">
										<input type="text" name="values[{name}]" value="{value}" size="{size}" class="pure-input-1-2" >
											<xsl:attribute name="title">
												<xsl:value-of select="$descr"/>
											</xsl:attribute>
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
									</xsl:when>
									<xsl:when test="type='date'">
										<input type="text" id="{name}"  name="values[{name}]" value="{value}" size="12" maxlength="12" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="$descr"/>
											</xsl:attribute>
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
									</xsl:when>

									<xsl:when test="type='location'">
										<input type="hidden" id="location_code" name="values[{name}]" value="{value}" />
										<input type="text"  id="location_name" name="location_name" value="{value}" class="pure-input-1-2" >
											<xsl:attribute name="title">
												<xsl:value-of select="$descr"/>
											</xsl:attribute>
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
										<div id="location_container"/>
									</xsl:when>


									<xsl:when test="type='integer' or type='int'">
										<input data-validation="number" type="text" id="{name}" name="values[{name}]" value="{value}" size="{size}">
											<xsl:attribute name="title">
												<xsl:value-of select="$descr"/>
											</xsl:attribute>
											<xsl:choose>
												<xsl:when test="nullable='1'">
													<xsl:attribute name="data-validation-optional">
														<xsl:text>true</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
									</xsl:when>
									<xsl:when test="type='numeric'">
										<input data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="." type="text" id="{name}" name="values[{name}]" value="{value}" size="{size}">
											<xsl:attribute name="title">
												<xsl:value-of select="$descr"/>
											</xsl:attribute>
											<xsl:choose>
												<xsl:when test="nullable='1'">
													<xsl:attribute name="data-validation-optional">
														<xsl:text>true</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
									</xsl:when>
									<xsl:when test="type='checkbox'">
										<xsl:choose>
											<xsl:when test="value = 1">
												<input type="checkbox" id="{name}" name="values[{name}]" value="1" checked="checked">
													<xsl:attribute name="title">
														<xsl:value-of select="$descr"/>
													</xsl:attribute>
												</input>
											</xsl:when>
											<xsl:otherwise>
												<input type="checkbox" id="{name}" name="values[{name}]" value="1">
													<xsl:attribute name="title">
														<xsl:value-of select="$descr"/>
													</xsl:attribute>
												</input>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:when>
									<xsl:when test="type='select'">
										<select id="{name}" name="values[{name}]" class="pure-input-1-2" >
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
											<option value="">
												<xsl:value-of select="php:function('lang', 'select value')"/>
											</option>
											<xsl:for-each select="valueset">
												<option value="{id}">
													<xsl:if test="selected != 0">
														<xsl:attribute name="selected" value="selected"/>
													</xsl:if>
													<xsl:value-of select="name"/>
												</option>
											</xsl:for-each>
										</select>
									</xsl:when>
									<xsl:when test="type='multiple_select'">
										<select id="{name}" name="values[{name}][]" multiple="multiple" class="pure-input-1-2" >
											<xsl:choose>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
											<xsl:for-each select="valueset">
												<option value="{id}">
													<xsl:if test="selected != 0">
														<xsl:attribute name="selected" value="selected"/>
													</xsl:if>
													<xsl:value-of select="name"/>
												</option>
											</xsl:for-each>
										</select>
									</xsl:when>
									<xsl:when test="type='link'">
										<input type="text" name="values[{name}]" value="{value}" size="30" class="pure-input-1-2" >
											<xsl:choose>
												<xsl:when test="disabled!=''">
													<xsl:attribute name="disabled">
														<xsl:text> disabled</xsl:text>
													</xsl:attribute>
												</xsl:when>
												<xsl:when test="nullable!='1' or required=1">
													<xsl:attribute name="data-validation">
														<xsl:text>required</xsl:text>
													</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
										<xsl:choose>
											<xsl:when test="value!=''">
												<br/>
												<a href="{value}" target="_blank">
													<xsl:value-of select="value"/>
												</a>
											</xsl:when>
										</xsl:choose>
									</xsl:when>
								</xsl:choose>
									
							</div>
						</xsl:for-each>
						<xsl:call-template name="attributes_values"/>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Save the record and return to the list')"/>
					</xsl:attribute>
				</input>
				<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{lang_apply}" onMouseout="window.status='';return true;">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Apply the values')"/>
					</xsl:attribute>
				</input>
				<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Leave the record untouched and return to the list')"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
		</form>
	</div>
	<script type="text/javascript">
		var oArgs = {menuaction: 'property.bolocation.get_locations'};
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.autocompleteHelper(strURL, 'location_name', 'location_code', 'location_container');
	</script>

</xsl:template>
