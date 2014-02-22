  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
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
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<a href="{$link_categories}" onMouseover="window.status='{lang_category_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_categories"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_edit">
					<xsl:value-of select="link_edit"/>
				</xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_category">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<a href="{$link_attribute_group}" onMouseover="window.status='';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_attribute_group"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_attribute">
					<xsl:value-of select="link_attribute"/>
				</xsl:variable>
				<a href="{$link_attribute}" onMouseover="window.status='{$lang_attribute_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_attribute"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_custom_function">
					<xsl:value-of select="link_custom_function"/>
				</xsl:variable>
				<a href="{$link_custom_function}" onMouseover="window.status='{$lang_custom_function_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_custom_function"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_edit">
					<xsl:value-of select="link_edit"/>
				</xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_standardtext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_config">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;">
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
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
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
					<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
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
		<div align="left">
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
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form name="form" method="post" action="{$form_action}">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_entity"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="entity_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="parent_list != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'parent')"/>
								</td>
								<td valign="top">
									<select id="parent_id" name="values[parent_id]">
										<option value="">
											<xsl:value-of select="php:function('lang', 'select parent')"/>
										</option>
										<xsl:apply-templates select="parent_list"/>
									</select>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'category')"/>
								</td>
								<td>
									<xsl:value-of select="value_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'name')"/>
						</td>
						<td>
							<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'descr')"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"/>
							</textarea>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="lang_location_form != ''">
							<tr>
								<td>
									<xsl:value-of select="lang_location_form"/>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="value_location_form = 1">
											<input type="checkbox" name="values[location_form]" value="1" checked="checked" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_location_form_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="values[location_form]" value="1" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_location_form_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="lang_documentation != ''">
							<tr>
								<td>
									<xsl:value-of select="lang_documentation"/>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="value_documentation = 1">
											<input type="checkbox" name="values[documentation]" value="1" checked="checked" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_documentation_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="values[documentation]" value="1" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_documentation_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_location_form = 1">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_include_in_location_form"/>
								</td>
								<td>
									<xsl:call-template name="include_list"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_include_this_entity"/>
								</td>
								<td>
									<xsl:call-template name="include_list_2"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_start_this_entity"/>
								</td>
								<td>
									<xsl:call-template name="include_list_3"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="edit_prefix != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'prefix')"/>
								</td>
								<td>
									<input type="text" name="values[prefix]" value="{value_prefix}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_prefix_standardtext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="lookup_tenant != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'lookup tenant')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="tracking != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'tracking helpdesk')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="fileupload != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'enable file upload')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="jasperupload != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'jasper upload')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="loc_link != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'Link from location')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_project != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'start project')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_ticket != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'start ticket')"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'is eav')"/>
						</td>
						<td>
							<input type="checkbox" name="values[is_eav]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'This category is modelled in the database as a xml adapted entity attribute value model')"/>
								</xsl:attribute>
								<xsl:if test="value_is_eav = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<xsl:if test="value_is_eav = '1' or value_id != ''">
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
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'enable bulk')"/>
						</td>
						<td>
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
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="lang_location_level != ''">
							<tr>
								<td>
									<xsl:value-of select="lang_location_level"/>
								</td>
								<td valign="top">
									<xsl:variable name="lang_location_level_statustext">
										<xsl:value-of select="lang_location_level_statustext"/>
									</xsl:variable>
									<select name="values[location_level]" class="forms" onMouseover="window.status='{$lang_location_level_statustext}'; return true;" onMouseout="window.status='';return true;">
										<option value="">
											<xsl:value-of select="lang_no_location_level"/>
										</option>
										<xsl:apply-templates select="location_level_list/options"/>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="lang_location_link_level"/>
								</td>
								<td valign="top">
									<xsl:variable name="lang_location_link_level_statustext">
										<xsl:value-of select="lang_location_link_level_statustext"/>
									</xsl:variable>
									<select name="values[location_link_level]" title="{$lang_location_link_level_statustext}">
										<option value="">
											<xsl:value-of select="lang_no_location_link_level"/>
										</option>
										<xsl:apply-templates select="location_link_level_list/options"/>
									</select>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="category_list != '' and value_id = ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'template')"/>
								</td>
								<td valign="top">
									<select id="category_template" name="values[category_template]" onChange="get_template_attributes()">
										<option value="">
											<xsl:value-of select="php:function('lang', 'select template')"/>
										</option>
										<xsl:apply-templates select="category_list"/>
									</select>
								</td>
							</tr>
							<tr>
								<td width="19%" align="left" valign="top">
									<xsl:value-of select="php:function('lang', 'attributes')"/>
								</td>
								<td>
									<div id="paging_0"/>
									<div id="datatable-container_0"/>
									<input type="hidden" name="template_attrib" value=""/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr height="50">
						<td>
							<input type="submit" name="values[save]" value="{lang_save}" onClick="onActionsClick()">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'save')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
			var td_count = <xsl:value-of select="td_count"/>;

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						<!--permission:<xsl:value-of select="permission"/>, -->
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>
			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
	</xsl:template>

	<!-- list attribute -->
	<xsl:template match="list_attribute">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
				<table align="left">
					<tr>
						<td>
							<xsl:value-of select="sorting"/>
						</td>
						<td align="left">
							<xsl:variable name="link_up">
								<xsl:value-of select="link_up"/>
							</xsl:variable>
							<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_up"/>
							</a>
							<xsl:text> | </xsl:text>
							<xsl:variable name="link_down">
								<xsl:value-of select="link_down"/>
							</xsl:variable>
							<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;">
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
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<!-- list attribute_group -->
	<xsl:template match="list_attribute_group">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
							<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_up"/>
							</a>
							<xsl:text> | </xsl:text>
							<xsl:variable name="link_down">
								<xsl:value-of select="link_down"/>
							</xsl:variable>
							<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;">
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
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- add attribute group / edit attribute group -->
	<xsl:template match="edit_attrib_group" xmlns:php="http://php.net/xsl">
		<div align="left">
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
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" action="{$form_action}">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_entity"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="entity_name"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_category"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="category_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_id"/>
								</td>
								<td>
									<xsl:value-of select="value_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="parent_list != ''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'parent')"/>
								</td>
								<td valign="top">
									<select id="parent_id" name="values[parent_id]">
										<option value="">
											<xsl:value-of select="php:function('lang', 'select parent')"/>
										</option>
										<xsl:apply-templates select="parent_list"/>
									</select>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_group_name"/>
						</td>
						<td>
							<input type="text" name="values[group_name]" value="{value_group_name}" maxlength="100" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_group_name_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td>
							<input type="text" name="values[descr]" value="{value_descr}" size="60" maxlength="150" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_remark"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[remark]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_remark_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_remark"/>
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_attribtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_attribtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<!-- add attribute / edit attribute -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit_attrib">
		<div align="left">
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
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" action="{$form_action}">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="php:function('lang', 'entity')"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="entity_name"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="php:function('lang', 'category')"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="category_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'attribute id')"/>
								</td>
								<td>
									<xsl:value-of select="value_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'column name')"/>
						</td>
						<td>
							<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="50">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the name for the column')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'input text')"/>
						</td>
						<td>
							<input type="text" name="values[input_text]" value="{value_input_text}" size="60" maxlength="255">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the input text for records')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'statustext')"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[statustext]" maxlength="255">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter a statustext for the inputfield in forms')"/>
								</xsl:attribute>
								<xsl:value-of select="value_statustext"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'group')"/>
						</td>
						<td valign="top">
							<select name="values[group_id]" class="forms">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select a group')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'no group')"/>
								</option>
								<xsl:apply-templates select="attrib_group_list"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'datatype')"/>
						</td>
						<td valign="top">
							<select name="values[column_info][type]" class="forms">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select a datatype')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'no datatype')"/>
								</option>
								<xsl:apply-templates select="datatype_list"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'precision')"/>
						</td>
						<td>
							<input type="text" name="values[column_info][precision]" value="{value_precision}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the record length')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'scale')"/>
						</td>
						<td>
							<input type="text" name="values[column_info][scale]" value="{value_scale}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the scale if type is decimal')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'default')"/>
						</td>
						<td>
							<input type="text" name="values[column_info][default]" value="{value_default}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enter the default value')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'nullable')"/>
						</td>
						<td valign="top">
							<select name="values[column_info][nullable]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'chose if this column is nullable')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'select nullable')"/>
								</option>
								<xsl:apply-templates select="nullable_list"/>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'show in list')"/>
						</td>
						<td>
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
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'include in search')"/>
						</td>
						<td>
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
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'history')"/>
						</td>
						<td>
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
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'disabled')"/>
						</td>
						<td>
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
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'short description')"/>
						</td>
						<td>
							<input type="text" name="values[short_description]" value="{value_short_description}" size = "2" maxlength= "2">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'optional order of field in a short description')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'help message')"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[helpmsg]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'enables help message for this attribute')"/>
								</xsl:attribute>
								<xsl:value-of select="value_helpmsg"/>
							</textarea>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="multiple_choice = 1">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'choice')"/>
								</td>
								<td align="right">
									<xsl:call-template name="choice"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="custom_get_list = 1">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'custom get list function')"/>
								</td>
								<td>
									<input type="text" name="values[get_list_function]" value="{value_get_list_function}" size="60">
										<xsl:attribute name="title">
											<xsl:text>&lt;app&gt;.&lt;class&gt;.&lt;function&gt;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'get list function input')"/>
								</td>
								<td>
									<textarea cols="60" rows="10" name="values[get_list_function_input]">
										<xsl:attribute name="title">
											<xsl:text>parameter1 = value1, parameter2 = value2...</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_get_list_function_input"/>
									</textarea>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="custom_get_single = 1">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'custom get single function')"/>
								</td>
								<td>
									<input type="text" name="values[get_single_function]" value="{value_get_single_function}" size="60">
										<xsl:attribute name="title">
											<xsl:text>&lt;app&gt;.&lt;class&gt;.&lt;function&gt;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'get single function input')"/>
								</td>
								<td>
									<textarea cols="60" rows="10" name="values[get_single_function_input]">
										<xsl:attribute name="title">
											<xsl:text>parameter1 = value1, parameter2 = value2...</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_get_single_function_input"/>
									</textarea>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'save the attribute')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="php:function('lang', 'done')"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'back to the list')"/>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<!-- list custom_function -->
	<xsl:template match="list_custom_function">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
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
							<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_up"/>
							</a>
							<xsl:text> | </xsl:text>
							<xsl:variable name="link_down">
								<xsl:value-of select="link_down"/>
							</xsl:variable>
							<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;">
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
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete">
					<xsl:value-of select="link_delete"/>
				</xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- add custom_function / edit custom_function -->
	<xsl:template match="edit_custom_function" xmlns:php="http://php.net/xsl">
		<div align="left">
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
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" action="{$form_action}">
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_entity"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="entity_name"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_category"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="category_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_id"/>
								</td>
								<td>
									<xsl:value-of select="value_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_custom_functiontext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"/>
							</textarea>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_custom_function"/>
						</td>
						<td valign="top">
							<xsl:variable name="lang_custom_function_statustext">
								<xsl:value-of select="lang_custom_function_statustext"/>
							</xsl:variable>
							<select name="values[custom_function_file]" class="forms" onMouseover="window.status='{$lang_custom_function_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value="">
									<xsl:value-of select="lang_no_custom_function"/>
								</option>
								<xsl:apply-templates select="custom_function_list"/>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_active"/>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_active = 1">
									<input type="checkbox" name="values[active]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_active_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[active]" value="1" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_active_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'client-side')"/>
						</td>
						<td>
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
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_custom_functiontext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_custom_functiontext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
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
		<table cellpadding="2" cellspacing="2" width="80%" align="left">
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
							<xsl:value-of select="php:function('lang', 'delete value')"/>
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
										<xsl:value-of select="php:function('lang', 'delete this value from the list of multiple choice')"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top" colspan="2">
					<xsl:value-of select="php:function('lang', 'new value')"/>
				</td>
				<td valign="top" colspan="2">
					<input type="text" name="values[new_choice]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'new value for multiple choice')"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="include_list">
		<table cellpadding="2" cellspacing="2" width="80%" align="left">
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
					<td align="center">
						<xsl:choose>
							<xsl:when test="selected='selected'">
								<input type="checkbox" name="values[lookup_entity][]" value="{id}" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_include_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[lookup_entity][]" value="{id}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_include_statustext"/>
										<xsl:text>'; return true;</xsl:text>
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
		<table cellpadding="2" cellspacing="2" width="80%" align="left">
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
					<td align="center">
						<xsl:choose>
							<xsl:when test="selected='selected'">
								<input type="checkbox" name="values[include_entity_for][]" value="{id}" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_include_2_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[include_entity_for][]" value="{id}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_include_2_statustext"/>
										<xsl:text>'; return true;</xsl:text>
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
		<table cellpadding="2" cellspacing="2" width="80%" align="left">
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
					<td align="center">
						<xsl:choose>
							<xsl:when test="selected='selected'">
								<input type="checkbox" name="values[start_entity_from][]" value="{id}" checked="checked" onMouseout="window.status='';return true;">
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[start_entity_from][]" value="{id}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_include_3_statustext"/>
										<xsl:text>'; return true;</xsl:text>
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
