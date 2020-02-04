<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="log">
			<xsl:apply-templates select="log"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
	
<xsl:template match="list">
	<xsl:choose>
		<xsl:when test="menu != ''">
			<xsl:apply-templates select="menu"/>
		</xsl:when>
	</xsl:choose>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
				<!--	<xsl:with-param name="nextmatchs_params"/>
				</xsl:call-template> -->
			</td>
		</tr>
	</table>
	<table class="pure-table pure-table-bordered">
		<thead>
			<xsl:apply-templates select="table_header"/>
		</thead>
		<tbody>
			<xsl:apply-templates select="values"/>
		</tbody>
	</table>
	<xsl:apply-templates select="table_add"/>

</xsl:template>

<xsl:template match="table_header">
	<xsl:variable name="sort_code">
		<xsl:value-of select="sort_code"/>
	</xsl:variable>
	<tr>
		<th style="width:10%; text-align:left;">
			<a href="{$sort_code}">
				<xsl:value-of select="lang_code"/>
			</a>
		</th>
		<th  style="width:10%; text-align:left;">
			<xsl:value-of select="lang_user"/>
		</th>
		<th style="width:50%; text-align:left;">
			<xsl:value-of select="lang_exec"/>
		</th>
		<th  style="width:10%; text-align:left;">
			<xsl:value-of select="lang_edit"/>
		</th>
		<th style="width:10%; text-align:left;">
			<xsl:value-of select="lang_delete"/>
		</th>
	</tr>
</xsl:template>

<xsl:template match="values">
	<xsl:variable name="lang_edit_text">
		<xsl:value-of select="lang_edit_text"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_text">
		<xsl:value-of select="lang_delete_text"/>
	</xsl:variable>
	<tr>
		<td align="left">
			<xsl:value-of select="code"/>
		</td>
		<td align="left">
			<xsl:value-of select="user"/>
		</td>
		<td align="left">
			<xsl:value-of select="exec"/>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" title="{$lang_edit_text}">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" title="{$lang_delete_text}">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<xsl:template match="table_add">
	<dl>
		<dt height="50">
			<xsl:variable name="add_action">
				<xsl:value-of select="add_action"/>
			</xsl:variable>
			<xsl:variable name="lang_add">
				<xsl:value-of select="lang_add"/>
			</xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</form>
		</dt>
	</dl>
</xsl:template>

<!-- add / edit command -->
<xsl:template match="edit_command" xmlns:php="http://php.net/xsl">
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<div align="left">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" action="{$form_action}" class="pure-form pure-form-aligned">

			<xsl:choose>
				<xsl:when test="value_id != ''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_id"/>
						</label>
						<xsl:value-of select="value_id"/>
					</div>
				</xsl:when>
			</xsl:choose>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_code"/>
				</label>
				<input type="text" size="20" name="values[code]" value="{value_code}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_code_status_text"/>
					</xsl:attribute>
				</input>
			</div>
			<p>
				<xsl:value-of select="lang_help1"/>
			</p>
			<p>
				<xsl:value-of select="lang_help2"/>
			</p>
			<p>
				<xsl:value-of select="lang_help3"/>
			</p>
			<p>
				<xsl:value-of select="lang_help4"/>
			</p>
			<p>
				<xsl:value-of select="lang_help5"/>
			</p>
			<p>
				<xsl:value-of select="lang_help6"/>
			</p>
			<p>
				<xsl:value-of select="lang_binary_path"/>
				<xsl:text>: </xsl:text>
				<b>
					<xsl:value-of select="value_binary_path"/>
				</b>
			</p>

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_type"/>
				</label>
				<xsl:variable name="lang_type_status_text">
					<xsl:value-of select="lang_type_status_text"/>
				</xsl:variable>
				<select name="values[type]" class="forms" title="{$lang_type_status_text}">
					<option value="">
						<xsl:value-of select="php:function('lang', 'select')" />
					</option>
					<xsl:apply-templates select="type_list"/>
				</select>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_exec"/>
				</label>
				<input type="text" size="60" name="values[exec]" value="{value_exec}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_exec_status_text"/>
					</xsl:attribute>
				</input>
			</div>

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_descr"/>
				</label>
				<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_descr_status_text"/>
					</xsl:attribute>
					<xsl:value-of select="value_descr"/>
				</textarea>
			</div>

			<div class="pure-controls">
				<xsl:variable name="lang_save">
					<xsl:value-of select="lang_save"/>
				</xsl:variable>
				<input type="submit" name="values[save]" value="{$lang_save}" class="pure-button pure-button-primary">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_status_text"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_apply">
					<xsl:value-of select="lang_apply"/>
				</xsl:variable>
				<input type="submit" name="values[apply]" value="{$lang_apply}" class="pure-button pure-button-primary">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_apply_status_text"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="lang_cancel"/>
				</xsl:variable>
				<input type="submit" name="values[cancel]" value="{$lang_cancel}" class="pure-button pure-button-primary">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_cancel_status_text"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="log">
	<xsl:choose>
		<xsl:when test="menu != ''">
			<xsl:apply-templates select="menu"/>
		</xsl:when>
	</xsl:choose>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td>
				<xsl:call-template name="cat_filter"/>
			</td>

			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
				<!--	<xsl:with-param name="nextmatchs_params"/>
				</xsl:call-template> -->
			</td>
		</tr>
	</table>
	<table class="pure-table pure-table-bordered">
		<thead>
			<xsl:apply-templates select="table_header_log"/>
		</thead>
		<tbody>
			<xsl:apply-templates select="values_log"/>
		</tbody>
	</table>
</xsl:template>

<xsl:template match="table_header_log">
	<xsl:variable name="sort_id">
		<xsl:value-of select="sort_id"/>
	</xsl:variable>
	<xsl:variable name="sort_sender">
		<xsl:value-of select="sort_sender"/>
	</xsl:variable>
	<xsl:variable name="sort_code">
		<xsl:value-of select="sort_code"/>
	</xsl:variable>
	<tr class="th">
		<th  style="width:10%; text-align:left;">
			<a href="{$sort_id}">
				<xsl:value-of select="lang_id"/>
			</a>
		</th>
		<th  style="width:10%; text-align:left;">
			<a href="{$sort_code}">
				<xsl:value-of select="lang_code"/>
			</a>
		</th>
		<th  style="width:10%; text-align:left;">
			<a href="{$sort_sender}">
				<xsl:value-of select="lang_sender"/>
			</a>
		</th>
		<th  style="width:10%; text-align:left;">
			<xsl:value-of select="lang_param"/>
		</th>
		<th  style="width:10%; text-align:left;">
			<xsl:value-of select="lang_datetime"/>
		</th>
		<th  style="width:10%; text-align:center;">
			<xsl:value-of select="lang_success"/>
		</th>
	</tr>
</xsl:template>

<xsl:template match="values_log">
	<xsl:variable name="lang_edit_text">
		<xsl:value-of select="lang_edit_text"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_text">
		<xsl:value-of select="lang_delete_text"/>
	</xsl:variable>
	<tr>
		<td style="text-align:left;">
			<xsl:value-of select="id"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="code"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="sender"/>
		</td>
		<td style="text-align:left;">
			<xsl:choose>
				<xsl:when test="link_redirect != ''">
					<a href="{link_redirect}" >
						<xsl:value-of select="param"/>
					</a>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="param"/>
				</xsl:otherwise>
			</xsl:choose>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="datetime"/>
		</td>
		<td style="text-align:center;">
			<xsl:value-of select="success"/>
		</td>
	</tr>
</xsl:template>

	
<xsl:template match="type_list">
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
