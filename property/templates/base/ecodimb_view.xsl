<!-- $Id$ -->

	<xsl:template name="ecodimb_view">
		<xsl:apply-templates select="ecodimb_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="ecodimb_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_ecodimb"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_ecodimb"></xsl:value-of>
				<xsl:text> [</xsl:text>
				<xsl:value-of select="value_ecodimb_descr"></xsl:value-of>
				<xsl:text>]</xsl:text>
			</td>
		</tr>
	</xsl:template>
