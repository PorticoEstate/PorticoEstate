<!-- $Id: config.xsl 4237 2009-11-27 23:17:21Z sigurd $ -->

	<xsl:template name="config_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="delete">
				<xsl:apply-templates select="delete"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list_section">
		<xsl:choose>
			<xsl:when test="menu != ''">
				<xsl:apply-templates select="menu"/> 
			</xsl:when>
		</xsl:choose>
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
					<!--	<xsl:with-param name="nextmatchs_params"/>
					</xsl:call-template> -->
				</td>
			</tr>
		</table>
		<table class="pure-table pure-table-bordered">
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_attribute"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_config_text"><xsl:value-of select="lang_view_config_text"/></xsl:variable>
		<xsl:variable name="lang_edit_config_text"><xsl:value-of select="lang_edit_config_text"/></xsl:variable>
		<xsl:variable name="lang_delete_config_text"><xsl:value-of select="lang_delete_config_text"/></xsl:variable>
		<xsl:variable name="lang_attribute_text"><xsl:value-of select="lang_attribute_text"/></xsl:variable>
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
					<xsl:variable name="link_attribute"><xsl:value-of select="link_attribute"/></xsl:variable>
					<a href="{$link_attribute}" title="{$lang_attribute_text}"><xsl:value-of select="text_attribute"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" title="{$lang_edit_config_text}"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" title="{$lang_view_config_text}"><xsl:value-of select="text_view"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" title="{$lang_delete_config_text}"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="list_attrib">
		<xsl:choose>
			<xsl:when test="menu != ''">
				<xsl:apply-templates select="menu"/> 
			</xsl:when>
		</xsl:choose>
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
				<td align="left">
					<xsl:value-of select="lang_section"/>
					<xsl:text>: </xsl:text>
					<xsl:value-of select="value_section_name"/>
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
				<xsl:apply-templates select="table_header_attrib"/>
				<xsl:apply-templates select="values_attrib"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header_attrib">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_value"/>
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
		<xsl:variable name="lang_edit_config_text"><xsl:value-of select="lang_edit_config_text"/></xsl:variable>
		<xsl:variable name="lang_delete_config_text"><xsl:value-of select="lang_delete_config_text"/></xsl:variable>
		<xsl:variable name="lang_value_text"><xsl:value-of select="lang_value_text"/></xsl:variable>
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
					<xsl:variable name="link_value"><xsl:value-of select="link_value"/></xsl:variable>
					<a href="{$link_value}" title="{$lang_value_text}"><xsl:value-of select="text_value"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" title="{$lang_edit_config_text}"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" title="{$lang_delete_config_text}"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="list_value">
		<xsl:choose>
			<xsl:when test="menu != ''">
				<xsl:apply-templates select="menu"/> 
			</xsl:when>
		</xsl:choose>
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
				<td align="left">
					<xsl:value-of select="lang_section"/>
					<xsl:text>: </xsl:text>
					<xsl:variable name="link_section"><xsl:value-of select="link_section"/></xsl:variable>
					<a href="{$link_section}"><xsl:value-of select="value_section_name"/></a>
					<xsl:text>-> </xsl:text>
					<xsl:value-of select="lang_attrib"/>
					<xsl:text>: </xsl:text>
					<xsl:value-of select="value_attrib_name"/>
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
				<xsl:apply-templates select="table_header_values"/>
				<xsl:apply-templates select="values_values"/>
				<xsl:choose>
					<xsl:when test="table_add != ''">
						<xsl:apply-templates select="table_add"/>
					</xsl:when>
				</xsl:choose>

		</table>
	</xsl:template>

	<xsl:template match="table_header_values">
		<xsl:variable name="sort_value"><xsl:value-of select="sort_value"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_value}"><xsl:value-of select="lang_value"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_value">
		<xsl:variable name="lang_edit_config_text"><xsl:value-of select="lang_edit_config_text"/></xsl:variable>
		<xsl:variable name="lang_delete_config_text"><xsl:value-of select="lang_delete_config_text"/></xsl:variable>
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
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" title="{$lang_edit_config_text}"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" title="{$lang_delete_config_text}"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_add_statustext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<xsl:choose>
					<xsl:when test="done_action !=''">
						<td>	
							<form method="post" action="{done_action}">
								<input type="submit" name="add" value="{lang_done}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_done_statustext"/>
									</xsl:attribute>
								</input>
							</form>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
	</xsl:template>


<!-- add / edit section -->
	<xsl:template match="edit_section">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
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
			<xsl:choose>
				<xsl:when test="value_id != ''">
				<tr>
				<td valign="top" width="30%">
						<xsl:value-of select="lang_id"/>
					</td>
					<td align="left">
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="20" name="values[name]" value="{value_name}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_name_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_descr_status_text"/>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr height="50">
				<td colspan = "2" align = "center"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- view_section  -->
	<xsl:template match="view_section">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_id"/>
				</td>
				<td>
					<xsl:value-of select="value_id"/>
				</td>
			</tr>
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" name="values[name]" value="{value_name}"> </input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" readonly="true" rows="10">
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
		</table>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr height="50">
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>
	
<!-- add / edit attribute -->
	<xsl:template match="edit_attrib">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
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

			<tr>
				<td valign="top" width="30%">
					<xsl:value-of select="lang_section"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_section"/>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="value_id != ''">
				<tr>
				<td valign="top" width="30%">
						<xsl:value-of select="lang_id"/>
					</td>
					<td align="left">
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="20" name="values[name]" value="{value_name}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_name_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_input_type"/>
				</td>
				<td>
					<xsl:variable name="lang_input_type_status_text"><xsl:value-of select="lang_input_type_status_text"/></xsl:variable>
					<select name="values[input_type]" class="forms" title="{$lang_input_type_status_text}">
						<option value=""><xsl:value-of select="lang_no_input_type"/></option>
						<xsl:apply-templates select="input_type_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[descr]" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_descr_status_text"/>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
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
				<td colspan = "2" align = "center"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- add / edit value -->
	<xsl:template match="edit_value">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
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

			<tr>
				<td valign="top" width="30%">
					<xsl:value-of select="lang_section"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_section"/>
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%">
					<xsl:value-of select="lang_attrib"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_attrib"/>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="value_id != ''">
				<tr>
				<td valign="top" width="30%">
						<xsl:value-of select="lang_id"/>
					</td>
					<td align="left">
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_value"/>
				</td>
				<td>
				<xsl:choose>
					<xsl:when test="value_input_type = 'listbox'">
						<xsl:variable name="lang_value_status_text"><xsl:value-of select="lang_value_status_text"/></xsl:variable>
						<select name="values[value]" class="forms" title="{$lang_value_status_text}">
							<option value=""><xsl:value-of select="lang_no_value"/></option>
							<xsl:apply-templates select="choice_list"/>
						</select>
					</xsl:when>
					<xsl:when test="value_input_type = 'date'">
						<input type="text" id="date_value" name="values[value]" size="10" value="{value_attrib_value}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_date_statustext"/>
							</xsl:attribute>
						</input>
						<img id="date_value-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
						<input type="hidden" name="values[input_type]" value="date"></input>
					</xsl:when>
					<xsl:otherwise>
						<input type="text" size="20" name="values[value]" value="{value_attrib_value}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_value_status_text"/>
							</xsl:attribute>
						</input>

					</xsl:otherwise>
				</xsl:choose>
				</td>
			</tr>
			<tr height="50">
				<td colspan = "2" align = "left"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_apply_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_status_text"/>
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

	
	<xsl:template match="input_type_list">
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

	<xsl:template match="choice_list">
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
						<input type="checkbox" name="values[delete_choice][]" value="{id}" >
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
				<td valign="top">
					<xsl:value-of select="lang_new_value"/>
				</td>
				<td>
					<input type="text" name="values[new_choice]">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_new_value_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</table>
	</xsl:template>

	<xsl:template match="delete">
			<table cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td align="center" colspan="2"><xsl:value-of select="lang_confirm_msg"/></td>
				</tr>
				<tr>
					<td>
						<xsl:variable name="delete_action"><xsl:value-of select="delete_action"/></xsl:variable>
						<xsl:variable name="lang_yes"><xsl:value-of select="lang_yes"/></xsl:variable>
						<form method="POST" action="{$delete_action}">
							<input type="submit" class="forms" name="confirm" value="{$lang_yes}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_yes_statustext"/>
								</xsl:attribute>
							</input>
						</form>
					</td>
					<td align="right">
						<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
						<xsl:variable name="lang_no"><xsl:value-of select="lang_no"/></xsl:variable>
						<form method="POST" action="{$done_action}">
							<input type="submit" class="forms" name="cancel" value="{$lang_no}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_no_statustext"/>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
	</xsl:template>
