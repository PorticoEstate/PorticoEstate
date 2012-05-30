  <!-- $Id$ -->
	<xsl:template name="building_part_select">
		<xsl:variable name="lang_building_part_statustext">
			<xsl:value-of select="lang_building_part_statustext"/>
		</xsl:variable>
		<xsl:variable name="select_name">
			<xsl:value-of select="select_building_part"/>
		</xsl:variable>
		<select name="{$select_name}" class="forms" title="{$lang_building_part_statustext}">
			<option value="">
				<xsl:value-of select="lang_no_building_part"/>
			</option>
			<xsl:apply-templates select="building_part_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="building_part_list">
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
