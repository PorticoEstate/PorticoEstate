<!-- $Id: help_data.xsl,v 1.2 2006/10/26 11:26:00 sigurdne Exp $ -->

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
