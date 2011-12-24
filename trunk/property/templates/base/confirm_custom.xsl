<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="confirm">
				<xsl:apply-templates select="confirm"></xsl:apply-templates>
			</xsl:when>
		</xsl:choose>
	</xsl:template>


<!-- update_cat -->

	<xsl:template match="confirm">
		<table cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"></xsl:call-template>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="lang_confirm_msg != ''">
					<tr>
						<td align="center" colspan="2"><xsl:value-of select="lang_confirm_msg"></xsl:value-of></td>
					</tr>
					<tr>
						<td>
							<xsl:variable name="run_action"><xsl:value-of select="run_action"></xsl:value-of></xsl:variable>
							<xsl:variable name="lang_yes"><xsl:value-of select="lang_yes"></xsl:value-of></xsl:variable>
							<form method="POST" action="{$run_action}">
								<input type="submit" class="forms" name="confirm" value="{$lang_yes}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_yes_statustext"></xsl:value-of>
									</xsl:attribute>
								</input>
							</form>
						</td>
						<td align="right">
							<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
							<a href="{$done_action}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_no_statustext"></xsl:value-of>
								</xsl:attribute>
								<xsl:value-of select="lang_no"></xsl:value-of>
							</a>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>		
					<tr>
						<td align="center">
							<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
							<a href="{$done_action}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
								</xsl:attribute>
								<xsl:value-of select="lang_done"></xsl:value-of>
							</a>
						</td>
					</tr>
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>
