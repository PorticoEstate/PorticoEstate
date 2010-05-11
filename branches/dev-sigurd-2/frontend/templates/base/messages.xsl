<xsl:template match="messages_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content" style="padding: 2em;">
			 	<table>
			 		<tr>
			 			<th>Dato</th>
			 			<th>Tittel</th>
			 			<th>Fra</th>
			 		</tr>
			 	<xsl:for-each select="message">
			 		<tr>
			 			<td><img src="frontend/templates/base/images/16x16/email.png" class="list_image"/> <xsl:value-of disable-output-escaping="yes" select="date"/></td>
			 		 	<td><a href="index.php?menuaction=frontend.uimessages.index&amp;message_id={id}"><xsl:value-of select="subject" disable-output-escaping="yes"/></a></td>
			 			<td><xsl:value-of disable-output-escaping="yes" select="from"/></td>
			 		</tr>
			 	</xsl:for-each>
			 	</table>
		</div>	
	</div>
</xsl:template>


