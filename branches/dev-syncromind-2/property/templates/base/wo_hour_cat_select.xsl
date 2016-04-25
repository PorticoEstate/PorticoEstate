
<!-- $Id$ -->
<xsl:template name="wo_hour_cat_select">
		<select name="wo_hour_cat_id" onMouseout="window.status='';return true;">
			<xsl:attribute name="onMouseover">
				<xsl:text>window.status='</xsl:text>
				<xsl:value-of select="lang_wo_hour_cat_filter_statustext"/>
				<xsl:text>'; return true;</xsl:text>
			</xsl:attribute>
			<option value="">
				<xsl:value-of select="lang_no_wo_hour_cat"/>
			</option>
			<xsl:apply-templates select="wo_hour_cat_list"/>
		</select>
</xsl:template>

<!-- New template-->
<xsl:template match="wo_hour_cat_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>
