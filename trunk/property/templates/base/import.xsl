<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="import">
			<xsl:apply-templates select="import"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="import">
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td>
				<form method="post" name="form" action="{import_action}">
					<input type="hidden" name="importfile" value="{importfile}"/>
					<table cellpadding="2" cellspacing="2" width="90%" align="left">
						<tr>
							<td valign="top" title="{lang_import_statustext}" style="cursor: help;">
								<xsl:value-of select="lang_import"/>
							</td>
							<td>
								<xsl:text> </xsl:text>
								<xsl:variable name="lang_import"><xsl:value-of select="lang_import"/></xsl:variable>
								<input type="submit" name="confirm" value="{$lang_import}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_import_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text>
								<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
								<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_cancel_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:call-template name="table_header"/>
		<xsl:call-template name="values"/>
	</table>
</xsl:template>

<xsl:template name="table_header">
	<tr class="th">
		<xsl:for-each select="table_header">
			<td class="th_text" width="{with}" align="{align}">
				<xsl:value-of select="header"/>
			</td>
		</xsl:for-each>
	</tr>
</xsl:template>

<xsl:template name="values">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"/>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select="row">
				<td class="small_text" align="left">
					<xsl:value-of select="value"/>				
				</td>
			</xsl:for-each>
		</tr>
	</xsl:for-each>
</xsl:template>
