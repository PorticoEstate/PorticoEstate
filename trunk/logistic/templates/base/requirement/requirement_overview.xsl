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

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_phpgw_i18n"/>
	
	<div style="margin: 20px; padding: 20px;" class="content-wrp">
		  <form action="" name="acl_form" id="acl_form" method="post">
				<div id="paging"></div>
	
				<div id="requirement-container"></div>

				<div id="allocation-container"></div>
			</form>
	</div>
	<xsl:call-template name="datasource-definition" />
</xsl:template>

<xsl:template name="datasource-definition">

	<script>
	YAHOO.util.Event.onDOMReady(function(){
	 
	 	<xsl:choose>
			<xsl:when test="//datatable/actions">
				YAHOO.portico.actions = [
					<xsl:for-each select="//datatable/actions">
						{
							my_name: "<xsl:value-of select="my_name"/>",
							text: "<xsl:value-of select="text"/>",
							<xsl:if test="parameters">
								parameters: <xsl:value-of select="parameters"/>,
						    </xsl:if>
							action: "<xsl:value-of select="action"/>"
						}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
					</xsl:for-each>
				];
			</xsl:when>
			<xsl:otherwise>
				YAHOO.portico.actions = [];
			</xsl:otherwise>
		</xsl:choose>
	 
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
			
			var oArgs = {
				menuaction:'logistic.uirequirement_resource_allocation.index',
				requirement_id: '2',
				phpgw_return_as: 'json'
			};
			
			var requestUrl = phpGWLink('index.php', oArgs, true);

			var myColumnDefs = [ 
	            {key:"id", sortable:true}, 
	            {key:"requirement_id", sortable:true}, 
	            {key:"location_id", sortable:true}, 
	            {key:"resource_id", sortable:true} 
	        ]; 

			YAHOO.portico.inlineTableHelper('allocation-container', requestUrl, myColumnDefs);
  	}); 
  </script>
</xsl:template>