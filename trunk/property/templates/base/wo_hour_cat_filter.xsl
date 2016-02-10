
<!-- $Id$ -->
<xsl:template name="wo_hour_cat_filter">
		<xsl:variable name="select_action">
			<xsl:value-of select="select_action"/>
		</xsl:variable>
		<xsl:variable name="lang_submit">
			<xsl:value-of select="lang_submit"/>
		</xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="wo_hour_cat_id" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_wo_hour_cat_filter_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value="">
					<xsl:value-of select="lang_no_wo_hour_cat"/>
				</option>
				<xsl:apply-templates select="wo_hour_cat_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
</xsl:template>

<!-- New template-->
<xsl:template match="wo_hour_cat_list">
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
