<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="procedure_tabview">
		<div class="identifier-header">
			<h1>
				<xsl:value-of select="php:function('lang', 'Procedure')" />
			</h1>
		</div>
		<xsl:value-of disable-output-escaping="yes" select="tabs" />

		<div id ='procedure'>
			<xsl:choose>
				<xsl:when test="view = 'view_procedure'">
					<xsl:call-template name="view_procedure" />
				</xsl:when>
			</xsl:choose>

		</div>
		<div id ='documents'>
			<xsl:choose>
				<xsl:when test="view = 'view_documents_for_procedure'">
					<xsl:call-template name="view_procedure_documents" />
				</xsl:when>
			</xsl:choose>

		</div>

	</div>
	
</xsl:template>
