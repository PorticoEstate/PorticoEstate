<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
	<div class="content">
		<div class="error">
			<xsl:value-of select="php:function('lang', 'Access denied')" />
		</div>
	</div>

</xsl:template>
