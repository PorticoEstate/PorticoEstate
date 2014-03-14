  <!-- $Id: hello_world.xsl 10804 2013-02-13 13:24:06Z sigurdne $ -->
	<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h1>
		<xsl:value-of select="php:function('lang', 'mobilefrontend')"/>
	</h1>
	<h2>
		<xsl:value-of select="message"/>
	</h2>
	</xsl:template>
