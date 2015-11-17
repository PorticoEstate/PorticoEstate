<xsl:template match="contract" xmlns:php="http://php.net/xsl">
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	<div class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="{$tab_selected}">
				<em style="margin-left: 1em; float: left;"><xsl:value-of select="php:function('lang', 'not_implemented')"/></em>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
		</div>
	</div>
</xsl:template>