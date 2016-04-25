<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<div class="yui-content">

		<xsl:variable name="controller_params">
			<xsl:text>menuaction:controller.uicalendar.view_calendar_for_year, noframework:1, location_code:</xsl:text>
			<xsl:value-of select="location_code" />
		</xsl:variable>

		<xsl:variable name="controller_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $controller_params )" />
		</xsl:variable>
		
		<iframe id="controller_content" width="100%" height="500" src="{$controller_url}">
		
			<p>Your browser does not support iframes.</p>
		</iframe>
		<xsl:value-of select="php:function('lang', 'controller')"/>
	</div>

</xsl:template>




