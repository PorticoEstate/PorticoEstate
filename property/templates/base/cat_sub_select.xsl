
<!-- $Id$ -->
<xsl:template name="cat_sub_select">
	<xsl:param name="class" />
	<xsl:param name="id" />
	<xsl:variable name="lang_cat_sub_statustext">
		<xsl:value-of select="lang_cat_sub_statustext"/>
	</xsl:variable>
	<xsl:variable name="cat_sub_name">
		<xsl:value-of select="cat_sub_name"/>
	</xsl:variable>
	<select name="{$cat_sub_name}"  title="{$lang_cat_sub_statustext}" class="pure-input-1-2">
		<xsl:attribute name="data-validation">
			<xsl:text>category</xsl:text>
		</xsl:attribute>
		<xsl:choose>
			<xsl:when test="$class !=''">
				<xsl:attribute name="class">
					<xsl:value-of select="$class"/>
				</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="class">
					<xsl:text>pure-input-1-2</xsl:text>
				</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="$id !=''">
				<xsl:attribute name="id">
					<xsl:value-of select="$id"/>
				</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="id">
					<xsl:text>global_category_id</xsl:text>
				</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>

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
			<option value="{$id}" selected="selected" title="{title}">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$id}"  title="{title}">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
