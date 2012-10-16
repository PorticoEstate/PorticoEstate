<!-- $Id: control_tabs.xsl 9951 2012-08-31 10:14:12Z vator $ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="requirement_tabview">

	<xsl:choose>
		<xsl:when test="view = 'requirement_details'">
			<xsl:call-template name="yui_phpgw_i18n"/>
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Requirement')"/></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="requirement_details" />
		</xsl:when>
		<xsl:when test="view = 'requirement_values'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Requirement values')"/></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="requirement_values" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
