
<!-- $Id$ -->
<xsl:template name="cat_select">
		<xsl:variable name="lang_cat_statustext">
			<xsl:value-of select="lang_cat_statustext"/>
		</xsl:variable>
		<xsl:variable name="select_name">
			<xsl:value-of select="select_name"/>
		</xsl:variable>
		<select name="{$select_name}" class="forms" title="{$lang_cat_statustext}">
		<xsl:attribute name="data-validation">
			<xsl:text>required</xsl:text>
		</xsl:attribute>
			<option value="">
				<xsl:value-of select="lang_no_cat"/>
			</option>
			<xsl:apply-templates select="cat_list"/>
		</select>
</xsl:template>

<xsl:template name="cat_select_investment">
	<xsl:variable name="lang_cat_statustext">
		<xsl:value-of select="lang_cat_statustext"/>
	</xsl:variable>
	<xsl:variable name="select_name">
		<xsl:value-of select="select_name"/>
	</xsl:variable>
	<select name="{$select_name}" data-validation="write_period_num" id="period_num" class="forms" title="{$lang_cat_statustext}">
		<!--xsl:attribute name="data-validation">
			<xsl:text>required</xsl:text>
		</xsl:attribute-->
		<xsl:attribute name="data-validation-error-msg">
			<xsl:value-of select="php:function('lang', 'Please - select write off period or enter new number of period !')"/>
		</xsl:attribute>
		<option value="">
			<xsl:value-of select="lang_no_cat"/>
		</option>
		<xsl:apply-templates select="cat_list"/>
                        
	</select>
</xsl:template>
<!-- New template-->
<xsl:template match="cat_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}{cat_id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}{cat_id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>
