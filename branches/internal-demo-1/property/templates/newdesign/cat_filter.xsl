<!-- $Id: cat_filter.xsl,v 1.2 2006/04/17 11:36:05 sigurdne Exp $ -->

	<xsl:template name="cat_filter">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="select_name"><xsl:value-of select="select_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>

		<form method="post" action="{$select_action}" class="menu" title="Category">
			<select name="{$select_name}">
				<option value=""><xsl:value-of select="lang_no_cat"/></option>
				<xsl:apply-templates select="cat_list"/>
			</select>
			<input type="submit" name="submit" value="Submit"/>
		</form>
	</xsl:template>

	<xsl:template match="cat_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
