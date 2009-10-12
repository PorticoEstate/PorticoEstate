<!-- $Id$ -->

	<xsl:template name="project_group_view">
		<xsl:apply-templates select="project_group_data"/>
	</xsl:template>

	<xsl:template match="project_group_data">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_project_group"/>
				</td>
				<td>
					<xsl:value-of select="value_project_group"/>
					<xsl:text> [</xsl:text>
					<xsl:value-of select="value_project_group_descr"/>
					<xsl:text>]</xsl:text>
				</td>
			</tr>
	</xsl:template>
