<!-- $Id$ -->

	<xsl:template name="abook_view">
		<xsl:apply-templates select="abook_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="abook_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_contact"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_abid"></xsl:value-of>
				<xsl:text> - </xsl:text>
				<xsl:value-of select="value_contact_name"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
