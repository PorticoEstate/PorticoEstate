  <!-- $Id$ -->
	<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h1>
		<xsl:value-of select="php:function('lang', 'mobilefrontend')"/>
	</h1>
	<h2>
		<xsl:value-of select="message"/>
	</h2>
	</xsl:template>
