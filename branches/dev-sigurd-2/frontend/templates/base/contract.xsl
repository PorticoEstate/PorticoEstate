<xsl:template match="contract" xmlns:php="http://php.net/xsl">
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<div class="yui-navset" id="ticket_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
  			      KontraktID: <xsl:value-of select="id" />
			</div>
		</div>
</xsl:template>

