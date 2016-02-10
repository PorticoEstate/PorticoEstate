
<!-- $Id$ -->
<xsl:template name="project_group_view">
		<xsl:apply-templates select="project_group_data"/>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="project_group_data">
	<div class="pure-control-group">
		<label for="name">
				<xsl:value-of select="lang_project_group"/>
		</label>
				<xsl:value-of select="value_project_group"/>
				<xsl:text> [</xsl:text>
				<xsl:value-of select="value_project_group_descr"/>
				<xsl:text>]</xsl:text>
				<xsl:choose>
					<xsl:when test="value_project_group_budget != ''">
						<xsl:value-of select="php:function('lang', 'budget')"/>
						<xsl:text>: </xsl:text>
						<xsl:value-of select="value_project_group_budget"/>
					</xsl:when>
				</xsl:choose>
	</div>
</xsl:template>
