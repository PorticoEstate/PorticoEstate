<xsl:template match="messages_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content" style="padding: 2em;">
			 	<ul>
			 	<xsl:for-each select="message">
			 		<li class="ticket_detail">
			 		 <img src="frontend/templates/base/images/16x16/email.png" class="list_image"/>
			 			<xsl:value-of disable-output-escaping="yes" select="date"/> - <a href="index.php?menuaction=frontend.uimessages.index&amp;message_id={id}"><xsl:value-of select="subject" disable-output-escaping="yes"/></a> <xsl:value-of disable-output-escaping="yes" select="from"/>
			 		<xsl:value-of disable-output-escaping="yes" select="status"/>
			 		</li>
			 	</xsl:for-each>
			 	</ul>
		</div>	
	</div>
</xsl:template>


