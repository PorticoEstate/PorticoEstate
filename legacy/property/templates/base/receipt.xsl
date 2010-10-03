<!-- $Id: receipt.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="receipt">
		<xsl:for-each select="message" >
			<tr>
				<td class="th_text" colspan="2" align="left">
					<xsl:value-of select="msg"/>
				</td>
			</tr>
		</xsl:for-each>

		<xsl:for-each select="error" >
			<tr>
				<td class="th_text" colspan="2" align="left">
					<xsl:value-of select="msg"/>
				</td>
			</tr>
		</xsl:for-each>
	</xsl:template>
