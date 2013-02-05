  <!-- $Id$ -->
	<xsl:template name="status_select">
		<xsl:variable name="lang_status_statustext">
			<xsl:value-of select="lang_status_statustext"/>
		</xsl:variable>
		<xsl:variable name="status_name">
			<xsl:value-of select="status_name"/>
		</xsl:variable>
		<select name="{$status_name}" class="forms" title="{$lang_status_statustext}">
			<option value="">
				<xsl:value-of select="lang_no_status"/>
			</option>
			<xsl:apply-templates select="status_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="status_list">
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
