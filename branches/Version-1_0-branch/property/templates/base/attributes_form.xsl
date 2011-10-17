<!-- $Id$ -->
	<xsl:template name="attributes_form">
		<xsl:apply-templates select="attributes_values"/>
	</xsl:template>


	<xsl:template name="attributes_values" xmlns:php="http://php.net/xsl">
		<xsl:for-each select="attributes_group" >
			<div id="{link}">
				<table cellpadding="2" cellspacing="2" width="100%" align="center" border="0">
					<xsl:apply-templates select="attributes"/>
				</table>
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="attributes" xmlns:php="http://php.net/xsl">
			<xsl:variable name="statustext"><xsl:value-of select="statustext"/></xsl:variable>
			<tr>
				<td align="left" width="19%" valign="top" title="{$statustext}">
					<xsl:choose>
						<xsl:when test="helpmsg=1">
							<xsl:variable name="help_url"><xsl:value-of select="help_url"/></xsl:variable>
							<a href="javascript:var w=window.open('{$help_url}','','left=50,top=100,width=550,height=400,scrollbars')">
								<xsl:text>[</xsl:text><xsl:value-of select="input_text"/><xsl:text>]</xsl:text>
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
									<xsl:value-of select="php:function('lang', 'edit')" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('lang', 'add')" />
								</xsl:otherwise>
							</xsl:choose>
							<xsl:text> ]</xsl:text>
						</xsl:when>
					</xsl:choose>
				</td>
				<td align="left">
					<xsl:choose>
						<xsl:when test="name!=''">
							<input type="hidden" name="values_attribute[{counter}][name]" value="{name}"></input>
							<input type="hidden" name="values_attribute[{counter}][datatype]" value="{datatype}"></input>
							<input type="hidden" name="values_attribute[{counter}][history]" value="{history}"></input>
							<input type="hidden" name="values_attribute[{counter}][attrib_id]" value="{id}"></input>
							<input type="hidden" name="values_attribute[{counter}][nullable]" value="{nullable}"></input>
							<input type="hidden" name="values_attribute[{counter}][input_text]" value="{input_text}"></input>
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
											<xsl:value-of select="php:function('lang', 'select')" />
										</option>
										<xsl:for-each select="choice">
											<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
											<xsl:choose>
												<xsl:when test="checked='checked'">
													<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="value"/></option>
												</xsl:when>
												<xsl:otherwise>
													<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="value"/></option>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</select>
								</xsl:when>
								<xsl:when test="datatype='AB'">
									<table>
										<tr>
											<td>
												<xsl:variable name="contact_name"><xsl:value-of select="name"/><xsl:text>_name</xsl:text></xsl:variable>
												<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
												<xsl:variable name="clear_function"><xsl:text>clear_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
												<input type="hidden" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="5">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
												<input  size="30" type="text" name="{$contact_name}" value="{contact_name}"  onClick="{$lookup_function}" readonly="readonly"> 
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
												<input type="checkbox" name="clear_{name}_box"  onClick="{$clear_function}" >
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'delete')" />
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
														<a href="mailto:{contact_email}"><xsl:value-of select="contact_email"/></a>
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
												<xsl:variable name="org_name"><xsl:value-of select="name"/><xsl:text>_name</xsl:text></xsl:variable>
												<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
												<input type="hidden" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="5">
													<xsl:choose>
														<xsl:when test="disabled!=''">
															<xsl:attribute name="disabled">
																<xsl:text> disabled</xsl:text>
															</xsl:attribute>
														</xsl:when>
													</xsl:choose>
												</input>
												<input  size="30" type="text" name="{$org_name}" value="{org_name}"  onClick="{$lookup_function}" readonly="readonly"> 
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
										<xsl:choose>
											<xsl:when test="org_tel!=''">
												<tr>
													<td>
														<xsl:value-of select="org_tel"/>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>
										<xsl:choose>
											<xsl:when test="org_email!=''">
												<tr>
													<td>
														<a href="mailto:{org_email}"><xsl:value-of select="org_email"/></a>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>
									</table>
								</xsl:when>
								<xsl:when test="datatype='VENDOR'">
									<xsl:variable name="vendor_name"><xsl:value-of select="name"/><xsl:text>_org_name</xsl:text></xsl:variable>
									<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
									<input  size="30" type="text" name="{$vendor_name}" value="{vendor_name}"  onClick="{$lookup_function}" readonly="readonly"> 
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
									<select name="values_attribute[{counter}][value]" class="forms">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
										<option value="">
											<xsl:value-of select="php:function('lang', 'select')" />
										</option>
										<xsl:for-each select="choice">
											<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
											<xsl:choose>
												<xsl:when test="selected='1'">
													<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
												</xsl:when>
												<xsl:otherwise>
													<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</select>
								</xsl:when>
								<xsl:when test="datatype='custom2'">
									<xsl:variable name="custom_name"><xsl:value-of select="name"/><xsl:text>_name</xsl:text></xsl:variable>
									<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
									<input  size="30" type="text" name="{$custom_name}" value="{custom_name}"  onClick="{$lookup_function}" readonly="readonly"> 
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
									<xsl:variable name="user_name"><xsl:value-of select="name"/><xsl:text>_user_name</xsl:text></xsl:variable>
									<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
									<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
									<input  size="30" type="text" name="{$user_name}" value="{user_name}"  onClick="{$lookup_function}" readonly="readonly"> 
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
									<input type="text" id="values_attribute_{counter}" name="values_attribute[{counter}][value]" value="{value}"  size="12" maxlength="12" >
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
									<img id="values_attribute_{counter}-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
								</xsl:when>
								<xsl:when test="datatype='T'">
									<textarea cols="{//textareacols}" rows="{//textarearows}" name="values_attribute[{counter}][value]" >
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
										<xsl:value-of select="value"/>
									</textarea>
								</xsl:when>
								<xsl:when test="datatype='pwd'">
									<table>
										<tr>
											<td>
												<input type="password" name="values_attribute[{counter}][value]"  size="30">
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
										<tr>
											<td>
												<input type="password" name="values_attribute[{counter}][value2]"  size="30">
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
								<xsl:when test="datatype='bolean'">
									<input type="checkbox" name="values_attribute[{counter}][value]" value="1">
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
									<input type="text" name="values_attribute[{counter}][value]" value="{value}" size="30">
										<xsl:choose>
											<xsl:when test="disabled!=''">
												<xsl:attribute name="disabled">
													<xsl:text> disabled</xsl:text>
												</xsl:attribute>
											</xsl:when>
										</xsl:choose>
									</input>
									<xsl:choose>
										<xsl:when test="value!=''">
											<br/><a href="{value}" target="_blank"><xsl:value-of select="value"/></a>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
								<xsl:when test="datatype='event'">
									<xsl:choose>
										<xsl:when test="warning!=''">
											<xsl:value-of select="warning"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:variable name="event_descr"><xsl:value-of select="name"/><xsl:text>_descr</xsl:text></xsl:variable>
											<xsl:variable name="lookup_function"><xsl:text>lookup_</xsl:text><xsl:value-of select="name"/><xsl:text>();</xsl:text></xsl:variable>
											<table>
												<tr>
													<td>
														<input type="text" name="{name}" value="{value}" onClick="{$lookup_function}" readonly="readonly" size="6"></input>
														<input  size="30" type="text" name="{$event_descr}" value="{descr}"  onClick="{$lookup_function}" readonly="readonly"> 
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
												<xsl:choose>
													<xsl:when test="next!=''">
														<tr>
															<td>
																<xsl:value-of select="lang_next_run"/>
																<xsl:text>: </xsl:text>
																<xsl:value-of select="next"/>
															</td>
														</tr>
														<tr>
															<td>
																<xsl:value-of select="lang_enabled"/>
																<xsl:text>: </xsl:text>
																<xsl:value-of select="enabled"/>
															</td>
														</tr>
													</xsl:when>
												</xsl:choose>
											</table>
										</xsl:otherwise>
									</xsl:choose>
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
							<xsl:choose>
								<xsl:when test="history=1">
									<input type="text" name="values_attribute[{counter}][date]" value="" onFocus="{//dateformat_validate}" onKeyUp="{//onKeyUp}" onBlur="{//onBlur}" size="12" maxlength="10" >
									</input>
									<xsl:variable name="link_history"><xsl:value-of select="link_history"/></xsl:variable>
									<xsl:variable name="lang_history_help"><xsl:value-of select="//lang_history_help"/></xsl:variable>
									<xsl:variable name="lang_history"><xsl:value-of select="//lang_history"/></xsl:variable>
									<a href="javascript:var w=window.open('{$link_history}','','left=50,top=100,width=550,height=400,scrollbars')"
										title="{$lang_history_help}">
										<xsl:value-of select="//lang_history"/></a>
								</xsl:when>
							</xsl:choose>
						</xsl:when>
					</xsl:choose>
				</td>
			</tr>
	</xsl:template>

	<xsl:template name="choice">
		<xsl:variable name="counter"><xsl:value-of select="counter"/></xsl:variable>
		<table cellpadding="2" cellspacing="2" width="50%" align="left">
			<xsl:for-each select="choice" >
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
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
					</td>
					<td align="left">
						<xsl:choose>
							<xsl:when test="checked='checked'">
								<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}" checked="checked"></input>
							</xsl:when>
							<xsl:otherwise>
								<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}"></input>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
