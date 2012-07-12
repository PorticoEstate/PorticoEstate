  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<xsl:call-template name="msgbox"/>
			</xsl:when>
		</xsl:choose>
		<div class="yui-navset" id="general_edit_tabview">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form method="post" action="{$form_action}">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="79%" align="center">
							<xsl:choose>
								<xsl:when test="id_type != 'auto'">
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'id')"/>
										</td>
										<td>
											<xsl:choose>
												<xsl:when test="value_id != ''">
													<xsl:value-of select="value_id"/>
												</xsl:when>
												<xsl:otherwise>
													<input type="text" name="values[{id_name}]" value="{value_id}">
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'Enter the ID')"/>
														</xsl:attribute>
													</input>
												</xsl:otherwise>
											</xsl:choose>
										</td>
									</tr>
								</xsl:when>
								<xsl:otherwise>
									<xsl:choose>
										<xsl:when test="value_id != ''">
											<tr>
												<td valign="top">
													<xsl:value-of select="php:function('lang', 'id')"/>
												</td>
												<td>
													<xsl:value-of select="value_id"/>
												</td>
											</tr>
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
								<xsl:variable name="name">
									<xsl:value-of select="name"/>
								</xsl:variable>
								<tr>
									<td align="left" width="19%" valign="top" title="{descr}">
										<xsl:value-of select="descr"/>
									</td>
									<td align="left">
										<xsl:choose>
											<xsl:when test="type='text'">
												<textarea cols="{//textareacols}" rows="{//textarearows}" name="values[{name}]">
													<xsl:value-of select="value"/>
												</textarea>
											</xsl:when>
											<xsl:when test="type='varchar' or type='integer' or type='int'">
												<input type="text" name="values[{name}]" value="{value}" size="{size}">
													<xsl:attribute name="title">
														<xsl:value-of select="descr"/>
													</xsl:attribute>
												</input>
											</xsl:when>
											<xsl:when test="type='checkbox'">
												<xsl:choose>
													<xsl:when test="value = 1">
														<input type="checkbox" name="values[{name}]" value="1" checked="checked">
															<xsl:attribute name="title">
																<xsl:value-of select="descr"/>
															</xsl:attribute>
														</input>
													</xsl:when>
													<xsl:otherwise>
														<input type="checkbox" name="values[{name}]" value="1">
															<xsl:attribute name="title">
																<xsl:value-of select="descr"/>
															</xsl:attribute>
														</input>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:when>
											<xsl:when test="type='select'">
												<select name="values[{name}]">
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
												<select name="values[{name}][]" multiple="multiple">
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
												<input type="text" name="values[{name}]" value="{value}" size="30">
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
														<br/>
														<a href="{value}" target="_blank">
															<xsl:value-of select="value"/>
														</a>
													</xsl:when>
												</xsl:choose>
											</xsl:when>
										</xsl:choose>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</div>
					<xsl:call-template name="attributes_values"/>
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<tr height="50">
							<td valign="bottom">
								<input type="submit" name="values[save]" value="{lang_save}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Save the record and return to the list')"/>
									</xsl:attribute>
								</input>
							</td>
							<td valign="bottom">
								<input type="submit" name="values[apply]" value="{lang_apply}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Apply the values')"/>
									</xsl:attribute>
								</input>
							</td>
							<td align="right" valign="bottom">
								<input type="submit" name="values[cancel]" value="{lang_cancel}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Leave the record untouched and return to the list')"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</xsl:template>
