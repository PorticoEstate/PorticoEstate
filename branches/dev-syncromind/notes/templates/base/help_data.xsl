<!-- $Id$ -->

	<xsl:template name="help_data">
		<xsl:apply-templates select="xhelp"/>
	</xsl:template>

	<xsl:template match="xhelp">
		<xsl:choose>
			<xsl:when test="list">
				<xsl:apply-templates select="list"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
	<xsl:variable name="list_img" select="list_img"/>
		<table>
			<tr>
				<td colspan="2">
					<img src="{$list_img}"/>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right">1</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_1"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">2</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_2"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">3</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_3"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">4</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_4"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">5</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_5"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">6</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_6"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">7</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_7"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">8</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_8"/></td>
			</tr>
			<tr>
				<td valign="top" align="right">9</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_9"/></td>
			</tr>
		</table>
	</xsl:template>
