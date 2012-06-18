<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="control_tabview">

	<xsl:choose>
		<xsl:when test="view = 'control_details'">
		<xsl:call-template name="yui_booking_i18n"/>
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control')"/></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control" />
		</xsl:when>
		<xsl:when test="view = 'control_groups'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control_groups')"/> for <xsl:value-of select="control/title" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_groups" />
		</xsl:when>
 		<xsl:when test="view = 'control_locations'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control_locations')"/> for <xsl:value-of select="control/title" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_locations" />
		</xsl:when>
		<xsl:when test="view = 'control_component'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control_component')"/> for <xsl:value-of select="control/title" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_component" />
		</xsl:when>
		<xsl:when test="view = 'control_items'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control_items')"/> for <xsl:value-of select="control/title" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_items" />
		</xsl:when>
		<xsl:when test="view = 'sort_check_list'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Check_list')"/> for <xsl:value-of select="control/title" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="sort_check_list" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
