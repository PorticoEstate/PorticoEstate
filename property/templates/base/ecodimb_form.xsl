<!-- $Id$ -->

	<xsl:template name="ecodimb_form">
		<xsl:apply-templates select="ecodimb_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="ecodimb_data">
		<script type="text/javascript">
			self.name="first_Window";
			function ecodimb_lookup()
			{
				Window1=window.open('<xsl:value-of select="ecodimb_url"></xsl:value-of>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:choose>
			<xsl:when test="disabled='1'">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_ecodimb"></xsl:value-of>
					</td>
					<td>
						<input size="9" type="text" value="{value_ecodimb}" readonly="readonly"></input>
						<input size="30" type="text" value="{value_ecodimb_descr}" readonly="readonly"></input>
						<input size="9" type="hidden" name="ecodimb" value="{value_ecodimb}" readonly="readonly"></input>
						<input size="30" type="hidden" name="ecodimb_descr" value="{value_ecodimb_descr}" readonly="readonly"></input>
					</td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<tr>
					<td valign="top">
						<a href="javascript:ecodimb_lookup()" title="{lang_select_ecodimb_help}"><xsl:value-of select="lang_ecodimb"></xsl:value-of>
						</a>
					</td>
					<td>
						<input size="9" type="text" name="ecodimb" value="{value_ecodimb}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_select_ecodimb_help"></xsl:value-of>
							</xsl:attribute>
						</input>
						<input size="30" type="text" name="ecodimb_descr" value="{value_ecodimb_descr}" onClick="ecodimb_lookup();" readonly="readonly"> 
							<xsl:attribute name="title">
								<xsl:value-of select="lang_select_ecodimb_help"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
