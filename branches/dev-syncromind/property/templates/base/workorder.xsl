  <!-- $Id$ -->
	<xsl:template match="data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
				<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
		</xsl:choose>
		<xsl:call-template name="jquery_phpgw_i18n"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="add">
		<xsl:apply-templates select="menu"/>
		<table width="50%" cellpadding="2" cellspacing="2" align="center">
			<tr height="50">
				<td>
					<xsl:variable name="add_action">
						<xsl:value-of select="add_action"/>
					</xsl:variable>
					<xsl:variable name="lang_add">
						<xsl:value-of select="lang_add"/>
					</xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" class="forms" name="add" value="{$lang_add}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_add_statustext"/>
							</xsl:attribute>
						</input>
					</form>
					<xsl:variable name="search_action">
						<xsl:value-of select="search_action"/>
					</xsl:variable>
					<xsl:variable name="lang_search">
						<xsl:value-of select="lang_search"/>
					</xsl:variable>
					<form method="post" action="{$search_action}">
						<input type="submit" class="forms" name="search" value="{$lang_search}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_search_statustext"/>
							</xsl:attribute>
						</input>
					</form>
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" class="forms" name="done" value="{$lang_done}">
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
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">

		<xsl:variable name="lang_done">
			<xsl:value-of select="lang_done"/>
		</xsl:variable>
		<xsl:variable name="lang_save">
			<xsl:value-of select="lang_save"/>
		</xsl:variable>

		<script type="text/javascript">
			function calculate_workorder()
			{
				document.getElementsByName("calculate_workorder")[0].value = 1;
				document.form.submit();
			}
			function send_workorder()
			{
				document.getElementsByName("send_workorder")[0].value = 1;
				document.form.submit();
			}
			function set_tab(tab)
			{
				document.form.tab.value = tab;			
			}

		</script>
		<table cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<div id="receipt"></div>
			<input type="hidden" id = "lean" name="lean" value="{lean}"/>
			<xsl:choose>
				<xsl:when test="mode='edit' and lean = 0">
					<td>
						<table>
							<tr height="50">
								<td>
									<input type="button" class="pure-button pure-button-primary" name="save" value="{$lang_save}" onClick="document.form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_save_statustext"/>
										</xsl:attribute>
									</input>
								</td>
								<td>
									<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_done_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</td>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_workorder_id!='' and mode='edit' and lean = 0">
					<td>
						<table>
							<tr>
								<td valign="top">
									<xsl:variable name="lang_calculate">
										<xsl:value-of select="lang_calculate"/>
									</xsl:variable>
									<input type="button" class="pure-button pure-button-primary" name="calculate" value="{$lang_calculate}" onClick="calculate_workorder()">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_calculate_statustext"/>
										</xsl:attribute>
									</input>
								</td>
								<td valign="top">
									<xsl:variable name="lang_send">
										<xsl:value-of select="lang_send"/>
									</xsl:variable>
									<input type="button" class="pure-button pure-button-primary" name="send" value="{$lang_send}" onClick="send_workorder()">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_send_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</td>
				</xsl:when>
			</xsl:choose>
		</table>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form ENCTYPE="multipart/form-data" method="post" id='form' name="form" action="{$form_action}" class= "pure-form pure-form-aligned">
			<input type="hidden" name="send_workorder" value=""/>
			<input type="hidden" name='calculate_workorder'  value=""/>
			<xsl:variable name="decimal_separator">
				<xsl:value-of select="decimal_separator"/>
			</xsl:variable>

			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<div id="general">
						<fieldset>
							<xsl:choose>
								<xsl:when test="value_project_id!=''">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="lang_project_id"/>
										</label>
										<xsl:choose>
											<xsl:when test="lean = 0">
												<xsl:variable name="project_link"><xsl:value-of select="project_link"/>&amp;id=<xsl:value-of select="value_project_id"/></xsl:variable>
												<a href="{$project_link}">
													<xsl:value-of select="value_project_id"/>
												</a>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="value_project_id"/>
											</xsl:otherwise>
										</xsl:choose>
										<input type="hidden" name="values[project_id]" value="{value_project_id}"/>
									</div>
								</xsl:when>
								<xsl:otherwise>
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="lang_project_id"/>
										</label>
										<input type="text" name="values[project_id]" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_title_statustext"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:otherwise>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_project_name"/>
								</label>
								<xsl:value-of select="value_project_name"/>
							</div>
							<xsl:choose>
								<xsl:when test="value_workorder_id!='' and mode='edit'">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="php:function('lang', 'move to another project')"/>
										</label>
										<input type="text" name="values[new_project_id]" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'move to another project')"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="location_template_type='form'">
									<xsl:call-template name="location_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_view"/>
									<xsl:choose>
										<xsl:when test="contact_phone !=''">
											<div class="pure-control-group">
												<label for="name">
													<xsl:value-of select="lang_contact_phone"/>
												</label>
												<xsl:value-of select="contact_phone"/>
											</div>
										</xsl:when>
									</xsl:choose>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="suppressmeter =''">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="lang_power_meter"/>
										</label>
										<xsl:value-of select="value_power_meter"/>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_coordinator"/>
								</label>
								<xsl:value-of select="value_coordinator"/>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'janitor')"/>
								</label>
								<select name="values[user_id]" class="forms" >
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'janitor')"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</option>
									<xsl:apply-templates select="user_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_branch"/>
								</label>
								<xsl:for-each select="branch_list[selected='selected']">
									<xsl:value-of select="name"/>
									<xsl:if test="position() != last()">, </xsl:if>
								</xsl:for-each>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_other_branch"/>
								</label>
								<xsl:value-of select="value_other_branch"/>
							</div>
							<xsl:for-each select="value_origin">
								<div class="pure-control-group">
									<label for="name">
										<xsl:value-of select="descr"/>
									</label>
									<table>
										<xsl:for-each select="data">
											<tr>
												<td class="th_text" align="left">
													<a href="{link}" title="{statustext}">
														<xsl:value-of select="id"/>
													</a>
													<xsl:text> </xsl:text>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</div>
							</xsl:for-each>
							<xsl:choose>
								<xsl:when test="value_workorder_id!=''">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="lang_workorder_id"/>
										</label>
										<xsl:value-of select="value_workorder_id"/>
									</div>
									<xsl:choose>
										<xsl:when test="mode='edit'">
											<div class="pure-control-group">
												<label for="name">
													<xsl:value-of select="lang_copy_workorder"/>
												</label>
												<input type="checkbox" name="values[copy_workorder]" value="True">
													<xsl:attribute name="title">
														<xsl:value-of select="lang_copy_workorder_statustext"/>
													</xsl:attribute>
												</input>
											</div>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_title"/>
								</label>
								<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
								<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
								<input type="text" name="values[title]" value="{value_title}" size="60">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_title_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_descr"/>
								</label>
								<textarea cols="60" rows="6" name="values[descr]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_descr_statustext"/>
									</xsl:attribute>
									<xsl:value-of select="value_descr"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_status"/>
								</label>
								<xsl:call-template name="status_select"/>
							</div>
							<xsl:choose>
								<xsl:when test="need_approval='1' and mode='edit'">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="lang_ask_approval"/>
										</label>
										<div class="pure-custom">
											<table>
												<xsl:for-each select="value_approval_mail_address">
													<tr>
														<td>
															<input type="checkbox" name="values[approval][{id}]" value="True">
																<xsl:attribute name="title">
																	<xsl:value-of select="//lang_ask_approval_statustext"/>
																</xsl:attribute>
															</input>
														</td>
														<td>
															<input type="text" name="values[mail_address][{id}]" value="{address}">
																<xsl:attribute name="title">
																	<xsl:value-of select="//lang_ask_approval_statustext"/>
																</xsl:attribute>
															</input>
															<xsl:if test="default = '1'">
																<xsl:text>&lt;=</xsl:text>
															</xsl:if>
														</td>
													</tr>
												</xsl:for-each>
											</table>
										</div>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="value_workorder_id!=''">
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="php:function('lang', 'approved')"/>
										</label>
										<input type="hidden" name="values[approved_orig]" value="{value_approved}"/>
										<input type="checkbox" name="values[approved]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'approved')"/>
											</xsl:attribute>
											<xsl:if test="value_approved = '1'">
												<xsl:attribute name="checked">
													<xsl:text>checked</xsl:text>
												</xsl:attribute>
											</xsl:if>
											<xsl:if test="mode != 'edit'">
												<xsl:attribute name="disabled">
													<xsl:text>disabled</xsl:text>
												</xsl:attribute>
											</xsl:if>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_remark"/>
								</label>
								<textarea cols="60" rows="6" name="values[remark]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_remark_statustext"/>
									</xsl:attribute>
									<xsl:value-of select="value_remark"/>
								</textarea>
							</div>
						</fieldset>
					</div>
					<div id="budget">
						<fieldset>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'Workorder start date')"/>
									<div id="ctx"><!--Align lightbox to me--></div> 
								</label>
								<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
									<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Select the estimated start date for the Project')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_end_date">
									<xsl:value-of select="php:function('lang', 'Workorder end date')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_end_date"/>
								</label>
								<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_end_date"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_tender_deadline">
									<xsl:value-of select="php:function('lang', 'tender deadline')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_tender_deadline"/>
								</label>
								<input type="text" id="values_tender_deadline" name="values[tender_deadline]" size="10" value="{value_tender_deadline}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_tender_deadline"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_tender_received">
									<xsl:value-of select="php:function('lang', 'tender received')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_tender_received"/>
								</label>
								<input type="text" id="values_tender_received" name="values[tender_received]" size="10" value="{value_tender_received}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_tender_received"/>
									</xsl:attribute>
								</input>
								<xsl:if test="value_tender_delay > 0">
									<xsl:text> </xsl:text>
									<xsl:value-of select="php:function('lang', 'delay')"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="value_tender_delay"/>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_inspection_on_completion">
									<xsl:value-of select="php:function('lang', 'inspection on completion')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_inspection_on_completion"/>
								</label>
								<input type="text" id="values_inspection_on_completion" name="values[inspection_on_completion]" size="10" value="{value_inspection_on_completion}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_inspection_on_completion"/>
									</xsl:attribute>
								</input>
								<xsl:if test="value_end_date_delay > 0">
									<xsl:text> </xsl:text>
									<xsl:value-of select="php:function('lang', 'delay')"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="value_end_date_delay"/>
								</xsl:if>
							</div>
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:call-template name="event_form"/>
									<xsl:call-template name="vendor_form"/>
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="php:function('lang', 'send order')"/>
										</label>
										<div class="pure-custom">
											<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_4'">
													<xsl:call-template name="table_setup">
														<xsl:with-param name="container" select ='container'/>
														<xsl:with-param name="requestUrl" select ='requestUrl' />
														<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
														<xsl:with-param name="tabletools" select ='tabletools' />
														<xsl:with-param name="data" select ='data' />
														<xsl:with-param name="config" select ='config' />
													</xsl:call-template>
												</xsl:if>
											</xsl:for-each>
										</div>
									</div>
									<div class="pure-control-group">
										<label for="name">
											<xsl:value-of select="php:function('lang', 'extra mail address')"/>
										</label>
										<input type="text" name="values[vendor_email][]" value="{value_extra_mail_address}">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'The order will also be sent to this one')"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="event_view"/>
									<xsl:call-template name="vendor_view"/>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:call-template name="ecodimb_form"/>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="b_group_data/lang_b_account"/>
								</label>
								<input type="text" size="9" value="{b_group_data/value_b_account_id}" readonly="readonly">
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</input>
								<input type="text" size="30" value="{b_group_data/value_b_account_name}" readonly="readonly">
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:call-template name="b_account_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="b_account_view"/>
								</xsl:otherwise>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_cat_sub"/>
								</label>
								<xsl:call-template name="cat_sub_select"/>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_continuous">
									<xsl:value-of select="php:function('lang', 'continuous')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_continuous"/>
								</label>
								<input type="checkbox" name="values[continuous]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_continuous"/>
									</xsl:attribute>
									<xsl:if test="value_continuous = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:if test="mode != 'edit'">
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_fictive_periodization">
									<xsl:value-of select="php:function('lang', 'fictive periodization')"/>
								</xsl:variable>
								<label for="name">
									<xsl:value-of select="$lang_fictive_periodization"/>
								</label>
								<input type="checkbox" name="values[fictive_periodization]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_fictive_periodization"/>
									</xsl:attribute>
									<xsl:if test="value_fictive_periodization = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:if test="mode != 'edit'">
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'contract sum')"/>
								</label>
								<input type="text" data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="{$decimal_separator}" name="values[contract_sum]" value="{value_contract_sum}">
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_addition_percentage"/>
								</label>
								<input type="text" data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="{$decimal_separator}" name="values[addition_percentage]" value="{value_addition_percentage}" >
									<xsl:attribute name="title">
										<xsl:value-of select="lang_addition_percentage_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text> [ % ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_budget"/>
								</label>
								<input type="text" data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="{$decimal_separator}" name="values[budget]" value="{value_budget}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_budget_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_addition_rs"/>
								</label>
								<input type="text" data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="{$decimal_separator}" name="values[addition_rs]" value="{value_addition_rs}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_addition_rs_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'when')"/>
								</label>
								<div class="pure-custom">
									<table>
										<tr>
											<td>
												<select name="values[budget_year]">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'year')"/>
													</xsl:attribute>
													<option value="0">
														<xsl:value-of select="php:function('lang', 'year')"/>
													</option>
													<xsl:apply-templates select="year_list/options"/>
												</select>
											</td>
											<xsl:choose>
												<xsl:when test="periodization_data/id !=''">
													<td>
														<input type="checkbox" name="values[budget_periodization]" value="{periodization_data/id}" checked='checked'>
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'periodization')"/>
																<xsl:text>::</xsl:text>
																<xsl:value-of select="periodization_data/descr"/>
															</xsl:attribute>
														</input>
													</td>
												</xsl:when>
											</xsl:choose>
										</tr>
									</table>
								</div>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'budget')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
											<xsl:if test="container = 'datatable-container_5'">
												<xsl:call-template name="table_setup">
													<xsl:with-param name="container" select ='container'/>
													<xsl:with-param name="requestUrl" select ='requestUrl' />
													<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
													<xsl:with-param name="tabletools" select ='tabletools' />
													<xsl:with-param name="data" select ='data' />
													<xsl:with-param name="config" select ='config' />
												</xsl:call-template>
											</xsl:if>
									</xsl:for-each>
								</div>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:choose>
										<xsl:when test="link_claim !=''">
											<a href="{link_claim}">
												<xsl:value-of select="lang_charge_tenant"/>
											</a>
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="lang_charge_tenant"/>
										</xsl:otherwise>
									</xsl:choose>
								</label>
								<input type="checkbox" name="values[charge_tenant]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_charge_tenant_statustext"/>
									</xsl:attribute>
									<xsl:if test="charge_tenant = '1'">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_calculation"/>
								</label>
								<xsl:value-of select="value_calculation"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								<xsl:value-of select="lang_incl_tax"/>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'sum estimated cost')"/>
								</label>
								<xsl:value-of select="value_sum_estimated_cost"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'billable hours')"/>
								</label>
								<input type="text" data-validation="number" data-validation-allowing="float" data-validation-decimal-separator="." id="values_billable_hour" name="values[billable_hours]" size="10" value="{value_billable_hours}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>		
								</input>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:choose>
										<xsl:when test="value_workorder_id!='' and mode='edit'">
											<xsl:variable name="lang_add_invoice_statustext">
												<xsl:value-of select="php:function('lang', 'add invoice')"/>
											</xsl:variable>
											<a href="javascript:showlightbox_manual_invoide({value_workorder_id})" title="{$lang_add_invoice_statustext}">
												<xsl:value-of select="php:function('lang', 'add invoice')"/>
											</a>
										</xsl:when>
									</xsl:choose>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_2'">
											<xsl:call-template name="table_setup">
												<xsl:with-param name="container" select ='container'/>
												<xsl:with-param name="requestUrl" select ='requestUrl' />
												<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
												<xsl:with-param name="tabletools" select ='tabletools' />
												<xsl:with-param name="data" select ='data' />
												<xsl:with-param name="config" select ='config' />
											</xsl:call-template>
										</xsl:if>
									</xsl:for-each>
								</div>
							</div>
						</fieldset>
					</div>
					<div id="coordination">
						<fieldset>
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<div class="pure-control-group">
										<xsl:variable name="lang_contact_statustext">
											<xsl:value-of select="php:function('lang', 'click this link to select')"/>
										</xsl:variable>										
										<label for="name">
											<a href="javascript:notify_contact_lookup()" title="{$lang_contact_statustext}">
												<xsl:value-of select="php:function('lang', 'contact')"/>
											</a>
										</label>
										<input type="hidden" id="notify_contact" name="notify_contact" value=""/>
										<input type="hidden" name="notify_contact_name" value=""/>
									</div>									
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">		
								<label for="name">
									<xsl:value-of select="php:function('lang', 'notify')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
											<xsl:if test="container = 'datatable-container_3'">
												<xsl:call-template name="table_setup">
													<xsl:with-param name="container" select ='container'/>
													<xsl:with-param name="requestUrl" select ='requestUrl' />
													<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
													<xsl:with-param name="tabletools" select ='tabletools' />
													<xsl:with-param name="data" select ='data' />
													<xsl:with-param name="config" select ='config' />
												</xsl:call-template>
											</xsl:if>
									</xsl:for-each>
								</div>
							</div>
							<xsl:choose>
								<xsl:when test="suppresscoordination =''">
									<div class="pure-control-group">								
										<label for="name">
											<xsl:value-of select="lang_key_fetch"/>
										</label>
										<xsl:variable name="lang_key_fetch_statustext">
											<xsl:value-of select="lang_key_fetch_statustext"/>
										</xsl:variable>
										<select name="values[key_fetch]" class="forms" onMouseover="window.status='{$lang_key_fetch_statustext}'; return true;">
											<option value="">
												<xsl:value-of select="lang_no_key_fetch"/>
											</option>
											<xsl:apply-templates select="key_fetch_list"/>
										</select>
									</div>
									<div class="pure-control-group">								
										<label for="name">
											<xsl:value-of select="lang_key_deliver"/>
										</label>
										<xsl:variable name="lang_key_deliver_statustext">
											<xsl:value-of select="lang_key_deliver_statustext"/>
										</xsl:variable>
										<select name="values[key_deliver]" class="forms" onMouseover="window.status='{$lang_key_deliver_statustext}'; return true;">
											<option value="">
												<xsl:value-of select="lang_no_key_deliver"/>
											</option>
											<xsl:apply-templates select="key_deliver_list"/>
										</select>
									</div>
									<div class="pure-control-group">								
										<label for="name">
											<xsl:value-of select="lang_key_responsible"/>
										</label>
										<xsl:for-each select="key_responsible_list">
											<xsl:choose>
												<xsl:when test="selected">
													<xsl:value-of select="name"/>
												</xsl:when>
											</xsl:choose>
										</xsl:for-each>
									</div>
								</xsl:when>
							</xsl:choose>
						</fieldset>
					</div>
					<xsl:choose>
						<xsl:when test="value_workorder_id!=''">
							<div id="documents">
								<fieldset>
									<div class="pure-control-group">								
										<label for="name">
											<xsl:value-of select="//lang_files"/>
										</label>
										<div class="pure-custom">
											<xsl:for-each select="datatable_def">
													<xsl:if test="container = 'datatable-container_1'">
														<xsl:call-template name="table_setup">
															<xsl:with-param name="container" select ='container'/>
															<xsl:with-param name="requestUrl" select ='requestUrl' />
															<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
															<xsl:with-param name="tabletools" select ='tabletools' />
															<xsl:with-param name="data" select ='data' />
															<xsl:with-param name="config" select ='config' />
														</xsl:call-template>
													</xsl:if>
											</xsl:for-each>
										</div>
									</div>
									<xsl:call-template name="file_upload"/>
								</fieldset>
							</div>
							<div id="history">
								<fieldset>
									<div class="pure-control-group">
										<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_0'">
													<xsl:call-template name="table_setup">
														<xsl:with-param name="container" select ='container'/>
														<xsl:with-param name="requestUrl" select ='requestUrl' />
														<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
														<xsl:with-param name="tabletools" select ='tabletools' />
														<xsl:with-param name="data" select ='data' />
														<xsl:with-param name="config" select ='config' />
													</xsl:call-template>
												</xsl:if>
										</xsl:for-each>
									</div>
								</fieldset>
							</div>
						</xsl:when>
					</xsl:choose>
					<script type="text/javascript">
						var base_java_url = <xsl:value-of select="base_java_url"/>;
						var base_java_notify_url = <xsl:value-of select="base_java_notify_url"/>;
					</script>
			</div>
			<div class="proplist-col">
				<xsl:choose>
					<xsl:when test="mode='edit'">
						<input type="hidden" name="values[save]" value="1"/>
						<input type="submit" class="pure-button pure-button-primary" name="save" value="{$lang_save}" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"/>
							</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:when test="mode='view'">
						<xsl:variable name="lang_edit">
							<xsl:value-of select="lang_edit"/>
						</xsl:variable>
						<input type="button" class="pure-button pure-button-primary" name="edit" value="{$lang_edit}" onClick="document.edit_form.submit();">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_edit_statustext"/>
							</xsl:attribute>
						</input>
					</xsl:when>
				</xsl:choose>
				<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_done_statustext"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
		
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<form name="done_form" id="done_form" method="post" action="{$done_action}"></form>

		<xsl:variable name="edit_action">
			<xsl:value-of select="edit_action"/>
		</xsl:variable>
		<form name="edit_form" id="edit_form" method="post" action="{$edit_action}"></form>

	</xsl:template>

	<!-- New template-->
	<xsl:template match="key_fetch_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="key_deliver_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- add_invoice -->
	<xsl:template match="add_invoice" xmlns:php="http://php.net/xsl">
		<xsl:choose>
			<xsl:when test="normalize-space(redirect) != ''">
				<script>
					window.parent.location = '<xsl:value-of select="redirect"/>';
					window.close();
				</script>
			</xsl:when>
		</xsl:choose>

		<xsl:variable name="lang_datetitle">
			<xsl:value-of select="php:function('lang', 'Select date')"/>
		</xsl:variable>

		<script type="text/javascript">
			function window_close()
			{
				window.close();
			}
		</script>

		<div align="center"  id="dialog1" class="yui-pe-content">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td colspan="2" align="center">
						<xsl:value-of select="message"/>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action">
					<xsl:value-of select="form_action"/>
				</xsl:variable>
				<form method="post" id="add_invoice" name="form" action="{$form_action}">
<!--
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'Auto TAX')"/>
						</td>
						<td>
							<input type="checkbox" name="values[auto_tax]" value="True" checked="checked" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Set tax')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
-->
					<xsl:call-template name="location_form"/>
					<xsl:call-template name="b_account_form"/>
					<xsl:call-template name="project_group_form"/>
					<xsl:call-template name="ecodimb_form"/>
					<tr>
						<xsl:call-template name="vendor_form"/>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'janitor')"/>
						</td>
						<td valign="top">
							<select name="values[janitor]" class="forms">
								<option value="">
									<xsl:value-of select="php:function('lang', 'no janitor')"/>
								</option>
								<xsl:apply-templates select="janitor_list/options_lid"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'supervisor')"/>
						</td>
						<td valign="top">
							<select name="values[supervisor]" class="forms">
								<option value="">
									<xsl:value-of select="php:function('lang', 'no supervisor')"/>
								</option>
								<xsl:apply-templates select="supervisor_list/options_lid"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'B - responsible')"/>
						</td>
						<td valign="top">
							<select name="values[budget_responsible]" class="forms">
								<option value="">
									<xsl:value-of select="php:function('lang', 'Select B-Responsible')"/>
								</option>
								<xsl:apply-templates select="budget_responsible_list/options_lid"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'order id')"/>
						</td>
						<td>
							<input type="text" name="values[order_id]" value="{value_order_id}" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Order # that initiated the invoice')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'tax code')"/>
						</td>
						<td valign="top">
							<select name="values[tax_code]" class="forms" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'tax code')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="tax_code_list/options"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'art')"/>
						</td>
						<td valign="top">
							<select name="values[artid]" class="forms" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'You have to select type of invoice')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'Select Invoice Type')"/>
								</option>
								<xsl:apply-templates select="art_list/options"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'Type invoice II')"/>
						</td>
						<td valign="top">
							<select name="values[typeid]" class="forms">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Select the type  invoice. To do not use type -  select NO TYPE')"/>
								</xsl:attribute>
								<option value="">
							<xsl:value-of select="php:function('lang', 'No type')"/>
								</option>
								<xsl:apply-templates select="type_list/options"/>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'voucher id')"/>
						</td>
						<td>
							<input type="text" name="values[voucher_out_id]" value="{value_voucher_out_id}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'voucher id')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'Invoice Number')"/>
						</td>
						<td>
							<input type="text" name="values[invoice_id]" value="{value_invoice_id}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Enter Invoice Number')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'KID nr')"/>
						</td>
						<td>
							<input type="text" name="values[kidnr]" value="{value_kidnr}" >
								<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Enter Kid nr')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'amount')"/>
						</td>
						<td>
							<input type="text" name="values[amount]" value="{value_amount}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'amount of the invoice')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'invoice date')"/>
						</td>
						<td>
							<input type="text" id="invoice_date" name="values[invoice_date]" size="10" value="{value_invoice_date}" readonly="readonly" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Enter the invoice date')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'payment date')"/>
						</td>
						<td>
							<input type="text" id="payment_date" name="values[payment_date]" size="10" value="{value_payment_date}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'payment date')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'paid')"/>
						</td>
						<td>
							<input type="text" id="paid_date" name="values[paid_date]" size="10" value="{value_paid_date}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'paid')"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'remark')"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[merknad]">
								<xsl:value-of select="value_merknad"/>
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td colspan ="2">
							<xsl:variable name="lang_add">
								<xsl:value-of select="php:function('lang', 'add')"/>
							</xsl:variable>
							<input type="submit" name="add" value="{$lang_add}" title='{$lang_add}'>
							</input>
							<xsl:variable name="cancel_action">
								<xsl:value-of select="cancel_action"/>
							</xsl:variable>
							<!--
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="php:function('lang', 'cancel')"/>
							</xsl:variable>	
							<input type="submit" name="done" value="{$lang_cancel}" title="{$lang_cancel}" onClick="javascript:window_close()">
							</input>-->
						</td>
					</tr>
				</form>
			</table>
		</div>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options_lid">
		<option value="{lid}">
			<xsl:if test="selected = 'selected'">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="lastname"/>
			<xsl:text>, </xsl:text>
			<xsl:value-of disable-output-escaping="yes" select="firstname"/>
		</option>
	</xsl:template>

