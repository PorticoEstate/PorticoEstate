  <!-- $Id$ -->
	<xsl:template name="tenant_view">
		<xsl:apply-templates select="tenant_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="tenant_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_tenant"/>
			</td>
			<td>
				<xsl:value-of select="value_tenant_id"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="value_last_name"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="value_first_name"/>
			</td>
		</tr>
	</xsl:template>
