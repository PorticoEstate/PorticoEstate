<xsl:template match="phpgw">
	<div id="datatable-toolbar">
		<div id="datatable-pages" style="float:left; line-height:2em"></div>
	</div>
	<div id="datatable"></div>

	<xsl:apply-templates />
</xsl:template>