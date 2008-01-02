<!-- $Id: filter_district.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="filter_district">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="select_district_name"><xsl:value-of select="select_district_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>

		<form method="post" action="{$select_action}" class="menu" title="District">
			<select name="{$select_district_name}" title="District">
				<option value=""><xsl:value-of select="lang_no_district"/></option>
				<xsl:apply-templates select="district_list"/>
			</select>
			<input type="submit" name="submit" value="Submit"/>
		</form>
	</xsl:template>

	<xsl:template match="district_list">
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
