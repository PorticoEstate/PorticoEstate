  <!-- $Id: app_data.xsl 8437 2011-12-26 19:11:48Z sigurdne $ -->
	<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h1>
		<xsl:value-of select="php:function('lang', 'mobilefrontend')"/>
	</h1>
	<h2>
		<xsl:value-of select="message"/>
	</h2>
	</xsl:template>
