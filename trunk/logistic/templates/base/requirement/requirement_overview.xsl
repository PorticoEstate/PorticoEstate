<xsl:template  match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_phpgw_i18n"/>
	
	<div class="content-wrp">
		
		  <form action="" name="acl_form" id="acl_form" method="post">
				<div id="paging"></div>
	
				<div id="requirement-container"></div>

				<div id="allocation-container"></div>
			</form>

			<xsl:apply-templates select="datasource-definition"/>
	</div>
</xsl:template>

<xsl:template match="datasource-definition">
	<script>
		/*YAHOO.portico.inlineTableHelper('requirement-container', url, colDefs, null, null);

		YAHOO.portico.updateinlineTableHelper('allocation-container');
	*/
	
		YAHOO.portico.setupDatasource = function() {
			<xsl:if test="source">
				YAHOO.portico.dataSourceUrl = '<xsl:value-of select="source"/>';
					YAHOO.portico.initialSortedBy = false;
					YAHOO.portico.initialFilters = false;
					<xsl:if test="sorted_by">
						YAHOO.portico.initialSortedBy = {key: '<xsl:value-of select="sorted_by/key"/>', dir: '<xsl:value-of select="sorted_by/dir"/>'};
					</xsl:if>
			</xsl:if>

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

			YAHOO.portico.editor_action = "<xsl:value-of select="//datatable/editor_action"/>";
			YAHOO.portico.disable_left_click = "<xsl:value-of select="//datatable/disable_left_click"/>";

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
		}
		
		var actions = new Array();

		<xsl:choose>
			<xsl:when test="//js_lang != ''">
				var lang = <xsl:value-of select="//js_lang"/>;
			</xsl:when>
		</xsl:choose>

	</script>
</xsl:template>