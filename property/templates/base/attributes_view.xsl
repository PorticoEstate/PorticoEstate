
<!-- $Id$ -->
<xsl:template name="attributes_view">
	<xsl:apply-templates select="attributes_values"/>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="attributes_values">
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

	<xsl:choose>
		<xsl:when test="datatype='section'">
			<div class="pure-control-group">
				<xsl:value-of select="descr" disable-output-escaping="yes"/>
			</div>
		</xsl:when>
	</xsl:choose>

	<div class="pure-control-group">
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
							<xsl:value-of select="input_text"/>
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
						<xsl:for-each select="choice">
							<xsl:choose>
								<xsl:when test="checked='checked'">
									<xsl:value-of disable-output-escaping="yes" select="value"/>
								</xsl:when>
							</xsl:choose>
						</xsl:for-each>
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
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
						<xsl:value-of select="vendor_name"/>
					</xsl:when>
					<xsl:when test="datatype='custom1'">
						<xsl:for-each select="choice">
							<xsl:choose>
								<xsl:when test="checked='checked'">
									<xsl:value-of disable-output-escaping="yes" select="value"/>
								</xsl:when>
							</xsl:choose>
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="datatype='custom2'">
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
						<xsl:value-of select="custom_name"/>
					</xsl:when>
					<xsl:when test="datatype='custom3'">
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
						<xsl:value-of select="custom_name"/>
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
						<xsl:value-of select="value"/>
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
						<xsl:value-of disable-output-escaping="yes" select="value"/>
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
						<xsl:choose>
							<xsl:when test="value!=''">
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
						<xsl:value-of select="value"/>
					</xsl:when>
					<xsl:when test="datatype='N'">
						<xsl:value-of select="value"/>
					</xsl:when>
					<xsl:when test="datatype='email'">
						<xsl:value-of select="value"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="value"/>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="history=1">
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
			<input id="id_{$name}_{id}" type="{input_type}" name="values_attribute[{$attrib_id}][value][]" value="{id}">
				<xsl:attribute name="disabled">
					<xsl:text>disabled</xsl:text>
				</xsl:attribute>
				<xsl:if test="checked='checked'">
					<xsl:attribute name="checked">
						<xsl:text> checked</xsl:text>
					</xsl:attribute>
				</xsl:if>
			</input>
			<xsl:value-of select="value"/>
			<br></br>
		</xsl:for-each>
	</div>
</xsl:template>
