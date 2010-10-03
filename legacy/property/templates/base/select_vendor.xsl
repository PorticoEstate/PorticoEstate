<!-- $Id: select_vendor.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="select_vendor">
	<xsl:variable name="lang_vendor_statustext"><xsl:value-of select="lang_vendor_statustext"/></xsl:variable>
		<select name="vendor_id" class="forms" onMouseover="window.status='{$lang_vendor_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value=""><xsl:value-of select="lang_no_vendor"/></option>
				<xsl:apply-templates select="vendor_list"/>
		</select>
	</xsl:template>

	<xsl:template match="vendor_list">
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
