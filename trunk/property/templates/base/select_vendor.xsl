<!-- $Id$ -->

	<xsl:template name="select_vendor">
		<xsl:variable name="lang_vendor_statustext"><xsl:value-of select="lang_vendor_statustext"></xsl:value-of></xsl:variable>
		<select name="vendor_id" class="forms" onMouseover="window.status='{$lang_vendor_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_vendor"></xsl:value-of></option>
			<xsl:apply-templates select="vendor_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="vendor_list">
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
