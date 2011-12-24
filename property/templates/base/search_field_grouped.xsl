<!-- $Id$ -->

	<xsl:template name="search_field_grouped">
		<xsl:variable name="query"><xsl:value-of select="query"/></xsl:variable>
		<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
		<table>
			<tr>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_date_search"><xsl:value-of select="link_date_search"/></xsl:variable>
					<xsl:variable name="lang_date_search_help"><xsl:value-of select="lang_date_search_help"/></xsl:variable>
					<xsl:variable name="lang_date_search"><xsl:value-of select="lang_date_search"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_date_search}','','left=50,top=100,width=300,height=300')" onMouseOver="overlib('{$lang_date_search_help}', CAPTION, '{$lang_date_search}')" onMouseOut="nd()">
						<xsl:value-of select="lang_date_search"/></a>					

					<table>
						<xsl:choose>
							<xsl:when test="start_date!=''">
								<tr>
									<td class="small_text" align="left">
										<xsl:value-of select="start_date"/>
									</td>
								</tr>
								<tr>
									<td class="small_text" align="left">
										<xsl:value-of select="end_date"/>
									</td>
								</tr>
							</xsl:when>
							<xsl:otherwise>
								<tr>
									<td class="small_text" align="left">
										<xsl:value-of select="lang_none"/>
									</td>
								</tr>
							</xsl:otherwise>
						</xsl:choose>
					</table>
				</td>

				<td valign="top" align="right">
					<input type="hidden" name="start_date" value="{start_date}"/>
					<input type="hidden" name="end_date" value="{end_date}"/>
					<input type="text" name="query" value="{$query}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchfield_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text>
					<input type="submit" name="submit" value="{$lang_search}" onMouseout="window.status='';return true;"> 
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_searchbutton_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>
