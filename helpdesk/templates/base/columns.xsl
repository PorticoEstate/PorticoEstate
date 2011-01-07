<!-- $Id: columns.xsl 6705 2010-12-26 23:10:55Z sigurdne $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="columns">
				<xsl:apply-templates select="columns"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="columns">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
				<form method="post" name="form" action="{$form_action}">
					<tr>
						<td valign="top">
							<b><xsl:value-of select="lang_columns"/></b>
						</td>
					</tr>
					<xsl:apply-templates select="column_list"/>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_save_statustext"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>


				</form>
			</table>
		</div>
	</xsl:template>

	<xsl:template match="column_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<tr>
			<td>
				<xsl:choose>
					<xsl:when test="selected">
						<input id="column{$id}" name="values[columns][]" value="{$id}" checked="checked" type="checkbox"></input>
					</xsl:when>
					<xsl:otherwise>
						<input id="column{$id}" name="values[columns][]" value="{$id}" type="checkbox"></input>
					</xsl:otherwise>
				</xsl:choose>

				<xsl:value-of select="name"/>
			</td>
		</tr>
	</xsl:template>
