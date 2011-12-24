<!-- $Id$ -->

	<xsl:template name="select_location">
		<xsl:variable name="select_name_location"><xsl:value-of select="select_name_location"></xsl:value-of></xsl:variable>
		<select name="{$select_name_location}" onMouseout="window.status='';return true;">
			<xsl:attribute name="onMouseover">
				<xsl:text>window.status='</xsl:text>
				<xsl:value-of select="lang_location_statustext"></xsl:value-of>
				<xsl:text>'; return true;</xsl:text>
			</xsl:attribute>
			<option value=""><xsl:value-of select="lang_no_location"></xsl:value-of></option>
			<xsl:apply-templates select="location_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="location_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected = 'selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="descr"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="descr"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
