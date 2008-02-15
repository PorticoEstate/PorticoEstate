<!-- $Id: owner_filter.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="owner_filter">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="owner_name"><xsl:value-of select="owner_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>

		<form method="post" action="{$select_action}" class="menu" title="Owner">
			<select name="{$owner_name}">
				<option value=""><xsl:value-of select="lang_show_all"/></option>
				<xsl:apply-templates select="owner_list"/>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</select>
		</form>
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
