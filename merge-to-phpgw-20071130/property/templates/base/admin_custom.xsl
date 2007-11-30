<!-- $Id: admin_custom.xsl,v 1.2 2006/03/03 17:48:40 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
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




<!-- list custom_function -->

	<xsl:template match="list">
		
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="filter_location"/>
				</td>
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
		<xsl:variable name="sort_acl_location"><xsl:value-of select="sort_acl_location"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_acl_location}"><xsl:value-of select="lang_acl_location"/></a>
			</td>
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
					<xsl:value-of select="acl_location"/>
				</td>
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
					<xsl:value-of select="lang_location"/>
				</td>
				<td>
					<xsl:call-template name="select_location"/>
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
	
