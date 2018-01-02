<!-- $Id$ -->

<xsl:template match="delete">
	<xsl:variable name="delete_url"><xsl:value-of select="delete_url"/></xsl:variable>
	<form method="POST" action="{$delete_url}">
		<table cellpadding="2" cellspacing="2" align="center">		
			<tr>
				<td><xsl:value-of select="lang_confirm_msg" /></td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="delete">
						<xsl:attribute name="value">
							<xsl:value-of select="lang_delete" />
						</xsl:attribute>
					</input>
				</td>
				<td align="right">
					<input type="submit" name="cancel">
						<xsl:attribute name="value">
							<xsl:value-of select="lang_cancel"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>
