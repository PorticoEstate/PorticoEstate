<!-- $Id$ -->

	<xsl:template name="cat_select">
		<xsl:variable name="lang_cat_statustext"><xsl:value-of select="lang_cat_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="select_name"><xsl:value-of select="select_name"></xsl:value-of></xsl:variable>
		<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_cat_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_cat"></xsl:value-of></option>
			<xsl:apply-templates select="cat_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="cat_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}{cat_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}{cat_id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
