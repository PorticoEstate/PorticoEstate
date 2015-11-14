<xsl:template match="contract" xmlns:php="http://php.net/xsl">
	<xsl:variable name="location_id"><xsl:value-of select="location_id"/></xsl:variable>
	<div class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="{$location_id}">
				<em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'not_implemented')"/></em>
			</div>
		</div>
	</div>
</xsl:template>