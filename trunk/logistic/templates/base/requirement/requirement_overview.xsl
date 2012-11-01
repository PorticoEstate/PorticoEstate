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

<xsl:template name="requirement_overview" xmlns:php="http://php.net/xsl">
	<div id="resource_alloc_wrp" class="content-wrp">
	
			<xsl:variable name="add_req_params">
				<xsl:text>menuaction:logistic.uirequirement.edit, id:</xsl:text>
				<xsl:value-of select="activity/id" />
			</xsl:variable>
			<xsl:variable name="add_req_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $add_req_params )" />
			</xsl:variable>
			
			<h2 style="float:left;">Krav</h2>
			<a id="add_requirement_btn" class="btn focus" href="{$add_req_url}"><xsl:value-of select="php:function('lang', 'Add requirement')" /></a>
			<div style="clear:both;" id="paging"></div>
			<div style="margin-bottom: 40px;" id="requirement-container"></div>
				
			<h2 style="clear:both;">Allokerte ressurser</h2>
			<div id="allocation-container"></div>
	</div>
	<xsl:call-template name="datasource-def" />
</xsl:template>

<xsl:template name="datasource-def">

	<script>
	YAHOO.util.Event.onDOMReady(function(){
	 
   	YAHOO.portico.columnDefs = [
				<xsl:for-each select="//datatable/field">
					{
						resizeable: true,
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
							label: "<xsl:value-of select="label"/>",
						</xsl:if>
							sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
							hidden: true,
						</xsl:if>
						<xsl:if test="formatter">
							formatter: <xsl:value-of select="formatter"/>,
						</xsl:if>
						<xsl:if test="editor">
							editor: <xsl:value-of select="editor"/>,
					  </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
			
			var reqUrl = '<xsl:value-of select="//datatable/source"/>';
			
			YAHOO.portico.inlineTableHelper('requirement-container', reqUrl, YAHOO.portico.columnDefs);
  	});
  	
  	$(document).ready(function(){
			var requirement_id = $("#requirement-container table").children("tr").eq(1).find("td.requirement_id").find("div").text();
			updateAllocationTable( requirement_id );
		});
		
		function updateAllocationTable(requirement_id){
		
			var oArgs = {
					menuaction:'logistic.uirequirement_resource_allocation.index',
					requirement_id: requirement_id,
					type: "requirement_id",
					phpgw_return_as: 'json'
				};
				
				var requestUrl = phpGWLink('index.php', oArgs, true);
			
				var myColumnDefs = [ 
			        {key:"id", sortable:true},
			        {key:"fm_bim_item_name", label:'Navn på ressurs', sortable:true},
			        {key:"resource_type_descr", label:'Ressurstype', sortable:true}, 
			        {key:"location_code", label:'Lokasjonskode', sortable:true},
			        {key:"fm_bim_item_address", label:'Adresse', sortable:true}
			    ]; 
			
				YAHOO.portico.inlineTableHelper('allocation-container', requestUrl, myColumnDefs);
		}
  </script>
</xsl:template>