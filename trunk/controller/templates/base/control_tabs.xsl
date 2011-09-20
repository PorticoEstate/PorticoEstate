<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="yui-navset yui-navset-top" id="control_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div id="details">
				<xsl:call-template name="control" />
			</div>
			<div id="list">
				<xsl:call-template name="control" />
				<h4><xsl:value-of select="php:function('lang', 'list')" /></h4>
			</div>
			<div id="list">
				<h4><xsl:value-of select="php:function('lang', 'dates')" /></h4>
				<xsl:value-of disable-output-escaping="yes" select="date"/>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role')"/>;
	</script>
</xsl:template>
