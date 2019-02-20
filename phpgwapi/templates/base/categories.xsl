<!-- $Id$ -->

<xsl:template name="categories">
	<xsl:param name="class" />
	<xsl:choose>
		<xsl:when test="cat_filter != ''">
			<xsl:apply-templates select="cat_filter"/>
		</xsl:when>
		<xsl:when test="cat_select != ''">
			<xsl:apply-templates select="cat_select">
				<xsl:with-param name="class">
					<xsl:value-of select="$class"/>
				</xsl:with-param>
			</xsl:apply-templates>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="cat_filter">
	<!--<xsl:param name="class" />-->
	<xsl:variable name="select_url">
		<xsl:value-of select="select_url"/>
	</xsl:variable>
	<xsl:variable name="select_name">
		<xsl:value-of select="select_name"/>
	</xsl:variable>
	<xsl:variable name="lang_submit">
		<xsl:value-of select="lang_submit"/>
	</xsl:variable>
	<form method="post" action="{$select_url}">
		<select name="{$select_name}" onChange="this.form.submit();">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_cat_statustext"/>
			</xsl:attribute>
			<option value="none">
				<xsl:value-of select="lang_no_cat"/>
			</option>
			<xsl:apply-templates select="cat_list"/>
		</select>
		<noscript>
			<xsl:text> </xsl:text>
			<input type="submit" name="submit" value="{$lang_submit}"/>
		</noscript>
	</form>
</xsl:template>

<xsl:template match="cat_select" xmlns:php="http://php.net/xsl">
	<xsl:param name="class" />
	<xsl:variable name="lang_cat_statustext">
		<xsl:value-of select="lang_cat_statustext"/>
	</xsl:variable>
	<xsl:variable name="select_name">
		<xsl:value-of select="select_name"/>
	</xsl:variable>
	<select id = "global_category_id" name="{$select_name}" title="{$lang_cat_statustext}">
		<xsl:choose>
			<xsl:when test="$class != ''">
				<xsl:attribute name="class">
					<xsl:value-of select="$class"/>
				</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="class">
					<xsl:value-of select="class"/>
				</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="disabled = 1">
			<xsl:attribute name="disabled">
				<xsl:text>disabled</xsl:text>
			</xsl:attribute>
		</xsl:if>
		<xsl:if test="required = 1">
			<xsl:attribute name="data-validation">
				<xsl:text>required</xsl:text>
			</xsl:attribute>
			<xsl:attribute name="data-validation-error-msg">
				<xsl:value-of select="php:function('lang', 'Please select a category !')"/>
			</xsl:attribute>
		</xsl:if>
		<option value="">
			<xsl:value-of select="lang_no_cat"/>
		</option>
		<xsl:apply-templates select="cat_list"/>
	</select>
	<xsl:if test="disabled = 1">
		<input type="hidden" name="{$select_name}" value="{hidden_value}"/>
	</xsl:if>

</xsl:template>

<xsl:template match="cat_list">
	<option value="{cat_id}">
		<xsl:if test="selected != ''">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:if test="description != ''">
			<xsl:attribute name="title">
				<xsl:value-of select="description"/>
			</xsl:attribute>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

