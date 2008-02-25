<xsl:template match="phpgw">
	<div id="datatable"></div>
	<div id="dt-pag-nav">
	    <span id="prevLink">Previous</span>
	    | Showing items
	    <span id="startIndex">0</span> - <span id="endIndex"></span>
	    <span id="ofTotal"></span>
	    |
	    <span id="nextLink">Next</span>
	</div>
	<div id="serverpagination"></div>
	<xsl:apply-templates />
</xsl:template>