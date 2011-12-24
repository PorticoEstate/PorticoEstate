<!-- $Id$ -->
<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
-->
<func:function name="phpgw:conditional">
	<xsl:param name="test"></xsl:param>
	<xsl:param name="true"></xsl:param>
	<xsl:param name="false"></xsl:param>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"></xsl:value-of>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"></xsl:value-of>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"></xsl:apply-templates>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"></xsl:apply-templates>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list_project"></xsl:apply-templates>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="list_project">

	<xsl:apply-templates select="menu"></xsl:apply-templates>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td>
				<xsl:call-template name="categories"></xsl:call-template>
			</td>
			<td align="left">
				<xsl:call-template name="status_filter"></xsl:call-template>
			</td>
			<td align="left">
				<xsl:call-template name="wo_hour_cat_filter"></xsl:call-template>
			</td>
			<td align="center">
				<xsl:call-template name="user_id_filter"></xsl:call-template>
			</td>
			<td align="right">
				<xsl:call-template name="search_field"></xsl:call-template>
			</td>
			<td class="small_text" valign="top" align="left">
				<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
				<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
					<xsl:value-of select="lang_download"></xsl:value-of></a>
			</td>
		</tr>
		<tr>
			<td colspan="6" width="100%">
				<xsl:call-template name="nextmatchs"></xsl:call-template>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:call-template name="table_header"></xsl:call-template>
		<xsl:choose>
			<xsl:when test="values">
				<xsl:call-template name="values"></xsl:call-template>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="table_add !=''">
				<xsl:apply-templates select="table_add"></xsl:apply-templates>
			</xsl:when>
		</xsl:choose>
	</table>
</xsl:template>

<xsl:template match="table_add">
	<tr>
		<td height="50">
			<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
			<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"></xsl:value-of>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- add / edit -->

	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"></xsl:value-of>
			function add_workorder()
			{
				document.add_workorder_form.submit();
			}
		</script>

		<table cellpadding="2" cellspacing="2" align="center">
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
				<xsl:when test="value_project_id &gt; 0">
					<td valign="top">
						<xsl:variable name="lang_add_workorder"><xsl:value-of select="lang_add_workorder"></xsl:value-of></xsl:variable>
						<input type="button" name="add_workorder" value="{$lang_add_workorder}" onClick="add_workorder()">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_add_workorder_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</xsl:when>
			</xsl:choose>
		</table>

		<form method="post" name="form" action="{form_action}">
			<div class="yui-navset" id="project_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="80%" align="center">
							<xsl:choose>
								<xsl:when test="value_project_id &gt; 0">
									<tr>
										<td title="{lang_copy_project_statustext}">
											<xsl:value-of select="lang_copy_project"></xsl:value-of>
										</td>
										<td>
											<input type="checkbox" name="values[copy_project]" value="True">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_copy_project_statustext"></xsl:value-of>
												</xsl:attribute>
											</input>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="lang_project_id"></xsl:value-of>
										</td>
										<td>
											<xsl:value-of select="value_project_id"></xsl:value-of>
										</td>
									</tr>


									<tr>
										<td valign="top">
											<a href="{link_select_request}" title="{lang_select_request_statustext}"><xsl:value-of select="lang_select_request"></xsl:value-of></a>
										</td>
									</tr>

									<xsl:for-each select="value_origin">
										<xsl:variable name="origin_location"><xsl:value-of select="location"></xsl:value-of></xsl:variable>
										<tr>
											<td valign="top">
												<xsl:value-of select="descr"></xsl:value-of>
											</td>
											<td>
												<table>

													<xsl:for-each select="data">
														<tr>

															<td class="th_text" align="left">
																<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
																<xsl:text> </xsl:text>
																<xsl:choose>
																	<xsl:when test="$origin_location ='.project.request'">
																		<input type="checkbox" name="values[delete_request][]" value="{id}" onMouseout="window.status='';return true;">
																			<xsl:attribute name="title">
																				<xsl:value-of select="//lang_delete_request_statustext"></xsl:value-of>
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
									<xsl:for-each select="value_origin">
										<tr>
											<td valign="top">
												<xsl:value-of select="descr"></xsl:value-of>
											</td>
											<td>
												<table>
													<xsl:for-each select="data">
														<tr>
															<td class="th_text" align="left">
																<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
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
									<xsl:value-of select="lang_name"></xsl:value-of>
								</td>
								<td>
									<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
									<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>
									<input type="text" name="values[name]" value="{value_name}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_name_statustext"></xsl:value-of>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"></xsl:value-of>
								</td>
								<td>
									<textarea cols="60" rows="6" name="values[descr]">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
										</xsl:attribute>
										<xsl:value-of select="value_descr"></xsl:value-of>
									</textarea>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="lang_coordinator"></xsl:value-of>
								</td>
								<td>
									<xsl:call-template name="user_id_select"></xsl:call-template>
								</td>
							</tr>
							<xsl:call-template name="contact_form"></xsl:call-template>
							<tr>
								<td>
									<xsl:value-of select="lang_category"></xsl:value-of>
								</td>
								<td>
									<xsl:call-template name="categories"></xsl:call-template>
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
							<xsl:choose>
								<xsl:when test="value_project_id &gt; 0">
									<tr>
										<td>
											<xsl:value-of select="lang_confirm_status"></xsl:value-of>
										</td>
										<td>
											<input type="checkbox" name="values[confirm_status]" value="True" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_confirm_statustext"></xsl:value-of>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
		<xsl:choose>
			<xsl:when test="need_approval='1'">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_ask_approval"></xsl:value-of>
					</td>
					<td>
						<table>
							<xsl:for-each select="value_approval_mail_address">
								<tr>
									<td>
										<input type="checkbox" name="values[approval][{id}]" value="True">
											<xsl:attribute name="title">
												<xsl:value-of select="//lang_ask_approval_statustext"></xsl:value-of>
											</xsl:attribute>
										</input>
									</td>
									<td>
										<input type="text" name="values[mail_address][{id}]" value="{address}">
											<xsl:attribute name="title">
												<xsl:value-of select="//lang_ask_approval_statustext"></xsl:value-of>
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
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_remark"></xsl:value-of>
			</td>
			<td>
				<textarea cols="60" rows="6" name="values[remark]">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_remark_statustext"></xsl:value-of>
					</xsl:attribute>
					<xsl:value-of select="value_remark"></xsl:value-of>
				</textarea>
			</td>
		</tr>
		<xsl:apply-templates select="custom_attributes/attributes"></xsl:apply-templates>
	</table>
</div>



<div id="location">

	<table cellpadding="2" cellspacing="2" width="80%" align="center">
		<xsl:choose>
			<xsl:when test="location_type='form'">
				<xsl:call-template name="location_form"></xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="location_view"></xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="suppressmeter =''">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_power_meter"></xsl:value-of>
					</td>
					<td>
						<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_power_meter_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</table>
</div>

<div id="budget">
	<table cellpadding="2" cellspacing="2" width="80%" align="center">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_start_date"></xsl:value-of>
			</td>
			<td>
				<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_start_date_statustext"></xsl:value-of>
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
				<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_end_date_statustext"></xsl:value-of>
					</xsl:attribute>
				</input>
				<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
			</td>
		</tr>

		<xsl:call-template name="project_group_form"></xsl:call-template>

		<xsl:choose>
			<xsl:when test="ecodimb_data!=''">
				<xsl:call-template name="ecodimb_form"></xsl:call-template>
			</xsl:when>
		</xsl:choose>

		<xsl:choose>
			<xsl:when test="b_account_data!=''">
				<xsl:call-template name="b_account_form"></xsl:call-template>
			</xsl:when>
		</xsl:choose>

		<tr>
			<td valign="top">
				<xsl:value-of select="lang_budget"></xsl:value-of>
			</td>
			<td>
				<input type="text" name="values[budget]" value="{value_budget}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_budget_statustext"></xsl:value-of>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
			</td>
		</tr>
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_reserve"></xsl:value-of>
			</td>
			<td>
				<input type="text" name="values[reserve]" value="{value_reserve}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_reserve_statustext"></xsl:value-of>
					</xsl:attribute>
				</input>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
			</td>
		</tr>
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_sum"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_sum"></xsl:value-of>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
			</td>
		</tr>
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_remainder"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_remainder"></xsl:value-of>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
			</td>
		</tr>
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_reserve_remainder"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_reserve_remainder"></xsl:value-of>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
				<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"></xsl:value-of>
				<xsl:text> % )</xsl:text>
			</td>
		</tr>

		<tr>
			<td valign="top">
				<xsl:value-of select="lang_planned_cost"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="value_planned_cost"></xsl:value-of>
				<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
			</td>
		</tr>

		<tr>
			<td class="th_text" valign="top">
				<xsl:value-of select="lang_workorder_id"></xsl:value-of>
			</td>
			<xsl:choose>
				<xsl:when test="sum_workorder_budget=''">
					<td class="th_text">
						<xsl:value-of select="lang_no_workorders"></xsl:value-of>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td>
						<!-- DataTable -->
						<div id="paging_0"> </div>
						<div id="datatable-container_0"></div>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</tr>
			<tr>
				<td valign="top" class="th_text">
					<xsl:value-of select="php:function('lang', 'invoice')"></xsl:value-of>
				</td>
				<td>
					<div id="paging_2"> </div>
					<div id="datatable-container_2"></div>
				</td>
			</tr>
<!--
	<tr>
		<td valign="top">
			<xsl:value-of select="php:function('lang', 'actual cost')" />
		</td>
		<td>
			<xsl:value-of select="sum_workorder_actual_cost"/>
			<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
		</td>
	</tr>
-->
</table>
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
							edit_action		:  	<xsl:value-of select="edit_action"></xsl:value-of>,
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
		</div>
		<div id="coordination">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:variable name="lang_contact_statustext"><xsl:value-of select="php:function('lang', 'click this link to select')"></xsl:value-of></xsl:variable>
				<tr>
					<td valign="top">
						<a href="javascript:notify_contact_lookup()" title="{$lang_contact_statustext}">
							<xsl:value-of select="php:function('lang', 'contact')"></xsl:value-of>
						</a>
					</td>
					<td><table><tr><td>
						<input type="hidden" id="notify_contact" name="notify_contact" value="" title="{$lang_contact_statustext}">
						</input>
						<input size="30" type="text" name="notify_contact_name" value="" onClick="notify_contact_lookup();" readonly="readonly" title="{$lang_contact_statustext}"></input>
					</td></tr></table></td>
				</tr>
				<tr>
					<td valign="top" class="th_text">
						<xsl:value-of select="php:function('lang', 'notify')"></xsl:value-of>
					</td>
					<td>
						<div id="paging_3"> </div>
						<div id="datatable-container_3"></div>
						<div id="datatable-buttons_3"></div>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="suppresscoordination =''">
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_branch"></xsl:value-of>
							</td>
							<td>
								<xsl:variable name="lang_branch_statustext"><xsl:value-of select="lang_branch_statustext"></xsl:value-of></xsl:variable>
								<select name="values[branch][]" class="forms" multiple="multiple" title="{$lang_branch_statustext}">
									<xsl:apply-templates select="branch_list"></xsl:apply-templates>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_other_branch"></xsl:value-of>
							</td>
							<td>
								<input type="text" name="values[other_branch]" value="{value_other_branch}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_other_branch_statustext"></xsl:value-of>
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_key_fetch"></xsl:value-of>
							</td>
							<td>
								<xsl:variable name="lang_key_fetch_statustext"><xsl:value-of select="lang_key_fetch_statustext"></xsl:value-of></xsl:variable>
								<select name="values[key_fetch]" class="forms" title="{$lang_key_fetch_statustext}">
									<option value=""><xsl:value-of select="lang_no_key_fetch"></xsl:value-of></option>
									<xsl:apply-templates select="key_fetch_list"></xsl:apply-templates>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_key_deliver"></xsl:value-of>
							</td>
							<td>
								<xsl:variable name="lang_key_deliver_statustext"><xsl:value-of select="lang_key_deliver_statustext"></xsl:value-of></xsl:variable>
								<select name="values[key_deliver]" class="forms" onMouseover="window.status='{$lang_key_deliver_statustext}'; return true;" onMouseout="window.status='';return true;">
									<option value=""><xsl:value-of select="lang_no_key_deliver"></xsl:value-of></option>
										<xsl:apply-templates select="key_deliver_list"></xsl:apply-templates>
									</select>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_key_responsible"></xsl:value-of>
							</td>
							<td>
								<xsl:variable name="lang_key_responsible_statustext"><xsl:value-of select="lang_key_responsible_statustext"></xsl:value-of></xsl:variable>
								<select name="values[key_responsible]" class="forms" onMouseover="window.status='{$lang_key_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
									<option value=""><xsl:value-of select="lang_no_key_responsible"></xsl:value-of></option>
									<xsl:apply-templates select="key_responsible_list"></xsl:apply-templates>
								</select>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
		</div>
<div id="history">
	<!-- <hr noshade="noshade" width="100%" align="center" size="1"/>
		table cellpadding="2" cellspacing="2" width="80%" align="center">
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
		</table> -->
		<div id="paging_1"> </div>
		<div id="datatable-container_1"></div>

	</div>
	<xsl:call-template name="attributes_values"></xsl:call-template>
</div>
</div>
<table>
	<tr height="50">
		<td>
			<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
			<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_save_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
	</tr>
</table>
		</form>
		<table>
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>

		<!-- AQUI VA EL SCRIPT -->

		<xsl:variable name="add_workorder_action"><xsl:value-of select="add_workorder_action"></xsl:value-of>&amp;project_id=<xsl:value-of select="value_project_id"></xsl:value-of></xsl:variable>
		<form method="post" name="add_workorder_form" action="{$add_workorder_action}">
		</form>



	</xsl:template>

	<xsl:template match="workorder_budget">
		<xsl:variable name="workorder_link"><xsl:value-of select="//workorder_link"></xsl:value-of>&amp;id=<xsl:value-of select="workorder_id"></xsl:value-of></xsl:variable>
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

			<td align="right">
				<a href="{$workorder_link}"><xsl:value-of select="workorder_id"></xsl:value-of></a>
			</td>
			<td align="right">
				<xsl:value-of select="budget"></xsl:value-of>
			</td>
			<td align="right">
				<xsl:value-of select="calculation"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="vendor_name"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="status"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="branch_list">
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

	<xsl:template match="key_responsible_list">
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

	<xsl:template match="key_fetch_list">
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


	<xsl:template match="key_deliver_list">
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


	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_date"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_action"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
				<xsl:value-of select="value_date"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_action"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="table_header_workorder_budget">
		<tr class="th">
			<td class="th_text" width="4%" align="right">
				<xsl:value-of select="lang_workorder_id"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_budget"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_calculation"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_vendor"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_status"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>


<!-- view -->

	<xsl:template match="view">
		<div class="yui-navset" id="project_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs"></xsl:value-of>
			<div class="yui-content">
				<div id="general">
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<tr>
							<td>
								<xsl:value-of select="lang_project_id"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_project_id"></xsl:value-of>
							</td>
						</tr>

						<xsl:for-each select="value_origin">
							<tr>
								<td valign="top">
									<xsl:value-of select="descr"></xsl:value-of>
								</td>
								<td class="th_text" align="left">
									<xsl:for-each select="data">
										<a href="{link}" title="{statustext}"><xsl:value-of select="id"></xsl:value-of></a>
										<xsl:text> </xsl:text>
									</xsl:for-each>
								</td>
							</tr>
						</xsl:for-each>

						<xsl:choose>
							<xsl:when test="project_group_data!=''">
								<xsl:call-template name="project_group_view"></xsl:call-template>
							</xsl:when>
						</xsl:choose>

						<tr>
							<td valign="top">
								<xsl:value-of select="lang_name"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_name"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_descr"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_descr"></xsl:value-of>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_coordinator"></xsl:value-of>
							</td>
							<xsl:for-each select="user_list">
								<xsl:choose>
									<xsl:when test="selected">
										<td>
											<xsl:value-of select="name"></xsl:value-of>
										</td>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</tr>

						<tr>
							<td>
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
						<xsl:apply-templates select="custom_attributes/attributes"></xsl:apply-templates>
					</table>
				</div>

				<div id="location">
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<xsl:call-template name="location_view"></xsl:call-template>

						<xsl:choose>
							<xsl:when test="contact_phone !=''">
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_contact_phone"></xsl:value-of>
									</td>
									<td align="left">
										<xsl:value-of select="contact_phone"></xsl:value-of>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>

						<xsl:choose>
							<xsl:when test="suppressmeter =''">
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_power_meter"></xsl:value-of>
									</td>
									<td>
										<xsl:value-of select="value_power_meter"></xsl:value-of>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
					</table>
				</div>

				<div id="budget">
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_budget"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_budget"></xsl:value-of>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_reserve"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_reserve"></xsl:value-of>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_sum"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_sum"></xsl:value-of>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_reserve_remainder"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="value_reserve_remainder"></xsl:value-of>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
								<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"></xsl:value-of>
								<xsl:text> % )</xsl:text>
							</td>
						</tr>

						<tr>
							<td class="th_text" valign="top">
								<xsl:value-of select="lang_workorder_id"></xsl:value-of>
							</td>
							<xsl:choose>
								<xsl:when test="sum_workorder_budget=''">
									<td class="th_text">
										<xsl:value-of select="lang_no_workorders"></xsl:value-of>
									</td>
								</xsl:when>
								<xsl:otherwise>
									<td>
										<table width="100%" cellpadding="2" cellspacing="2" align="center">
											<xsl:apply-templates select="table_header_workorder_budget"></xsl:apply-templates>
											<xsl:apply-templates select="workorder_budget"></xsl:apply-templates>
											<tr class="th">
												<td class="th_text" width="5%" align="right">
													<xsl:value-of select="lang_sum"></xsl:value-of>
												</td>
												<td class="th_text" width="5%" align="right">
													<xsl:value-of select="sum_workorder_budget"></xsl:value-of>
												</td>
												<td class="th_text" width="5%" align="right">
													<xsl:value-of select="sum_workorder_calculation"></xsl:value-of>
												</td>
												<td>
												</td>
												<td>
												</td>
											</tr>
										</table>
									</td>
								</xsl:otherwise>
							</xsl:choose>
						</tr>

						<tr>
							<td valign="top">
								<xsl:value-of select="lang_actual_cost"></xsl:value-of>
							</td>
							<td>
								<xsl:value-of select="sum_workorder_actual_cost"></xsl:value-of>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"></xsl:value-of> ]
							</td>
						</tr>
					</table>
				</div>

				<xsl:choose>
					<xsl:when test="suppresscoordination =''">

						<div id="coordination">
							<table cellpadding="2" cellspacing="2" width="80%" align="center">
								<tr>
									<td>
										<xsl:value-of select="lang_status"></xsl:value-of>
									</td>
									<xsl:for-each select="status_list">
										<xsl:choose>
											<xsl:when test="selected">
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
										<xsl:value-of select="value_start_date"></xsl:value-of>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_end_date"></xsl:value-of>
									</td>
									<td>
										<xsl:value-of select="value_end_date"></xsl:value-of>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_branch"></xsl:value-of>
									</td>
									<td>
										<xsl:for-each select="branch_list[selected='selected']">
											<xsl:value-of select="name"></xsl:value-of>
											<xsl:if test="position() != last()">, </xsl:if>
										</xsl:for-each>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_other_branch"></xsl:value-of>
									</td>
									<td>
										<xsl:value-of select="value_other_branch"></xsl:value-of>
									</td>
								</tr>
								<tr>
									<td>
										<xsl:value-of select="lang_key_fetch"></xsl:value-of>
									</td>
									<td>
										<xsl:for-each select="key_fetch_list">
											<xsl:choose>
												<xsl:when test="selected">
													<xsl:value-of select="name"></xsl:value-of>
												</xsl:when>
											</xsl:choose>
										</xsl:for-each>
									</td>
								</tr>
								<tr>
									<td>
										<xsl:value-of select="lang_key_deliver"></xsl:value-of>
									</td>
									<td>
										<xsl:for-each select="key_deliver_list">
											<xsl:choose>
												<xsl:when test="selected">
													<xsl:value-of select="name"></xsl:value-of>
												</xsl:when>
											</xsl:choose>
										</xsl:for-each>
									</td>
								</tr>
								<tr>
									<td>
										<xsl:value-of select="lang_key_responsible"></xsl:value-of>
									</td>

									<td>
										<xsl:for-each select="key_responsible_list">
											<xsl:choose>
												<xsl:when test="selected">
													<xsl:value-of select="name"></xsl:value-of>
												</xsl:when>
											</xsl:choose>
										</xsl:for-each>
									</td>
								</tr>
							</table>
						</div>
					</xsl:when>
				</xsl:choose>


				<div id="history">
					<hr noshade="noshade" width="100%" align="center" size="1"></hr>
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<xsl:choose>
							<xsl:when test="record_history=''">
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_no_history"></xsl:value-of>
									</td>
								</tr>
							</xsl:when>
							<xsl:otherwise>
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_history"></xsl:value-of>
									</td>
								</tr>
								<xsl:apply-templates select="table_header_history"></xsl:apply-templates>
								<xsl:apply-templates select="record_history"></xsl:apply-templates>
							</xsl:otherwise>
						</xsl:choose>
					</table>
					<hr noshade="noshade" width="100%" align="center" size="1"></hr>
				</div>
			</div>
		</div>
		<table>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
					<td>
					</td>
					<xsl:variable name="edit_action"><xsl:value-of select="edit_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_edit"><xsl:value-of select="lang_edit"></xsl:value-of></xsl:variable>
					<form method="post" action="{$edit_action}">
						<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_edit_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template xmlns:php="http://php.net/xsl" match="bulk_update_status">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<form name="form" method="post" action="{update_action}">
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'start date')"></xsl:value-of>
					</td>
					<td>
						<input type="text" id="values_start_date" name="start_date" size="10" value="{start_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_start_date_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
						<img id="values_start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'end date')"></xsl:value-of>
					</td>
					<td>
						<input type="text" id="values_end_date" name="end_date" size="10" value="{end_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_end_date_statustext"></xsl:value-of>
							</xsl:attribute>
						</input>
						<img id="values_end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'user')"></xsl:value-of>
					</td>
					<td>
						<select name="user_id">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select user')"></xsl:value-of>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select')"></xsl:value-of>
							</option>
							<xsl:apply-templates select="user_list/options"></xsl:apply-templates>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'type')"></xsl:value-of>
					</td>
					<td>
						<select name="type" onChange="this.form.submit();">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select type')"></xsl:value-of>
							</xsl:attribute>
							<xsl:apply-templates select="type_list/options"></xsl:apply-templates>
						</select>
					</td>
				</tr>

				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'status filter')"></xsl:value-of>
					</td>
					<td>
						<select name="status_filter">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select status')"></xsl:value-of>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select status')"></xsl:value-of>
							</option>
							<xsl:apply-templates select="status_list_filter/options"></xsl:apply-templates>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'status new')"></xsl:value-of>
					</td>
					<td>
						<select name="status_new">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select status')"></xsl:value-of>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select status')"></xsl:value-of>
							</option>
							<xsl:apply-templates select="status_list_new/options"></xsl:apply-templates>
						</select>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<input type="submit" name="get_list">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'get list')"></xsl:value-of>
							</xsl:attribute>
						</input>
						<input type="submit" name="execute" onClick="onActionsClick()">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'save')"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="total_records"></xsl:value-of>
					</td>
					<td>
						<div id="paging_0"> </div>
						<div id="datatable-container_0"></div>
						<input type="hidden" name="id_to_update" value=""></input>
					</td>
				</tr>
			</form>
		</table>
	</div>
	<!--  DATATABLE DEFINITIONS-->
	<script type="text/javascript">
		var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
	//	var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
		var datatable = new Array();
		var myColumnDefs = new Array();
		var td_count = 5;
		<xsl:for-each select="datatable">
			datatable[<xsl:value-of select="name"></xsl:value-of>] = [
			{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				edit_action		:  	<xsl:value-of select="edit_action"></xsl:value-of>,
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


	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of>
		</option>
	</xsl:template>

