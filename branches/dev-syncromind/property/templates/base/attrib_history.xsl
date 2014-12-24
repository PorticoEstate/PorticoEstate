  <!-- $Id$ -->
	<!-- attrib_history -->
	<func:function name="phpgw:conditional">
	   <xsl:param name="test"/>
	   <xsl:param name="true"/>
	   <xsl:param name="false"/>

	   <func:result>
		   <xsl:choose>
			   <xsl:when test="$test">
				   <xsl:value-of select="$true"/>
			   </xsl:when>
			   <xsl:otherwise>
				   <xsl:value-of select="$false"/>
			   </xsl:otherwise>
		   </xsl:choose>
	   </func:result>
   </func:function>
   
	<xsl:template match="attrib_history">
		<div id="tab-content">
			<div>
				<fieldset>
					<div class="pure-control-group">
						<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
									<xsl:call-template name="table_setup">
									  <xsl:with-param name="container" select ='container'/>
									  <xsl:with-param name="requestUrl" select ='requestUrl' />
									  <xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									</xsl:call-template>
								</xsl:if>
						</xsl:for-each>
					</div>
				</fieldset>
			</div>
		</div>
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
			var options = {disableFilter:true};
			var oTable<xsl:number value="($num - 1)"/> = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns, options);

		</script>
	</xsl:template>