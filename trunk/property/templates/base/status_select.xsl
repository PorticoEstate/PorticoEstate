<!-- $Id$ -->

	<xsl:template name="status_select">
		<xsl:variable name="lang_status_statustext"><xsl:value-of select="lang_status_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="status_name"><xsl:value-of select="status_name"></xsl:value-of></xsl:variable>
		<select name="{$status_name}" class="forms" onMouseover="window.status='{$lang_status_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_status"></xsl:value-of></option>
			<xsl:apply-templates select="status_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="status_list">
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
