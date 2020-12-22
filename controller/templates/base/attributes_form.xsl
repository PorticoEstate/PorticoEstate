
<!-- $Id$ -->
<xsl:template name="attributes_form">
	<xsl:apply-templates select="attributes_values"/>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="attributes_values">
	<xsl:param name="template_set" />
	<script type="text/javascript">
		help_Popup = function(requestUrl)
		{
		TINY.box.show({iframe:requestUrl, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		};
	</script>
	<xsl:variable name="lang_hour">
		<xsl:value-of select="php:function('lang', 'hour')" />
	</xsl:variable>
	<xsl:variable name="lang_min">
		<xsl:value-of select="php:function('lang', 'minute')" />
	</xsl:variable>
	<xsl:variable name="statustext">
		<xsl:value-of select="statustext"/>
	</xsl:variable>

	<xsl:variable name="textareacols">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|property|textareacols')" />
	</xsl:variable>
	<xsl:variable name="textarearows">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|property|textarearows')" />
	</xsl:variable>

	<xsl:variable name="supress_history_date">
		<xsl:value-of select="supress_history_date"/>
	</xsl:variable>

	<xsl:for-each select="attributes_group">
		<div id="{link}">
			<fieldset>
				<xsl:apply-templates select="attributes">
					<xsl:with-param name="lang_hour">
						<xsl:value-of select="$lang_hour" />
					</xsl:with-param>
					<xsl:with-param name="lang_min">
						<xsl:value-of select="$lang_min" />
					</xsl:with-param>
					<xsl:with-param name="statustext">
						<xsl:value-of select="$statustext" />
					</xsl:with-param>
					<xsl:with-param name="textareacols">
						<xsl:value-of select="$textareacols" />
					</xsl:with-param>
					<xsl:with-param name="textarearows">
						<xsl:value-of select="$textarearows" />
					</xsl:with-param>
					<xsl:with-param name="supress_history_date">
						<xsl:value-of select="$supress_history_date" />
					</xsl:with-param>
					<xsl:with-param name="template_set">
						<xsl:value-of select="$template_set" />
					</xsl:with-param>
				</xsl:apply-templates>
			</fieldset>
		</div>
	</xsl:for-each>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="attributes">
	<xsl:param name="lang_hour" />
	<xsl:param name="lang_min" />
	<xsl:param name="statustext" />
	<xsl:param name="textareacols" />
	<xsl:param name="textarearows" />
	<xsl:param name="supress_history_date" />
	<xsl:param name="template_set" />

	<xsl:variable name="form_class">
		<xsl:choose>
			<xsl:when test="$template_set = 'bootstrap' and (datatype='CH' or datatype='R')">
				<xsl:text>pure-control-group</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>pure-control-group</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:choose>
		<xsl:when test="datatype='section'">
			<div class="{$form_class}">
				<xsl:value-of select="descr" disable-output-escaping="yes"/>
			</div>
		</xsl:when>
	</xsl:choose>

	<div class="{$form_class}">
		<xsl:choose>
			<xsl:when test="not(hide_row)">
				<label id="label_{name}">
					<xsl:choose>
						<xsl:when test="helpmsg=1">
							<xsl:variable name="help_url">
								<xsl:value-of select="help_url"/>
							</xsl:variable>
							<a href="javascript:help_Popup('{$help_url}');">
								<xsl:text>[</xsl:text>
								<xsl:value-of select="input_text"/>
								<xsl:text>]</xsl:text>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="input_text" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="datatype='pwd'">
							<br/>
							<xsl:text>[ </xsl:text>
							<xsl:choose>
								<xsl:when test="value!=''">
									<xsl:value-of select="php:function('lang', 'edit')"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('lang', 'add')"/>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:text> ]</xsl:text>
						</xsl:when>
					</xsl:choose>
				</label>
			</xsl:when>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="name!=''">
				<input type="hidden" name="values_attribute[{id}][name]" value="{name}"/>
				<input type="hidden" name="values_attribute[{id}][datatype]" value="{datatype}"/>
				<input type="hidden" name="values_attribute[{id}][precision]" value="{precision}"/>
				<input type="hidden" name="values_attribute[{id}][history]" value="{history}"/>
				<input type="hidden" name="values_attribute[{id}][attrib_id]" value="{id}"/>
				<input type="hidden" name="values_attribute[{id}][nullable]" value="{nullable}"/>
				<input type="hidden" name="values_attribute[{id}][input_text]" value="{input_text}"/>
				<input type="hidden" name="values_attribute[{id}][disabled]" value="{disabled}"/>
				<xsl:choose>
					<xsl:when test="datatype='R'">
						<xsl:call-template name="choice"/>
					</xsl:when>
					<xsl:when test="datatype='CH'">
						<xsl:call-template name="choice"/>
					</xsl:when>
					<xsl:when test="datatype='LB'">
						<select id="id_{name}" name="values_attribute[{id}][value]" title="{$statustext}" class="pure-input-1" >
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:for-each select="choice">
								<xsl:sort select="value"/>
								<option value="{id}">
									<xsl:if test="checked='checked'">
										<xsl:attribute name="selected" value="selected"/>
									</xsl:if>
									<xsl:value-of disable-output-escaping="yes" select="value"/>
								</option>
							</xsl:for-each>
						</select>
						<br/>
						<a id="add_new_value_{name}" href="#" onClick="addNewValueToCustomAttribute('id_{name}', {location_id}, {id}, '{input_text}', '{lang_new_value}');">
							<img src="{add_img}" width="23"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'add')"/>
							<xsl:text> (</xsl:text>
							<xsl:value-of select="input_text"/>
							<xsl:text>)</xsl:text>
						</a>
						<br/>
						<a id="delete_value_{name}" href="#" onClick="deleteValueFromCustomAttribute('id_{name}', {location_id}, {id});">
							<img src="{delete_img}" width="23"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'delete')"/>
							<xsl:text> (</xsl:text>
							<xsl:value-of select="input_text"/>
							<xsl:text>)</xsl:text>
						</a>
					</xsl:when>
					<xsl:when test="datatype='AB'">
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
						<xsl:choose>
							<xsl:when test="contact_tel!=''">
								<xsl:value-of select="contact_tel"/>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="contact_email!=''">
								<a href="mailto:{contact_email}">
									<xsl:value-of select="contact_email"/>
								</a>
							</xsl:when>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="datatype='ABO'">
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
						<xsl:choose>
							<xsl:when test="org_tel!=''">
								<xsl:value-of select="org_tel"/>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="org_email!=''">
								<a href="mailto:{org_email}">
									<xsl:value-of select="org_email"/>
								</a>
							</xsl:when>
						</xsl:choose>
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
						<input type="text" id="{name}" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input size="30" type="text" id="{$vendor_name}" name="{$vendor_name}" value="{vendor_name}" onClick="{$lookup_function}" readonly="readonly">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='custom1'">
						<select name="values_attribute[{id}][value]">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:for-each select="choice">
								<xsl:variable name="id">
									<xsl:value-of select="id"/>
								</xsl:variable>
								<xsl:choose>
									<xsl:when test="selected='1'">
										<option value="{$id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{$id}">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</select>
					</xsl:when>
					<xsl:when test="datatype='custom2'">
						<xsl:variable name="custom_name">
							<xsl:value-of select="name"/>
							<xsl:text>_name</xsl:text>
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
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input size="30" type="text" name="{$custom_name}" value="{custom_name}" onClick="{$lookup_function}" readonly="readonly">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='custom3'">
						<xsl:variable name="custom_id">
							<xsl:value-of select="name"/>
							<xsl:text>_id</xsl:text>
						</xsl:variable>

						<xsl:variable name="custom_name">
							<xsl:value-of select="name"/>
							<xsl:text>_name</xsl:text>
						</xsl:variable>

						<xsl:variable name="custom_container">
							<xsl:value-of select="name"/>
							<xsl:text>_container</xsl:text>
						</xsl:variable>

						<input id="{$custom_id}" name="values_attribute[{id}][value]" type="hidden" value="{value}">
							<xsl:choose>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input id="{$custom_name}" name="{$custom_name}" type="text" value="{custom_name}">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<div id="{$custom_container}"/>
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
						<input type="text" id="{name}" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input size="30" type="text" id="{$user_name}" name="{$user_name}" value="{user_name}" onClick="{$lookup_function}" readonly="readonly">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='D'">
						<input type="text" id="values_attribute_{id}" name="values_attribute[{id}][value]" value="{value}" size="12" maxlength="12">
							<xsl:attribute name="readonly">
								<xsl:text> readonly</xsl:text>
							</xsl:attribute>
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='DT'">
						<xsl:variable name="clear_function">
							<xsl:text>clear_</xsl:text>
							<xsl:value-of select="name"/>
							<xsl:text>();</xsl:text>
						</xsl:variable>

						<table>
							<tr>
								<td>
									<input type="text" id="values_attribute_{id}" name="values_attribute[{id}][value][date]" value="{value/date}" size="12" maxlength="12">
										<xsl:attribute name="readonly">
											<xsl:text> readonly</xsl:text>
										</xsl:attribute>
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
											<xsl:when test="nullable!='1'">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
								</td>
								<td>
									<input type="text" id="values_attribute_{id}_hour" name="values_attribute[{id}][value][hour]" value="{value/hour}" size="2" maxlength="2" title="{$lang_hour}">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
								</td>
								<td>
									<xsl:text> : </xsl:text>
								</td>
								<td>
									<input type="text" id="values_attribute_{id}_min" name="values_attribute[{id}][value][min]" value="{value/min}" size="2" maxlength="2" title="{$lang_min}">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
								</td>
								<td>
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
						</table>
					</xsl:when>
					<xsl:when test="datatype='T'">
						<textarea id="id_{name}"  name="values_attribute[{id}][value]">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
							<xsl:attribute name="cols">
								<xsl:choose>
									<xsl:when test="$textareacols!=''">
										<xsl:value-of select="$textareacols"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:text>60</xsl:text>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:attribute name="rows">
								<xsl:choose>
									<xsl:when test="$textarearows!=''">
										<xsl:value-of select="$textarearows"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:text>6</xsl:text>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:value-of select="value"/>
						</textarea>
					</xsl:when>
					<xsl:when test="datatype='pwd'">
						<input type="password" name="values_attribute[{id}][value]" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input type="password" name="values_attribute[{id}][value2]" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='bolean'">
						<input id="id_{name}" type="checkbox" name="values_attribute[{id}][value]" value="1">
							<xsl:choose>
								<xsl:when test="value!=''">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='link'">
						<input type="text" name="values_attribute[{id}][value]" value="{value}" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
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
					<xsl:when test="datatype='event'">
						<xsl:choose>
							<xsl:when test="warning!=''">
								<xsl:value-of select="warning"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="event_descr">
									<xsl:value-of select="name"/>
									<xsl:text>_descr</xsl:text>
								</xsl:variable>
								<xsl:variable name="lookup_function">
									<xsl:text>lookup_</xsl:text>
									<xsl:value-of select="name"/>
									<xsl:text>();</xsl:text>
								</xsl:variable>
								<div class="pure-custom">
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6"/>
									<input size="30" type="text" name="{$event_descr}" value="{descr}" onClick="{$lookup_function}" readonly="readonly">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
									<xsl:choose>
										<xsl:when test="next!=''">
											<div>
												<xsl:value-of select="lang_next_run"/>
												<xsl:text>: </xsl:text>
												<xsl:value-of select="next"/>
											</div>
											<div>
												<xsl:value-of select="lang_enabled"/>
												<xsl:text>: </xsl:text>
												<xsl:value-of select="enabled"/>
											</div>
										</xsl:when>
									</xsl:choose>
								</div>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="datatype='I'">
						<input data-validation="number" data-validation-allowing="negative" id="id_{name}" type="text" name="values_attribute[{id}][value]" value="{value}" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable='1'">
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='N'">
						<input data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="." id="id_{name}" type="text" name="values_attribute[{id}][value]" value="{value}" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable='1'">
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:when test="datatype='email'">
						<input data-validation="email" id="id_{name}" type="text" name="values_attribute[{id}][value]" value="{value}" size="30">
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable='1'">
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:when>
					<xsl:otherwise>
						<input id="id_{name}" type="text" name="values_attribute[{id}][value]" size="30">
							<xsl:attribute name="value">
								<xsl:choose>
									<xsl:when test="value!=''">
										<xsl:value-of select="value"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="default_value"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
							<xsl:choose>
								<xsl:when test="disabled!=''">
									<xsl:attribute name="disabled">
										<xsl:text> disabled</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:when test="nullable!='1'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="history=1 and $supress_history_date !=1">
						<input type="text" id="values_attribute_{id}_date" name="values_attribute[{id}][date]" value="" size="12" maxlength="10" readonly="readonly">
						</input>
						<xsl:variable name="link_history">
							<xsl:value-of select="link_history"/>
						</xsl:variable>
						<xsl:variable name="lang_history_help">
							<xsl:value-of select="//lang_history_help"/>
						</xsl:variable>
						<xsl:variable name="lang_history">
							<xsl:value-of select="php:function('lang', 'history')" />
						</xsl:variable>
						<a href="javascript:JqueryPortico.showlightbox_history('{$link_history}')" title="{$lang_history}">
							<xsl:value-of select="$lang_history"/>
						</a>
					</xsl:when>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template name="choice">
	<xsl:variable name="attrib_id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:variable name="name">
		<xsl:value-of select="name"/>
	</xsl:variable>
	<div class="pure-custom">
		<xsl:for-each select="choice">
			<xsl:choose>
				<xsl:when test="checked='checked'">
					<input id="id_{$name}_{id}" type="{input_type}" name="values_attribute[{$attrib_id}][value][]" value="{id}" checked="checked"/>
				</xsl:when>
				<xsl:otherwise>
					<input id="id_{$name}_{id}" type="{input_type}" name="values_attribute[{$attrib_id}][value][]" value="{id}"/>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:value-of select="value"/>
			<br></br>
		</xsl:for-each>
	</div>
</xsl:template>
