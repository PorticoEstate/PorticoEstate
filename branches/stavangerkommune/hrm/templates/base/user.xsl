<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="edit">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="training">
				<xsl:apply-templates select="training"/>
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
				<xsl:apply-templates select="values"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_last_name"><xsl:value-of select="sort_last_name"/></xsl:variable>
		<xsl:variable name="sort_first_name"><xsl:value-of select="sort_first_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_first_name}"><xsl:value-of select="lang_first_name"/></a>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_last_name}"><xsl:value-of select="lang_last_name"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_training"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_user_text"><xsl:value-of select="lang_view_user_text"/></xsl:variable>
		<xsl:variable name="lang_edit_user_text"><xsl:value-of select="lang_edit_user_text"/></xsl:variable>
		<xsl:variable name="lang_training_user_text"><xsl:value-of select="lang_training_user_text"/></xsl:variable>

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
					<xsl:value-of select="first_name"/>
				</td>
				<td align="left">
					<xsl:value-of select="last_name"/>
				</td>
				<td align="center">
					<xsl:variable name="link_training"><xsl:value-of select="link_training"/></xsl:variable>
					<a href="{$link_training}" onMouseover="window.status='{$lang_training_user_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_training"/></a>
				</td>
<!--				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_user_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_user_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
				</td>-->
			</tr>
	</xsl:template>


<!-- training  -->
	<xsl:template match="training">
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
			<xsl:call-template name="user_values"/>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_training"/>
				<xsl:apply-templates select="values_training"/>
			</table>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_add"/>
				<tr>
					<xsl:variable name="link_cv_action"><xsl:value-of select="link_cv"/></xsl:variable>
					<xsl:variable name="text_cv"><xsl:value-of select="text_cv"/></xsl:variable>
					<form method="post" action="{$link_cv_action}" target="_new">
					<td align="left" height="50">
						<input type="submit" name="view_tender" value="{$text_cv}"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cv_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					</form>
				</tr>
			</table>

		</table>
		</div>
	</xsl:template>
	
	<xsl:template match="table_header_training">
		<xsl:variable name="sort_title"><xsl:value-of select="sort_title"/></xsl:variable>
		<xsl:variable name="sort_place"><xsl:value-of select="sort_place"/></xsl:variable>
		<xsl:variable name="sort_start_date"><xsl:value-of select="sort_start_date"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_category"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_title}"><xsl:value-of select="lang_title"/></a>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_place}"><xsl:value-of select="lang_place"/></a>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_place}"><xsl:value-of select="lang_credits"/></a>
			</td>
			<td class="th_text" width="10%" align="center">
				<a href="{$sort_start_date}"><xsl:value-of select="lang_start_date"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_end_date"/>
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

	<xsl:template match="values_training">
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
					<xsl:value-of select="title"/>
				</td>
				<td align="left">
					<xsl:value-of select="place"/>
				</td>
				<td align="right">
					<xsl:value-of select="credits"/>
				</td>
				<td align="center">
					<xsl:value-of select="start_date"/>
				</td>
				<td align="center">
					<xsl:value-of select="end_date"/>
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



	<xsl:template match="table_add">
			<tr>
				<xsl:choose>
				<xsl:when test="add_action != ''">
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_training_text"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				</xsl:when>
				</xsl:choose>
				<td height="50">
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_training_text"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>


<!-- add / edit  -->
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
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
					<xsl:variable name="lang_cat_statustext"><xsl:value-of select="lang_cat_statustext"/></xsl:variable>
					<select name="values[cat_id]" class="forms" onMouseover="window.status='{$lang_cat_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_cat"/></option>
						<xsl:apply-templates select="cat_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<input type="text" size="60" name="values[title]" value="{value_title}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_title_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_start_date-trigger" src="{img_cal}" alt="lang_date_selector" title="lang_select_date" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_end_date-trigger" src="{img_cal}" alt="lang_date_selector" title="lang_select_date" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'credits')" />
				</td>
				<td>
					<input type="text" size="60" name="values[credits]" value="{value_credits}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'credits')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reference"/>
				</td>
				<td>
					<input type="text" size="60" name="values[reference]" value="{value_reference}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_reference_text"/>
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
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_skill"/>
				</td>
				<td>
				<xsl:variable name="lang_skill_status_text"><xsl:value-of select="lang_skill_status_text"/></xsl:variable>
					<select name="values[skill]" class="forms" onMouseover="window.status='{$lang_skill_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_skill"/></option>
						<xsl:apply-templates select="skill_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_place"/>
				</td>
				<td>
				<xsl:variable name="lang_place_status_text"><xsl:value-of select="lang_place_status_text"/></xsl:variable>
					<select name="place_id" onChange ="modplace(this.form)" class="forms" onMouseover="window.status='{$lang_place_status_text}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_place"/></option>
						<option value="new_place"><xsl:value-of select="lang_new_place"/></option>
						<xsl:apply-templates select="place_list"/>
					</select>
				</td>
			</tr>
		</table>
		<div id="div1" STYLE="display: none">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_new_place_name"/>
				</td>
				<td>
					<input type="text" size="60" name="values[new_place_name]" value="{value_new_place_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_place_name_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_place_address"/>
				</td>
				<td>
					<input type="text" size="60" name="values[new_place_address]" value="{value_new_place_address}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_place_address_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_place_zip"/>
				</td>
				<td>
					<input type="text" size="6" name="values[new_place_zip]" value="{value_new_place_zip}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_place_zip_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:value-of select="lang_new_place_town"/>
					<input type="text" size="40" name="values[new_place_town]" value="{value_new_place_town}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_place_town_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_place_remark"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[new_place_remark]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_new_place_remark_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_new_place_remark"/>		
					</textarea>
				</td>
			</tr>
		</table>
		</div>				
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


<!-- view  -->
	<xsl:template match="view" xmlns:php="http://php.net/xsl">
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
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
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
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" name="values[title]" value="{value_title}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_title_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<input type="text" id="values[start_date]" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<input type="text" id="values[end_date]" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'credits')" />
				</td>
				<td>
					<input type="text" size="4" value="{value_credits}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'credits')" />
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_reference"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" name="values[reference]" value="{value_reference}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_reference_text"/>
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
					<xsl:value-of select="lang_place"/>
				</td>
				<td>
					<xsl:for-each select="place_list[selected='selected']" >
						<xsl:value-of select="name"/>
						<xsl:if test="position() != last()">, </xsl:if>
					</xsl:for-each>
				</td>
			</tr>
		</table>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr height="50">
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
		</form>
		</div>
	</xsl:template>


	<xsl:template name="user_values">
		<xsl:for-each select="user_values" >
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
			<td class="small_text" align="left">
				<xsl:value-of select="name"/>					
			</td>
			<xsl:choose>
				<xsl:when test="type = 'link'">
					<td class="small_text" align="left">
						<a href="{link_value}"><xsl:value-of select="value"/></a>
					</td>
				</xsl:when>
				<xsl:when test="type = 'mail'">
					<td class="small_text" align="left">
						<a href="mailto:{link_value}"><xsl:value-of select="link_value"/></a>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td class="small_text" align="left">
						<xsl:value-of select="value"/>					
					</td>
				</xsl:otherwise>
			</xsl:choose>
			</tr>
		</xsl:for-each>
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

	<xsl:template match="place_list">
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
