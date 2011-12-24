<!-- $Id$ -->

	<xsl:template name="select_multi_user">
		<xsl:variable name="lang_user_statustext"><xsl:value-of select="lang_user_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="select_name_user"><xsl:value-of select="select_name_user"></xsl:value-of></xsl:variable>
		<select name="{$select_name_user}" class="forms" onMouseover="window.status='{$lang_user_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_user"></xsl:value-of></option>
			<xsl:apply-templates select="user_list"></xsl:apply-templates>
		</select>
	</xsl:template>

	<xsl:template match="user_list">
		<xsl:variable name="account_id"><xsl:value-of select="account_id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$account_id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="account_lid"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$account_id}"><xsl:value-of disable-output-escaping="yes" select="account_lid"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
