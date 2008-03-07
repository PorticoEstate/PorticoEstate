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

<xsl:template match="locationDataTable">
	<script type="text/javascript">
	var type_id = <xsl:value-of select="type_id"/>;
	var locationColumnDefs = [
		 <xsl:for-each select="columns/column">
  			{key:"<xsl:value-of select="name"/>", label:"<xsl:value-of select="descr"/>", sortable:true}
			<xsl:if test="position()!=last()">
  				<xsl:text>, </xsl:text>
      		</xsl:if>
		</xsl:for-each>
	];
	</script>
</xsl:template>

