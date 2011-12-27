  <!-- $Id$ -->
	<xsl:template name="ecodimb_view">
		<xsl:apply-templates select="ecodimb_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="ecodimb_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_ecodimb"/>
			</td>
			<td>
				<xsl:value-of select="value_ecodimb"/>
				<xsl:text> [</xsl:text>
				<xsl:value-of select="value_ecodimb_descr"/>
				<xsl:text>]</xsl:text>
			</td>
		</tr>
	</xsl:template>
