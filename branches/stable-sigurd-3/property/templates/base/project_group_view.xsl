<!-- $Id: project_group_view.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

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
