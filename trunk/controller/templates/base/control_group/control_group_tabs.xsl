<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="yui-navset yui-navset-top" id="control_group_tabview">
	
		<h1><xsl:value-of select="php:function('lang', 'Control_group')" /></h1>
	
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div id="control_group">
				<xsl:call-template name="control_group" />
			</div>
			<div id="control_items">
				<xsl:call-template name="control_group_items" />
			</div>
		</div>
	</div>
	<!--  script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role')"/>;
	</script-->
</xsl:template>
