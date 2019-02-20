
<!-- $Id$ -->
<xsl:template name="user_id_select">
	<xsl:param name="class" />
	<xsl:variable name="lang_user_statustext">
		<xsl:value-of select="lang_user_statustext"/>
	</xsl:variable>
	<xsl:variable name="select_user_name">
		<xsl:value-of select="select_user_name"/>
	</xsl:variable>
	<select name="{$select_user_name}" id="user_id" title="{$lang_user_statustext}">
		<xsl:choose>
			<xsl:when test="$class != ''">
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
		<option value="">
			<xsl:value-of select="lang_no_user"/>
		</option>
		<xsl:apply-templates select="user_list"/>
	</select>
</xsl:template>

<!-- New template-->
<xsl:template match="user_list">
	<xsl:variable name="user_id">
		<xsl:value-of select="user_id"/>
	</xsl:variable>
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected = 1">
			<option value="{$user_id}{$id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$user_id}{$id}">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
