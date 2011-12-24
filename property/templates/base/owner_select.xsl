<!-- $Id$ -->

	<xsl:template name="owner_select">
		<xsl:variable name="lang_owner_statustext"><xsl:value-of select="lang_owner_statustext"/></xsl:variable>
		<xsl:variable name="owner_name"><xsl:value-of select="owner_name"/></xsl:variable>
		<select name="{$owner_name}" class="forms" onMouseover="window.status='{$lang_owner_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_show_all"/></option>
			<xsl:apply-templates select="owner_list"/>
		</select>
	</xsl:template>

	<xsl:template match="owner_list">
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
