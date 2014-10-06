<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:call-template name="yui_phpgw_i18n"/>
<div class="yui-navset yui-navset-top" id="procedure_tabview">
	<xsl:choose>
		<xsl:when test="view = 'view_procedure'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Procedure')" /></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			 
			<xsl:call-template name="view_procedure" />
		</xsl:when>
		<xsl:when test="view = 'view_documents_for_procedure'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Procedure')" /></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="view_procedure_documents" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
