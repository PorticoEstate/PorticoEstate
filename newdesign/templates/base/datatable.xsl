<xsl:template match="phpgw">
	<div id="datatable-toolbar" style="width: 100%; background: #eee; border-top: 3px #eee solid">
		<div id="datatable-buttons" style="float: left; padding: 0em 0.5em">

		</div>

		<div id="pagination-buttons" style="float: left; padding: 0em 0.5em">

		</div>
		<div id="datatable-pages" style="float:left; line-height:2em; padding: 0em 0.5em"></div>
		<div style="clear:left"></div>
	</div>
	<div id="datatable" style="margin: 1px;"></div>

	<xsl:apply-templates />
</xsl:template>