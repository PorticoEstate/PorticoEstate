<xsl:template match="messages_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content" style="padding: 2em;">
			<div style="float: left">
			 	<table>
			 		<tr>
			 			<th>Dato</th>
			 			<th style="padding-left: 2em;">Tittel</th>
			 			<th style="padding-left: 2em;">Fra</th>
			 		</tr>
			 	<xsl:for-each select="message">
			 		<tr>
			 			<td><img src="frontend/templates/base/images/16x16/email.png" class="list_image"/> <xsl:value-of disable-output-escaping="yes" select="date"/></td>
			 		 	<td style="padding-left: 2em;"><a href="index.php?menuaction=frontend.uimessages.index&amp;message_id={id}"><xsl:value-of select="subject" disable-output-escaping="yes"/></a></td>
			 			<td style="padding-left: 2em;"><xsl:value-of disable-output-escaping="yes" select="from"/></td>
			 		</tr>
			 	</xsl:for-each>
			 	</table>
			 </div>
		 	<div style="float: left">
		 		<xsl:copy-of select="view"/>
		 	</div>
		</div>	
	</div>
</xsl:template>


