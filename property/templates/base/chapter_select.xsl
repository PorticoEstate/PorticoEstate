<!-- $Id$ -->

	<xsl:template name="chapter_select">
		<xsl:variable name="lang_chapter_statustext"><xsl:value-of select="lang_chapter_statustext"/></xsl:variable>
		<xsl:variable name="select_name"><xsl:value-of select="select_chapter"/></xsl:variable>
		<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_chapter_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_chapter"/></option>
			<xsl:apply-templates select="chapter_list"/>
		</select>
	</xsl:template>

	<xsl:template match="chapter_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
