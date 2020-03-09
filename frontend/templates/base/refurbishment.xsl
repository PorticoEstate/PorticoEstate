<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<xsl:param name="template_set"/>
	
	<xsl:variable name="tab_selected">
		<xsl:value-of select="tab_selected"/>
	</xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div>
				<xsl:if test="$template_set != 'bootstrap'">
					<xsl:attribute name="id">tab-content</xsl:attribute>
					<xsl:value-of disable-output-escaping="yes" select="tabs" />
				</xsl:if>
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
