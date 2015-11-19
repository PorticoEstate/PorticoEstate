<xsl:template match="contract" xmlns:php="http://php.net/xsl">
	
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<div class="pure-g">
						<div class="pure-u-1">
							<xsl:value-of select="php:function('lang', 'not_implemented')"/>
						</div>
					</div>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
			</div>
		</div>
	</div>
</xsl:template>