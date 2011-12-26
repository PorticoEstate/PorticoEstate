  <!-- $Id$ -->
	<xsl:template name="choice">
		<table cellpadding="2" cellspacing="2" width="80%" align="left">
			<xsl:choose>
				<xsl:when test="value_choice!=''">
					<tr class="th">
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="lang_value"/>
						</td>
						<td class="th_text" width="15%" align="center">
							<xsl:value-of select="lang_delete_value"/>
						</td>
					</tr>
					<xsl:for-each select="value_choice">
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
							<td align="center">
								<input type="checkbox" name="values[delete_choice][]" value="{id}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="//lang_delete_choice_statustext"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_value"/>
				</td>
				<td>
					<input type="text" name="values[new_choice]" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_new_value_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>
