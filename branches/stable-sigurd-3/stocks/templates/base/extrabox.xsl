<!-- $Id$ -->

	<xsl:template name="extrabox">
		<xsl:apply-templates select="xextrabox"/>
	</xsl:template>

	<xsl:template match="xextrabox">
		<table cellspacing="2" cellpadding="2" border="1" border-color="black" bgcolor="white">
			<tr>
				<td>
					<b><xsl:value-of select="lang_name"/></b>
				</td>
				<td>
					<b><xsl:value-of select="lang_symbol"/></b>
				</td>
				<td align="right">
					<b><xsl:value-of select="lang_price"/></b>
				</td>
				<td align="right">
					<b><xsl:value-of select="lang_change"/></b>
				</td>
				<td align="right">
					<b>% <xsl:value-of select="lang_change"/></b>
				</td>
				<td align="center">
					<b><xsl:value-of select="lang_date"/></b>
				</td>
				<td align="center">
					<b><xsl:value-of select="lang_time"/></b>
				</td>
			</tr>
			<xsl:apply-templates select="values"/>
		</table>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="color"><xsl:value-of select="color"/></xsl:variable>
			<tr>
				<td>
					<xsl:value-of select="name"/>
				</td>
				<td>
					<xsl:value-of select="symbol"/>
				</td>
				<td align="right">
					<xsl:value-of select="price0"/>
				</td>
				<td align="right">
					<font color="{$color}">
						<xsl:value-of select="dollarchange"/>
					</font>
				</td>
				<td align="right">
					<font color="{$color}">
						<xsl:value-of select="percentchange"/>
					</font>
				</td>
				<td align="center">
					<xsl:value-of select="date"/>
				</td>
				<td align="center">
					<xsl:value-of select="time"/>
				</td>
			</tr>
	</xsl:template>
