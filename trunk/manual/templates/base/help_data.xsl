<!-- $Id: help_data.xsl 17497 2006-10-26 11:26:00Z sigurdne $ -->

	<xsl:template name="help_data">
		<xsl:apply-templates select="xhelp"/>
	</xsl:template>

	<xsl:template match="xhelp">
		<table>
			<tr>
				<td>
					<xsl:value-of select="intro" disable-output-escaping="yes"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="text" disable-output-escaping="yes" />
				</td>
			</tr>
		</table>
	</xsl:template>
