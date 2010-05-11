<xsl:template match="messages_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content" style="padding: 2em;">
			 <img src="frontend/templates/base/images/32x32/email.png" class="list_image"/>Meldinger<br/>
			 <hr/>
			 	<ul>
			 	<xsl:for-each select="message">
			 		<li class="ticket_detail">
			 			<xsl:value-of select="date"/> - <a href="index.php?menuaction=frontend.uimessages.index&amp;message_id={id}"><xsl:value-of select="subject"/></a> <xsl:value-of select="from"/>
			 		</li>
			 	</xsl:for-each>
			 	</ul>
		</div>	
	</div>
</xsl:template>


