<xsl:template match="messages_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			Innboks
			<xsl:copy-of select="."/>
		</div>	
	</div>
</xsl:template>


