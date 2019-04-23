
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:call-template name="jquery_phpgw_i18n"/>
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="list_attribute">
			<xsl:apply-templates select="list_attribute"/>
		</xsl:when>
		<xsl:when test="list_attribute_group">
			<xsl:apply-templates select="list_attribute_group"/>
		</xsl:when>
		<xsl:when test="edit_attrib_group">
			<xsl:apply-templates select="edit_attrib_group"/>
		</xsl:when>
		<xsl:when test="edit_attrib">
			<xsl:apply-templates select="edit_attrib"/>
		</xsl:when>
		<xsl:when test="list_config">
			<xsl:apply-templates select="list_config"/>
		</xsl:when>
		<xsl:when test="edit_config">
			<xsl:apply-templates select="edit_config"/>
		</xsl:when>
		<xsl:when test="list_category">
			<xsl:apply-templates select="list_category"/>
		</xsl:when>
		<xsl:when test="list_custom_function">
			<xsl:apply-templates select="list_custom_function"/>
		</xsl:when>
		<xsl:when test="edit_custom_function">
			<xsl:apply-templates select="edit_custom_function"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="list">
	<xsl:apply-templates select="menu"/>
	<table class="pure-table pure-table-bordered pure-table-striped">
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
			<xsl:value-of select="descr"/>
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

<!-- New template-->
<xsl:template match="list_category">
	<xsl:apply-templates select="menu"/>
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_entity"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="entity_name"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table class="pure-table pure-table-bordered pure-table-striped">
		<xsl:apply-templates select="table_header_category"/>
		<xsl:apply-templates select="values_category"/>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_category">
	<xsl:variable name="sort_id">
		<xsl:value-of select="sort_id"/>
	</xsl:variable>
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="5%" align="right">
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
			<xsl:value-of select="lang_prefix"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_attribute_group"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_attribute"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_custom_function"/>
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
<xsl:template match="values_category">
	<xsl:variable name="lang_attribute_standardtext">
		<xsl:value-of select="lang_attribute_standardtext"/>
	</xsl:variable>
	<xsl:variable name="lang_custom_function_standardtext">
		<xsl:value-of select="lang_custom_function_standardtext"/>
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
			<xsl:value-of select="descr"/>
		</td>
		<td align="left">
			<xsl:value-of select="prefix"/>
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
			<xsl:variable name="link_custom_function">
				<xsl:value-of select="link_custom_function"/>
			</xsl:variable>
			<a href="{$link_custom_function}" onMouseover="window.status='{$lang_custom_function_standardtext}';return true;">
				<xsl:value-of select="text_custom_function"/>
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

<!-- New template-->
<xsl:template match="list_config">
	<xsl:apply-templates select="menu"/>
	<table class="pure-table pure-table-bordered pure-table-striped">
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
	<table class="pure-table pure-table-bordered pure-table-striped">
		<xsl:apply-templates select="table_header_list_config"/>
		<xsl:apply-templates select="values_list_config"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_list_config">
	<xsl:variable name="sort_column_name">
		<xsl:value-of select="sort_column_name"/>
	</xsl:variable>
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="center">
			<a href="{$sort_column_name}">
				<xsl:value-of select="lang_column_name"/>
			</a>
		</td>
		<td class="th_text" width="10%" align="center">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values_list_config">
	<xsl:variable name="lang_edit_standardtext">
		<xsl:value-of select="lang_edit_standardtext"/>
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
			<xsl:value-of select="name"/>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
	</tr>
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
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_standardtext"/>
						<xsl:text>'; return true;</xsl:text>
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
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_standardtext"/>
						<xsl:text>'; return true;</xsl:text>
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
		var base_java_url = <xsl:value-of select="base_java_url"/>;
	</script>

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form name="form" class="pure-form pure-form-aligned" method="post" id="form" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<fieldset>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_entity"/>
						</label>
						<label>
							<xsl:value-of select="entity_name"/>
						</label>
					</div>
					<xsl:choose>
						<xsl:when test="parent_list != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'parent')"/>
								</label>
								<label valign="top">
									<select id="parent_id" name="values[parent_id]">
										<option value="">
											<xsl:value-of select="php:function('lang', 'select parent')"/>
										</option>
										<xsl:apply-templates select="parent_list"/>
									</select>
								</label>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'category')"/>
								</label>
								<xsl:value-of select="value_id"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'name')"/>
						</label>

						<input type="text" data-validation="required" name="values[name]" value="{value_name}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_name_standardtext"/>
							</xsl:attribute>
						</input>

					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'descr')"/>
						</label>
						<textarea cols="60" rows="10" name="values[descr]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_descr_standardtext"/>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
					<xsl:choose>
						<xsl:when test="lang_location_form != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_location_form"/>
								</label>
								<xsl:choose>
									<xsl:when test="value_location_form = 1">
										<input type="checkbox" name="values[location_form]" value="1" checked="checked">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_location_form_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[location_form]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_location_form_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="lang_documentation != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_documentation"/>
								</label>
								<xsl:choose>
									<xsl:when test="value_documentation = 1">
										<input type="checkbox" name="values[documentation]" value="1" checked="checked">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_documentation_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[documentation]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_documentation_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="edit_prefix != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'prefix')"/>
								</label>
								<input type="text" name="values[prefix]" value="{value_prefix}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_prefix_standardtext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="org_unit != ''">
							<div class="pure-control-group">
								<label>
									<xsl:variable name="lang_org_unit">
										<xsl:value-of select="php:function('lang', 'department')"/>
									</xsl:variable>
									<xsl:value-of select="$lang_org_unit"/>
								</label>
								<input type="checkbox" name="values[org_unit]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'department')"/>
									</xsl:attribute>
									<xsl:if test="value_org_unit = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="lookup_tenant != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'lookup tenant')"/>
								</label>
								<input type="checkbox" name="values[lookup_tenant]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'If this entity type is to look up tenants')"/>
									</xsl:attribute>
									<xsl:if test="value_lookup_tenant = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="tracking != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'tracking helpdesk')"/>
								</label>
								<input type="checkbox" name="values[tracking]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'activate tracking of dates in helpdesk main list')"/>
									</xsl:attribute>
									<xsl:if test="value_tracking = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="fileupload != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'enable file upload')"/>
								</label>
								<input type="checkbox" name="values[fileupload]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'If files can be uploaded for this category')"/>
									</xsl:attribute>
									<xsl:if test="value_fileupload = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="jasperupload != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'jasper upload')"/>
								</label>
								<input type="checkbox" name="values[jasperupload]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'allow to upload definition of jasper reports')"/>
									</xsl:attribute>
									<xsl:if test="value_jasperupload = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="loc_link != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Link from location')"/>
								</label>
								<input type="checkbox" name="values[loc_link]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enable link from location detail')"/>
									</xsl:attribute>
									<xsl:if test="value_loc_link = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_project != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'start project')"/>
								</label>
								<input type="checkbox" name="values[start_project]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enable start project from this category')"/>
									</xsl:attribute>
									<xsl:if test="value_start_project = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_ticket != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'start ticket')"/>
								</label>
								<input type="checkbox" name="values[start_ticket]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enable start ticket from this category')"/>
									</xsl:attribute>
									<xsl:if test="value_start_ticket = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'is eav')"/>
						</label>
						<input type="checkbox" name="values[is_eav]" value="1">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'This category is modelled in the database as a xml adapted entity attribute value model')"/>
							</xsl:attribute>
							<xsl:if test="value_is_eav = '1'">
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:if test="value_is_eav = '1' or value_id > 0">
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
						<xsl:choose>
							<xsl:when test="value_is_eav = '1'">
								<input type="hidden" name="values[is_eav]" value="1"/>
							</xsl:when>
						</xsl:choose>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'enable bulk')"/>
						</label>
						<input type="checkbox" name="values[enable_bulk]" value="1">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'This category is allowed to reperesent bulk entities')"/>
							</xsl:attribute>
							<xsl:if test="value_enable_bulk = '1'">
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
								<xsl:value-of select="php:function('lang', 'This category is allowed to link to controller')"/>
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
					<xsl:choose>
						<xsl:when test="lang_location_level != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_location_level"/>
								</label>
								<select name="values[location_level]" class="forms">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_location_level_statustext"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="lang_no_location_level"/>
									</option>
									<xsl:apply-templates select="location_level_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_location_link_level"/>
								</label>
								<xsl:variable name="lang_location_link_level_statustext">
									<xsl:value-of select="lang_location_link_level_statustext"/>
								</xsl:variable>
								<select name="values[location_link_level]" title="{$lang_location_link_level_statustext}">
									<option value="">
										<xsl:value-of select="lang_no_location_link_level"/>
									</option>
									<xsl:apply-templates select="location_link_level_list/options"/>
								</select>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<xsl:variable name="lang_entity_group">
							<xsl:value-of select="php:function('lang', 'entity group')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_entity_group"/>
						</label>
						<select name="values[entity_group_id]" title="$lang_entity_group">
							<xsl:apply-templates select="entity_group_list/options"/>
						</select>
					</div>
					<xsl:choose>
						<xsl:when test="value_location_form = 1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_include_in_location_form"/>
								</label>
								<div class="pure-custom" >
									<xsl:call-template name="include_list"/>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_include_this_entity"/>
								</label>
								<div class="pure-custom" >
									<xsl:call-template name="include_list_2"/>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_start_this_entity"/>
								</label>
								<div class="pure-custom" >
									<xsl:call-template name="include_list_3"/>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="category_list != '' and value_id = ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'template')"/>
								</label>
								<select id="category_template" name="values[category_template]" onChange="get_template_attributes()">
									<option value="">
										<xsl:value-of select="php:function('lang', 'select template')"/>
									</option>
									<xsl:apply-templates select="category_list"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'attributes')"/>
								</label>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl'/>
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
											<xsl:with-param name="data" select ='data'/>
											<xsl:with-param name="config" select ='config'/>
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-controls">
						<input type="hidden" name="template_attrib" value=""/>
						<input type="button" class="pure-button pure-button-primary" name="values[save]" value="{lang_save}" onClick="onActionsClick();">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:attribute>
						</input>
					</div>
				</fieldset>
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
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<div class="pure-controls">
						<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_standardtext"/>
							</xsl:attribute>
						</input>
					</div>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- list attribute -->
<xsl:template match="list_attribute">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_entity"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="entity_name"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_category"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="category_name"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table class="pure-table pure-table-bordered pure-table-striped">
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
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="20%" align="left">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="1%" align="left">
			<xsl:value-of select="lang_datatype"/>
		</td>
		<td class="th_text" width="1%" align="left">
			<xsl:value-of select="lang_attrib_group"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_sorting}">
				<xsl:value-of select="lang_sorting"/>
			</a>
		</td>
		<td class="th_text" width="1%" align="center">
			<xsl:value-of select="lang_search"/>
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
	<xsl:variable name="lang_edit_text">
		<xsl:value-of select="lang_edit_text"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_text">
		<xsl:value-of select="lang_delete_text"/>
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
		<td>
			<xsl:value-of select="input_text"/>
		</td>
		<td>
			<xsl:value-of select="datatype"/>
		</td>
		<td>
			<xsl:value-of select="attrib_group"/>
		</td>
		<td>
			<table class="pure-table pure-table-bordered pure-table-striped">
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
			<xsl:value-of select="search"/>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<!-- list attribute_group -->
<xsl:template match="list_attribute_group">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_entity"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="entity_name"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_category"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="category_name"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_attrib_group"/>
		<xsl:apply-templates select="values_attrib_group"/>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_attrib_group">
	<xsl:variable name="sort_sorting">
		<xsl:value-of select="sort_sorting"/>
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
		<td class="th_text" width="20%" align="left">
			<xsl:value-of select="lang_descr"/>
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
<xsl:template match="values_attrib_group">
	<xsl:variable name="lang_up_text">
		<xsl:value-of select="lang_up_text"/>
	</xsl:variable>
	<xsl:variable name="lang_down_text">
		<xsl:value-of select="lang_down_text"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_text">
		<xsl:value-of select="lang_edit_text"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_text">
		<xsl:value-of select="lang_delete_text"/>
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
			<xsl:value-of select="name"/>
		</td>
		<td>
			<xsl:value-of select="descr"/>
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
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- add attribute group / edit attribute group -->
<xsl:template match="edit_attrib_group" xmlns:php="http://php.net/xsl">
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
						<xsl:value-of select="lang_entity"/>
					</label>
					<xsl:value-of select="entity_name"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_category"/>
					</label>
					<xsl:value-of select="category_name"/>
				</div>
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
				<xsl:choose>
					<xsl:when test="parent_list != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'parent')"/>
							</label>
							<select id="parent_id" name="values[parent_id]">
								<option value="">
									<xsl:value-of select="php:function('lang', 'select parent')"/>
								</option>
								<xsl:apply-templates select="parent_list"/>
							</select>
						</div>
					</xsl:when>
				</xsl:choose>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_group_name"/>
					</label>
					<input type="text" data-validation="required" name="values[group_name]" value="{value_group_name}" maxlength="100">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_group_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_descr"/>
					</label>
					<input type="text" data-validation="required" name="values[descr]" value="{value_descr}" size="60" maxlength="150">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_remark"/>
					</label>
					<textarea cols="60" rows="10" name="values[remark]">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_remark_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_save_attribtext"/>
							<xsl:text>'; return true;</xsl:text>
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
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_done_attribtext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</form>
	</div>
</xsl:template>

<!-- add attribute / edit attribute -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit_attrib">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<div id="tab-content">
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<div class="yui-content">
			<div id="general">
				<div align="left">
					<xsl:variable name="form_action">
						<xsl:value-of select="form_action"/>
					</xsl:variable>
					<form method="post" class="pure-form pure-form-aligned" action="{$form_action}">
						<dl>
							<xsl:choose>
								<xsl:when test="msgbox_data != ''">
									<dt>
										<xsl:call-template name="msgbox"/>
									</dt>
								</xsl:when>
							</xsl:choose>
						</dl>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'entity')"/>
							</label>
							<xsl:value-of select="entity_name"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'category')"/>
							</label>
							<xsl:value-of select="category_name"/>
						</div>
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'attribute id')"/>
									</label>
									<xsl:value-of select="value_id"/>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'column name')"/>
							</label>
							<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="50">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the name for the column')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'input text')"/>
							</label>
							<input type="text" name="values[input_text]" value="{value_input_text}" size="60" maxlength="255">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the input text for records')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'statustext')"/>
							</label>
							<textarea cols="60" rows="10" name="values[statustext]" maxlength="255">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter a statustext for the inputfield in forms')"/>
								</xsl:attribute>
								<xsl:value-of select="value_statustext"/>
							</textarea>
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
							<select name="values[column_info][type]" class="forms">
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
							<select name="values[column_info][nullable]">
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
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'history')"/>
							</label>
							<input type="checkbox" name="values[history]" value="1">
								<xsl:if test="value_history = 1">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enable history for this attribute')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'disabled')"/>
							</label>
							<input type="checkbox" name="values[disabled]" value="1">
								<xsl:if test="value_disabled = 1">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'this attribute turn up as disabled in the form')"/>
								</xsl:attribute>
							</input>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'short description')"/>
							</label>
							<input type="text" name="values[short_description]" value="{value_short_description}" size = "2" maxlength= "2">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'optional order of field in a short description')"/>
								</xsl:attribute>
							</input>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'help message')"/>
							</label>
							<textarea cols="60" rows="10" name="values[helpmsg]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enables help message for this attribute')"/>
								</xsl:attribute>
								<xsl:value-of select="value_helpmsg"/>
							</textarea>
						</div>

						<xsl:choose>
							<xsl:when test="datatype = 'link'">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'action')"/>
									</label>
									<textarea cols="60" rows="10" name="values[javascript_action]">
										<xsl:attribute name="title">
											<xsl:text>optional javascript, __id__ is replaced by id</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_javascript_action"/>
									</textarea>
								</div>
							</xsl:when>
						</xsl:choose>

						<xsl:choose>
							<xsl:when test="multiple_choice = 1">
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
										<xsl:value-of select="php:function('lang', 'choice')"/>
									</label>
									<xsl:call-template name="choice"/>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="custom_get_list = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'custom get list function')"/>
									</label>
									<input type="text" name="values[get_list_function]" value="{value_get_list_function}" size="60">
										<xsl:attribute name="title">
											<xsl:text>&lt;app&gt;.&lt;class&gt;.&lt;function&gt;</xsl:text>
										</xsl:attribute>
									</input>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'get list function input')"/>
									</label>
									<textarea cols="60" rows="10" name="values[get_list_function_input]">
										<xsl:attribute name="title">
											<xsl:text>parameter1 = value1, parameter2 = value2...</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_get_list_function_input"/>
									</textarea>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="custom_get_single = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'custom get single function')"/>
									</label>
									<input type="text" name="values[get_single_function]" value="{value_get_single_function}" size="60">
										<xsl:attribute name="title">
											<xsl:text>&lt;app&gt;.&lt;class&gt;.&lt;function&gt;</xsl:text>
										</xsl:attribute>
									</input>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'get single function input')"/>
									</label>
									<textarea cols="60" rows="10" name="values[get_single_function_input]">
										<xsl:attribute name="title">
											<xsl:text>parameter1 = value1, parameter2 = value2...</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_get_single_function_input"/>
									</textarea>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'save the attribute')"/>
								</xsl:attribute>
							</input>
						</div>
					</form>
					<div class="pure-control-group">
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="php:function('lang', 'done')"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="pure-button pure-button-primary" name="done" value="{$lang_done}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'back to the list')"/>
								</xsl:attribute>
							</input>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<!-- list custom_function -->
<xsl:template match="list_custom_function">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_entity"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="entity_name"/>
			</td>
		</tr>
		<tr>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_category"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="category_name"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_custom_function"/>
		<xsl:choose>
			<xsl:when test="values_custom_function != ''">
				<xsl:apply-templates select="values_custom_function"/>
			</xsl:when>
		</xsl:choose>
		<xsl:apply-templates select="table_add"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_custom_function">
	<xsl:variable name="sort_sorting">
		<xsl:value-of select="sort_sorting"/>
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
		<td class="th_text" width="20%" align="left">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_active"/>
		</td>
		<td class="th_text" width="10%" align="center">
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
<xsl:template match="values_custom_function">
	<xsl:variable name="lang_up_text">
		<xsl:value-of select="lang_up_text"/>
	</xsl:variable>
	<xsl:variable name="lang_down_text">
		<xsl:value-of select="lang_down_text"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_text">
		<xsl:value-of select="lang_edit_text"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_text">
		<xsl:value-of select="lang_delete_text"/>
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
			<xsl:value-of select="file_name"/>
		</td>
		<td>
			<xsl:value-of select="descr"/>
		</td>
		<td align="center">
			<xsl:value-of select="active"/>
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
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- add custom_function / edit custom_function -->
<xsl:template match="edit_custom_function" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<div id="tab-content">
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<div id="general">
			<div align="left">
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" class="pure-form pure-form-aligned" action="{$form_action}">
					<dl>
						<xsl:choose>
							<xsl:when test="msgbox_data != ''">
								<dt>
									<xsl:call-template name="msgbox"/>
								</dt>
							</xsl:when>
						</xsl:choose>
					</dl>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_entity"/>
						</label>

						<xsl:value-of select="entity_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_category"/>
						</label>
						<xsl:value-of select="category_name"/>
					</div>
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
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea cols="60" rows="10" name="values[descr]">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_custom_functiontext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_custom_function"/>
						</label>
						<xsl:variable name="lang_custom_function_statustext">
							<xsl:value-of select="lang_custom_function_statustext"/>
						</xsl:variable>
						<select name="values[custom_function_file]" class="forms" onMouseover="window.status='{$lang_custom_function_statustext}'; return true;">
							<option value="">
								<xsl:value-of select="lang_no_custom_function"/>
							</option>
							<xsl:apply-templates select="custom_function_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_active"/>
						</label>
						<xsl:choose>
							<xsl:when test="value_active = 1">
								<input type="checkbox" name="values[active]" value="1" checked="checked">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_active_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[active]" value="1">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_active_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'client-side')"/>
						</label>
						<input type="checkbox" name="values[client_side]" value="1">
							<xsl:attribute name="title">
								<xsl:text>otherwise: server-side</xsl:text>
							</xsl:attribute>
							<xsl:if test="value_client_side = '1'">
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_custom_functiontext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
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
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_custom_functiontext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

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

<!-- custom_function_list -->
<xsl:template match="custom_function_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected=1">
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
<xsl:template match="parent_list">
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{id}">
				<xsl:value-of disable-output-escaping="yes" select="name"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="category_list">
	<option value="{id}">
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="choice">
	<xsl:variable name="lang_id">
		<xsl:value-of select="php:function('lang', 'id')"/>
	</xsl:variable>
	<xsl:variable name="lang_value">
		<xsl:value-of select="php:function('lang', 'value')"/>
	</xsl:variable>
	<xsl:variable name="lang_title">
		<xsl:value-of select="php:function('lang', 'title')"/>
	</xsl:variable>
	<xsl:variable name="lang_sorting">
		<xsl:value-of select="php:function('lang', 'sorting')"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_value">
		<xsl:value-of select="php:function('lang', 'delete value')"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_title">
		<xsl:value-of select="php:function('lang', 'delete this value from the list of multiple choice')"/>
	</xsl:variable>
	<table class="pure-table pure-table-bordered pure-table-striped">
		<thead>
			<tr>
				<th width="5%" align="left">
					<xsl:value-of select="$lang_id"/>
				</th>
				<th width="40%" align="left">
					<xsl:value-of select="$lang_value"/>
				</th>
				<th  width="40%" align="left">
					<xsl:value-of select="$lang_title"/>
				</th>
				<th width="5%" align="left">
					<xsl:value-of select="$lang_sorting"/>
				</th>
				<th width="10%" align="center">
					<xsl:value-of select="$lang_delete_value"/>
				</th>
			</tr>
		</thead>
		<xsl:for-each select="value_choice">
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 != 0">
							<xsl:text>pure-table-odd</xsl:text>
						</xsl:when>
					</xsl:choose>
				</xsl:attribute>
				<td align="left">
					<xsl:value-of select="id"/>
				</td>
				<td align="left">
					<input type="textbox" name="values[edit_choice][{id}]" value="{value}" size="15">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_value"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="left">
					<input type="textbox" name="values[title_choice][{id}]" value="{title}" size='15'>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'value')" />
						</xsl:attribute>
					</input>
				</td>
				<td align="center">
					<input type="textbox" name="values[order_choice][{id}]" value="{order}" size="4">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_sorting"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="center">
					<input type="checkbox" name="values[delete_choice][]" value="{id}">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_delete_title"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</xsl:for-each>
		<tr>
			<td valign="top">
				<input type="text" name="values[new_choice_id]" size = '3'>
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'new id for multiple choice')"/>
					</xsl:attribute>
				</input>
			</td>
			<td valign="top">
				<input type="text" name="values[new_choice]">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'new value for multiple choice')"/>
					</xsl:attribute>
				</input>
			</td>
			<td valign="top">
				<input type="text" name="values[new_title_choice]">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'title')" />
					</xsl:attribute>
				</input>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template name="include_list">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr class="th">
			<td class="th_text" width="85%" align="left">
				<xsl:value-of select="lang_name"/>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
		<xsl:for-each select="include_list">
			<tr>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="selected='selected' or selected = 1">
							<input type="checkbox" name="values[lookup_entity][]" value="{id}" checked="checked">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_include_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[lookup_entity][]" value="{id}">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_include_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template name="include_list_2">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr class="th">
			<td class="th_text" width="85%" align="left">
				<xsl:value-of select="lang_name"/>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
		<xsl:for-each select="include_list_2">
			<tr>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="selected='selected' or selected = 1">
							<input type="checkbox" name="values[include_entity_for][]" value="{id}" checked="checked">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_include_2_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[include_entity_for][]" value="{id}">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_include_2_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template name="include_list_3">
	<table class="pure-table pure-table-bordered pure-table-striped">
		<tr class="th">
			<td class="th_text" width="85%" align="left">
				<xsl:value-of select="lang_name"/>
			</td>
			<td class="th_text" width="15%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
		<xsl:for-each select="include_list_3">
			<tr>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="selected='selected' or selected = 1">
							<input type="checkbox" name="values[start_entity_from][]" value="{id}" checked="checked">
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[start_entity_from][]" value="{id}">
								<xsl:attribute name="title">
									<xsl:value-of select="//lang_include_3_statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
