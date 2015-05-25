  <!-- $Id$ -->
	<xsl:template name="select_district">
		<xsl:variable name="lang_district_statustext">
			<xsl:value-of select="lang_district_statustext"/>
		</xsl:variable>
		<xsl:variable name="select_district_name">
			<xsl:value-of select="select_district_name"/>
		</xsl:variable>
		<xsl:variable name="district_required">
			<xsl:value-of select="district_required"/>
		</xsl:variable>
		<select name="{$select_district_name}" class="forms" onMouseover="window.status='{$lang_district_statustext}'; return true;" onMouseout="window.status='';return true;">
			<xsl:if test="$district_required = 1">
				<xsl:attribute name="data-validation">
					<xsl:text>required</xsl:text>
				</xsl:attribute>
				<xsl:attribute name="data-validation-error-msg">
					<xsl:value-of select="php:function('lang', 'Please select a district !')"/>
				</xsl:attribute>
			</xsl:if>
			<option value="">
				<xsl:value-of select="lang_no_district"/>
			</option>
			<xsl:apply-templates select="district_list"/>
		</select>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="district_list">
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
