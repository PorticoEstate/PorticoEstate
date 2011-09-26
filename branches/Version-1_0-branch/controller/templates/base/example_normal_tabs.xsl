<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="yui-navset yui-navset-top" id="example_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<div id="general">
				<h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
		        <div id="documents_container"/>
				<a class='button'>
					<xsl:attribute name="href"><xsl:value-of select="resource/add_document_link"/></xsl:attribute>
					<xsl:if test="resource/permission/write">
						<xsl:value-of select="php:function('lang', 'Add Document')" />
					</xsl:if>
				</a>
				<h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
				<div id="permissions_container"/>
			</div>
			<div id="list">
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

