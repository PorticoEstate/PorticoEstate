`	<xsl:template name="extrabox">
		<xsl:apply-templates select="xextrabox"/>
	</xsl:template>

	<xsl:template match="xextrabox">
		<table cellspacing="2" cellpadding="2" border="1" border-color="black" bgcolor="white">
			<tr>
				<td>
					<b><xsl:text>Email Data goes here</xsl:text></b>
				</td>
			</tr>
			<xsl:apply-templates select="values"/>
		</table>
	</xsl:template>
