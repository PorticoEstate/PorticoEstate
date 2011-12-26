  <!-- $Id$ -->
	<xsl:template name="select_part_of_town">
		<xsl:variable name="lang_town_statustext">
			<xsl:value-of select="lang_town_statustext"/>
		</xsl:variable>
		<xsl:variable name="select_name_part_of_town">
			<xsl:value-of select="select_name_part_of_town"/>
		</xsl:variable>
		<select name="{$select_name_part_of_town}" class="forms" onMouseover="window.status='{$lang_town_statustext}'; return true;" onMouseout="window.status='';return true;">
			<option value="">
				<xsl:value-of select="lang_no_part_of_town"/>
			</option>
			<xsl:apply-templates select="part_of_town_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="part_of_town_list">
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
