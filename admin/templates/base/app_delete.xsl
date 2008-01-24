<!-- $Id: app_delete.xsl 16579 2006-03-26 20:00:56Z sigurdne $ -->

	<xsl:template name="app_delete">
		<xsl:apply-templates select="delete"/>
	</xsl:template>

	<xsl:template match="delete">
			<table cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td align="center" colspan="2"><xsl:value-of select="lang_confirm_msg"/></td>
				</tr>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
						<xsl:variable name="lang_no"><xsl:value-of select="lang_no"/></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="forms" name="cancel" value="{$lang_no}" />
						</form>
					</td>
					<td align="right">
						<xsl:variable name="delete_action"><xsl:value-of select="delete_action"/></xsl:variable>
						<xsl:variable name="lang_yes"><xsl:value-of select="lang_yes"/></xsl:variable>
						<form method="post" action="{$delete_action}">
							<input type="submit" class="forms" name="confirm" value="{$lang_yes}"/>
						</form>
					</td>
				</tr>
			</table>
	</xsl:template>
