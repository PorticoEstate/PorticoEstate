
<!-- $Id: external_project_view.xsl 14719 2016-02-10 19:45:46Z sigurdne $ -->
<xsl:template name="external_project_view">
	<xsl:apply-templates select="external_project_data"/>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="external_project_data">
	<div class="pure-control-group">
		<label for="name">
			<xsl:value-of select="lang_external_project"/>
		</label>
		<xsl:value-of select="value_external_project_id"/>
		<xsl:text> [</xsl:text>
		<xsl:value-of select="value_external_project_name"/>
		<xsl:text>]</xsl:text>
		<xsl:choose>
			<xsl:when test="value_external_project_budget != ''">
				<xsl:value-of select="php:function('lang', 'budget')"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="value_external_project_budget"/>
			</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>
