<!-- $Id$ -->

	<xsl:template name="tenant_view">
		<xsl:apply-templates select="tenant_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="tenant_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_tenant"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_tenant_id"></xsl:value-of>
				<xsl:text> </xsl:text>
				<xsl:value-of select="value_last_name"></xsl:value-of>
				<xsl:text> </xsl:text>
				<xsl:value-of select="value_first_name"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>
