<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- BEGIN cat_list -->

<xsl:template match="edit">
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{form_action}">
		<table border="0" cellspacing="2" cellpadding="2" class="pure-table pure-table-bordered">
			<xsl:apply-templates select="cat_header"/>
			<xsl:apply-templates select="cat_data"/>
		</table>
		<xsl:apply-templates select="cat_add"/>
	</form>
</xsl:template>

<!-- BEGIN cat_header -->

<xsl:template match="cat_header">
	<tr class="th">
		<th width="45%">
			<xsl:value-of select="lang_name"/>
		</th>
		<th width="1%" align="center">
			<xsl:value-of select="lang_status"/>
		</th>
		<th width="45%" align="center">
			<xsl:value-of select="lang_edit"/>
		</th>
	</tr>
</xsl:template>

<!-- BEGIN cat_data -->

<xsl:template match="cat_data" xmlns:php="http://php.net/xsl">
	<tr>
		<xsl:choose>
			<xsl:when test="main = 'yes'">
				<td class="alarm">
					<b>
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</b>
				</td>
			</xsl:when>
			<xsl:otherwise>
				<td>
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</td>
			</xsl:otherwise>
		</xsl:choose>
		<td align="center">
			<xsl:value-of select="status_text"/>
		</td>
		<td align="center">
			<xsl:if test="main != 'yes'">
				<select name="values[{cat_id}]" >
					<option value="">
						<xsl:value-of select="php:function('lang', 'select')"/>
					</option>
					<xsl:apply-templates select="group_list/options"/>
				</select>
			</xsl:if>
		</td>
	</tr>
</xsl:template>

<!-- BEGIN cat_add -->

<xsl:template match="cat_add">
	<table>
		<tr height="50" valign="bottom">
			<td colspan="2">
				<xsl:variable name="lang_add">
					<xsl:value-of select="php:function('lang', 'save')"/>
				</xsl:variable>
				<input type="submit" name="save" value="{$lang_add}" class="pure-button pure-button-primary" >
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</td>
			<td colspan="3" align="right">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="//cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{$cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- END cat_list -->


<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 1">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>