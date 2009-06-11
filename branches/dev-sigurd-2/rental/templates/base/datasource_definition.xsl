<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:phpgw="http://phpgroupware.org/functions">

	<xsl:template name="datasource-definition">
		<xsl:param name="number">1</xsl:param>
		<xsl:param name="form"></xsl:param>
		<xsl:param name="filters"></xsl:param>
		<xsl:param name="container_name"></xsl:param>
		<xsl:param name="context_menu_labels">[]</xsl:param>
		<xsl:param name="context_menu_actions">[]</xsl:param>
		<script>
			YAHOO.rental.setupDatasource.push(function() {
				<xsl:if test="source">
		            this.dataSourceURL = '<xsl:value-of select="source"/>';
		        </xsl:if>
	
				this.columnDefs = [
					<xsl:for-each select="field">
						{
							key: "<xsl:value-of select="key"/>",
							<xsl:if test="label">
							label: "<xsl:value-of select="label"/>",
						    </xsl:if>
							sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
							<xsl:if test="hidden">
							hidden: <xsl:value-of select="hidden"/>,
						    </xsl:if>
							<xsl:if test="formatter">
							formatter: <xsl:value-of select="formatter"/>,
						    </xsl:if>
							className: "<xsl:value-of select="className"/>"
						}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
					</xsl:for-each>
				];
				
				this.formBinding = '<xsl:value-of select="$form"/>';
				this.filterBinding = '<xsl:value-of select="$filters"/>';
				this.containerName = '<xsl:value-of select="$container_name"/>';
				this.contextMenuName = 'contextMenu<xsl:value-of select="$number"/>';
				this.contextMenuLabels = <xsl:value-of select="$context_menu_labels"/>;
				this.contextMenuActions = <xsl:value-of select="$context_menu_actions"/>;
			});
		</script>
	</xsl:template>
	
</xsl:stylesheet>