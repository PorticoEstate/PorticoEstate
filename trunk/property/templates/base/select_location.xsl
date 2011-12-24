<!-- $Id$ -->

	<xsl:template name="select_location">
		<xsl:variable name="select_name_location"><xsl:value-of select="select_name_location"/></xsl:variable>
		<select name="{$select_name_location}" onMouseout="window.status='';return true;">
			<xsl:attribute name="onMouseover">
				<xsl:text>window.status='</xsl:text>
				<xsl:value-of select="lang_location_statustext"/>
				<xsl:text>'; return true;</xsl:text>
			</xsl:attribute>
			<option value=""><xsl:value-of select="lang_no_location"/></option>
			<xsl:apply-templates select="location_list"/>
		</select>
	</xsl:template>

	<xsl:template match="location_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected = 'selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="descr"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="descr"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
