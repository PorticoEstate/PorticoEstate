
<!-- $Id$ -->
<xsl:template name="branch_select">
	<xsl:param name="class" />
	<xsl:variable name="lang_branch_statustext">
		<xsl:value-of select="lang_branch_statustext"/>
	</xsl:variable>
	<xsl:variable name="select_name">
		<xsl:value-of select="select_branch"/>
	</xsl:variable>
	<select name="{$select_name}" title="{$lang_branch_statustext}">
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
		<xsl:attribute name="data-validation">
			<xsl:text>required</xsl:text>
		</xsl:attribute>
		<option value="">
			<xsl:value-of select="lang_no_branch"/>
		</option>
		<xsl:apply-templates select="branch_list"/>
	</select>
</xsl:template>

<!-- New template-->
<xsl:template match="branch_list">
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
