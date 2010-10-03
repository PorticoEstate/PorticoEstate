<!-- $Id: vendor_view.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="vendor_view">
		<xsl:apply-templates select="vendor_data"/>
	</xsl:template>

	<xsl:template match="vendor_data">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td>
					<xsl:value-of select="value_vendor_id"/>
					<xsl:text> - </xsl:text>
					<xsl:value-of select="value_vendor_name"/>
				</td>
			</tr>
	</xsl:template>
