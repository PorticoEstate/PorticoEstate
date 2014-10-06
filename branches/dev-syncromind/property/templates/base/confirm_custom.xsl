  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="confirm">
				<xsl:apply-templates select="confirm"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- update_cat -->
	<xsl:template match="confirm" xmlns:php="http://php.net/xsl">
		<table cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="lang_confirm_msg != ''">
					<tr>
						<td align="center" colspan="2">
							<xsl:value-of select="lang_confirm_msg"/>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:variable name="run_action">
								<xsl:value-of select="run_action"/>
							</xsl:variable>
							<xsl:variable name="lang_yes">
								<xsl:value-of select="lang_yes"/>
							</xsl:variable>
							<form method="POST" action="{$run_action}">
								<input type="submit" class="forms" name="confirm" value="{$lang_yes}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_yes_statustext"/>
									</xsl:attribute>
								</input>
								<input type="checkbox" name="debug" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'debug')"/>
									</xsl:attribute>
									<xsl:if test="debug = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</form>
						</td>
						<td align="right">
							<xsl:variable name="done_action">
								<xsl:value-of select="done_action"/>
							</xsl:variable>
							<a href="{$done_action}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_no_statustext"/>
								</xsl:attribute>
								<xsl:value-of select="lang_no"/>
							</a>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<td align="center">
							<xsl:variable name="done_action">
								<xsl:value-of select="done_action"/>
							</xsl:variable>
							<a href="{$done_action}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_done_statustext"/>
								</xsl:attribute>
								<xsl:value-of select="lang_done"/>
							</a>
						</td>
					</tr>
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>
