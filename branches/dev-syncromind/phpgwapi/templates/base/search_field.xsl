<!-- $Id$ -->

<xsl:template name="search_field">
	<xsl:apply-templates select="search_data"/>
</xsl:template>

<xsl:template match="search_data">
	<xsl:variable name="select_url">
		<xsl:value-of select="select_url"/>
	</xsl:variable>
	<xsl:variable name="query">
		<xsl:value-of select="query"/>
	</xsl:variable>
	<xsl:variable name="lang_search">
		<xsl:value-of select="lang_search"/>
	</xsl:variable>
	<form method="post" action="{$select_url}">
		<input type="text" name="query" value="{$query}">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_searchfield_statustext"/>
			</xsl:attribute>
		</input>
		<xsl:text> </xsl:text>
		<input type="submit" name="submit" value="{$lang_search}">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_searchbutton_statustext"/>
			</xsl:attribute>
		</input>
	</form>
</xsl:template>
