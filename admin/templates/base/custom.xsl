<!-- $Id$ -->

	<xsl:template name="custom">
		<xsl:choose>
			<xsl:when test="list_attribute">
				<xsl:apply-templates select="list_attribute"/>
			</xsl:when>
			<xsl:when test="edit_attrib">
				<xsl:apply-templates select="edit_attrib"/>
			</xsl:when>
			<xsl:when test="list_custom_function">
				<xsl:apply-templates select="list_custom_function"/>
			</xsl:when>
			<xsl:when test="edit_custom_function">
				<xsl:apply-templates select="edit_custom_function"/>
			</xsl:when>
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


<!-- list attribute -->

	<xsl:template match="list_attribute">
		
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_appname"/>
					<xsl:text>: </xsl:text>
					<xsl:value-of select="appname"/>
				</td>
			</tr>
			<tr>
				<td align="left">
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
				<xsl:apply-templates select="table_header_attrib"/>
				<xsl:choose>
					<xsl:when test="values_attrib != ''">
					<xsl:apply-templates select="values_attrib"/>
					</xsl:when>
				</xsl:choose>
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

	<xsl:template match="edit_attrib" xmlns:php="http://php.net/xsl">
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
					<xsl:value-of select="php:function('lang', 'appname')" />
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="appname"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_location"/>
					<xsl:value-of select="php:function('lang', 'location')" />
				</td>
				<td align="left">
					<xsl:choose>
						<xsl:when test="value_location != ''">
							<xsl:value-of select="value_location"/>
							<input type="hidden" name="location" value="{value_location}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="select_location"/>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="value_id != ''">
					<tr>
						<td valign="top">
					<xsl:value-of select="php:function('lang', 'Attribute ID')" />
						</td>
						<td>
							<xsl:value-of select="value_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'column name')" />
				</td>
				<td>
					<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20">
						<xsl:attribute name="title">
						    <xsl:value-of select="php:function('lang', 'enter the name for the column')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'input text')" />
				</td>
				<td>
					<input type="text" name="values[input_text]" value="{value_input_text}" maxlength="20">
						<xsl:attribute name="title">
						    <xsl:value-of select="php:function('lang', 'enter the input text for records')" />
   						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'statustext')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[statustext]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Enter a statustext for the inputfield in forms')" />
						</xsl:attribute>
						<xsl:value-of select="value_statustext"/>		
					</textarea>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'group')" />
				</td>
				<td valign="top">
					<select name="values[group_id]" class="forms">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'select a group')" />
						</xsl:attribute>
							<option value="">
							<xsl:value-of select="php:function('lang', 'no group')" />
						</option>
						<xsl:apply-templates select="attrib_group_list/options"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'datatype')" />
				</td>
				<td valign="top">
					<xsl:variable name="lang_datatype_statustext"><xsl:value-of select="lang_datatype_statustext"/></xsl:variable>
					<select name="values[column_info][type]" class="forms">
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
					<input type="text" name="values[column_info][precision]" value="{value_precision}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_precision_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_scale"/>
				</td>
				<td>
					<input type="text" name="values[column_info][scale]" value="{value_scale}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_scale_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_default"/>
				</td>
				<td>
					<input type="text" name="values[column_info][default]" value="{value_default}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_default_statustext"/>
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
					<select name="values[column_info][nullable]" class="forms">
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
									<xsl:attribute name="title">
										<xsl:value-of select="lang_list_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[list]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_list_statustext"/>
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
								<input type="checkbox" name="values[search]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_include_search_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[search]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_include_search_statustext"/>
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
								<input type="checkbox" name="values[history]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_history_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[history]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_history_statustext"/>
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
								<input type="checkbox" name="values[disabled]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_disabled_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[disabled]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_disabled_statustext"/>
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
					<textarea cols="60" rows="10" name="values[helpmsg]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_helpmsg_statustext"/>
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
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_attribtext"/>
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
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_attribtext"/>
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
				<td class="th_text" align="left">
					<xsl:value-of select="lang_appname"/>
					<xsl:text>: </xsl:text>
					<xsl:value-of select="appname"/>
				</td>
			</tr>
			<tr>
				<td align="left">
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
			
			<form method="post" action="{form_action}">

			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_appname"/>
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="appname"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_location"/>
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="location"/>
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
					<select name="values[custom_function_file]" class="forms">
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
		<option value="{id}">
			<xsl:if test="selected = 1">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
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

	<xsl:template name="choice" xmlns:php="http://php.net/xsl">
			<table cellpadding="2" cellspacing="2" width="80%" align="left">
			<xsl:choose>
				<xsl:when test="value_choice!=''">
					<tr class="th">
						<td class="th_text" width="5%" align="left">
							<xsl:value-of select="php:function('lang', 'id')" />
						</td>
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="php:function('lang', 'value')" />
						</td>
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="php:function('lang', 'order')" />
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
						<xsl:value-of select="id"/>
					</td>
					<td align="left">
						<input type="textbox" name="values[edit_choice][{id}]" value="{value}" size='15'>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'value')" />
							</xsl:attribute>
						</input>
					</td>
					<td align="center">
						<input type="textbox" name="values[order_choice][{id}]" value="{order}" size='4'>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'order')" />
							</xsl:attribute>
						</input>
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
				<td valign="top" colspan='2'>
					<xsl:value-of select="lang_new_value"/>
				</td>
				<td valign="top" colspan='2'>
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
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>
