<!-- $Id: control_component_tabs.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="control_group_component_tabview">
	<xsl:choose>
		<xsl:when test="view = 'view_component_for_control_group'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'component_for_control_group')"/></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			 
			<xsl:call-template name="view_component_for_control_group" />
		</xsl:when>
		<xsl:when test="view = 'add_component_to_control_group'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Add_component_for_control_group')"/></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="add_component_to_control_group" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
