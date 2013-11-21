  <!-- $Id$ -->
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
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="priority_form">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
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
					<tr>
						<td class="small_text" align="left">
							<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
						</td>
						<td class="small_text" align="left">
							<input type="text" size="5" name="values[authorities_demands]" value="{value_authorities_demands}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" name="values[update]" value="{$lang_save}">
							</input>
						</td>
					</tr>
				</table>
			</div>
		</form>
		<!-- to reload the table -->
		<xsl:choose>
			<xsl:when test="//exchange_values!=''">
				<script type="text/javascript">
					<xsl:value-of select="//exchange_values"/>
				</script>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
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
				<xsl:value-of select="name"/>
				<xsl:text>::</xsl:text>
				<xsl:value-of select="descr"/>
			</td>
			<td class="small_text" align="left">
				<input type="text" size="3" name="values[priority_key][{id}]" value="{priority_key}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_priority_key_statustext"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
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
					<xsl:when test="value_request_id!='' and acl_add_project='1'">
						<tr>
							<td valign="top" align="center">
								<xsl:variable name="generate_project_action">
									<xsl:value-of select="generate_project_action"/>
								</xsl:variable>
								<xsl:variable name="lang_generate_project">
									<xsl:value-of select="lang_generate_project"/>
								</xsl:variable>
								<form method="post" action="{$generate_project_action}">
									<input type="hidden" name="origin" value="{value_acl_location}"/>
									<input type="hidden" name="origin_id" value="{value_request_id}"/>
									<input type="hidden" name="location_code" value="{location_code}"/>
									<input type="hidden" name="bypass" value="true"/>
									<input type="hidden" name="descr" value="{value_descr}"/>
									<input type="hidden" name="tenant_id" value="{tenant_id}"/>
									<input type="hidden" name="p_num" value="{p_num}"/>
									<input type="hidden" name="p_entity_id" value="{p_entity_id}"/>
									<input type="hidden" name="p_cat_id" value="{p_cat_id}"/>
									<input type="submit" class="forms" name="generate_project" value="{$lang_generate_project}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_generate_project_statustext"/>
										</xsl:attribute>
									</input>
								</form>
							</td>
							<td valign="top">
								<xsl:variable name="add_to_project_link">
									<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uiproject.index,from:workorder,lookup:true,origin:.project.request')"/>
								</xsl:variable>
								<form method="post" action="{$add_to_project_link}">
									<xsl:variable name="lang_add_to_project">
										<xsl:value-of select="php:function('lang', 'add to project as order')"/>
									</xsl:variable>
									<input type="hidden" name="origin_id" value="{value_request_id}"/>
									<input type="hidden" name="query" value="{loc1}"/>
									<input type="submit" name="location" value="{$lang_add_to_project}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')"/>
										</xsl:attribute>
									</input>
								</form>
							</td>
							<td valign="top">
								<xsl:variable name="add_to_project_link2">
									<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uiproject.index,from:project,lookup:true,origin:.project.request')"/>
								</xsl:variable>
								<form method="post" action="{$add_to_project_link2}">
									<input type="hidden" name="origin_id" value="{value_request_id}"/>
									<input type="hidden" name="query" value="{loc1}"/>
									<input type="submit" name="location" value="{php:function('lang', 'add to project as relation')}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to link this request to an existing project')"/>
										</xsl:attribute>
									</input>
								</form>
							</td>
							<td>
								<form method="post" action="{ticket_link}">
									<xsl:variable name="lang_start_ticket">
										<xsl:value-of select="php:function('lang', 'start ticket')"/>
									</xsl:variable>
									<input type="hidden" name="values[subject]" value="{value_title}"/>
									<input type="hidden" name="values[details]" value="{value_descr}"/>
									<input type="submit" name="start_ticket" value="{$lang_start_ticket}">
									</input>
								</form>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
		</div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">
			<div class="yui-navset yui-navset-top" id="project_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<div id="general" class="content-wrp requirement">
						<div class="requirement-responsibility-left">
							<h2><xsl:value-of select="php:function('lang', 'request')"/></h2>
							<span class="messages">
								<xsl:choose>
									<xsl:when test="value_request_id!=''">
										<label>
											<xsl:value-of select="php:function('lang', 'id')"/>
										</label>
										<xsl:value-of select="value_request_id"/>
										<br/>
									</xsl:when>
								</xsl:choose>
							</span>
							<span class="messages">
								<xsl:choose>
									<xsl:when test="value_request_id!=''">
										<xsl:for-each select="value_origin">
											<label for="msg_table" class="messages">
												<xsl:value-of select="descr"/>
											</label>
											<table name="msg_table">
												<xsl:for-each select="data">
													<td class="th_text" align="left">
														<a href="{link}" title="{statustext}">
															<xsl:value-of select="id"/>
														</a>
														<xsl:text> </xsl:text>
													</td>
												</xsl:for-each>
											</table>
										</xsl:for-each>
										<xsl:choose>
											<xsl:when test="value_target!=''">
											<br/>			
											</xsl:when>
										</xsl:choose>
										<xsl:for-each select="value_target">
											<label>
												<xsl:value-of select="descr"/>
											</label>
											<table name="msg_table">
												<xsl:for-each select="data">
													<td class="th_text" align="left">
														<a href="{link}" title="{statustext}">
															<xsl:value-of select="id"/>
														</a>
														<xsl:text> </xsl:text>
													</td>
												</xsl:for-each>
											</table>
										</xsl:for-each>

									</xsl:when>
									<xsl:otherwise>
										<xsl:for-each select="value_origin">
											<label class="messages">
												<xsl:value-of select="descr"/>
											</label>
											<table>
												<xsl:for-each select="data">
													<td class="th_text" align="left">
														<a href="{link}" title="{statustext}">
															<xsl:value-of select="id"/>
														</a>
														<xsl:text> </xsl:text>
													</td>
												</xsl:for-each>
											</table>
										</xsl:for-each>
									</xsl:otherwise>
								</xsl:choose>
							</span>
						</div>
						<div class="clearBoth">&nbsp;</div>
						<hr/>
						<div>
							<table>
								<tr>
									<td valign = 'top'>
										<div class="requirement-responsibility-left">
										<table>
											<tr>
												<td>
													<div class="requirement-responsibility-left-sub">
														<label>
															<xsl:value-of select="php:function('lang', 'responsible unit')"/>
														</label>
														<br/>
														<select name="values[responsible_unit]" class="forms" style="width:300px;">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'Set responsible unit')"/>
															</xsl:attribute>
															<option value="0">
																<xsl:value-of select="php:function('lang', 'select')"/>
															</option>
															<xsl:apply-templates select="responsible_unit_list/options"/>
														</select>
													</div>
												</td>
											<td>
												<div class="requirement-responsibility-right">
													<label>
														<xsl:value-of select="php:function('lang', 'request status')"/>
													</label>
													<br/>
													<select name="values[status]" class="forms" style="width:200px;">
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'Set the status of the request')"/>
														</xsl:attribute>
														<option value="0">
															<xsl:value-of select="php:function('lang', 'no status')"/>
														</option>
														<xsl:apply-templates select="status_list/options"/>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="coordinator_select">
													<dt>
														<label>
															<xsl:value-of select="lang_coordinator"/>
														</label>
													</dt>
													<dd>
														<xsl:call-template name="user_id_select"/>
													</dd>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</td>
							<td>
								<table>
									<tr>
										<td>
											<div class="requirement-responsibility-right">
												<!--<legend>
														<xsl:value-of select="php:function('lang', 'location')"/>
													</legend> -->
												<dl class="proplist-col">
													<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
													<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
													<xsl:choose>
														<xsl:when test="mode ='edit'">
															<xsl:call-template name="location_form2"/>
														</xsl:when>
														<xsl:otherwise>
															<xsl:call-template name="location_view2"/>
															<xsl:choose>
																<xsl:when test="contact_phone !=''">
																	<dt>
																		<label>
																			<xsl:value-of select="lang_contact_phone"/>
																		</label>
																	</dt>
																	<dd>
																		<xsl:value-of select="contact_phone"/>
																	</dd>
																</xsl:when>
															</xsl:choose>
														</xsl:otherwise>
													</xsl:choose>
													<xsl:choose>
														<xsl:when test="suppressmeter =''">
															<dt>
																<label>
																	<xsl:value-of select="lang_power_meter"/>
																</label>
															</dt>
															<dd>
																<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12">
																	<xsl:attribute name="title">
																		<xsl:value-of select="lang_power_meter_statustext"/>
																	</xsl:attribute>
																</input>
															</dd>
														</xsl:when>
													</xsl:choose>
												</dl>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						</table>
						</div>
						<div class="clearBoth">&nbsp;</div>
						<hr/>
						<div class="requirement-description">
							<h3>
								<xsl:value-of select="php:function('lang', 'description')"/>
							</h3>
							<label>
								<xsl:value-of select="php:function('lang', 'building part')"/>
							</label>
							<br/>
							<select name="values[building_part]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select building part')"/>
								</xsl:attribute>
								<option value="0">
									<xsl:value-of select="php:function('lang', 'select building part')"/>
								</option>
								<xsl:apply-templates select="building_part_list/options"/>
							</select>
							<br/>
							<xsl:variable name="lang_request_title">
								<xsl:value-of select="php:function('lang', 'request action mouseover title')"/>
							</xsl:variable>
							<label title="{$lang_request_title}">
								<xsl:value-of select="php:function('lang', 'request action title')"/>
							</label>
							<br/>
							<input type="text" name="values[title]" value="{value_title}" size="120" title="{$lang_request_title}">
							</input>
							<br/>
							<xsl:variable name="lang_request_description">
								<xsl:value-of select="php:function('lang', 'request condition mouseover description')"/>
							</xsl:variable>
							<label title="{$lang_request_description}">
								<xsl:value-of select="php:function('lang', 'request condition description')"/>
							</label>
							<br/>
							<textarea cols="120" rows="6" name="values[descr]" title="{$lang_request_description}">
								<xsl:value-of select="value_descr"/>
							</textarea>
						</div>
						<div class="clearBoth">&nbsp;</div>
						<hr/>
						<div class="requirement-condition">
							<h3>
								<xsl:value-of select="php:function('lang', 'condition')"/>
							</h3>
							<dl class="proplist-col">
								<table>
									<xsl:apply-templates select="custom_attributes/attributes"/>
									<tr>
										<td colspan="2">
											<table border="1" width="100%" cellpadding="2" cellspacing="2" align="center">
												<xsl:call-template name="table_header_importance"/>
												<xsl:apply-templates select="condition_list"/>
											</table>
										</td>
									</tr>
									<xsl:choose>
										<xsl:when test="authorities_demands/options!=''">
											<tr>
												<td align="left">
													<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
												</td>
												<td>
													<select name="values[authorities_demands]" class="forms">
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'Is there a demand from the authorities to correct this condition?')"/>
															<xsl:text> + </xsl:text>
															<xsl:value-of select="value_authorities_demands"/>
														</xsl:attribute>
														<option value="0">
															<xsl:value-of select="php:function('lang', 'no authorities demands')"/>
														</option>
														<xsl:apply-templates select="authorities_demands/options"/>
													</select>
												</td>
											</tr>
										</xsl:when>
									</xsl:choose>
								</table>
							</dl>
						</div>
						<div class="clearBoth">&nbsp;</div>
						<hr/>
						<div>
							<div class="requirement-action-left">
								<h3>
									<!-- xsl:value-of select="php:function('lang', 'economy and progress')"/ -->
									<xsl:value-of select="php:function('lang', 'action year')"/>
								</h3>
								<label>
									<xsl:value-of select="php:function('lang', 'recommended year')"/>
								</label>
								<br/>
								<input type="text" id="recommended_year" name="values[recommended_year]" size="10" value="{value_recommended_year}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'year')"/>
									</xsl:attribute>
								</input>
								<xsl:choose>
									<xsl:when test="show_dates !=''">
										<br/>
										<label>
											<xsl:value-of select="php:function('lang', 'start date')"/>
										</label>
										<br/>
										<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_start_date_statustext"/>
											</xsl:attribute>
										</input>
										<br/>
										<label>
											<xsl:value-of select="php:function('lang', 'end date')"/>
										</label>
										<br/>
										<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_end_date_statustext"/>
											</xsl:attribute>
										</input>
									</xsl:when>
								</xsl:choose>
								<br/>
								<label>
									<xsl:value-of select="php:function('lang', 'entry date')"/>
								</label>
								<br/>
								<xsl:value-of select="value_entry_date"/>
							</div>
							<div class="requirement-action-right">
								<h3>
									<!-- xsl:value-of select="php:function('lang', 'economy and progress')"/ -->
									<xsl:value-of select="php:function('lang', 'action cost overview')"/>
								</h3>
								<div class="requirement-action-sub-left">
									<div>
										<label class="requirement-action-label">
											<xsl:value-of select="php:function('lang', 'cost operation')"/>
										</label>
										<input type="text" name="values[amount_operation]" value="{value_amount_operation}">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_budget_statustext"/>
											</xsl:attribute>
										</input>
										<xsl:text> </xsl:text>
									</div>
									<div>
										<label class="requirement-action-label">
											<xsl:value-of select="php:function('lang', 'cost investment')"/>
										</label>
										<input type="text" name="values[amount_investment]" value="{value_amount_investment}">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_budget_statustext"/>
											</xsl:attribute>
										</input>
										<xsl:text> </xsl:text>
									</div>
									<div>
										<label class="requirement-action-label">
											<xsl:value-of select="php:function('lang', 'cost estimate')"/>
										</label>
										<xsl:value-of select="value_budget"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
									</div>
									<div>
										<label class="requirement-action-label">
											<xsl:value-of select="php:function('lang', 'multiplier')"/>
										</label>
										<xsl:value-of select="value_multiplier"/>
									</div>
									<div>
										<label class="requirement-action-label">
											<xsl:value-of select="php:function('lang', 'total cost estimate')"/>
										</label>
										<xsl:value-of select="value_total_cost_estimate"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
									</div>
								</div>
								<div class="requirement-action-sub-right">
									<div>
										<label class="requirement-action-label-wide">
											<xsl:value-of select="php:function('lang', 'potential grants')"/>
										</label>
										<input type="text" name="values[amount_potential_grants]" value="{value_amount_potential_grants}">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_budget_statustext"/>
											</xsl:attribute>
										</input>
										<xsl:text> </xsl:text>
									</div>
									<div>
										<label class="requirement-action-label-wide">
											<xsl:value-of select="php:function('lang', 'category')"/>
										</label>
										<xsl:call-template name="categories"/>
									</div>
									<xsl:choose>
										<xsl:when test="notify='yes'">
											<div>
												<label class="requirement-action-label-wide">
													<xsl:value-of select="lang_notify"/>
												</label>
												<input type="checkbox" name="values[notify]" value="True">
													<xsl:attribute name="title">
														<xsl:value-of select="lang_notify_statustext"/>
													</xsl:attribute>
												</input>
												<input type="text" name="values[mail_address]" value="{value_notify_mail_address}">
													<xsl:attribute name="title">
														<xsl:value-of select="lang_notify_statustext"/>
													</xsl:attribute>
												</input>
											</div>
										</xsl:when>
									</xsl:choose>
								</div>
							</div>
						</div>
						<div class="clearBoth">&nbsp;</div>
						<hr/>
						<div class="requirement-related">
							<h3><!-- xsl:value-of select="php:function('lang', 'related')"/-->
								<xsl:value-of select="php:function('lang', 'economy and progress')"/>
							</h3>
							<div id="datatable-container_2"/>
						</div>
					</div>
					<div id="documents">
						<table>
							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td width="19%" align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<div id="datatable-container_1"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="fileupload = 1">
									<xsl:call-template name="file_upload"/>
								</xsl:when>
							</xsl:choose>
							<tr height="50">
								<td>
									<xsl:variable name="lang_save">
										<xsl:value-of select="lang_save"/>
									</xsl:variable>
									<input type="submit" name="values[save]" value="{$lang_save}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_save_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</div>
					<div id="history">
						<div id="paging_0"> </div>
						<div id="datatable-container_0"/>
						<div id="contextmenu_0"/>
						<script type="text/javascript">
							var property_js = <xsl:value-of select="property_js"/>;
							var datatable = new Array();
							var myColumnDefs = new Array();

							<xsl:for-each select="datatable">
								datatable[<xsl:value-of select="name"/>] = [
								{
									values:<xsl:value-of select="values"/>,
									total_records: <xsl:value-of select="total_records"/>,
									edit_action:  <xsl:value-of select="edit_action"/>,
									is_paginator:  <xsl:value-of select="is_paginator"/>,
									footer:<xsl:value-of select="footer"/>
								}
							]
							</xsl:for-each>

							<xsl:for-each select="myColumnDefs">
								myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
							</xsl:for-each>
						</script>
					</div>
				</div>
			</div>
			<div class="controlButton">
				<table>
					<tr height="50">
						<xsl:choose>
							<xsl:when test="mode = 'edit'">
								<td style="padding-right: 5px;">
									<xsl:variable name="lang_save">
										<xsl:value-of select="lang_save"/>
									</xsl:variable>
									<input type="submit" name="values[save]" value="{$lang_save}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_save_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>
						<td>
							<xsl:variable name="done_action">
								<xsl:value-of select="done_action"/>
							</xsl:variable>
							<xsl:variable name="lang_done">
								<xsl:value-of select="lang_done"/>
							</xsl:variable>
							<input type="button" name="done" value="{$lang_done}" onclick="location.href='{$done_action}'">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_done_statustext"/>
								</xsl:attribute>
							</input>
						</td>
						<xsl:choose>
							<xsl:when test="mode = 'view'">
								<td>
									<xsl:variable name="edit_action">
										<xsl:value-of select="edit_action"/>
									</xsl:variable>
									<xsl:variable name="lang_edit">
										<xsl:value-of select="php:function('lang', 'edit')"/>
									</xsl:variable>
									<input type="button" class="forms" name="edit" value="{$lang_edit}" onclick="location.href='{$edit_action}'">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'edit')"/>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>
					</tr>
				</table>
			</div>
		</form>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="condition_list">
		<tr>
			<xsl:choose>
				<xsl:when test="condition_type_list != ''">
				</xsl:when>
				<xsl:otherwise>
					<td class="small_text" align="left">
						<xsl:value-of select="condition_type_name"/>
					</td>
				</xsl:otherwise>
			</xsl:choose>
			<!--
<td class="small_text" align="center">
<select name="values[condition][{condition_type}][reference]" class="forms">
<xsl:attribute name="title">
<xsl:value-of select="php:function('lang', 'select value')" />
</xsl:attribute>
<xsl:apply-templates select="reference/options"/>
</select>
</td>
-->
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][degree]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')"/>
					</xsl:attribute>
					<xsl:apply-templates select="degree/options"/>
				</select>
			</td>
			<xsl:choose>
				<xsl:when test="condition_type_list != ''">
					<td class="small_text" align="left">
						<select name="values[condition][{condition_type}][condition_type]" class="forms">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select value')"/>
							</xsl:attribute>
							<xsl:apply-templates select="condition_type_list/options"/>
						</select>
					</td>
				</xsl:when>
			</xsl:choose>
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][consequence]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')"/>
					</xsl:attribute>
					<xsl:apply-templates select="consequence/options"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select name="values[condition][{condition_type}][probability]" class="forms">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')"/>
					</xsl:attribute>
					<xsl:apply-templates select="probability/options"/>
				</select>
			</td>
			<!--
<td class="small_text" align="center">
<xsl:value-of select="failure"/>
</td>
-->
			<td class="small_text" align="right">
				<xsl:value-of select="weight"/>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="risk"/>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="score"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
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
				<select disabled="" class="forms">
					<xsl:apply-templates select="degree"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select disabled="" class="forms">
					<xsl:apply-templates select="probability"/>
				</select>
			</td>
			<td class="small_text" align="center">
				<select disabled="" class="forms">
					<xsl:apply-templates select="consequence"/>
				</select>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:if test="descr != ''">
				<xsl:attribute name="title">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<!-- New template-->
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

	<!-- New template-->
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

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" name="table_header_importance">
		<tr>
			<xsl:choose>
				<xsl:when test="//condition_type_list != ''">
				</xsl:when>
				<xsl:otherwise>
					<th width="20%" align="left">
						<xsl:attribute name="title">
							<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'consequence type')"/>
					</th>
				</xsl:otherwise>
			</xsl:choose>
			<!--
<th width="20%" align="left">
<xsl:value-of select="php:function('lang', 'reference level')" />
</th>
-->
			<th width="20%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'condition degree')"/>
			</th>
			<xsl:choose>
				<xsl:when test="//condition_type_list != ''">
					<th width="20%" align="left">
						<xsl:attribute name="title">
							<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'consequence type')"/>
					</th>
				</xsl:when>
			</xsl:choose>
			<th width="20%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Konsekvensgrad iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Consequence')"/>
			</th>
			<th width="20%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Sannsynlighet iht NS 3424</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Probability')"/>
			</th>
			<!--
<th width="5%" align="center">
<xsl:value-of select="php:function('lang', 'failure')" />
</th>
-->
			<th width="5%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Vekt = konfigurerbar verdi pr konsekvenstype</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'weight')"/>
			</th>
			<th width="5%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Risiko = Konsekvensgrad x Sannsynlighetsgrad</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'risk')"/>
			</th>
			<th width="5%" align="center">
				<xsl:attribute name="title">
					<xsl:text>Poeng = Tilstandsgrad x Risiko x Vekt</xsl:text>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'score')"/>
			</th>
		</tr>
	</xsl:template>
