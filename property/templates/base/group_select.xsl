<!-- $Id$ -->

	<xsl:template name="group_select">
		<xsl:variable name="lang_group_statustext"><xsl:value-of select="lang_group_statustext"/></xsl:variable>
		<xsl:variable name="select_group_name"><xsl:value-of select="select_group_name"/></xsl:variable>
		<select name="{$select_group_name}" class="forms" title="{$lang_group_statustext}" onMouseover="window.status='{$lang_group_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_group"/></option>
			<xsl:apply-templates select="group_list"/>
		</select>
	</xsl:template>

	<xsl:template match="group_list">
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
