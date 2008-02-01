	<xsl:template match="search_results">
		<div id="tts_search_results">
			<h1><xsl:value-of select="lang/search_results" /></h1>
			<xsl:call-template name="ticket_list" />
		</div>
	</xsl:template>
