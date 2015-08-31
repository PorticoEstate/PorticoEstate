<!-- $Id$ -->
<xsl:template name="select_location" xmlns:php="http://php.net/xsl">
	<xsl:variable name="select_name_location">
		<xsl:value-of select="select_name_location"/>
	</xsl:variable>
	<select name="{$select_name_location}" onMouseout="window.status='';return true;">
		<xsl:attribute name="title">
			<xsl:value-of select="lang_location_statustext"/>
		</xsl:attribute>
		<xsl:if test="select_location_required = '1'">
			<xsl:attribute name="data-validation">
				<xsl:text>required</xsl:text>
			</xsl:attribute>
			<xsl:attribute name="data-validation-error-msg">
				<xsl:value-of select="php:function('lang', 'Please enter a location !')"/>
			</xsl:attribute>

		</xsl:if>

		<option value="">
			<xsl:value-of select="lang_no_location"/>
		</option>
		<xsl:apply-templates select="location_list"/>
	</select>
</xsl:template>

<xsl:template match="location_list">
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="descr"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{id}">
				<xsl:value-of disable-output-escaping="yes" select="descr"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
