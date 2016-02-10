
<!-- $Id$ -->
<xsl:template name="filter_part_of_town">
		<xsl:variable name="select_action">
			<xsl:value-of select="select_action"/>
		</xsl:variable>
		<xsl:variable name="select_name_part_of_town">
			<xsl:value-of select="select_name_part_of_town"/>
		</xsl:variable>
		<xsl:variable name="lang_submit">
			<xsl:value-of select="lang_submit"/>
		</xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="{$select_name_part_of_town}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_town_statustext"/>
				</xsl:attribute>
				<option value="">
					<xsl:value-of select="lang_no_part_of_town"/>
				</option>
				<xsl:apply-templates select="part_of_town_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
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
