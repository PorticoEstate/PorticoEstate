<!-- $Id$ -->


<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="edit_item">
			<xsl:apply-templates select="edit_item"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view_item">
			<xsl:apply-templates select="view_item"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="list_attribute">
			<xsl:apply-templates select="list_attribute"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="edit_attrib">
			<xsl:apply-templates select="edit_attrib"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="add_activity">
			<xsl:apply-templates select="add_activity"></xsl:apply-templates>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"></xsl:apply-templates>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="list">
	<xsl:apply-templates select="menu"></xsl:apply-templates>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"></xsl:call-template>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<xsl:choose>
				<xsl:when test="member_of_list != ''">
					<td align="left">
						<xsl:call-template name="filter_member_of"></xsl:call-template>
					</td>
				</xsl:when>
			</xsl:choose>

			<td align="left">
				<xsl:call-template name="cat_filter"></xsl:call-template>
			</td>
			<td align="left">
				<xsl:call-template name="filter_vendor"></xsl:call-template>
			</td>
			<td align="right">
				<xsl:call-template name="search_field"></xsl:call-template>
			</td>
			<td valign="top">
				<table>
					<tr>
						<td class="small_text" valign="top" align="left">
							<xsl:variable name="link_columns"><xsl:value-of select="link_columns"></xsl:value-of></xsl:variable>
							<xsl:variable name="lang_columns_help"><xsl:value-of select="lang_columns_help"></xsl:value-of></xsl:variable>
							<xsl:variable name="lang_columns"><xsl:value-of select="lang_columns"></xsl:value-of></xsl:variable>
							<a href="javascript:var w=window.open('{$link_columns}','','left=50,top=100,width=300,height=600')" onMouseOver="overlib('{$lang_columns_help}', CAPTION, '{$lang_columns}')" onMouseOut="nd()">
								<xsl:value-of select="lang_columns"></xsl:value-of></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"></xsl:call-template>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:call-template name="table_header"></xsl:call-template>
		<xsl:call-template name="values"></xsl:call-template>
		<xsl:choose>
			<xsl:when test="table_add!=''">
				<xsl:apply-templates select="table_add"></xsl:apply-templates>
			</xsl:when>
		</xsl:choose>
	</table>
</xsl:template>

<xsl:template match="add_activity">
	<table>
		<tr>
			<td align="left">
				<xsl:value-of select="lang_id"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_agreement_id"></xsl:value-of>
			</td>
		</tr>

		<tr>
			<td valign="top">
				<xsl:value-of select="lang_name"></xsl:value-of>
			</td>
			<td>
				<input type="text" disabled="disabled" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_name_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td>
				<textarea cols="60" disabled="disabled" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
					<xsl:value-of select="value_descr"></xsl:value-of>		
				</textarea>
			</td>
		</tr>
	</table>
	<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
	<form name="form2" method="post" action="{$add_action}">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"></xsl:call-template>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:call-template name="values4"></xsl:call-template>
				</xsl:when>
			</xsl:choose>					
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="center">
					<input type="hidden" name="values[agreement_id]" value="{agreement_id}"></input>
					<xsl:variable name="img_check"><xsl:value-of select="img_check"></xsl:value-of></xsl:variable>
					<a href="javascript:check_all_checkbox2('values[select]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"></img></a>
				</td>
			</tr>

			<tr height="50">
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_save_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>


<xsl:template name="table_header">
	<tr class="th">
		<xsl:for-each select="table_header">
			<td class="th_text" width="{with}" align="{align}">
				<xsl:choose>
					<xsl:when test="sort_link!=''">
						<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="header"></xsl:value-of></a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="header"></xsl:value-of>					
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</xsl:for-each>
	</tr>
</xsl:template>


<xsl:template name="values">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select="row">
				<xsl:choose>
					<xsl:when test="link">
						<td class="small_text" align="center">
							<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"></xsl:value-of></a>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td class="small_text" align="left">
							<xsl:value-of select="value"></xsl:value-of>				
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:choose>
				<xsl:when test="//acl_manage != '' and total_cost!=''">
					<td align="center">
						<input type="hidden" name="values[activity_id][{activity_id}]" value="{activity_id}"></input>
						<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"></input>
						<input type="checkbox" name="values[select][{activity_id}]" value="{cost}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:for-each>
</xsl:template>

<xsl:template name="values2">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select="row">
				<xsl:choose>
					<xsl:when test="link">
						<td class="small_text" align="center">
							<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"></xsl:value-of></a>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td class="small_text" align="left">
							<xsl:value-of select="value"></xsl:value-of>				
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:choose>
				<xsl:when test="//acl_manage != '' and total_cost!=''">
					<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"></input>
					<input type="hidden" name="values[m_cost][{activity_id}]" value="{m_cost}"></input>
					<input type="hidden" name="values[w_cost][{activity_id}]" value="{w_cost}"></input>
					<input type="hidden" name="values[total_cost][{activity_id}]" value="{total_cost}"></input>
					<input type="hidden" name="values[select][0]" value="{activity_id}"></input>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:for-each>
</xsl:template>

<xsl:template name="values3">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
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
				<xsl:value-of select="activity_id"></xsl:value-of>				
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="num"></xsl:value-of>				
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="descr"></xsl:value-of>				
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="unit"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="m_cost"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="w_cost"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="total_cost"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="this_index"></xsl:value-of>				
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="index_count"></xsl:value-of>				
			</td>
			<td class="small_text" align="center">
				<xsl:value-of select="index_date"></xsl:value-of>				
			</td>
			<xsl:choose>
				<xsl:when test="acl_read != ''">
					<td align="center">
						<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>
						<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"></xsl:value-of></a>
					</td>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acl_edit != ''">
					<td align="center">
						<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
						<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
					</td>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acl_delete != ''">
					<td align="center">
						<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
						<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
					</td>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="acl_manage != '' and total_cost!=''">
					<td align="center">
						<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"></input>
						<input type="hidden" name="values[m_cost][{activity_id}]" value="{m_cost}"></input>
						<input type="hidden" name="values[w_cost][{activity_id}]" value="{w_cost}"></input>
						<input type="hidden" name="values[total_cost][{activity_id}]" value="{total_cost}"></input>

						<input type="checkbox" name="values[select][]" value="{activity_id}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:for-each>
</xsl:template>

<xsl:template name="values4">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
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
				<xsl:value-of select="id"></xsl:value-of>				
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="num"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="base_descr"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="descr"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="unit"></xsl:value-of>				
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="ns3420"></xsl:value-of>				
			</td>
			<td align="center">
				<input type="checkbox" name="values[select][]" value="{id}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_select_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:for-each>
</xsl:template>

<xsl:template match="table_add">
	<tr>
		<td height="50">
			<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
			<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_statustext"></xsl:value-of>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"></xsl:value-of>
		</script>
		<div class="yui-navset" id="edit_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
			<div class="yui-content">		

				<div id="general">
					<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
					<table cellpadding="2" cellspacing="2" align="center" width="79%">
						<tr><td>
								<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$edit_url}">
									<table cellpadding="2" cellspacing="2" width="100%" align="center" border="0">
										<xsl:choose>
											<xsl:when test="msgbox_data != ''">
												<tr>
													<td align="left" colspan="3">
														<xsl:call-template name="msgbox"></xsl:call-template>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>
										<xsl:choose>
											<xsl:when test="value_agreement_id!=''">
												<tr>
													<td align="left">
														<xsl:value-of select="lang_id"></xsl:value-of>
													</td>
													<td align="left">
														<xsl:value-of select="value_agreement_id"></xsl:value-of>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>

										<tr>
											<td valign="top">
												<xsl:value-of select="lang_name"></xsl:value-of>
											</td>
											<td>
												<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_name_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											</td>
										</tr>
										<tr>
											<td>
												<xsl:value-of select="lang_status"></xsl:value-of>
											</td>
											<td>
												<xsl:call-template name="status_select"></xsl:call-template>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<xsl:value-of select="lang_descr"></xsl:value-of>
											</td>
											<td>
												<textarea cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
													<xsl:value-of select="value_descr"></xsl:value-of>		
												</textarea>
											</td>
										</tr>
										<tr>
											<td align="left">
												<xsl:value-of select="lang_category"></xsl:value-of>
											</td>
											<td align="left">
												<xsl:call-template name="cat_select"></xsl:call-template>
											</td>
										</tr>
										<xsl:call-template name="vendor_form"></xsl:call-template>
										<tr>
											<td align="left">
												<xsl:value-of select="lang_agreement_group"></xsl:value-of>
											</td>
											<td valign="top">
												<xsl:variable name="lang_agreement_group_statustext"><xsl:value-of select="lang_agreement_group_statustext"></xsl:value-of></xsl:variable>
												<select name="values[group_id]" class="forms" onMouseover="window.status='{$lang_agreement_group_statustext}'; return true;" onMouseout="window.status='';return true;">
													<option value=""><xsl:value-of select="lang_no_agreement_group"></xsl:value-of></option>
													<xsl:apply-templates select="agreement_group_list"></xsl:apply-templates>
												</select>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<xsl:value-of select="lang_start_date"></xsl:value-of>
											</td>
											<td>
												<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_start_date_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<xsl:value-of select="lang_end_date"></xsl:value-of>
											</td>
											<td>
												<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_end_date_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
											</td>
										</tr>
										<tr>
											<td valign="top">
												<xsl:value-of select="lang_termination_date"></xsl:value-of>
											</td>
											<td>
												<input type="text" id="values_termination_date" name="values[termination_date]" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_termination_date_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<img id="values_termination_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
											</td>
										</tr>

										<xsl:choose>
											<xsl:when test="files!=''">
												<!-- <xsl:call-template name="file_list"/> -->
												<tr>
													<td width="19%" align="left" valign="top">
														<xsl:value-of select="//lang_files"></xsl:value-of>
													</td>
													<td>
														<!-- DataTable 2 EDIT-->
														<div id="datatable-container_2"></div>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>

										<xsl:choose>
											<xsl:when test="fileupload = 1">
												<xsl:call-template name="file_upload"></xsl:call-template>
											</xsl:when>
										</xsl:choose>

										<xsl:choose>
											<xsl:when test="member_of_list != ''">
												<tr>
													<td valign="top">
														<xsl:value-of select="lang_member_of"></xsl:value-of>
													</td>
													<td>
														<xsl:variable name="lang_member_of_statustext"><xsl:value-of select="lang_member_of_statustext"></xsl:value-of></xsl:variable>
														<select name="values[member_of][]" disabled="disabled" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
															<xsl:apply-templates select="member_of_list"></xsl:apply-templates>
														</select>
													</td>
												</tr>
											</xsl:when>
										</xsl:choose>
										<xsl:choose>
											<xsl:when test="attributes_group != ''">
												<xsl:call-template name="attributes_values"></xsl:call-template>
											</xsl:when>
										</xsl:choose>

										<tr height="50">
											<td valign="bottom">
												<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
												<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_save_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<!-- </td><td valign="bottom">  -->
												<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
												<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<!-- </td><td align="right" valign="bottom">-->
												<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
												<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											</td>
										</tr>
									</table>
								</form>
						</td></tr>
						<tr><td><br></br><br></br></td></tr>
						<tr><td align="right" valign="bottom">
								<form method="post" name="alarm" action="{$edit_url}">
									<input type="hidden" name="values[entity_id]" value="{value_agreement_id}"></input>
									<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
										<tr><td class="center" align="left"><xsl:value-of select="lang_alarm"></xsl:value-of></td></tr>

										<!-- DataTable 0  EDIT-->
										<tr><td class="center" align="left" colspan="10"><div id="datatable-container_0"></div></td></tr>
										<tr><td class="center" align="right" colspan="10"><div id="datatable-buttons_0"></div></td></tr>
										<tr><td class="center" align="left" colspan="10"><xsl:value-of select="alarm_data/add_alarm/lang_add_alarm"></xsl:value-of><xsl:text> : </xsl:text><xsl:value-of select="alarm_data/add_alarm/lang_day_statustext"></xsl:value-of><xsl:value-of select="alarm_data/add_alarm/lang_hour_statustext"></xsl:value-of><xsl:value-of select="alarm_data/add_alarm/lang_minute_statustext"></xsl:value-of><xsl:value-of select="alarm_data/add_alarm/lang_user"></xsl:value-of></td></tr>
										<tr><td class="center" align="left" colspan="10"><div id="datatable-buttons_1"></div></td></tr>
										<!-- <xsl:call-template name="alarm_form"/>  -->
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">

					<xsl:choose>
						<xsl:when test="table_update!=''">
							<xsl:variable name="update_action"><xsl:value-of select="update_action"></xsl:value-of></xsl:variable>

							<form method="post" name="form2" action="{$update_action}">
								<input type="hidden" name="values[agreement_id]" value="{value_agreement_id}"></input>
								<table width="100%" cellpadding="2" cellspacing="2" align="center" border="0">
									<tr>
										<xsl:for-each select="set_column">
											<td></td>
										</xsl:for-each>
										<td colspan="15" width="100%" class="small_text" valign="bottom" align="right">
											<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
											<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
											<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
											<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
												<xsl:value-of select="lang_download"></xsl:value-of></a>
										</td>
									</tr>
									<!-- DataTable 1 EDIT_ITEMS-->
									<tr><td colspan="15" width="100%">
											<div id="paging_1"> </div>
											<div id="datatable-container_1"></div>
											<div id="contextmenu_1"></div>
									</td></tr>	

									<!--
							<tr><td colspan="15" width="100%"><xsl:call-template name="nextmatchs"/></td></tr>
							<xsl:call-template name="table_header"/>
							<xsl:call-template name="values3"/>
							<tr><xsl:for-each select="set_column" ><td></td></xsl:for-each><td align="center"><xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable><a href="javascript:check_all_checkbox2('values[select]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a></td></tr>
							-->
						</table>
						<br></br>
						<table width="70%" cellpadding="2" cellspacing="2">
							<!-- Buttons 2 -->
							<div id="datatable-buttons_2" class="div-buttons">

								<input class="mybottonsUpdates calendar-opt" type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_date_statustext"></xsl:value-of>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
								<img id="values_date-trigger" class="calendar-opt" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
								<div style="width:25px;height:15px;position:relative;float:left;"></div>
							</div>

							<!-- <xsl:apply-templates select="table_update"/>  -->
						</table>
					</form>
				</xsl:when>
			</xsl:choose>						
			<xsl:choose>
				<xsl:when test="value_agreement_id!=''">
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_add"></xsl:apply-templates>
					</table>
				</xsl:when>
			</xsl:choose>	

		</div>	
	</div>
</div>

<!--  DATATABLE DEFINITIONS-->
		<style type="text/css">
			.calendar-opt
			{
			position:relative;
			float:left;
			}
			.index-opt
			{
			position:relative;
			float:left;
			margin-top:2px;
			}
			.div-buttons
			{
			position:relative;
			float:left;
			width:750px;
			height:100px;
			}
		</style> 
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				permission		:	<xsl:value-of select="permission"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>

			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>
		</script>			
	</xsl:template>

<!-- add item / edit item -->

	<xsl:template match="edit_item">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"></xsl:value-of>
		</script>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>

			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>
		</script>		
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
		<div align="left">
			<form name="form" method="post" action="{$edit_url}">
				<table cellpadding="2" cellspacing="2" width="79%" align="center">
					<xsl:choose>
						<xsl:when test="msgbox_data != ''">
							<tr>
								<td align="left" colspan="2">
									<xsl:call-template name="msgbox"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_agreement_id!=''">
							<tr>
								<td align="left">
									<xsl:value-of select="lang_agreement"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:value-of select="value_agreement_id"></xsl:value-of>
									<xsl:text> [</xsl:text>
									<xsl:value-of select="agreement_name"></xsl:value-of>
									<xsl:text>] </xsl:text>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_id!=''">
							<tr>
								<td align="left">
									<xsl:value-of select="lang_id"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:value-of select="value_id"></xsl:value-of>
									<xsl:text> [</xsl:text>
									<xsl:value-of select="value_num"></xsl:value-of>
									<xsl:text>] </xsl:text>
								</td>
							</tr>
							<tr>
								<td align="left">
									<xsl:value-of select="lang_descr"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:value-of select="activity_descr"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_m_cost"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[m_cost]" value="{value_m_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_m_cost_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_w_cost"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[w_cost]" value="{value_w_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_w_cost_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_total_cost"></xsl:value-of>
						</td>
						<td>
							<xsl:value-of select="value_total_cost"></xsl:value-of>  
						</td>
					</tr>

					<xsl:choose>
						<xsl:when test="attributes_values != ''">
							<tr>
								<td colspan="2" width="50%" align="left">				
									<xsl:call-template name="attributes_form"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr height="50">
						<td valign="bottom" colspan="2" width="30%">
							<input type="hidden" name="values[index_count]" value="{index_count}"></input>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<!-- </td>
				<td valign="bottom"> -->
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"></xsl:value-of></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_apply_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<!--</td>
				<td align="right" valign="bottom"> -->
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td> 
			</tr>
		</table>
	</form>

	<xsl:choose>
		<xsl:when test="values != ''">

			<xsl:variable name="update_action"><xsl:value-of select="update_action"></xsl:value-of></xsl:variable>

			<form method="post" name="form2" action="{$update_action}">

				<input type="hidden" name="values[agreement_id]" value="{value_agreement_id}"></input>
				<style type="text/css">
					.calendar-opt
					{
					position:relative;
					float:left;
					}
					.index-opt
					{
					position:relative;
					float:left;
					margin-top:2px;
					}
					.div-buttons
					{
					position:relative;
					float:left;
					width:750px;
					height:100px;
					}
				</style>

				<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
					<tr><td><br></br></td></tr>
					<!-- DataTable 0 EDIT_ITEM-->
					<tr><td class="center" align="left" colspan="10"><div id="datatable-container_0"></div></td></tr>
					<tr><td><br></br></td></tr>
					<tr><td class="center" align="left" colspan="10">
							<div id="datatable-buttons_0" class="div-buttons">
								<input type="text" id="values_date" class="calendar-opt" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_date_statustext"></xsl:value-of>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
								<img id="values_date-trigger" class="calendar-opt" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
								<div style="width:25px;height:15px;position:relative;float:left;"></div>
							</div>	
					</td></tr>
				</table>

				<!--
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values2"/>
					</table>
					<table width="70%" cellpadding="2" cellspacing="2" align="center">
					<xsl:choose>
						<xsl:when test="table_update!=''">
							<xsl:apply-templates select="table_update"/>
						</xsl:when>
					</xsl:choose>
						<tr>
							<td></td><td></td>
							<td class="small_text" align="left">
								<a href="{delete_action}" onMouseover="window.status='{lang_delete_last_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_delete_last"/></a>
							</td>
						</tr>

					</table>
					-->
				</form>
			</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>


<xsl:template match="table_update">
	<tr>
		<td>
			<xsl:value-of select="lang_new_index"></xsl:value-of>
			<input type="text" name="values[new_index]" size="12" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_new_index_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
		<td>
			<input type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_date_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
			<img id="values_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
		</td>
		<td height="50">
			<xsl:variable name="lang_update"><xsl:value-of select="lang_update"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[update]" value="{$lang_update}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_update_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
	</tr>
</xsl:template>


<!-- view -->

	<xsl:template match="view">
		<div align="left">
			<table cellpadding="2" cellspacing="2" align="center">
				<tr><td>
						<table cellpadding="2" cellspacing="2" width="79%" align="center">
							<tr>
								<td align="left">
									<xsl:value-of select="lang_id"></xsl:value-of>
								</td>
								<td align="left">
									<xsl:value-of select="value_agreement_id"></xsl:value-of>
								</td>
							</tr>

							<tr>
								<td valign="top">
									<xsl:value-of select="lang_name"></xsl:value-of>
								</td>
								<td>
									<xsl:value-of select="value_name"></xsl:value-of>
								</td>
							</tr>
							<tr>
								<td align="left">
									<xsl:value-of select="lang_status"></xsl:value-of>
								</td>
								<xsl:for-each select="status_list">
									<xsl:choose>
										<xsl:when test="selected='selected'">
											<td>
												<xsl:value-of select="name"></xsl:value-of>
											</td>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"></xsl:value-of>
								</td>
								<td>
									<textarea disabled="disabled" cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_descr"></xsl:value-of>		
									</textarea>
								</td>
							</tr>
							<tr>
								<td align="left">
									<xsl:value-of select="lang_category"></xsl:value-of>
								</td>
								<xsl:for-each select="cat_list">
									<xsl:choose>
										<xsl:when test="selected='selected'">
											<td>
												<xsl:value-of select="name"></xsl:value-of>
											</td>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</tr>
							<xsl:call-template name="vendor_view"></xsl:call-template>
							<tr>
								<td align="left">
									<xsl:value-of select="lang_agreement_group"></xsl:value-of>
								</td>
								<xsl:for-each select="agreement_group_list">
									<xsl:choose>
										<xsl:when test="selected='selected'">
											<td>
												<xsl:value-of select="name"></xsl:value-of>
											</td>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_start_date"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;"></input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_end_date"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;"></input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_termination_date"></xsl:value-of>
								</td>
								<td>
									<input type="text" id="termination_date" name="termination_date" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;"></input>
								</td>
							</tr>

							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list_view"/> -->
									<tr>
										<td width="19%" align="left" valign="top">
											<xsl:value-of select="//lang_files"></xsl:value-of>
										</td>
										<td>
											<!-- DataTable 2 VIEW-->
											<div id="datatable-container_2"></div>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>


							<xsl:choose>
								<xsl:when test="attributes_view != ''">
									<tr>
										<td colspan="2" width="50%" align="left">				
											<xsl:apply-templates select="attributes_view"></xsl:apply-templates>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

							<xsl:choose>
								<xsl:when test="member_of_list != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_member_of"></xsl:value-of>
										</td>
										<!--	<td valign="top">
							<xsl:for-each select="member_of_list[selected='selected']" >
								<xsl:value-of select="name"/>
								<xsl:if test="position() != last()">, </xsl:if>
							</xsl:for-each>
						</td>-->

						<td>
							<xsl:variable name="lang_member_of_statustext"><xsl:value-of select="lang_member_of_statustext"></xsl:value-of></xsl:variable>
							<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="member_of_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
</td></tr>
<tr><td>
		<table>
			<tr>
				<td class="th_text" align="left" colspan="4">
					<xsl:value-of select="lang_alarm"></xsl:value-of>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left" colspan="4">

					<!--  DataTable 0 VIEW -->
					<div id="datatable-container_0"></div>
					<!-- <xsl:call-template name="alarm_view"/>  -->
				</td>
			</tr>


		</table>
	</td>
</tr>
			</table>
			<br></br><br></br>
			<xsl:choose>
				<xsl:when test="values!=''">
					<table align="center">
						<tr>
							<td align="center">
								<xsl:value-of select="lang_total_records"></xsl:value-of>
								<xsl:text> </xsl:text>
								<xsl:value-of select="num_records"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td>
								<!--  DataTable 1 VIEW-->	
								<div id="paging_1"></div>
								<div id="datatable-container_1"></div>
							</td>
						</tr>
						<!--
						<tr>
							<td colspan="12" width="100%">
								<xsl:call-template name="nextmatchs"/>
							</td>
						</tr>
						<tr>
							<td colspan="12" width="100%">
								<xsl:call-template name="table_header"/><xsl:call-template name="values"/>
							</td>
						</tr> -->
					</table> 

				</xsl:when>
			</xsl:choose>						
			<table width="80%" cellpadding="2" cellspacing="2" align="center">

				<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
				<form name="form" method="post" action="{$edit_url}">
					<tr height="50">
						<td align="left" valign="bottom">
							<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
			</table>
		</div>

		<!--  DATATABLE DEFINITIONS--> 
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>

		</script>			
	</xsl:template>

<!-- view item -->

	<xsl:template match="view_item">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="79%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_agreement_id!=''">
						<tr>
							<td align="left">
								<xsl:value-of select="lang_agreement"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:value-of select="value_agreement_id"></xsl:value-of>
								<xsl:text> [</xsl:text>
								<xsl:value-of select="agreement_name"></xsl:value-of>
								<xsl:text>] </xsl:text>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_id!=''">
						<tr>
							<td align="left">
								<xsl:value-of select="lang_id"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:value-of select="value_id"></xsl:value-of>
								<xsl:text> [</xsl:text>
								<xsl:value-of select="value_num"></xsl:value-of>
								<xsl:text>] </xsl:text>
							</td>
						</tr>
						<tr>
							<td align="left">
								<xsl:value-of select="lang_descr"></xsl:value-of>
							</td>
							<td align="left">
								<xsl:value-of select="activity_descr"></xsl:value-of>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>

				<tr>
					<td valign="top">
						<xsl:value-of select="lang_m_cost"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_m_cost"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_w_cost"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_w_cost"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_total_cost"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_total_cost"></xsl:value-of>
					</td>
				</tr>

				<xsl:choose>
					<xsl:when test="attributes_view != ''">
						<tr>
							<td colspan="2" width="50%" align="left">				
								<xsl:apply-templates select="attributes_view"></xsl:apply-templates>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>

				<xsl:choose>
					<xsl:when test="values != ''">
						<xsl:variable name="update_action"><xsl:value-of select="update_action"></xsl:value-of></xsl:variable><br></br>
						<!-- DataTable 0  VIEW_ITEMS-->
						<tr>
							<td colspan="2" width="50%" align="left">				
								<br></br>
								<div id="datatable-container_0"></div>
							</td>
						</tr>

						<!--  
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values2"/>
					</table>
				-->					

			</xsl:when>
		</xsl:choose>
	</table>
	<xsl:variable name="edit_url"><xsl:value-of select="edit_url"></xsl:value-of></xsl:variable>
	<form name="form" method="post" action="{$edit_url}">
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<tr height="50">
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
					<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>

</div>

<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>

		</script>			
	</xsl:template>



	<xsl:template match="table_add2">
		<tr>
			<td height="50">
				<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_standardtext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<td height="50">
				<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_standardtext"></xsl:value-of>
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
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_attrib"></xsl:apply-templates>
			<xsl:apply-templates select="values_attrib"></xsl:apply-templates>
			<xsl:apply-templates select="table_add2"></xsl:apply-templates>
		</table>
	</xsl:template>
	<xsl:template match="table_header_attrib">
		<xsl:variable name="sort_sorting"><xsl:value-of select="sort_sorting"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td class="th_text" width="1%" align="center">
				<xsl:value-of select="lang_datatype"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<a href="{$sort_sorting}"><xsl:value-of select="lang_sorting"></xsl:value-of></a>
			</td>
			<td class="th_text" width="1%" align="center">
				<xsl:value-of select="lang_search"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_attrib"> 
		<xsl:variable name="lang_up_text"><xsl:value-of select="lang_up_text"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_down_text"><xsl:value-of select="lang_down_text"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_attribute_attribtext"><xsl:value-of select="lang_delete_attribtext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_edit_attribtext"><xsl:value-of select="lang_edit_attribtext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_attribtext"><xsl:value-of select="lang_delete_attribtext"></xsl:value-of></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
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
				<xsl:value-of select="column_name"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="input_text"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="datatype"></xsl:value-of>
			</td>
			<td>
				<table align="left">
					<tr>
						<td>
							<xsl:value-of select="sorting"></xsl:value-of>
						</td>

						<td align="left">
							<xsl:variable name="link_up"><xsl:value-of select="link_up"></xsl:value-of></xsl:variable>
							<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_up"></xsl:value-of></a>
							<xsl:text> | </xsl:text>
							<xsl:variable name="link_down"><xsl:value-of select="link_down"></xsl:value-of></xsl:variable>
							<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_down"></xsl:value-of></a>
						</td>

					</tr>
				</table>
			</td>
			<td align="center">
				<xsl:value-of select="search"></xsl:value-of>
			</td>
			<td align="center">
				<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_attribtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_attribtext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
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
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>

				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" action="{$form_action}">
					<tr>
						<td valign="top">
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<xsl:value-of select="lang_id"></xsl:value-of>
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<xsl:value-of select="value_id"></xsl:value-of>
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>	
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_column_name"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_column_name_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_input_text"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[input_text]" value="{value_input_text}" size="60" maxlength="50" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_input_text_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_statustext"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[statustext]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_statustext_attribtext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_statustext"></xsl:value-of>		
							</textarea>

						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_datatype"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_datatype_statustext"><xsl:value-of select="lang_datatype_statustext"></xsl:value-of></xsl:variable>
							<select name="values[column_info][type]" class="forms" onMouseover="window.status='{$lang_datatype_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_datatype"></xsl:value-of></option>
								<xsl:apply-templates select="datatype_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_precision"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[column_info][precision]" value="{value_precision}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_precision_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_scale"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[column_info][scale]" value="{value_scale}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_scale_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_default"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[column_info][default]" value="{value_default}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_default_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_nullable"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_nullable_statustext"><xsl:value-of select="lang_nullable_statustext"></xsl:value-of></xsl:variable>
							<select name="values[column_info][nullable]" class="forms" onMouseover="window.status='{$lang_nullable_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_nullable"></xsl:value-of></option>
								<xsl:apply-templates select="nullable_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_list"></xsl:value-of>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_list = 1">
									<input type="checkbox" name="values[list]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[list]" value="1" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_include_search"></xsl:value-of>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_search = 1">
									<input type="checkbox" name="values[search]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[search]" value="1" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"></xsl:value-of>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="multiple_choice != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_choice"></xsl:value-of>
								</td>
								<td align="right">
									<xsl:call-template name="choice"></xsl:call-template>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_attribtext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>

				</form>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_attribtext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

<!-- datatype_list -->	

	<xsl:template match="datatype_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- nullable_list -->	

	<xsl:template match="nullable_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="member_of_list">
		<xsl:variable name="id"><xsl:value-of select="cat_id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="agreement_group_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
