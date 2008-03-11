<xsl:template match="phpgw">
	<div id="datatable-toolbar" style="width: 100%; background: #eee; border-top: 3px #eee solid">

		<div id="datatable-buttons" style="float: left; padding: 0em 0.5em">

		</div>
		<div id="filter-buttons" style="float: left; padding: 0em 0.5em">
			<xsl:apply-templates select="filter" />
		</div>
		<div id="pagination-buttons" style="float: left; padding: 0em 0.5em">

		</div>
		<div id="datatable-pages" style="float:left; line-height:2em; padding: 0em 0.5em"></div>
		<div style="clear:left"></div>
	</div>
	<div id="datatable" style="margin: 1px;"></div>
	<div id="center-loader"><div>Loading...</div></div>

	<xsl:apply-templates select="locationDataTable" />
</xsl:template>

<xsl:template match="filter">

		<select id="filter_{id}_select" name="{name}">
			<xsl:for-each select="options">
				<option value="{id}">
					<xsl:if test="id=../selected">
						<xsl:attribute name="selected" value="selected" />
					</xsl:if>
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		</select>
		<input type="submit" id="filter_{id}_button" name="filter_{name}_button" value="{title}" title="{descr}" />

</xsl:template>

<xsl:template match="locationDataTable">
	<script type="text/javascript">
	var type_id = <xsl:value-of select="type_id"/>;
	var datasource_url = "<xsl:value-of select="datasrouce_url"/>";

	var locationColumnDefs = [
		 <xsl:for-each select="columns/column">
  			{key:"<xsl:value-of select="name"/>", label:"<xsl:value-of select="descr"/>", sortable:true }
			<xsl:if test="position()!=last()">
 					<xsl:text>, </xsl:text>
      		</xsl:if>
		</xsl:for-each>
	];
	</script>
</xsl:template>



