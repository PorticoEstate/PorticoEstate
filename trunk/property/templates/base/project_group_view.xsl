<!-- $Id$ -->

	<xsl:template name="project_group_view">
		<xsl:apply-templates select="project_group_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template xmlns:php="http://php.net/xsl" match="project_group_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_project_group"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_project_group"></xsl:value-of>
				<xsl:text> [</xsl:text>
				<xsl:value-of select="value_project_group_descr"></xsl:value-of>
				<xsl:text>]</xsl:text>
				<xsl:choose>
					<xsl:when test="value_project_group_budget != ''">
						<xsl:value-of select="php:function('lang', 'budget')"></xsl:value-of>
						<xsl:text>: </xsl:text>
						<xsl:value-of select="value_project_group_budget"></xsl:value-of>
					</xsl:when>
				</xsl:choose>
			</td>
		</tr>
	</xsl:template>
