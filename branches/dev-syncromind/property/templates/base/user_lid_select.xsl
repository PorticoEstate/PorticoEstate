  <!-- $Id$ -->
	<xsl:template name="user_lid_select">
		<xsl:variable name="lang_user_statustext">
			<xsl:value-of select="lang_user_statustext"/>
		</xsl:variable>
		<xsl:variable name="select_user_name">
			<xsl:value-of select="select_user_name"/>
		</xsl:variable>
		<select name="{$select_user_name}" class="forms" onMouseover="window.status='{$lang_user_statustext}'; return true;" onMouseout="window.status='';return true;">
			<!--<option value=""><xsl:value-of select="lang_no_user"/></option> -->
			<xsl:apply-templates select="user_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="user_list">
		<xsl:variable name="lid">
			<xsl:value-of select="lid"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="firstname"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="lastname"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}">
					<xsl:value-of disable-output-escaping="yes" select="firstname"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="lastname"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
