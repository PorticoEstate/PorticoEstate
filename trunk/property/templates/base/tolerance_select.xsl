<!-- $Id$ -->

	<xsl:template name="tolerance_select">
		<xsl:variable name="lang_tolerance_statustext"><xsl:value-of select="lang_tolerance_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="select_name"><xsl:value-of select="select_tolerance"></xsl:value-of></xsl:variable>
		<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_tolerance_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_tolerance"></xsl:value-of></option>
			<xsl:apply-templates select="tolerance_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="tolerance_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
