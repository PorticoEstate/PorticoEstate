<!-- $Id$ -->

<xsl:template name="cats">
	<xsl:choose>
		<xsl:when test="cat_list">
			<xsl:apply-templates select="cat_list"/>
		</xsl:when>
		<xsl:when test="cat_edit">
			<xsl:apply-templates select="cat_edit"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- BEGIN cat_list -->

<xsl:template match="cat_list">
	<table border="0" cellspacing="2" cellpadding="2" class="pure-table pure-table-bordered">
		<tr>
			<td colspan="6" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
		<tr>
			<td colspan="6" width="100%" align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<xsl:apply-templates select="cat_header"/>
		<xsl:apply-templates select="cat_data"/>
	</table>
	<xsl:apply-templates select="cat_add"/>
</xsl:template>

<!-- BEGIN cat_header -->

<xsl:template match="cat_header">
	<xsl:variable name="sort_name" select="sort_name"/>
	<xsl:variable name="sort_descr" select="sort_descr"/>
	<xsl:variable name="lang_sort_statustext" select="lang_sort_statustext"/>
	<tr class="th">
		<th width="20%">
			<a href="{$sort_name}" title="{$lang_sort_statustext}" class="th_text">
				<xsl:value-of select="lang_name"/>
			</a>
		</th>
		<th width="32%">
			<a href="{$sort_descr}" title="{$lang_sort_statustext}" class="th_text">
				<xsl:value-of select="lang_descr"/>
			</a>
		</th>
		<th width="8%" align="center">
			<xsl:value-of select="lang_status"/>
		</th>
		<th width="8%" align="center">
			<xsl:value-of select="lang_add_sub"/>
		</th>
		<th width="8%" align="center">
			<xsl:value-of select="lang_edit"/>
		</th>
		<th width="8%" align="center">
			<xsl:value-of select="lang_delete"/>
		</th>
	</tr>
</xsl:template>

<!-- BEGIN cat_data -->

<xsl:template match="cat_data">
	<xsl:variable name="lang_add_sub_statustext">
		<xsl:value-of select="lang_add_sub_statustext"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_statustext">
		<xsl:value-of select="lang_edit_statustext"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_statustext">
		<xsl:value-of select="lang_delete_statustext"/>
	</xsl:variable>
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class"/>
				</xsl:when>
				<xsl:when test="position() mod 2 = 0">
					<xsl:text>row_off</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>row_on</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="status != 1">
					<xsl:text> inactive</xsl:text>
				</xsl:when>
			</xsl:choose>
		</xsl:attribute>
		<xsl:choose>
			<xsl:when test="main = 'yes'">
				<td class="alarm">
					<b>
						<xsl:value-of disable-output-escaping="yes" select="name"/>
					</b>
				</td>
				<td class="alarm">
					<b>
						<xsl:value-of select="descr"/>
					</b>
				</td>
			</xsl:when>
			<xsl:otherwise>
				<td>
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</td>
				<td>
					<xsl:value-of select="descr"/>
				</td>
			</xsl:otherwise>
		</xsl:choose>
		<td align="center">
			<xsl:value-of select="status_text"/>
		</td>
		<td align="center">
			<xsl:variable name="add_sub_url" select="add_sub_url"/>
			<a href="{add_sub_url}" title="{$lang_add_sub_statustext}" class="th_text">
				<xsl:value-of select="lang_add_sub"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="edit_url" select="edit_url"/>
			<a href="{edit_url}" title="{$lang_edit_statustext}" class="th_text">
				<xsl:value-of select="lang_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="delete_url" select="delete_url"/>
			<a href="{delete_url}" title="{$lang_delete_statustext}" class="th_text">
				<xsl:value-of select="lang_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- BEGIN cat_add -->

<xsl:template match="cat_add">
	<xsl:variable name="action_url">
		<xsl:value-of select="action_url"/>
	</xsl:variable>
	<form method="post" action="{$action_url}">
		<table>
			<tr height="50" valign="bottom">
				<td colspan="2">
					<xsl:variable name="lang_add">
						<xsl:value-of select="lang_add"/>
					</xsl:variable>
					<input type="submit" name="add" value="{$lang_add}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td colspan="3" align="right">
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<input type="submit" name="done" value="{$lang_done}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_done_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

<!-- END cat_list -->

<!-- BEGIN cat_edit -->

<xsl:template match="cat_edit" xmlns:php="http://php.net/xsl">
	<xsl:variable name="edit_url" select="edit_url"/>
	<form method="post" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td colspan="3" align="center">
					<xsl:value-of select="message"/>
				</td>
			</tr>
			<tr>
				<td width="23%" colspan="2">
					<xsl:value-of select="lang_parent"/>
				</td>
				<td width="77%">
					<xsl:call-template name="categories"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<xsl:value-of select="lang_name"/>:</td>
				<td>
					<input name="values[name]" size="50">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_name_statustext"/>
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="value_name"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td colspan="2" valign="top">
					<xsl:value-of select="lang_descr"/>:</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual" >
						<xsl:attribute name="title">
							<xsl:value-of select="lang_descr_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="value_color">
						<xsl:value-of select="lang_color"/>
					</label>:</td>
				<td>
					<input name="values[color]" id="value_color" value="{value_color}" />
					<img src="img_color_selector" onclick="colorSelector('colorSelector);" alt="{lang_color_selector}" title="lang_color_selector" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="value_icon">
						<xsl:value-of select="php:function('lang', 'icon')"/>
					</label>:</td>
				<td>
					<input name="values[icon]" id="value_icon" value="{value_icon}" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="value_active">
						<xsl:value-of select="php:function('lang', 'active')"/>
					</label>:</td>
				<td>
					<select name="values[active]" id="value_active">
						<xsl:apply-templates select="active_list/options"/>
					</select>
				</td>
			</tr>

			<tr height="50" valign="bottom">
				<td>
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<xsl:variable name="old_parent">
						<xsl:value-of select="old_parent"/>
					</xsl:variable>
					<input type="hidden" name="values[old_parent]" value="{$old_parent}"/>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<xsl:variable name="lang_apply" select="lang_apply"/>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right">
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
	
