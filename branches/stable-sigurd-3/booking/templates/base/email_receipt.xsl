<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<p><xsl:value-of select="php:function('lang', 'The emails were sent successfully')" /></p>

    </div>
</xsl:template>
