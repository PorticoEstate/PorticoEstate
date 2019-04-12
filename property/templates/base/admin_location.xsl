
<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="list_attribute">
			<xsl:apply-templates select="list_attribute"/>
		</xsl:when>
		<xsl:when test="edit_attrib">
			<xsl:apply-templates select="edit_attrib"/>
		</xsl:when>
		<xsl:when test="edit_config">
			<xsl:apply-templates select="edit_config"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="list">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
				<!--<xsl:with-param name="nextmatchs_params"/>
				</xsl:call-template> -->
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header"/>
		<xsl:apply-templates select="values"/>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header">
	<xsl:variable name="sort_id">
		<xsl:value-of select="sort_id"/>
	</xsl:variable>
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="right">
			<a href="{$sort_id}">
				<xsl:value-of select="lang_id"/>
			</a>
		</td>
		<td class="th_text" width="10%" align="center">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="20%" align="center">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_categories"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_attribute_group"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_attribute"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values">
	<xsl:variable name="lang_attribute_standardtext">
		<xsl:value-of select="lang_delete_standardtext"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_standardtext">
		<xsl:value-of select="lang_edit_standardtext"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_standardtext">
		<xsl:value-of select="lang_delete_standardtext"/>
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
		</xsl:attribute>
		<td align="right">
			<xsl:value-of select="id"/>
		</td>
		<td align="left">
			<xsl:value-of select="name"/>
		</td>
		<td align="left">
			<xsl:value-of select="first"/>
		</td>
		<td align="center">
			<xsl:variable name="link_categories">
				<xsl:value-of select="link_categories"/>
			</xsl:variable>
			<a href="{$link_categories}" onMouseover="window.status='{lang_category_text}';return true;">
				<xsl:value-of select="text_categories"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_attribute_group">
				<xsl:value-of select="link_attribute_group"/>
			</xsl:variable>
			<a href="{$link_attribute_group}" onMouseover="window.status='';return true;">
				<xsl:value-of select="text_attribute_group"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_attribute">
				<xsl:value-of select="link_attribute"/>
			</xsl:variable>
			<a href="{$link_attribute}" onMouseover="window.status='{$lang_attribute_standardtext}';return true;">
				<xsl:value-of select="text_attribute"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_standardtext}';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- edit_config  -->
<xsl:template match="edit_config">
	<div align="left">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" action="{$form_action}">

			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:for-each select="location_list">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="id"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="name"/>
						</td>
						<td align="left">
							<xsl:choose>
								<xsl:when test="selected='selected' or selected = 1">
									<input type="radio" name="values[{//column_name}]" value="{id}" checked="checked">
										<xsl:attribute name="title">
											<xsl:value-of select="//lang_config_statustext"/>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="radio" name="values[{//column_name}]" value="{id}">
										<xsl:attribute name="title">
											<xsl:value-of select="//lang_config_statustext"/>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
				</xsl:for-each>
				<tr height="50">
					<td>
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_standardtext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		<table>
			<tr>
				<td>
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_standardtext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_add">
	<tr>
		<td height="50">
			<xsl:variable name="add_action">
				<xsl:value-of select="add_action"/>
			</xsl:variable>
			<xsl:variable name="lang_add">
				<xsl:value-of select="lang_add"/>
			</xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_standardtext"/>
					</xsl:attribute>
				</input>
			</form>
		</td>
		<td height="50">
			<xsl:variable name="done_action">
				<xsl:value-of select="done_action"/>
			</xsl:variable>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<form method="post" action="{$done_action}">
				<input type="submit" name="add" value="{$lang_done}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_standardtext"/>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>

	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form method="post" class="pure-form pure-form-aligned" id="form" name="form" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<div class="pure-control-group">
					<xsl:choose>
						<xsl:when test="value_id > 0">
							<label>
								<xsl:value-of select="php:function('lang', 'id')"/>
							</label>
							<xsl:value-of select="value_id"/>
						</xsl:when>
					</xsl:choose>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'name')"/>
					</label>
					<input type="text" name="values[name]" data-validation="required" value="{value_name}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_name_standardtext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'descr')"/>
					</label>
					<textarea cols="60" rows="10" name="values[descr]">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_descr_standardtext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>
					</textarea>
				</div>
				<xsl:choose>
					<xsl:when test="value_id > 0">
						<div class="pure-control-group">
							<label>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Names of levels to list at this level')"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'list info')"/>
							</label>
							<xsl:call-template name="list_info"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'list address')"/>
							</label>
							<input type="checkbox" name="values[list_address]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'list address at this level')"/>
								</xsl:attribute>
								<xsl:if test="value_list_address = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'list documents')"/>
							</label>
							<input type="checkbox" name="values[list_documents]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'list documents at level')"/>
								</xsl:attribute>
								<xsl:if test="value_list_documents = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'enable controller')"/>
							</label>
							<input type="checkbox" name="values[enable_controller]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'This location level is allowed to link to controller')"/>
								</xsl:attribute>
								<xsl:if test="value_enable_controller > '0'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<xsl:if test="value_enable_controller > '1'">
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</xsl:if>

							</input>
						</div>
					</xsl:when>
				</xsl:choose>
				<tr height="50">
					<td>
						<xsl:variable name="lang_save">
							<xsl:value-of select="php:function('lang', 'save')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_standardtext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</div>
		</div>
	</form>
	<table>
		<tr>
			<td>
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="php:function('lang', 'done')"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_done_standardtext"/>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<!-- list attribute -->
<xsl:template match="list_attribute">
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td align="left">
				<xsl:value-of select="lang_type"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="value_type_name"/>
			</td>
		</tr>
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_attrib"/>
		<xsl:apply-templates select="values_attrib"/>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_attrib">
	<xsl:variable name="sort_sorting">
		<xsl:value-of select="sort_sorting"/>
	</xsl:variable>
	<xsl:variable name="sort_id">
		<xsl:value-of select="sort_id"/>
	</xsl:variable>
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="1%" align="center">
			<xsl:value-of select="lang_datatype"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_sorting}">
				<xsl:value-of select="lang_sorting"/>
			</a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values_attrib">
	<xsl:variable name="lang_up_text">
		<xsl:value-of select="lang_up_text"/>
	</xsl:variable>
	<xsl:variable name="lang_down_text">
		<xsl:value-of select="lang_down_text"/>
	</xsl:variable>
	<xsl:variable name="lang_attribute_attribtext">
		<xsl:value-of select="lang_delete_attribtext"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_attribtext">
		<xsl:value-of select="lang_edit_attribtext"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_attribtext">
		<xsl:value-of select="lang_delete_attribtext"/>
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
		</xsl:attribute>
		<td align="left">
			<xsl:value-of select="column_name"/>
		</td>
		<td align="left">
			<xsl:value-of select="input_text"/>
		</td>
		<td align="left">
			<xsl:value-of select="datatype"/>
		</td>
		<td>
			<table align="left">
				<tr>
					<td>
						<xsl:value-of select="sorting"/>
					</td>
					<td align="left">
						<xsl:variable name="link_up">
							<xsl:value-of select="link_up"/>
						</xsl:variable>
						<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;">
							<xsl:value-of select="text_up"/>
						</a>
						<xsl:text> | </xsl:text>
						<xsl:variable name="link_down">
							<xsl:value-of select="link_down"/>
						</xsl:variable>
						<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;">
							<xsl:value-of select="text_down"/>
						</a>
					</td>
				</tr>
			</table>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_attribtext}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_attribtext}';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- add attribute / edit attribute -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit_attrib">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form method="post" class="pure-form pure-form-aligned" id="form" name="form" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<div class="pure-control-group">
					<label>
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<xsl:value-of select="lang_id"/>
							</xsl:when>
							<xsl:otherwise>
							</xsl:otherwise>
						</xsl:choose>
					</label>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<xsl:value-of select="value_id"/>
						</xsl:when>
						<xsl:otherwise>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_column_name"/>
					</label>
					<input type="text" data-validation="required" name="values[column_name]" value="{value_column_name}" maxlength="20">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_column_name_statustext"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_input_text"/>
					</label>
					<input type="text" data-validation="required" name="values[input_text]" value="{value_input_text}" size="60" maxlength="50">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_input_text_statustext"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_statustext"/>
					</label>
					<textarea cols="60" rows="10" data-validation="required" name="values[statustext]">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_statustext_attribtext"/>
						</xsl:attribute>
						<xsl:value-of select="value_statustext"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_location_type"/>
					</label>
					<xsl:variable name="lang_entity_statustext">
						<xsl:value-of select="lang_entity_statustext"/>
					</xsl:variable>
					<xsl:variable name="select_location_type">
						<xsl:value-of select="select_location_type"/>
					</xsl:variable>
					<select name="{$select_location_type}" class="forms" title="{$lang_entity_statustext}" data-validation="required">
						<option value="">
							<xsl:value-of select="lang_no_location_type"/>
						</option>
						<xsl:apply-templates select="entity_list"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'group')"/>
					</label>
					<select name="values[group_id]" class="forms">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'select a group')"/>
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'no group')"/>
						</option>
						<xsl:apply-templates select="attrib_group_list"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'datatype')"/>
					</label>
					<select name="values[column_info][type]" class="forms" data-validation="required">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'select a datatype')"/>
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'no datatype')"/>
						</option>
						<xsl:apply-templates select="datatype_list"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'precision')"/>
					</label>
					<input type="text" name="values[column_info][precision]" value="{value_precision}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'enter the record length')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'scale')"/>
					</label>
					<input type="text" name="values[column_info][scale]" value="{value_scale}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'enter the scale if type is decimal')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'default')"/>
					</label>
					<input type="text" name="values[column_info][default]" value="{value_default}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'enter the default value')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'nullable')"/>
					</label>
					<select name="values[column_info][nullable]" data-validation="required">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'chose if this column is nullable')"/>
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'select nullable')"/>
						</option>
						<xsl:apply-templates select="nullable_list"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'show in lookup forms')"/>
					</label>
					<input type="checkbox" name="values[lookup_form]" value="1">
						<xsl:if test="value_lookup_form = 1">
							<xsl:attribute name="checked">
								<xsl:text>checked</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'check to show this attribue in lookup forms')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'show in list')"/>
					</label>
					<input type="checkbox" name="values[list]" value="1">
						<xsl:if test="value_list = 1">
							<xsl:attribute name="checked">
								<xsl:text>checked</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'check to show this attribute in entity list')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'include in search')"/>
					</label>
					<input type="checkbox" name="values[search]" value="1">
						<xsl:if test="value_search = 1">
							<xsl:attribute name="checked">
								<xsl:text>checked</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'check to show this attribute in location list')"/>
						</xsl:attribute>
					</input>
				</div>
				<xsl:choose>
					<xsl:when test="multiple_choice != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'include as filter')"/>
							</label>
							<input type="checkbox" name="values[table_filter]" value="1">
								<xsl:if test="value_table_filter = 1">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'check to act as filter in list')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_choice"/>
							</label>
							<xsl:call-template name="choice"/>
						</div>
					</xsl:when>
				</xsl:choose>
				<div class="pure-control-group">
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_attribtext"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</div>
	</form>
	<div class="pure-control-group">
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<xsl:variable name="lang_done">
			<xsl:value-of select="lang_done"/>
		</xsl:variable>
		<form method="post" action="{$done_action}">
			<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_done_attribtext"/>
				</xsl:attribute>
			</input>
		</form>
	</div>
</xsl:template>

<!-- New template-->
<!-- entity_list -->
<xsl:template match="entity_list">
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

<!-- New template-->
<!-- attrib_group_list -->
<xsl:template match="attrib_group_list">
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

<!-- New template-->
<!-- datatype_list -->
<xsl:template match="datatype_list">
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

<!-- New template-->
<!-- nullable_list -->
<xsl:template match="nullable_list">
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

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="choice">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<xsl:choose>
			<xsl:when test="value_choice!=''">
				<tr class="th">
					<td class="th_text" width="5%" align="left">
						<xsl:value-of select="php:function('lang', 'id')"/>
					</td>
					<td class="th_text" width="85%" align="left">
						<xsl:value-of select="php:function('lang', 'value')"/>
					</td>
					<td class="th_text" width="85%" align="left">
						<xsl:value-of select="php:function('lang', 'order')"/>
					</td>
					<td class="th_text" width="15%" align="center">
						<xsl:value-of select="lang_delete_value"/>
					</td>
				</tr>
				<xsl:for-each select="value_choice">
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
						</xsl:attribute>
						<td align="left">
							<xsl:value-of select="id"/>
						</td>
						<td align="left">
							<input type="textbox" name="values[edit_choice][{id}]" value="{value}" size="15">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'value')"/>
								</xsl:attribute>
							</input>
						</td>
						<td align="center">
							<input type="textbox" name="values[order_choice][{id}]" value="{order}" size="4">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'order')"/>
								</xsl:attribute>
							</input>
						</td>
						<td align="center">
							<input type="checkbox" name="values[delete_choice][]" value="{id}">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_delete_choice_statustext"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</xsl:for-each>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td valign="top" colspan="2">
				<xsl:value-of select="lang_new_value"/>
			</td>
			<td valign="top" colspan="2">
				<input type="text" name="values[new_choice]">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_new_value_statustext"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template name="list_info" xmlns:php="http://php.net/xsl">
	<table class="pure-table pure-table-bordered pure-table-striped pure-custom">
		<thead>
			<tr>
				<th class="th_text" width="85%" align="left">
					<xsl:value-of select="php:function('lang', 'location')"/>
				</th>
				<th class="th_text" width="15%" align="center">
					<xsl:value-of select="php:function('lang', 'select')"/>
				</th>
			</tr>
		</thead>

		<xsl:for-each select="value_list_info">
			<tr>
				<td align="left">
					<xsl:value-of select="name"/>
					<xsl:text> </xsl:text>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="selected='selected' or selected = 1">
							<input type="checkbox" name="values[list_info][{id}]" value="{id}" checked="checked">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_list_type_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[list_info][{id}]" value="{id}">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_list_type_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>
