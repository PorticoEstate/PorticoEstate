<!-- $Id$ -->

<xsl:template name="attributes_view">
	<xsl:apply-templates select="attributes_values"/>
</xsl:template>

<xsl:template match="attributes_values">
	<xsl:variable name="lang_attribute_statustext">
		<xsl:value-of select="lang_attribute_statustext"/>
	</xsl:variable>
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

		<td align="left" valign="top">
			<xsl:value-of select="input_text"/>
			<xsl:choose>
				<xsl:when test="datatype='D'">
					<xsl:text>[</xsl:text>
					<xsl:value-of select="//lang_dateformat"/>
					<xsl:text>]</xsl:text>
				</xsl:when>
			</xsl:choose>
		</td>
		<td align="left">
			<xsl:choose>
				<xsl:when test="name!=''">
					<input type="hidden" name="values_attribute[{counter}][name]" value="{name}"></input>
					<input type="hidden" name="values_attribute[{counter}][datatype]" value="{datatype}"></input>
					<xsl:choose>
						<xsl:when test="datatype='R'">
							<xsl:call-template name="choice_view"/>
						</xsl:when>
						<xsl:when test="datatype='CH'">
							<xsl:call-template name="choice_view"/>
						</xsl:when>
						<xsl:when test="datatype='LB'">
							<select disabled="disabled" class="forms" onMouseover="window.status='{statustext}'; return true;" onMouseout="window.status='';return true;">
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
							<input type="text" value="{value}" readonly="readonly" size="5" onMouseout="window.status='';return true;" >
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<input  size="30" type="text" value="{contact_name}"  readonly="readonly">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:when test="datatype='VENDOR'">
							<input type="text" value="{value}" readonly="readonly" size="6" onMouseout="window.status='';return true;" >
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<input  size="30" type="text" value="{vendor_name}"  readonly="readonly">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:when test="datatype='D'">
							<input type="text" name="values_attribute[{counter}][value]" value="{value}" readonly="readonly" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>';return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:when test="datatype='T'">
							<textarea cols="40" rows="6" name="values_attribute[{counter}][value]" wrap="virtual" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>';return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value"/>
							</textarea>
						</xsl:when>
						<xsl:otherwise>
							<input type="text" name="values_attribute[{counter}][value]" value="{value}" readonly="readonly" size="30" onMouseout="window.status='';return true;" >
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="statustext"/>
									<xsl:text>';return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="history=1">
							<xsl:variable name="link_history">
								<xsl:value-of select="link_history"/>
							</xsl:variable>
							<xsl:variable name="lang_history_help">
								<xsl:value-of select="//lang_history_help"/>
							</xsl:variable>
							<xsl:variable name="lang_history">
								<xsl:value-of select="//lang_history"/>
							</xsl:variable>
							<a href="javascript:var w=window.open('{$link_history}','','width=550,height=400,scrollbars')"
							   onMouseOver="overlib('{$lang_history_help}', CAPTION, '{$lang_history}')"
							   onMouseOut="nd()">
								<xsl:value-of select="//lang_history"/>
							</a>

						</xsl:when>
					</xsl:choose>

				</xsl:when>
			</xsl:choose>
		</td>
	</tr>
</xsl:template>


<xsl:template name="choice_view">
	<xsl:variable name="counter">
		<xsl:value-of select="counter"/>
	</xsl:variable>
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
				<xsl:variable name="checked">
					<xsl:value-of select="checked"/>
				</xsl:variable>
				<td align="left">
					<xsl:choose>
						<xsl:when test="checked='checked'">
							<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}" checked="$checked" disabled="disabled"></input>
						</xsl:when>
						<xsl:otherwise>
							<input type="{input_type}" name="values_attribute[{$counter}][value][]" value="{id}" disabled="disabled"></input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>


