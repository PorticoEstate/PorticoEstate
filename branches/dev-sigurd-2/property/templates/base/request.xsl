<!-- $Id: request.xsl,v 1.11 2007/01/04 14:36:16 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="priority_key">
				<xsl:apply-templates select="priority_form"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="priority_form">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
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
			<xsl:apply-templates select="priority_key"/>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[update]" value="{$lang_save}" >
					</input>
				</td>
			</tr>
		</table>
		</div>
		</form> 
	</xsl:template>

	<xsl:template match="priority_key">
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
					<xsl:value-of select="descr"/>
				</td>
				<td class="small_text" align="left">
					<input type="text" size="3" name="values[priority_key][{id}]" value="{priority_key}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_priority_key_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="list">
		<xsl:apply-templates select="menu"/> 
		<table width="100%"  cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="left">
					<xsl:call-template name="status_filter"/>
				</td>
				<td align="center">
					<xsl:call-template name="user_id_filter"/>
				</td>
				<xsl:choose>
					<xsl:when test="link_priority_key!=''">
						<td valign="top" align="right">
							<xsl:variable name="link_priority_key"><xsl:value-of select="link_priority_key"/></xsl:variable>
							<xsl:variable name="lang_priority_help"><xsl:value-of select="lang_priority_help"/></xsl:variable>
							<xsl:variable name="lang_priority_key"><xsl:value-of select="lang_priority_key"/></xsl:variable>
							<a href="javascript:var w=window.open('{$link_priority_key}','','width=300,height=300')"
								onMouseOver="overlib('{$lang_priority_help}', CAPTION, '{$lang_priority_key}')"
								onMouseOut="nd()">
								<xsl:value-of select="lang_priority_key"/></a>					
						</td>
					</xsl:when>
				</xsl:choose>

				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
				<td class="small_text" valign="top" align="left">
					<xsl:variable name="link_download"><xsl:value-of select="link_download"/></xsl:variable>
					<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"/></xsl:variable>
					<xsl:variable name="lang_download"><xsl:value-of select="lang_download"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_download}','','')"
						onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')"
						onMouseOut="nd()">
						<xsl:value-of select="lang_download"/></a>
				</td>
			</tr>
			<tr>
				<td colspan="14" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:call-template name="table_header"/>
					<xsl:choose>
						<xsl:when test="project_id!=''">
						<xsl:variable name="add_to_project_action"><xsl:value-of select="add_to_project_action"/></xsl:variable>
						<form method="post" action="{$add_to_project_action}">
						<xsl:choose>
							<xsl:when test="values">
								<xsl:call-template name="values"/>
							</xsl:when>
						</xsl:choose>
							<tr>
								<td height="50">
									<input type="submit" name="add" value="{lang_update_project}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_add_to_project_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</form>
						</xsl:when>
						<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="values">
						<xsl:call-template name="values"/>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="table_add !=''">
						<xsl:apply-templates select="table_add"/>
					</xsl:when>
				</xsl:choose>	
						</xsl:otherwise>
					</xsl:choose>
		</table>
	</xsl:template>

	<xsl:template name="values">
		<xsl:for-each select="values" >
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
				<xsl:for-each select="row" >
					<xsl:choose>
						<xsl:when test="link">
							<td align="center">
								<a href="{link}" target="{target}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>
							</td>
					<xsl:choose>
						<xsl:when test="//lookup!=''">
							<xsl:if test="position() = last()">
								<td valign="center">
									<input type="checkbox" name="add_request[request_id][]" value="{request_id}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_select_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</xsl:if>
						</xsl:when>
					</xsl:choose>


						</xsl:when>
						<xsl:otherwise>
							<td align="left">
								<xsl:value-of select="value"/>					
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
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

<!-- add / edit -->

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
			<xsl:choose>
				<xsl:when test="value_request_id!=''">
						<td valign="top">
							<xsl:variable name="generate_project_action"><xsl:value-of select="generate_project_action"/></xsl:variable>
							<xsl:variable name="lang_generate_project"><xsl:value-of select="lang_generate_project"/></xsl:variable>
							<form method="post" action="{$generate_project_action}">
							<input type="hidden" name="origin" value="{value_acl_location}"></input>
							<input type="hidden" name="origin_id" value="{value_request_id}"></input>
							<input type="hidden" name="location_code" value="{location_code}"></input>
							<input type="hidden" name="bypass" value="true"></input>
							<input type="hidden" name="descr" value="{value_descr}"></input>
							<input type="hidden" name="tenant_id" value="{tenant_id}"></input>
							<input type="hidden" name="p_num" value="{p_num}"></input>
							<input type="hidden" name="p_entity_id" value="{p_entity_id}"></input>
							<input type="hidden" name="p_cat_id" value="{p_cat_id}"></input>
							<input type="submit" class="forms" name="generate_project" value="{$lang_generate_project}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_generate_project_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							</form>
						</td>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">
			<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
			<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
			<xsl:choose>
				<xsl:when test="value_request_id!=''">
					<tr>
						<td>
							<xsl:value-of select="lang_copy_request"/>
						</td>
						<td>
							<input type="checkbox" name="values[copy_request]" value="True"  onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_copy_request_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_request_id"/>
						</td>
						<td>
							<xsl:value-of select="value_request_id"/>
						</td>
					</tr>
					<xsl:for-each select="value_origin" >
						<tr>
							<td class="th_text" valign ="top">
								<xsl:value-of select="descr"/>
							</td>
							<td>
							<table>
							
							<xsl:for-each select="data">
							<tr>
		
							<td class="th_text"  align="left" >
								<a href="{link}"  title="{//lang_origin_statustext}" style ="cursor:help"><xsl:value-of select="id"/></a>
								<xsl:text> </xsl:text>
		
								<xsl:choose>
									<xsl:when test="location ='.project.request'">
									<input type="checkbox" name="values[delete_request][]" value="{id}"  onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="//lang_delete_request_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
									</xsl:when>
								</xsl:choose>
							</td>
							</tr>
							</xsl:for-each>
							</table>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="value_origin" >
						<tr>
							<td class="th_text" valign ="top">
								<xsl:value-of select="descr"/>
							</td>
							<td>
								<table>							
									<xsl:for-each select="data">
										<tr>
											<td class="th_text"  align="left" >
												<a href="{link}"  title="{//lang_origin_statustext}" style ="cursor:help"><xsl:value-of select="id"/></a>
												<xsl:text> </xsl:text>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<input type="text" name="values[title]" value="{value_title}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_title_statustext"/>
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
					<textarea cols="60" rows="6" name="values[descr]" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>		
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="location_type='form'">
					<xsl:call-template name="location_form"/>
				</xsl:when>
				<xsl:otherwise>
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

				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_power_meter"/>
				</td>
				<td>
					<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_power_meter_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget"/>
				</td>
				<td>
					<input type="text" name="values[budget]" value="{value_budget}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_budget_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_coordinator"/>
				</td>
				<td>
					<xsl:call-template name="user_id_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_status"/>
				</td>
				<td>
					<xsl:call-template name="status_select"/>
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
								<xsl:value-of select="lang_start_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
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
								<xsl:value-of select="lang_end_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_branch"/>
				</td>
				<td>
					<xsl:variable name="lang_branch_statustext"><xsl:value-of select="lang_branch_statustext"/></xsl:variable>
						<select name="values[branch_id]" class="forms" onMouseover="window.status='{$lang_branch_statustext}'; return true;" onMouseout="window.status='';return true;">
							<option value=""><xsl:value-of select="lang_no_branch"/></option>
							<xsl:apply-templates select="branch_list"/>
						</select>
				</td>
			</tr>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_importance"/>
				</td>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_importance"/>
						<xsl:apply-templates select="condition_list"/>
						<tr>
							<td align="left">
								<xsl:value-of select="lang_authorities_demands"/>
							</td>
							<td align="center">
								<xsl:choose>
									<xsl:when test="authorities_demands='1'">
										<input type="checkbox" name="values[authorities_demands]" value="1" checked="checked" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_authorities_demands_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:when>
									<xsl:otherwise>
										<input type="checkbox" name="values[authorities_demands]" value="1"  onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_authorities_demands_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>

					</table>
					</td>
			</tr>

			<tr>
				<td align="left">
					<xsl:value-of select="lang_score"/>
				</td>
				<td>
					<xsl:value-of select="value_score"/>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="files!=''">
					<xsl:call-template name="file_list"/>
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<xsl:call-template name="file_upload"/>
				</xsl:when>
			</xsl:choose>

			<tr>
			<xsl:choose>
				<xsl:when test="notify='yes'">
				<td valign="top">
					<xsl:value-of select="lang_notify"/>
				</td>
				<td>
					<input type="checkbox" name="values[notify]" value="True"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_notify_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

					<input type="text" name="values[mail_address]" value="{value_notify_mail_address}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_notify_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				</xsl:when>
			</xsl:choose>
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
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_history"/>
						</td>
					</tr>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>

	<xsl:template match="condition_list">
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
					<xsl:value-of select="condition_type_name"/>
				</td>
				<td class="small_text" align="center">
					<xsl:variable name="lang_degree_statustext"><xsl:value-of select="//lang_degree_statustext"/></xsl:variable>
						<select name="values[condition][{condition_type}][degree]" class="forms" onMouseover="window.status='{$lang_degree_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="degree"/>
						</select>
				</td>
				<td class="small_text" align="center">
					<xsl:variable name="lang_probability_statustext"><xsl:value-of select="//lang_probability_statustext"/></xsl:variable>
						<select name="values[condition][{condition_type}][probability]" class="forms" onMouseover="window.status='{$lang_probability_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="probability"/>
						</select>
				</td>
				<td class="small_text" align="center">
					<xsl:variable name="lang_consequence_statustext"><xsl:value-of select="//lang_consequence_statustext"/></xsl:variable>
						<select name="values[condition][{condition_type}][consequence]" class="forms" onMouseover="window.status='{$lang_consequence_statustext}'; return true;" onMouseout="window.status='';return true;">
							<xsl:apply-templates select="consequence"/>
						</select>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="condition_list_view">
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
					<xsl:value-of select="condition_type_name"/>
				</td>
				<td class="small_text" align="center">
						<select  disabled='' class="forms" >
							<xsl:apply-templates select="degree"/>
						</select>
				</td>
				<td class="small_text" align="center">
						<select  disabled='' class="forms" >
							<xsl:apply-templates select="probability"/>
						</select>
				</td>
				<td class="small_text" align="center">
						<select  disabled='' class="forms" >
							<xsl:apply-templates select="consequence"/>
						</select>
				</td>
			</tr>
	</xsl:template>


	<xsl:template match="degree">
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

	<xsl:template match="probability">
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

	<xsl:template match="consequence">
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



	<xsl:template match="branch_list">
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

	<xsl:template match="degree_list_safety">
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

	<xsl:template match="degree_list_aesthetics">
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

	<xsl:template match="degree_list_indoor_climate">
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

	<xsl:template match="degree_list_consequential_damage">
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

	<xsl:template match="degree_list_user_gratification">
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

	<xsl:template match="degree_list_residential_environment">
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

	<xsl:template match="probability_list_safety">
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

	<xsl:template match="probability_list_aesthetics">
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


	<xsl:template match="probability_list_indoor_climate">
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

	<xsl:template match="probability_list_consequential_damage">
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

	<xsl:template match="probability_list_user_gratification">
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

	<xsl:template match="probability_list_residential_environment">
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

	<xsl:template match="consequence_list_safety">
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

	<xsl:template match="consequence_list_aesthetics">
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

	<xsl:template match="consequence_list_indoor_climate">
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

	<xsl:template match="consequence_list_consequential_damage">
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

	<xsl:template match="consequence_list_user_gratification">
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

	<xsl:template match="consequence_list_residential_environment">
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

	<xsl:template match="table_header_importance">
			<tr class="th">
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_subject"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_condition_degree"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_prob_worsening"/>
				</td>
				<td class="th_text" width="10%" align="left">
					<xsl:value-of select="lang_consequence"/>
				</td>
			</tr>
	</xsl:template>


<!-- view -->

	<xsl:template match="view">

		<div align="left">
		
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:for-each select="value_origin" >
			<tr>
				<td class="th_text" valign ="top">
					<xsl:value-of select="descr"/>
				</td>
				<td>
					<table>							
						<xsl:for-each select="data">
							<tr>
								<td class="th_text"  align="left" >
									<a href="{link}"  title="{//lang_origin_statustext}" style ="cursor:help"><xsl:value-of select="id"/></a>
									<xsl:text> </xsl:text>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</td>
			</tr>
			</xsl:for-each>
			<tr>
				<td>
					<xsl:value-of select="lang_request_id"/>
				</td>
				<td>
					<xsl:value-of select="value_request_id"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="value_project_id!=''">
					<tr>
						<td align="left" valign="top">
							<xsl:value-of select="//lang_project"/>
						</td>
						<td class="th_text"  align="left">
							<xsl:for-each select="value_project_id" >
									<xsl:variable name="link_project"><xsl:value-of select="//link_project"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
									<a href="{$link_project}" onMouseover="window.status='{//lang_project_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
									<xsl:text> </xsl:text>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_origin_id!=''">
					<tr>
						<td>
							<!-- FIXME-->
							<a href="{link_origin}" onMouseover="window.status='{lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_origin"/></a>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_title"/>
				</td>
				<td>
					<xsl:value-of select="value_title"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_descr"/>
				</td>
				<td>
					<xsl:value-of select="value_descr"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<xsl:for-each select="cat_list" >
					<xsl:choose>
						<xsl:when test="selected='selected'">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
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
				<td valign="top">
					<xsl:value-of select="lang_power_meter"/>
				</td>
				<td>
					<xsl:value-of select="value_power_meter"/>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget"/>
				</td>
				<td>
					<xsl:value-of select="value_budget"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_coordinator"/>
				</td>
				<xsl:for-each select="user_list" >
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_status"/>
				</td>
				<xsl:for-each select="status_list" >
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_start_date"/>
				</td>
				<td>
					<xsl:value-of select="value_start_date"/>			
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_end_date"/>
				</td>
				<td>
					<xsl:value-of select="value_end_date"/>			
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_branch"/>
				</td>
				<xsl:for-each select="branch_list" >
					<xsl:choose>
						<xsl:when test="selected">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="lang_importance"/>
				</td>
					<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:apply-templates select="table_header_importance"/>
						<xsl:apply-templates select="condition_list_view"/>
						<tr>
							<td align="left" colspan="3">
								<xsl:value-of select="lang_authorities_demands"/>
							</td>
							<td align="center">
								<xsl:choose>
									<xsl:when test="authorities_demands='1'">
										<b>x</b>
									</xsl:when>
								</xsl:choose>
							</td>
						</tr>

					</table>
					</td>
			</tr>

			<tr>
				<td align="left">
					<xsl:value-of select="lang_score"/>
				</td>
				<td>
					<xsl:value-of select="value_score"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="files!=''">
					<xsl:call-template name="file_list_view"/>
				</xsl:when>
			</xsl:choose>

			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
					<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

					</form>
					<xsl:variable name="edit_action"><xsl:value-of select="edit_action"/></xsl:variable>
					<xsl:variable name="lang_edit"><xsl:value-of select="lang_edit"/></xsl:variable>
					<form method="post" action="{$edit_action}">
					<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_edit_statustext"/>
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
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_history"/>
						</td>
					</tr>
					<xsl:apply-templates select="table_header_history"/>
					<xsl:apply-templates select="record_history"/>
				</xsl:otherwise>
			</xsl:choose>
		</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>
	
	
