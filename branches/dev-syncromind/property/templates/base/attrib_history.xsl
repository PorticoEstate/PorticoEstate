  <!-- $Id$ -->
	<!-- attrib_history -->
	<xsl:template match="attrib_history">
		<div>
			<br/>
		</div>
		<!--  DATATABLE -->
		<div align="left" id="paging_0"> </div>
		<div id="datatable-container_0"/>
		<div id="contextmenu_0"/>
		<div>
			<br/>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						permission  : <xsl:value-of select="permission"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>

		</script>
	</xsl:template>

	<xsl:template name="table_setup">
		<xsl:param name="container" />
		<xsl:param name="requestUrl" />
		<xsl:param name="ColumnDefs" />
		<table id="{$container}" class="display cell-border compact responsive no-wrap" width="100%">
			<thead>
				<tr>
					<xsl:for-each select="$ColumnDefs">
						<xsl:choose>
							<xsl:when test="hidden">
								<xsl:if test="hidden =0">
									<th>
										<xsl:value-of select="label"/>
									</th>
									</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<th>
									<xsl:value-of select="label"/>
								</th>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</tr>
			</thead>
		</table>
		<script>
			JqueryPortico.inlineTablesDefined += 1;
			var PreColumns = [
					<xsl:for-each select="$ColumnDefs">
					{
						data:			"<xsl:value-of select="key"/>",
						class:			"<xsl:value-of select="className"/>",
						orderable:		<xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:choose>
							<xsl:when test="hidden">
								<xsl:if test="hidden =0">
									visible			:true,
								</xsl:if>
								<xsl:if test="hidden =1">
									class:			'none',
									visible			:false,
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
									visible			:true,
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="formatter">
						 render: function (dummy1, dummy2, oData) {
								try {
									var ret = <xsl:value-of select="formatter"/>("<xsl:value-of select="key"/>", oData);
								}
								catch(err) {
									return err.message;
								}
								return ret;
							 },

						</xsl:if>
						defaultContent:	"<xsl:value-of select="defaultContent"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
	<![CDATA[
			columns = [];

			for(i=0;i < PreColumns.length;i++)
			{
				if ( PreColumns[i]['visible'] == true )
				{
					columns.push(PreColumns[i]);
				}
			}
	]]>
			<xsl:variable name="num">
				<xsl:number count="*"/>
			</xsl:variable>
			var options = {disablePagination:true, disableFilter:true};
			var oTable<xsl:number value="($num - 1)"/> = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns, options);

		</script>
	</xsl:template>