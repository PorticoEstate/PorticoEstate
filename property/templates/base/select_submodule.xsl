<!-- $Id$ -->

	<xsl:template name="select_submodule">
		<xsl:variable name="lang_submodule_statustext"><xsl:value-of select="lang_submodule_statustext"/></xsl:variable>
		<xsl:variable name="select_name_submodule"><xsl:value-of select="select_name_submodule"/></xsl:variable>
		<select name="{$select_name_submodule}" class="forms" onMouseover="window.status='{$lang_submodule_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_submodule"/></option>
			<xsl:apply-templates select="submodule_list"/>
		</select>
	</xsl:template>

	<xsl:template match="submodule_list">
		<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
