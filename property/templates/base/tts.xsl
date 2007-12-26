<!-- $Id: tts.xsl,v 1.20 2007/10/13 10:02:54 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="add2">
				<xsl:apply-templates select="add2"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view2"/>
			</xsl:when>
			<xsl:when test="list2">
				<xsl:apply-templates select="list2"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list">
		<xsl:variable name="autorefresh"><xsl:value-of select="autorefresh"/></xsl:variable>
		<meta http-equiv="refresh" content="{$autorefresh}"/>
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
			<xsl:choose>
				<xsl:when test="group_filters != ''">
					<xsl:variable name="select_action"><xsl:value-of select="select_action"/></xsl:variable>
					<form method="post" name="search" action="{$select_action}">
						<td>
							<xsl:call-template name="cat_select"/>
						</td>
						<td>
							<xsl:call-template name="select_district"/>
						</td>
						<td align="center">
							<xsl:call-template name="filter_select"/>
						</td>
						<td align="center">
							<xsl:call-template name="user_id_select"/>
						</td>
						<td align="right">
							<xsl:call-template name="search_field_grouped"/>
						</td>
					</form>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<xsl:call-template name="cat_filter"/>
					</td>
					<td>
						<xsl:call-template name="filter_district"/>
					</td>
					<td align="center">
						<xsl:call-template name="filter_filter"/>
					</td>
					<td align="center">
						<xsl:call-template name="user_id_filter"/>
					</td>
					<td align="right">
						<xsl:call-template name="search_field"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_excel"><xsl:value-of select="link_excel"/></xsl:variable>
					<xsl:variable name="lang_excel_help"><xsl:value-of select="lang_excel_help"/></xsl:variable>
					<xsl:variable name="lang_excel"><xsl:value-of select="lang_excel"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_excel}','','')"
						onMouseOver="overlib('{$lang_excel_help}', CAPTION, '{$lang_excel}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_excel"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="8" width="100%">
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
		<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
		<xsl:variable name="lang_id_statustext"><xsl:value-of select="lang_id_statustext"/></xsl:variable>
		<xsl:variable name="lang_opened_by_statustext"><xsl:value-of select="lang_opened_by_statustext"/></xsl:variable>
		<xsl:variable name="lang_assigned_to_statustext"><xsl:value-of select="lang_assigned_to_statustext"/></xsl:variable>
		<xsl:variable name="lang_finnish_statustext"><xsl:value-of select="lang_finnish_statustext"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="1%" align="right">
					<xsl:variable name="sort_priority"><xsl:value-of select="sort_priority"/></xsl:variable>
					<a href="{$sort_priority}" onMouseover="window.status='{$lang_priority_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_priority"/></a>
				</td>
				<td class="th_text" width="6%" align="right">
					<xsl:variable name="sort_id"><xsl:value-of select="sort_id"/></xsl:variable>
					<a href="{$sort_id}" onMouseover="window.status='{$lang_id_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_id"/></a>
				</td>
				<td class="th_text" width="10%">
					<xsl:value-of select="lang_subject"/>
				</td>
				<td class="th_text" width="15%" align="left">
					<xsl:value-of select="lang_location_code"/>
				</td>
				<td class="th_text" width="30%" align="left">
					<xsl:value-of select="lang_address"/>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_opened_by"><xsl:value-of select="sort_opened_by"/></xsl:variable>
					<a href="{$sort_opened_by}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_opened_by"/></a>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_assigned_to"><xsl:value-of select="sort_assigned_to"/></xsl:variable>
					<a href="{$sort_assigned_to}" onMouseover="window.status='{$lang_assigned_to_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_assigned_to"/></a>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_date"><xsl:value-of select="sort_date"/></xsl:variable>
					<a href="{$sort_date}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_time_created"/></a>
				</td>
				<xsl:for-each select="extra" >
					<td class="th_text" width="{with}" align="{align}">
						<xsl:value-of select="header"/>					
					</td>
				</xsl:for-each>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_finnish_date"><xsl:value-of select="sort_finnish_date"/></xsl:variable>
					<a href="{$sort_finnish_date}" onMouseover="window.status='{$lang_finnish_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_finnish_date"/></a>
				</td>
				<td class="th_text" width="15%" align="center">
					<xsl:value-of select="lang_delay"/>
				</td>
				<td class="th_text" width="15%" align="center">
					<xsl:value-of select="lang_status"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>		
		<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
			<tr bgcolor="{bgcolor}" >
				<td class="small_text" align="right">
					<xsl:value-of select="priostr"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="new_ticket"/>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="first"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="location_code"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="address"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="user"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="assignedto"/>
				</td>
				<td class="small_text" align="center">
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="date"/></a>
				</td>
				<xsl:for-each select="child_date" >
					<td class="small_text">
						<xsl:for-each select="date_info" >
							<xsl:variable name="link"><xsl:value-of select="link"/></xsl:variable>
							<a href="{$link}" onMouseover="window.status='';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="entry_date"/></a>
							<br/>
						</xsl:for-each>
					</td>
				</xsl:for-each>
				<td class="small_text" align="center">
					<xsl:value-of select="finnish_date"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="delay"/>
				</td>
				<td class="small_text" valign="top" align="center">
					<xsl:choose>
						<xsl:when test="//allow_edit_status != ''">
							<xsl:variable name="link_edit_status"><xsl:value-of select="link_edit_status"/></xsl:variable>
							<xsl:variable name="lang_edit_status"><xsl:value-of select="lang_edit_status"/></xsl:variable>
							<xsl:variable name="text_edit_status"><xsl:value-of select="text_edit_status"/></xsl:variable>
							<xsl:variable name="status"><xsl:value-of select="status"/></xsl:variable>
							<a href="{$link_edit_status}"
								onMouseOver="overlib('{$text_edit_status}', CAPTION, '{$lang_edit_status}')"
								onMouseOut="nd()">
								<xsl:value-of select="status"/></a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="status"/>
						</xsl:otherwise>
					</xsl:choose>
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
			</tr>
	</xsl:template>

	<xsl:template match="list2">
		<xsl:variable name="autorefresh"><xsl:value-of select="autorefresh"/></xsl:variable>
		<meta http-equiv="refresh" content="{$autorefresh}"/>		
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
					<xsl:call-template name="filter_filter"/>
				</td>
				<td colspan="10" align="right">
					<xsl:call-template name="search_field"/>
				</td>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_excel"><xsl:value-of select="link_excel"/></xsl:variable>
					<xsl:variable name="lang_excel_help"><xsl:value-of select="lang_excel_help"/></xsl:variable>
					<xsl:variable name="lang_excel"><xsl:value-of select="lang_excel"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_excel}','','')"
						onMouseOver="overlib('{$lang_excel_help}', CAPTION, '{$lang_excel}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_excel"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="12" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header2"/>
				<xsl:apply-templates select="values2"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header2">
		<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
		<xsl:variable name="lang_id_statustext"><xsl:value-of select="lang_id_statustext"/></xsl:variable>
		<xsl:variable name="lang_opened_by_statustext"><xsl:value-of select="lang_opened_by_statustext"/></xsl:variable>
		<xsl:variable name="lang_assigned_to_statustext"><xsl:value-of select="lang_assigned_to_statustext"/></xsl:variable>
		<xsl:variable name="lang_finnish_statustext"><xsl:value-of select="lang_finnish_statustext"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="1%" align="right">
					<xsl:variable name="sort_priority"><xsl:value-of select="sort_priority"/></xsl:variable>
					<a href="{$sort_priority}" onMouseover="window.status='{$lang_priority_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_priority"/></a>
				</td>
				<td class="th_text" width="6%" align="right">
					<xsl:variable name="sort_id"><xsl:value-of select="sort_id"/></xsl:variable>
					<a href="{$sort_id}" onMouseover="window.status='{$lang_id_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_id"/></a>
				</td>
				<td class="th_text" width="10%">
					<xsl:value-of select="lang_subject"/>
				</td>
				<td class="th_text" width="15%" align="left">
					<xsl:value-of select="lang_location_code"/>
				</td>
				<td class="th_text" width="30%" align="left">
					<xsl:value-of select="lang_address"/>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_opened_by"><xsl:value-of select="sort_opened_by"/></xsl:variable>
					<a href="{$sort_opened_by}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_opened_by"/></a>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_assigned_to"><xsl:value-of select="sort_assigned_to"/></xsl:variable>
					<a href="{$sort_assigned_to}" onMouseover="window.status='{$lang_assigned_to_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_assigned_to"/></a>
				</td>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_date"><xsl:value-of select="sort_date"/></xsl:variable>
					<a href="{$sort_date}" onMouseover="window.status='{$lang_opened_by_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_time_created"/></a>
				</td>
				<xsl:for-each select="extra" >
					<td class="th_text" width="{with}" align="{align}">
						<xsl:value-of select="header"/>					
					</td>
				</xsl:for-each>
				<td class="th_text" width="8%" align="center">
					<xsl:variable name="sort_finnish_date"><xsl:value-of select="sort_finnish_date"/></xsl:variable>
					<a href="{$sort_finnish_date}" onMouseover="window.status='{$lang_finnish_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_finnish_date"/></a>
				</td>
				<td class="th_text" width="15%" align="center">
					<xsl:value-of select="lang_delay"/>
				</td>
				<td class="th_text" width="15%" align="center">
					<xsl:value-of select="lang_status"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values2">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>		
		<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
			<tr bgcolor="{bgcolor}" >
				<td class="small_text" align="right">
					<xsl:value-of select="priostr"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="new_ticket"/>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="first"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="location_code"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="address"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="user"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="assignedto"/>
				</td>
				<td class="small_text" align="center">
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="date"/></a>
				</td>
				<xsl:for-each select="child_date" >
					<td class="small_text">
						<xsl:for-each select="date_info" >
							<xsl:variable name="link"><xsl:value-of select="link"/></xsl:variable>
							<a href="{$link}" onMouseover="window.status='';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="entry_date"/></a>
							<br/>
						</xsl:for-each>
					</td>
				</xsl:for-each>
				<td class="small_text" align="center">
					<xsl:value-of select="finnish_date"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="delay"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="status"/>
				</td>
			</tr>
	</xsl:template>


<!-- add -->

	<xsl:template match="add">
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
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
				<xsl:for-each select="value_origin" >
					<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
					<tr>
						<td valign ="top">
							<xsl:value-of select="descr"/>
						</td>
						<td>
							<table>							
								<xsl:for-each select="data">
									<tr>
										<td class="th_text"  align="left" >
											<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
											<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
											<xsl:text> </xsl:text>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</td>
					</tr>
				</xsl:for-each>
				<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
				<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
				
			<xsl:call-template name="location_form"/>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_assign_to"/>
				</td>
				<td>
					<xsl:call-template name="user_id_select"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_group"/>
				</td>
				<td>
					<xsl:call-template name="group_select"/>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="mailnotification != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_mailnotification"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="pref_send_mail = 1">
										<input type="checkbox" name="values[send_mail]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_mailnotification_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[send_mail]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_mailnotification_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td>
				<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
				<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
					<select name="{$select_priority_name}" class="forms" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="priority_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>


			<tr>
				<td valign="top">
					<xsl:value-of select="lang_subject"/>
				</td>
				<td>
					<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_subject_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_details"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[details]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_details_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_details"/>		
					</textarea>

				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_upload_file"/>
						</td>
						<td>
							<input type="file" name="file" size="40" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_file_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
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
								<xsl:value-of select="lang_save_statustext"/>
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
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		</div>

	</xsl:template>

<!-- add2 -->

	<xsl:template match="add2">
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
			<form name="form" method="post" action="{$form_action}">

			<xsl:call-template name="location_view"/>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td>
				<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
				<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
					<select name="{$select_priority_name}" class="forms" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="priority_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_contact_phone"/>
				</td>
				<td>
					<input type="text" name="values[extra][contact_phone]" value="{value_contact_phone}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_contact_phone_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_contact_email"/>
				</td>
				<td>
					<input type="text" name="values[extra][contact_email]" value="{value_contact_email}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_contact_email_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
<!--
			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>
-->

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_subject"/>
				</td>
				<td>
					<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_subject_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_details"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[details]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_details_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_details"/>		
					</textarea>

				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
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
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		</div>

	</xsl:template>

	<xsl:template match="priority_list">
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

<!-- view -->
	<xsl:template match="view">
		<script language="JavaScript">
			self.name="first_Window";
			function generate_order()
			{
				Window1=window.open('<xsl:value-of select="order_link"/>');
			}		
			function generate_request()
			{
				Window1=window.open('<xsl:value-of select="request_link"/>');
			}		
		</script>
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
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<tr class="th">
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_ticket"/>
				</td>
				<td class="th_text" valign="top">
					<xsl:value-of select="value_id"/>
					<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_subject_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<xsl:for-each select="value_origin" >
				<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
				<tr>
					<td valign ="top">
						<xsl:value-of select="descr"/>
					</td>
					<td>
						<table>							
							<xsl:for-each select="data">
								<tr>
									<td class="th_text"  align="left" >
										<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
										<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
										<xsl:text> </xsl:text>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</td>
				</tr>
			</xsl:for-each>
			<xsl:call-template name="location_view"/>
			<xsl:choose>
				<xsl:when test="contact_phone !=''">
					<tr>
						<td class="th_text"  align="left">
							<xsl:value-of select="lang_contact_phone"/>
						</td>
						<td  align="left">
							<xsl:value-of select="contact_phone"/>					
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_opendate"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_opendate"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_assignedfrom"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedfrom"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_assignedto"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedto"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_priority"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_category"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_category_name"/>
				</td>
			</tr>

			<xsl:for-each select="value_destination" >
				<xsl:variable name="link_destination_type"><xsl:value-of select="link"/></xsl:variable>
				<tr>
					<td class="th_text" valign ="top">
						<xsl:value-of select="descr"/>
					</td>
						<td class="th_text"  align="left" >
						<xsl:for-each select="data">
							<a href="{$link_destination_type}&amp;id={id}"  onMouseover="window.status='{//lang_destination_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
							<xsl:text> </xsl:text>
						</xsl:for-each>
					</td>
				</tr>
			</xsl:for-each>
<!--
			<xsl:for-each select="entity_origin_list" >
			<tr>
				<td class="th_text">
					<xsl:value-of select="name"/>
				</td>
				<td class="th_text">
					<xsl:for-each select="link_info" >
						<xsl:variable name="link_entity_origin"><xsl:value-of select="link"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
						<xsl:variable name="lang_entity_statustext"><xsl:value-of select="entry_date"/></xsl:variable>
						<a href="{$link_entity_origin}" onMouseover="window.status='{$lang_entity_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
						<xsl:text> </xsl:text>
					</xsl:for-each>
				</td>
			</tr>				
			</xsl:for-each>
			<xsl:choose>
				<xsl:when test="value_request_id!=''">
					<tr>
						<td  class="th_text" align="left" valign="top">
							<xsl:value-of select="//lang_request"/>
						</td>
						<td class="th_text"  align="left">
							<xsl:for-each select="value_request_id" >
									<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
									<xsl:variable name="lang_request_statustext"><xsl:value-of select="//lang_request_statustext"/>-<xsl:value-of select="entry_date"/></xsl:variable>
									<a href="{$link_request}" onMouseover="window.status='{$request_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
									<xsl:text> </xsl:text>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_project_id!=''">
					<tr>
						<td class="th_text" align="left" valign="top">
							<xsl:value-of select="//lang_project"/>
						</td>
						<td class="th_text"  align="left">
							<xsl:for-each select="value_project_id" >
									<xsl:variable name="link_project"><xsl:value-of select="//link_project"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
									<xsl:variable name="project_statustext"><xsl:value-of select="//lang_project_statustext"/>-<xsl:value-of select="entry_date"/></xsl:variable>
									<a href="{$link_project}" onMouseover="window.status='{$project_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
									<xsl:text> </xsl:text>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		-->


			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_details"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_details"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_additional_notes"/>
				</td>
				<xsl:choose>
					<xsl:when test="additional_notes=''">
						<td class="th_text">
							<xsl:value-of select="lang_no_additional_notes"/>
						</td>
					</xsl:when>
					<xsl:otherwise>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_additional_notes"/>
						<xsl:apply-templates select="additional_notes"/>
					</table>
					</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_assignedto"/>
				</td>
				<td>
					<xsl:call-template name="user_id_select"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_group"/>
				</td>
				<td>
					<xsl:call-template name="group_select"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="mailnotification != ''">
					<tr>
						<td>
							<xsl:value-of select="lang_mailnotification"/>
						</td>
						<td>
							<xsl:choose>
									<xsl:when test="pref_send_mail = 1">
										<input type="checkbox" name="values[send_mail]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_mailnotification_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[send_mail]" value="1" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_mailnotification_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td>
				<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
				<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
					<select name="{$select_priority_name}" class="forms" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="priority_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
				<xsl:variable name="lang_status_statustext"><xsl:value-of select="lang_status_statustext"/></xsl:variable>
				<xsl:variable name="status_name"><xsl:value-of select="status_name"/></xsl:variable>
					<select name="{$status_name}" class="forms" onMouseover="window.status='{$lang_status_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="status_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_note"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[note]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_details_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</textarea>

				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="files!=''">
			<tr>
				<td align="left" valign="top">
					<xsl:value-of select="//lang_files"/>
				</td>
				<td>
				<table>
					<tr class="th">
						<td class="th_text" width="85%" align="left">
							<xsl:value-of select="lang_filename"/>
						</td>
						<td class="th_text" width="15%" align="center">
							<xsl:value-of select="lang_delete_file"/>
						</td>
					</tr>
				<xsl:for-each select="files" >
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
						<xsl:choose>
							<xsl:when test="//link_to_files!=''">
								<xsl:variable name="link_to_file"><xsl:value-of select="//link_to_files"/>/<xsl:value-of select="directory"/>/<xsl:value-of select="file_name"/></xsl:variable>
								<a href="{$link_to_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="link_view_file"><xsl:value-of select="//link_view_file"/>&amp;file_name=<xsl:value-of select="file_name"/></xsl:variable>
								<a href="{$link_view_file}" target="_blank" onMouseover="window.status='{//lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="name"/></a>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:text> </xsl:text>
					</td>
					<td align="center">
						<input type="checkbox" name="values[delete_file][]" value="{name}"  onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="//lang_delete_file_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					</tr>
				</xsl:for-each>
				</table>
				</td>
			</tr>
		</xsl:when>
	</xsl:choose>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_upload_file"/>
						</td>
						<td>
							<input type="file" name="file" size="40" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_file_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
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
								<xsl:value-of select="lang_save_statustext"/>
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
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td valign="top">
					<xsl:variable name="request_link"><xsl:value-of select="request_link"/></xsl:variable>
					<form method="post" action="{$request_link}">
					<xsl:variable name="lang_generate_request"><xsl:value-of select="lang_generate_request"/></xsl:variable>
					<input type="submit" name="location" value="{$lang_generate_request}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_generate_request_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					</form>
				</td>
				<td valign="top">
					<xsl:variable name="order_link"><xsl:value-of select="order_link"/></xsl:variable>
					<form method="post" action="{$order_link}">
					<xsl:variable name="lang_generate_order"><xsl:value-of select="lang_generate_order"/></xsl:variable>
					<input type="submit" name="location" value="{$lang_generate_order}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_generate_order_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					</form>
				</td>
				<xsl:choose>
					<xsl:when test="link_entity!=''">
						<xsl:for-each select="link_entity" >
						<td valign="top">
							<xsl:variable name="link"><xsl:value-of select="link"/></xsl:variable>
							<form method="post" action="{$link}">
							<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>
							<input type="submit" name="location" value="{$name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_start_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							</form>
						</td>
						</xsl:for-each>
					</xsl:when>
				</xsl:choose>	
			</tr>
		</table>

		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="record_history=''">
					<tr>
						<td class="th_text" align="center">
							<xsl:value-of select="lang_no_history"/>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>

<!-- view2 -->
	<xsl:template match="view2">
		<script language="JavaScript">
			self.name="first_Window";
			function generate_order()
			{
				Window1=window.open('<xsl:value-of select="order_link"/>');
			}		
			function generate_request()
			{
				Window1=window.open('<xsl:value-of select="request_link"/>');
			}		
		</script>
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
			<form name="form" method="post" action="{$form_action}">
			<tr class="th">
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_ticket"/>
				</td>
				<td class="th_text" valign="top">
					<xsl:value-of select="value_id"/>
					<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_subject_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<xsl:call-template name="location_view"/>
			<xsl:choose>
				<xsl:when test="contact_phone !=''">
					<tr>
						<td class="th_text"  align="left">
							<xsl:value-of select="lang_contact_phone"/>
						</td>
						<td  align="left">
							<xsl:value-of select="contact_phone"/>					
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_opendate"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_opendate"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_assignedfrom"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedfrom"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_assignedto"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedto"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_priority"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_category"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_category_name"/>
				</td>
			</tr>

			<xsl:for-each select="value_origin" >
				<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
				<tr>
					<td class="th_text" valign ="top">
						<xsl:value-of select="descr"/>
					</td>
						<td class="th_text"  align="left" >
						<xsl:for-each select="data">
							<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
							<xsl:text> </xsl:text>
						</xsl:for-each>
					</td>
				</tr>
			</xsl:for-each>



			<xsl:for-each select="entity_origin_list" >
			<tr>
				<td class="th_text">
					<xsl:value-of select="name"/>
				</td>
				<td class="th_text">
					<xsl:for-each select="link_info" >
						<xsl:variable name="link_entity_origin"><xsl:value-of select="link"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
						<xsl:variable name="lang_entity_statustext"><xsl:value-of select="entry_date"/></xsl:variable>
						<a href="{$link_entity_origin}" onMouseover="window.status='{$lang_entity_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
						<xsl:text> </xsl:text>
					</xsl:for-each>
				</td>
			</tr>				
			</xsl:for-each>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_details"/>
				</td>
				<td valign="top">
					<xsl:value-of select="value_details"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_additional_notes"/>
				</td>
				<xsl:choose>
					<xsl:when test="additional_notes=''">
						<td class="th_text">
							<xsl:value-of select="lang_no_additional_notes"/>
						</td>
					</xsl:when>
					<xsl:otherwise>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_additional_notes"/>
						<xsl:apply-templates select="additional_notes"/>
					</table>
					</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_priority"/>
				</td>
				<td>
				<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
				<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
					<select name="{$select_priority_name}" class="forms" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="priority_list"/>
					</select>			
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
				<xsl:variable name="lang_status_statustext"><xsl:value-of select="lang_status_statustext"/></xsl:variable>
				<xsl:variable name="status_name"><xsl:value-of select="status_name"/></xsl:variable>
					<select name="{$status_name}" class="forms" onMouseover="window.status='{$lang_status_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="status_list"/>
					</select>			
				</td>
			</tr>
<!--			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
-->
			<tr>
				<td>
					<xsl:value-of select="lang_finnish_date"/>
				</td>
				<td>
					<input type="text" name="values[finnish_date]" value="{value_finnish_date}" onFocus="{dateformat_validate}" onKeyUp="{onKeyUp}" onBlur="{onBlur}" size="12" maxlength="10"  onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="statustext"/>
							<xsl:text>';return true;</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="disabled">
							<xsl:text>="disabled"</xsl:text>
						</xsl:attribute>
					</input>
					[<xsl:value-of select="lang_dateformat"/>]
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_new_note"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[note]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_details_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</textarea>

				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
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
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="record_history=''">
					<tr>
						<td class="th_text" align="center">
							<xsl:value-of select="lang_no_history"/>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>

	<xsl:template match="table_header_additional_notes">
			<tr class="th">
				<td class="th_text" width="4%" align="right">
					<xsl:value-of select="lang_count"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_date"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_user"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_note"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="additional_notes">
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
					<xsl:value-of select="value_count"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_user"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_note"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_header_history">
			<tr class="th">
				<td class="th_text" width="20%" align="left">
					<xsl:value-of select="lang_date"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_user"/>
				</td>
				<td class="th_text" width="30%" align="left">
					<xsl:value-of select="lang_action"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_new_value"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
					<xsl:value-of select="value_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_user"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_action"/>
				</td>
				<td align="left">
					<xsl:value-of select="value_new_value"/>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="priority_list">
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

	<xsl:template match="status_list">
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

