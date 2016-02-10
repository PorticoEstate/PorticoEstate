
<!-- $Id$ -->
<xsl:template name="select_permission">
		<xsl:variable name="select_action">
			<xsl:value-of select="select_action"/>
		</xsl:variable>
		<xsl:variable name="select_name_permission">
			<xsl:value-of select="select_name_permission"/>
		</xsl:variable>
		<select name="{$select_name_permission}" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_permission_statustext"/>
			</xsl:attribute>
			<option value="">
				<xsl:value-of select="lang_no_permission"/>
			</option>
			<xsl:apply-templates select="permission_list"/>
		</select>
</xsl:template>

<!-- New template-->
<xsl:template match="permission_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>
