<!-- $Id$ -->

	<xsl:template name="vendor_view">
		<xsl:apply-templates select="vendor_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="vendor_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_vendor"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_vendor_id"></xsl:value-of>
				<xsl:text> - </xsl:text>
				<xsl:value-of select="value_vendor_name"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
