<!-- $Id$ -->

<xsl:template match="country_filter">
	<xsl:variable name="select_url"><xsl:value-of select="select_url"/></xsl:variable>
	<xsl:variable name="select_name"><xsl:value-of select="select_name"/></xsl:variable>
	<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
	<form method="post" action="{$select_url}">
		<select name="{$select_name}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
			<xsl:attribute name="onMouseover">
				<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_country_statustext"/>
				<xsl:text>'; return true;</xsl:text>
			</xsl:attribute>
			<option value="none"><xsl:value-of select="lang_no_country"/></option>
				<xsl:apply-templates select="country_list"/>
		</select>
	</form>
</xsl:template>

<xsl:template match="country_select">
	<xsl:variable name="lang_country_statustext" select="lang_country_statustext"/>
	<xsl:variable name="select_name" select="select_name"/>
	<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_country_statustext}'; return true;" onMouseout="window.status='';return true;">
		<xsl:apply-templates select="country_list"/>
	</select>
</xsl:template>

<xsl:template match="country_list">
	<xsl:variable name="country_code" select="country_code" />
	<option value="{$country_code}">
		<xsl:choose>
			<xsl:when test="selected != ''">
			 	<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:when>
			<xsl:otherwise />
		</xsl:choose>
		<xsl:value-of select="country_name" />
	</option>
</xsl:template>

<xsl:template match="continent_select">
	<xsl:variable name="lang_continent_statustext" select="lang_continent_statustext"/>
	<xsl:variable name="select_name" select="select_name"/>
	<select name="{$select_name}" class="forms" onMouseover="window.status='{$lang_continent_statustext}'; return true;" onMouseout="window.status='';return true;">
		<xsl:apply-templates select="continent_list" />
	</select>
</xsl:template>

<xsl:template match="continent_list">
	<xsl:variable name="continent_name" select="continent_name" />
	<option value="{$continent_name}">
		<xsl:choose>
			<xsl:when test="selected != ''">
			 	<xsl:attribute name="selected">selected</xsl:attribute>
			</xsl:when>
			<xsl:otherwise />
		</xsl:choose>
		<xsl:value-of select="continent_title" />
	</option>
</xsl:template>
