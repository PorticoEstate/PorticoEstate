<!-- $Id: admin_entity.xsl,v 1.8 2007/10/13 10:02:54 sigurdne Exp $ -->

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
	

	<xsl:template match="list">		
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

	<xsl:template match="table_header">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"/></a>
			</td>
			<td class="th_text" width="10%" align="center">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
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

	<xsl:template match="values"> 
		<xsl:variable name="lang_attribute_standardtext"><xsl:value-of select="lang_delete_standardtext"/></xsl:variable>
		<xsl:variable name="lang_edit_standardtext"><xsl:value-of select="lang_edit_standardtext"/></xsl:variable>
		<xsl:variable name="lang_delete_standardtext"><xsl:value-of select="lang_delete_standardtext"/></xsl:variable>
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
					<xsl:variable name="link_categories"><xsl:value-of select="link_categories"/></xsl:variable>
					<a href="{$link_categories}" onMouseover="window.status='{lang_category_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_categories"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>
		
	<xsl:template match="list_category">		
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

	<xsl:template match="table_header_category">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"/></a>
			</td>
			<td class="th_text" width="10%" align="center">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_prefix"/>
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

	<xsl:template match="values_category"> 
		<xsl:variable name="lang_attribute_standardtext"><xsl:value-of select="lang_attribute_standardtext"/></xsl:variable>
		<xsl:variable name="lang_custom_function_standardtext"><xsl:value-of select="lang_custom_function_standardtext"/></xsl:variable>
		<xsl:variable name="lang_edit_standardtext"><xsl:value-of select="lang_edit_standardtext"/></xsl:variable>
		<xsl:variable name="lang_delete_standardtext"><xsl:value-of select="lang_delete_standardtext"/></xsl:variable>
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
					<xsl:variable name="link_attribute"><xsl:value-of select="link_attribute"/></xsl:variable>
					<a href="{$link_attribute}" onMouseover="window.status='{$lang_attribute_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_attribute"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_custom_function"><xsl:value-of select="link_custom_function"/></xsl:variable>
					<a href="{$link_custom_function}" onMouseover="window.status='{$lang_custom_function_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_custom_function"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>
	
	
		<xsl:template match="list_config">		
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
	
		<xsl:template match="table_header_list_config">
			<xsl:variable name="sort_column_name"><xsl:value-of select="sort_column_name"/></xsl:variable>
			<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="10%" align="center">
					<a href="{$sort_column_name}"><xsl:value-of select="lang_column_name"/></a>
				</td>
				<td class="th_text" width="10%" align="center">
					<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
				</td>
				<td class="th_text" width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
			</tr>
		</xsl:template>
	
		<xsl:template match="values_list_config"> 
			<xsl:variable name="lang_edit_standardtext"><xsl:value-of select="lang_edit_standardtext"/></xsl:variable>
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
						<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
						<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_standardtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
					</td>
				</tr>
	</xsl:template>


	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
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
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
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
	<xsl:template match="edit">
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
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" action="{$form_action}">
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_entity"/>
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="entity_name"/>
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
					<xsl:value-of select="lang_name"/>
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
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
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
				<xsl:when test="lang_prefix != ''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_prefix"/>
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
				<xsl:when test="lang_lookup_tenant != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_lookup_tenant"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_lookup_tenant = 1">
										<input type="checkbox" name="values[lookup_tenant]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_lookup_tenant_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[lookup_tenant]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_lookup_tenant_statustext"/>
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
				<xsl:when test="lang_tracking != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_tracking"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_tracking = 1">
										<input type="checkbox" name="values[tracking]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_tracking_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[tracking]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_tracking_statustext"/>
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
				<xsl:when test="lang_fileupload != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_fileupload"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_fileupload = 1">
										<input type="checkbox" name="values[fileupload]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_fileupload_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[fileupload]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_fileupload_statustext"/>
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
				<xsl:when test="lang_loc_link != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_loc_link"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_loc_link = 1">
										<input type="checkbox" name="values[loc_link]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_loc_link_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[loc_link]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_loc_link_statustext"/>
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
				<xsl:when test="lang_start_project != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_start_project"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_start_project = 1">
										<input type="checkbox" name="values[start_project]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_start_project_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[start_project]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_start_project_statustext"/>
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
				<xsl:when test="lang_start_ticket != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_start_ticket"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="value_start_ticket = 1">
										<input type="checkbox" name="values[start_ticket]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_start_ticket_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[start_ticket]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_start_ticket_statustext"/>
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
				<xsl:when test="lang_location_level != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_location_level"/>
						</td>
						<td valign="top">
							<xsl:variable name="lang_location_level_statustext"><xsl:value-of select="lang_location_level_statustext"/></xsl:variable>
							<select name="values[location_level]" class="forms" onMouseover="window.status='{$lang_location_level_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_location_level"/></option>
								<xsl:apply-templates select="location_level_list"/>
							</select>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_standardtext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
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
	<xsl:template match="table_header_attrib">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="1%" align="left">
				<xsl:value-of select="lang_datatype"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"/></a>
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

	<xsl:template match="values_attrib"> 
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"/></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"/></xsl:variable>
		<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"/></xsl:variable>
		<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"/></xsl:variable>
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
					<table align="left">
						<tr>
							<td>
								<xsl:value-of select="sorting"/>
							</td>

							<td align="left">
								<xsl:variable name="link_up"><xsl:value-of select="link_up"/></xsl:variable>
								<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_up"/></a>
								<xsl:text> | </xsl:text>
								<xsl:variable name="link_down"><xsl:value-of select="link_down"/></xsl:variable>
								<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_down"/></a>
							</td>

						</tr>
					</table>
				</td>
				<td align="center">
					<xsl:value-of select="search"/>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


<!-- add attribute / edit attribute -->

	<xsl:template match="edit_attrib">
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
			
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
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
					<xsl:value-of select="lang_column_name"/>
				</td>
				<td>
					<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_column_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_input_text"/>
				</td>
				<td>
					<input type="text" name="values[input_text]" value="{value_input_text}" size ="60" maxlength="50" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_input_text_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_statustext"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[statustext]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_statustext_attribtext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_statustext"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_datatype"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_datatype_statustext"><xsl:value-of select="lang_datatype_statustext"/></xsl:variable>
					<select name="values[column_info][type]" class="forms" onMouseover="window.status='{$lang_datatype_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_datatype"/></option>
						<xsl:apply-templates select="datatype_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_precision"/>
				</td>
				<td>
					<input type="text" name="values[column_info][precision]" value="{value_precision}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_precision_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_scale"/>
				</td>
				<td>
					<input type="text" name="values[column_info][scale]" value="{value_scale}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_scale_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_default"/>
				</td>
				<td>
					<input type="text" name="values[column_info][default]" value="{value_default}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_default_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_nullable"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_nullable_statustext"><xsl:value-of select="lang_nullable_statustext"/></xsl:variable>
					<select name="values[column_info][nullable]" class="forms" onMouseover="window.status='{$lang_nullable_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_nullable"/></option>
						<xsl:apply-templates select="nullable_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_list"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_list = 1">
								<input type="checkbox" name="values[list]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[list]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_include_search"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_search = 1">
								<input type="checkbox" name="values[search]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[search]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_history"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_history = 1">
								<input type="checkbox" name="values[history]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_history_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[history]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_history_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_disabled"/>
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_disabled = 1">
								<input type="checkbox" name="values[disabled]" value="1" checked="checked" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_disabled_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[disabled]" value="1" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_disabled_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_helpmsg"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[helpmsg]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_helpmsg_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_helpmsg"/>		
					</textarea>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="multiple_choice != ''">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_choice"/>
						</td>
						<td align="right">
							<xsl:call-template name="choice"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
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
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
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
	<xsl:template match="table_header_custom_function">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_active"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_custom_function"> 
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"/></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"/></xsl:variable>
		<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"/></xsl:variable>
		<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"/></xsl:variable>
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
				<td align = 'center'>
					<xsl:value-of select="active"/>
				</td>
				<td>
					<table align="left">
						<tr>
							<td>
								<xsl:value-of select="sorting"/>
							</td>

							<td align="left">
								<xsl:variable name="link_up"><xsl:value-of select="link_up"/></xsl:variable>
								<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_up"/></a>
								<xsl:text> | </xsl:text>
								<xsl:variable name="link_down"><xsl:value-of select="link_down"/></xsl:variable>
								<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_down"/></a>
							</td>

						</tr>
					</table>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


<!-- add custom_function / edit custom_function -->

	<xsl:template match="edit_custom_function">
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
			
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
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
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
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
					<xsl:variable name="lang_custom_function_statustext"><xsl:value-of select="lang_custom_function_statustext"/></xsl:variable>
					<select name="values[custom_function_file]" class="forms" onMouseover="window.status='{$lang_custom_function_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_custom_function"/></option>
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
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
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
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
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



<!-- location_level_list -->	

	<xsl:template match="location_level_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- datatype_list -->	

	<xsl:template match="datatype_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- custom_function_list -->	

	<xsl:template match="custom_function_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- nullable_list -->	

	<xsl:template match="nullable_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="choice">
			<table cellpadding="2" cellspacing="2" width="80%" align="left">
			<xsl:choose>
				<xsl:when test="value_choice!=''">
					<tr class="th">
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="lang_value"/>
						</td>
						<td class="th_text" width="15%" align="center">
							<xsl:value-of select="lang_delete_value"/>
						</td>
					</tr>
				<xsl:for-each select="value_choice" >
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
						<xsl:value-of select="value"/>
						<xsl:text> </xsl:text>
					</td>
					<td align="center">
						<input type="checkbox" name="values[delete_choice][]" value="{id}"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="//lang_delete_choice_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					</tr>
				</xsl:for-each>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_value"/>
				</td>
				<td>
					<input type="text" name="values[new_choice]" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_value_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</table>
	</xsl:template>

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
				<xsl:for-each select="include_list" >
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
								<input type="checkbox" name="values[lookup_entity][]" value="{id}"  onMouseout="window.status='';return true;">
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
				<xsl:for-each select="include_list_2" >
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
								<input type="checkbox" name="values[include_entity_for][]" value="{id}"  onMouseout="window.status='';return true;">
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
				<xsl:for-each select="include_list_3" >
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
								<input type="checkbox" name="values[start_entity_from][]" value="{id}"  onMouseout="window.status='';return true;">
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
	
