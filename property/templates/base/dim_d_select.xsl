
<!-- $Id$ -->
<xsl:template name="dim_d_select">
	<xsl:param name="class" />
	<xsl:variable name="lang_dim_d_statustext">
		<xsl:value-of select="lang_dim_d_statustext"/>
	</xsl:variable>
	<xsl:variable name="select_name">
		<xsl:value-of select="select_dim_d"/>
	</xsl:variable>
	<select name="{$select_name}" class="forms" title="{$lang_dim_d_statustext}">
		<xsl:choose>
			<xsl:when test="$class != ''">
				<xsl:attribute name="class">
					<xsl:value-of select="$class"/>
				</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="class">
					<xsl:value-of select="class"/>
				</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
		<option value="">
			<xsl:value-of select="lang_no_dim_d"/>
		</option>
		<xsl:apply-templates select="dim_d_list"/>
	</select>
</xsl:template>

<!-- New template-->
<xsl:template match="dim_d_list">
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
