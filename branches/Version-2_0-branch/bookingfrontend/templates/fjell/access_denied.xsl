<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
	<div id="content">
		<div class="error"><xsl:value-of select="php:function('lang', 'Access denied')" /></div>
	</div>

</xsl:template>
