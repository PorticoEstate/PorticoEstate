<xsl:template match="phpgw">
	<div class="content">
		<xsl:value-of select="data/output" disable-output-escaping="yes"/>
	</div>
</xsl:template>