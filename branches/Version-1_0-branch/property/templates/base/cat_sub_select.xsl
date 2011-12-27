  <!-- $Id$ -->
	<xsl:template name="cat_sub_select">
		<xsl:variable name="lang_cat_sub_statustext">
			<xsl:value-of select="lang_cat_sub_statustext"/>
		</xsl:variable>
		<xsl:variable name="cat_sub_name">
			<xsl:value-of select="cat_sub_name"/>
		</xsl:variable>
		<select name="{$cat_sub_name}" class="forms" title="{$lang_cat_sub_statustext}">
			<xsl:apply-templates select="cat_sub_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="cat_sub_list">
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
