<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:phpgw="http://phpgroupware.org/functions"
	xmlns:func="http://exslt.org/functions"
	extension-element-prefixes="func"
	exclude-result-prefixes="phpgw">
	
	<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
	-->
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
	
	<xsl:template match="phpgw" xmlns:php="http://php.net/xsl">
		<script>
			YAHOO.rental.setupDatasource = new Array();
		</script>
		<div id="rental_user_error">
			<xsl:value-of select="data/error"/>
		</div>
		<div id="rental_user_message">
			<xsl:value-of select="data/message"/>
		</div>
		<xsl:call-template name="pageForm"/>
		<xsl:call-template name="pageContent"/>
	</xsl:template>

	<xsl:template name="datasource-definition">
		<xsl:param name="number">1</xsl:param>
		<xsl:param name="form"></xsl:param>
		<xsl:param name="filters">[]</xsl:param>
		<xsl:param name="container_name"></xsl:param>
		<xsl:param name="context_menu_labels">[]</xsl:param>
		<xsl:param name="context_menu_actions">[]</xsl:param>
		<xsl:param name="columnDefinitions">[]</xsl:param>
		<xsl:param name="source"></xsl:param>
		<script>
			YAHOO.rental.setupDatasource.push(function() {
		        this.dataSourceURL = '<xsl:value-of select="$source"/>';
				this.columnDefs = <xsl:value-of select="$columnDefinitions"/>;
				this.formBinding = '<xsl:value-of select="$form"/>';
				this.filterBinding = <xsl:value-of select="$filters"/>;
				this.containerName = '<xsl:value-of select="$container_name"/>';
				this.contextMenuName = 'contextMenu<xsl:value-of select="$number"/>';
				this.contextMenuLabels = <xsl:value-of select="$context_menu_labels"/>;
				this.contextMenuActions = <xsl:value-of select="$context_menu_actions"/>;
			});
		</script>
	</xsl:template>	
</xsl:stylesheet>