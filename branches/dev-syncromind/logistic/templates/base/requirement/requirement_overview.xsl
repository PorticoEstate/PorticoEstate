
<xsl:template name="requirement_overview" xmlns:php="http://php.net/xsl">
	<div id="details">
	</div>
	<div id="allocation">

	<div id="resource_alloc_wrp" class="content-wrp">
	
			<xsl:variable name="activity_id">
				<xsl:value-of select="activity/id" />
			</xsl:variable>
			<xsl:variable name="add_req_params">
				<xsl:text>menuaction:logistic.uirequirement.edit, activity_id:</xsl:text>
				<xsl:value-of select="activity/id" />
			</xsl:variable>
			<xsl:variable name="add_req_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $add_req_params )" />
			</xsl:variable>
			
			<h2 style="float:left;"><xsl:value-of select="php:function('lang', 'Resource requirement')" /></h2>
			<xsl:if test="acl_add = '1'">
				<a id="add-requirement-btn" class="btn focus" onClick="load_requirement_edit({$activity_id});">
					<xsl:value-of select="php:function('lang', 'Add requirement')" />
				</a>
			</xsl:if>

			<div style="clear:both;" id="paging"></div>
			<div style="margin-bottom: 40px;">
				<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'requirement-container'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="data" select ='data' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
				</xsl:for-each>
			</div>
				
			<h2 style="clear:both;"><xsl:value-of select="php:function('lang', 'Allocated resouces')" /><span style="margin-left:470px;font-size:14px;">(<xsl:value-of select="php:function('lang', 'Click on table above to get allocations')" />)</span></h2>
			<form name='assign_task'>
				
				<!-- // Needed for case of only one checkbox in datatable-->
				<input type='hidden' name='assign_requirement' value = '0'/>
				<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'allocation-container'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="data" select ='data' />
								<xsl:with-param name="config" select ='config' />
							</xsl:call-template>
						</xsl:if>
				</xsl:for-each>
			</form>
	</div>
	</div>

</xsl:template>