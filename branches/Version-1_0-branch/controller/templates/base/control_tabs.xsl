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
				<h1><xsl:value-of select="php:function('lang', 'Control_groups')"/> for <xsl:value-of select="group_name" /></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_groups" />
		</xsl:when>
		<xsl:when test="view = 'control_items'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Control_items')"/></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_items" />
		</xsl:when>
		<xsl:when test="view = 'receipt'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Receipt')"/></h1>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="control_items_receipt" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
