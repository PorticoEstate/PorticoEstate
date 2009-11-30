<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_job">
				<xsl:apply-templates select="edit_job"/>
			</xsl:when>
			<xsl:when test="edit_job">
				<xsl:apply-templates select="view_job"/>
			</xsl:when>
			<xsl:when test="edit_qualification">
				<xsl:apply-templates select="edit_qualification"/>
			</xsl:when>
			<xsl:when test="view_qualification">
				<xsl:apply-templates select="view_qualification"/>
			</xsl:when>
			<xsl:when test="lookup_qualification">
				<xsl:apply-templates select="lookup_qualification"/>
			</xsl:when>
			<xsl:when test="task">
				<xsl:apply-templates select="task"/>
			</xsl:when>
			<xsl:when test="edit_task">
				<xsl:apply-templates select="edit_task"/>
			</xsl:when>
			<xsl:when test="edit_job">
				<xsl:apply-templates select="view_task"/>
			</xsl:when>
			<xsl:when test="qualification">
				<xsl:apply-templates select="qualification"/>
			</xsl:when>
			<xsl:when test="edit_qualification_type">
				<xsl:apply-templates select="edit_qualification_type"/>
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

		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header"/>
			<xsl:choose>
				<xsl:when test="values != ''">
				<xsl:variable name="print_action"><xsl:value-of select="print_action"/></xsl:variable>
				<form name = "form" method="post" action="{$print_action}">
				<xsl:apply-templates select="values"/>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align="center">
						<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
						 <a href="javascript:check_all_checkbox('values[select]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:variable name="lang_print"><xsl:value-of select="lang_print"/></xsl:variable>
						<input type="submit" name="print" value="{$lang_print}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_print_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				</form>
				</xsl:when>
			</xsl:choose>

			<xsl:apply-templates select="table_add_job"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_qualification"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_task"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_add_sub"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_print"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_qualification_job_text"><xsl:value-of select="lang_qualification_job_text"/></xsl:variable>
		<xsl:variable name="lang_task_job_text"><xsl:value-of select="lang_task_job_text"/></xsl:variable>
		<xsl:variable name="lang_view_job_text"><xsl:value-of select="lang_view_job_text"/></xsl:variable>
		<xsl:variable name="lang_edit_job_text"><xsl:value-of select="lang_edit_job_text"/></xsl:variable>
		<xsl:variable name="lang_delete_job_text"><xsl:value-of select="lang_delete_job_text"/></xsl:variable>
		<xsl:variable name="lang_add_sub_text"><xsl:value-of select="lang_add_sub_text"/></xsl:variable>
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
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="center">
					<xsl:variable name="link_qualification"><xsl:value-of select="link_qualification"/></xsl:variable>
					<a href="{$link_qualification}" onMouseover="window.status='{$lang_qualification_job_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_qualification"/></a>
					<xsl:text> [</xsl:text>
					<xsl:value-of select="quali_count"/>
					<xsl:text>]</xsl:text>
				</td>
				<td align="center">
					<xsl:variable name="link_task"><xsl:value-of select="link_task"/></xsl:variable>
					<a href="{$link_task}" onMouseover="window.status='{$lang_task_job_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_task"/></a>
					<xsl:text> [</xsl:text>
					<xsl:value-of select="task_count"/>
					<xsl:text>]</xsl:text>
				</td>

				<td align="center">
					<xsl:variable name="link_add_sub"><xsl:value-of select="link_add_sub"/></xsl:variable>
					<a href="{$link_add_sub}" onMouseover="window.status='{$lang_add_sub_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_add_sub"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_job_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_job_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_job_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
				<td align="center">
					<input type="checkbox" name="values[select][]" value="{id}"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>

			</tr>
	</xsl:template>


	<xsl:template match="table_add_job">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td height="50">
					<xsl:variable name="reset_action"><xsl:value-of select="reset_action"/></xsl:variable>
					<xsl:variable name="lang_reset"><xsl:value-of select="lang_reset"/></xsl:variable>
					<form method="post" action="{$reset_action}">
						<input type="submit" name="add" value="{$lang_reset}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_reset_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
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
									<xsl:value-of select="lang_add_statustext"/>
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
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add job / edit job-->
	<xsl:template match="edit_job">
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_parent"/>
				</td>
				<td>
				<xsl:variable name="lang_parent_status_text"><xsl:value-of select="lang_parent_status_text"/></xsl:variable>
					<select name="values[parent_id]" class="forms" onMouseover="window.status='{$lang_parent_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_parent"/></option>
						<xsl:apply-templates select="parent_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_status_text"/>
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
								<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>

				</td>
			</tr>

			<tr height="50">			
			<TD colspan = "2">
			<TABLE align = "center">
			<TR>
			<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</TR>
			</TABLE>
			</TD>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

<!-- view job-->
	<xsl:template match="view_job">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="done_action"/></xsl:variable>
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_parent"/>
				</td>
				<td>
					<xsl:for-each select="parent_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>

			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" name="values[name]" value="{value_name}"></input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" readonly="true" rows="10" name="values[descr]" wrap="virtual">
						<xsl:value-of select="value_descr"/>		
					</textarea>

				</td>
			</tr>

			<tr height="50">			
			<TD colspan = "2">
			<TABLE align = "center">
			<TR>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</TR>
			</TABLE>
			</TD>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

<!-- task  -->
	<xsl:template match="task">
		<div align="left">

		<table>
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
				<td valign="top">
					<xsl:value-of select="lang_job_name"/>
					<xsl:text>: </xsl:text>
				</td>
				<td>
					<xsl:value-of select="value_job_name"/>
				</td>
			</tr>
			
			
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_task"/>
				<xsl:apply-templates select="values_task"/>
			</table>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_add"/>
			</table>

		</table>
		</div>
	</xsl:template>
	
	<xsl:template match="table_header_task">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_task">
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"/></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"/></xsl:variable>
		<xsl:variable name="lang_view_text"><xsl:value-of select="lang_view_text"/></xsl:variable>
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
					<xsl:value-of select="name"/>
				</td>
				<td align="left">
					<xsl:value-of select="descr"/>
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
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
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



<!-- add task / edit task-->
	<xsl:template match="edit_task">
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_parent"/>
				</td>
				<td>
				<xsl:variable name="lang_parent_status_text"><xsl:value-of select="lang_parent_status_text"/></xsl:variable>
					<select name="values[parent_id]" class="forms" onMouseover="window.status='{$lang_parent_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_parent"/></option>
						<xsl:apply-templates select="parent_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_status_text"/>
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
								<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>

				</td>
			</tr>

			<tr height="50">			
			<TD colspan = "2">
			<TABLE align = "center">
			<TR>
			<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</TR>
			</TABLE>
			</TD>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- view task-->
	<xsl:template match="view_task">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="done_action"/></xsl:variable>
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_parent"/>
				</td>
				<td>
					<xsl:for-each select="parent_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>

			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" name="values[name]" value="{value_name}"></input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<textarea cols="60" readonly="true" rows="10" name="values[descr]" wrap="virtual">
						<xsl:value-of select="value_descr"/>		
					</textarea>

				</td>
			</tr>

			<tr height="50">			
			<TD colspan = "2">
			<TABLE align = "center">
			<TR>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</TR>
			</TABLE>
			</TD>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- qualification  -->
	<xsl:template match="qualification">
		<div align="left">

		<table>
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
				<td valign="top">
					<xsl:value-of select="lang_job_name"/>
					<xsl:text>: </xsl:text>
				</td>
				<td>
					<xsl:value-of select="value_job_name"/>
				</td>
			</tr>
			
			
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_qualification"/>
				<xsl:apply-templates select="values_qualification"/>
			</table>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_add"/>
			</table>

		</table>
		</div>
	</xsl:template>
	
	<xsl:template match="table_header_qualification">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"/></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_category"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_remark"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_qualification">
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"/></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"/></xsl:variable>
		<xsl:variable name="lang_view_text"><xsl:value-of select="lang_view_text"/></xsl:variable>
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
					<xsl:value-of select="category"/>
				</td>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="left">
					<xsl:value-of select="remark"/>
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
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
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

<!-- lookup_qualification  -->
	<xsl:template match="lookup_qualification">
		<div align="left">

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
				<td colspan="5" align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="5" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
			
				<xsl:apply-templates select="table_header_lookup_qualification"/>
				<xsl:choose>
					<xsl:when test="values_lookup_qualification != ''">
					<xsl:apply-templates select="values_lookup_qualification"/>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td>
						<input type="button" name="Done" value="{lang_cancel}" onclick="window.close()" />
					</td>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				</tr>
		</table>
		</div>
	</xsl:template>
	
	<xsl:template match="table_header_lookup_qualification">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="30%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="50%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_lookup_qualification">
		<xsl:variable name="lang_view_text"><xsl:value-of select="lang_view_text"/></xsl:variable>
		<xsl:variable name="lang_edit_text"><xsl:value-of select="lang_edit_text"/></xsl:variable>
		<xsl:variable name="lang_delete_text"><xsl:value-of select="lang_delete_text"/></xsl:variable>
			<form>			
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
				<td align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td class="small_text" valign="bottom" align = "center">
						<input type="hidden" name="hidden" value="{id}"></input>
						<input type="hidden" name="hidden" value="{name}"></input>
						<input type="hidden" name="hidden" value="{descr}"></input>
						<xsl:variable name="lang_select"><xsl:value-of select="lang_select"/></xsl:variable>
						<input type="button" name="select" value="{$lang_select}" onClick="Exchangequalification(this.form);" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
				</td>
			</tr>
			</form>
	</xsl:template>


<!-- add qualification / edit qualification -->
	<xsl:template match="edit_qualification">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}" name = "qualification_form">
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td width="10%">
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:variable name="lang_cat_status_text"><xsl:value-of select="lang_cat_status_text"/></xsl:variable>
					<select name="values[cat_id]" class="forms" onMouseover="window.status='{$lang_cat_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_cat"/></option>
						<xsl:apply-templates select="cat_list"/>
					</select>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
				<table>
					<tr>
						<td>
							<input type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_name_status_text"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="readonly"></xsl:attribute>			
							</input>
						</td>
						<td valign="top">
			        			<input type="button" value="{lang_open_popup}" onClick="qualifications_popup()"></input>
							<input type="hidden" name="values[quali_type_id]" value="{value_quali_type_id}"></input>
						</td>
					</tr>
				</table>
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
								<xsl:value-of select="lang_descr_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="readonly"></xsl:attribute>			
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_remark"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[remark]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_remark_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_skill"/>
				</td>
				<td>
				<xsl:variable name="lang_skill_status_text"><xsl:value-of select="lang_skill_status_text"/></xsl:variable>
					<select name="values[skill_id]" class="forms" onMouseover="window.status='{$lang_skill_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_skill"/></option>
						<xsl:apply-templates select="skill_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_experience"/>
				</td>
				<td>
				<xsl:variable name="lang_experience_status_text"><xsl:value-of select="lang_experience_status_text"/></xsl:variable>
					<select name="values[experience_id]" class="forms" onMouseover="window.status='{$lang_experience_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_experience"/></option>
						<xsl:apply-templates select="experience_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_alternative"/>
				</td>
				<td>
				<table>
					<tr>
						<td>
							<select multiple="true" size = "{qualification_list_size}" name="alternative_qualification[]" class="forms" onMouseover="window.status=''; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="qualification_list"/>
							</select>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr height="50">
				<td colspan = "2" align = "center"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- view qualification / view qualification -->
	<xsl:template match="view_qualification">
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
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td width="10%">
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:for-each select="cat_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input readonly="true" type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_status_text"/>
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
					<textarea readonly="true" cols="60" rows="10" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_remark"/>
				</td>
				<td>
					<textarea readonly="true" cols="60" rows="10" name="values[remark]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_remark_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>		
					</textarea>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_skill"/>
				</td>
				<td>
					<xsl:for-each select="skill_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_experience"/>
				</td>
				<td>
					<xsl:for-each select="experience_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_alternative"/>
				</td>
				<td>
					<xsl:for-each select="qualification_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
			<tr height="50">
				<td colspan = "2" align = "center"><table><tr>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
			</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

<!-- add / edit qualification type -->
	<xsl:template match="edit_qualification_type">
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
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" size="60" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_status_text"/>
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
								<xsl:value-of select="lang_descr_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr height="50">
				<td colspan = "2" align = "center"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

	<xsl:template match="parent_list">
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

	<xsl:template match="cat_list">
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

	<xsl:template match="skill_list">
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

	<xsl:template match="experience_list">
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

	<xsl:template match="qualification_list">
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

