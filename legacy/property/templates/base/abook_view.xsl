<!-- $Id: abook_view.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="abook_view">
		<xsl:apply-templates select="abook_data"/>
	</xsl:template>

	<xsl:template match="abook_data">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_contact"/>
				</td>
				<td>
					<xsl:value-of select="value_abid"/>
					<xsl:text> - </xsl:text>
					<xsl:value-of select="value_contact_name"/>
				</td>
			</tr>
	</xsl:template>
