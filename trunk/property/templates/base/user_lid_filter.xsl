<!-- $Id$ -->

	<xsl:template name="user_lid_filter">
		<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
		<xsl:variable name="select_user_name"><xsl:value-of select="select_user_name"/></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
		<form method="post" action="{$select_action}">
			<select name="{$select_user_name}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_user_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value="none"><xsl:value-of select="lang_no_user"/></option>
				<xsl:apply-templates select="user_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<xsl:template match="user_list">
		<xsl:variable name="lid"><xsl:value-of select="lid"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected">
					<xsl:value-of select="lastname"/>
					<xsl:text>, </xsl:text>
					<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}">
					<xsl:value-of select="lastname"/>
					<xsl:text>, </xsl:text>
					<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
