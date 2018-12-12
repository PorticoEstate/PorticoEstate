
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:call-template name="jquery_phpgw_i18n"/>
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:call-template name="jquery_phpgw_i18n"/>
			<xsl:apply-templates select="view"/>
		</xsl:when>
		<xsl:when test="priority_form">
			<xsl:apply-templates select="priority_form"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="priority_form">
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<form name="form" id="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<xsl:apply-templates select="priority_key"/>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
						</label>
						<input type="text" size="5" name="values[authorities_demands]" value="{value_authorities_demands}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name"></label>
						<xsl:variable name="lang_save">
							<xsl:value-of select="php:function('lang', 'save')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[update]" value="{$lang_save}"></input>
					</div>
				</fieldset>
			</div>
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
	<div class="pure-control-group">
		<label for="name">
			<xsl:value-of select="name"/>
			<xsl:text>::</xsl:text>
			<xsl:value-of select="descr"/>
		</label>
		<input type="text" size="3" name="values[priority_key][{id}]" value="{priority_key}">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_priority_key_statustext"/>
			</xsl:attribute>
		</input>
	</div>
</xsl:template>

<!-- add / edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
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
								<input type="submit" class="pure-button pure-button-primary" name="generate_project" value="{$lang_generate_project}">
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
								<input type="submit" class="pure-button pure-button-primary" name="location" value="{$lang_add_to_project}">
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
								<input type="submit" class="pure-button pure-button-primary" name="location" value="{php:function('lang', 'add to project as relation')}">
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
								<input type="submit" class="pure-button pure-button-primary" name="start_ticket" value="{$lang_start_ticket}">
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
	<form ENCTYPE="multipart/form-data" method="post" name="form" id="form" action="{$form_action}" class= "pure-form  pure-form-aligned">
		<div id="request_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general" class="pure-g">
				<div class="pure-u-1">
					<h2>
						<xsl:value-of select="php:function('lang', 'request')"/>
					</h2>
					<xsl:choose>
						<xsl:when test="value_request_id!=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<div class="pure-custom">
									<xsl:value-of select="value_request_id"/>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_link_survey!=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'condition survey')"/>
								</label>
								<div class="pure-custom">
									<a href="{value_link_survey}">
										<xsl:value-of select="value_condition_survey_id"/>
									</a>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_request_id!=''">
							<xsl:for-each select="value_origin">
								<div class="pure-control-group">
									<label for="msg_table" class="messages">
										<xsl:value-of select="descr"/>
									</label>
									<div class="pure-custom">
										<xsl:for-each select="data">
											<div>
												<a href="{link}" title="{statustext}">
													<xsl:value-of select="id"/>
												</a>
												<xsl:text> </xsl:text>
											</div>
										</xsl:for-each>
									</div>
								</div>
							</xsl:for-each>
							<xsl:for-each select="value_target">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="descr"/>
									</label>
									<div class="pure-custom">
										<xsl:for-each select="data">
											<div>
												<a href="{link}" title="{statustext}">
													<xsl:value-of select="id"/>
												</a>
												<xsl:text> </xsl:text>
											</div>
										</xsl:for-each>
									</div>
								</div>
							</xsl:for-each>
						</xsl:when>
						<xsl:when test="value_origin!=''">
							<xsl:for-each select="value_origin">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="descr"/>
									</label>
									<div class="pure-custom">
										<xsl:for-each select="data">
											<div>
												<a href="{link}" title="{statustext}">
													<xsl:value-of select="id"/>
												</a>
												<xsl:text> </xsl:text>
											</div>
										</xsl:for-each>
									</div>
								</div>
							</xsl:for-each>
						</xsl:when>
					</xsl:choose>
				</div>
				<div class="clearBoth">&nbsp;</div>
				<hr/>
				<div>
					<div class="pure-u-1">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsible unit')"/>
							</label>
							<select name="values[responsible_unit]" class="pure-input-1-2" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Set responsible unit')"/>
								</xsl:attribute>
								<option value="0">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="responsible_unit_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'request status')"/>
							</label>
							<select name="values[status]" class="pure-input-1-2" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Set the status of the request')"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please select a status !')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'no status')"/>
								</option>
								<xsl:apply-templates select="status_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_coordinator"/>
							</label>
							<xsl:call-template name="user_id_select"/>
						</div>
					</div>
					<div class="pure-u-1">
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
										<div class="pure-control-group">
											<label>
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
									<label>
										<xsl:value-of select="lang_power_meter"/>
									</label>
									<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" class="pure-input-1-2" >
										<xsl:attribute name="title">
											<xsl:value-of select="lang_power_meter_statustext"/>
										</xsl:attribute>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
					</div>
				</div>
						
				<div class="clearBoth">&nbsp;</div>
				<hr/>
				<div class="pure-u-1">
					<h3>
						<xsl:value-of select="php:function('lang', 'description')"/>
					</h3>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'building part')"/>
						</label>
						<select data-validation="required" name="values[building_part]" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select building part')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select a building part!')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select building part')"/>
							</option>
							<xsl:apply-templates select="building_part_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_request_title">
							<xsl:value-of select="php:function('lang', 'request action mouseover title')"/>
						</xsl:variable>
						<label title="{$lang_request_title}">
							<xsl:value-of select="php:function('lang', 'request action title')"/>
						</label>
						<input type="text" name="values[title]" value="{value_title}"  class="pure-input-1-2" title="{$lang_request_title}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a request TITLE !')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_request_description">
							<xsl:value-of select="php:function('lang', 'request condition mouseover description')"/>
						</xsl:variable>
						<label title="{$lang_request_description}">
							<xsl:value-of select="php:function('lang', 'request condition description')"/>
						</label>
						<textarea  class="pure-input-1-2" rows="6" name="values[descr]" title="{$lang_request_description}">
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
				</div>
				<div class="clearBoth">&nbsp;</div>
				<hr/>
				<div class="pure-u-1">
					<h3>
						<xsl:value-of select="php:function('lang', 'condition')"/>
					</h3>
					<xsl:apply-templates select="custom_attributes/attributes"/>
					<div>
						<xsl:apply-templates select="condition_list"/>
					</div>
					<xsl:choose>
						<xsl:when test="authorities_demands/options!=''">
							<div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'Authorities Demands')"/>
									</label>
									<select name="values[authorities_demands]" class="pure-input-1-2" >
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
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
				</div>
				<div class="clearBoth">&nbsp;</div>
				<hr/>
				<div class="pure-u-1 pure-u-lx-1-3">
					<h3>
						<xsl:value-of select="php:function('lang', 'action year')"/>
					</h3>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'recommended year')"/>
						</label>
						<input type="text" data-validation="number" id="recommended_year" name="values[recommended_year]" size="10" value="{value_recommended_year}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'year')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<xsl:choose>
						<xsl:when test="show_dates !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'start date')"/>
								</label>
								<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_start_date_statustext"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'end date')"/>
								</label>
								<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_end_date_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'entry date')"/>
						</label>
						<xsl:value-of select="value_entry_date"/>
					</div>
				</div>
				<div class="pure-u-1 pure-u-lx-1-3">

					<h3>
						<!-- xsl:value-of select="php:function('lang', 'economy and progress')"/ -->
						<xsl:value-of select="php:function('lang', 'action cost overview')"/>
					</h3>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'cost operation')"/>
						</label>
						<input type="text" data-validation="number" data-validation-allowing="float" id="amount_operation" name="values[amount_operation]" value="{value_amount_operation}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_budget_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
						</input>
						<xsl:text> </xsl:text>
					</div>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'cost investment')"/>
						</label>
						<input type="text" data-validation="number" data-validation-allowing="float" id="amount_investment" name="values[amount_investment]" value="{value_amount_investment}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_budget_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
						</input>
						<xsl:text> </xsl:text>
					</div>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'cost estimate')"/>
						</label>
						<xsl:value-of select="value_budget"/>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</div>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'multiplier')"/>
						</label>
						<xsl:value-of select="value_multiplier"/>
					</div>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'representative')"/>
						</label>
						<input type="text" data-validation="number" data-validation-allowing="float" id="representative" name="values[representative]" value="{value_representative}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'representative')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label class="requirement-action-label">
							<xsl:value-of select="php:function('lang', 'total cost estimate')"/>
						</label>
						<xsl:value-of select="value_total_cost_estimate"/>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</div>
				</div>
				<div class="pure-u-1 pure-u-lx-1-3">
					<h3>
						<xsl:value-of select="php:function('lang', 'extra')"/>
					</h3>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'potential grants')"/>
						</label>
						<input type="text" data-validation="number" data-validation-allowing="float" name="values[amount_potential_grants]" value="{value_amount_potential_grants}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_budget_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
						</input>
						<xsl:text> </xsl:text>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:call-template name="categories"/>
					</div>
					<xsl:choose>
						<xsl:when test="notify='yes'">
							<div class="pure-control-group">
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
				<div class="clearBoth">&nbsp;</div>
				<hr/>
				<div class="pure-u-1">
					<h3><!-- xsl:value-of select="php:function('lang', 'related')"/-->
						<xsl:value-of select="php:function('lang', 'economy and progress')"/>
					</h3>
					<!--div id="datatable-container_2"/-->
					<xsl:for-each select="datatable_def">
						<xsl:if test="container = 'datatable-container_2'">
							<xsl:call-template name="table_setup">
								<xsl:with-param name="container" select ='container'/>
								<xsl:with-param name="requestUrl" select ='requestUrl' />
								<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
								<xsl:with-param name="tabletools" select ='tabletools' />
								<xsl:with-param name="config" select ='config' />
								<xsl:with-param name="data" select ='data' />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</div>
			</div>
			<div id="documents">
				<xsl:choose>
					<xsl:when test="fileupload = 1">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="//lang_files"/>
							</label>
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_1'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl' />
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
										<xsl:with-param name="tabletools" select ='tabletools' />
										<xsl:with-param name="config" select ='config' />
										<xsl:with-param name="data" select ='data' />
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="fileupload = 1">
						<script type="text/javascript">
							var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
							var project_id = '<xsl:value-of select="value_request_id"/>';
						</script>
						<div class="pure-control-group">
							<xsl:call-template name="file_upload"/>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div id="history">
				<xsl:for-each select="datatable_def">
					<xsl:if test="container = 'datatable-container_0'">
						<xsl:call-template name="table_setup">
							<xsl:with-param name="container" select ='container'/>
							<xsl:with-param name="requestUrl" select ='requestUrl' />
							<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
							<xsl:with-param name="tabletools" select ='tabletools' />
							<xsl:with-param name="config" select ='config' />
							<xsl:with-param name="data" select ='data' />
						</xsl:call-template>
					</xsl:if>
				</xsl:for-each>
			</div>
		</div>
		<div class="controlButton">
			<xsl:choose>
				<xsl:when test="mode = 'edit'">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_save_new">
						<xsl:value-of select="php:function('lang', 'save new')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save_new]" value="{$lang_save_new}">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save_new"/>
						</xsl:attribute>
					</input>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="done_action">
				<xsl:value-of select="done_action"/>
			</xsl:variable>
			<xsl:variable name="lang_done">
				<xsl:value-of select="php:function('lang', 'done')"/>
			</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onclick="location.href='{$done_action}'">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_done_statustext"/>
				</xsl:attribute>
			</input>
			<xsl:choose>
				<xsl:when test="mode = 'view'">
					<xsl:variable name="edit_action">
						<xsl:value-of select="edit_action"/>
					</xsl:variable>
					<xsl:variable name="lang_edit">
						<xsl:value-of select="php:function('lang', 'edit')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="edit" value="{$lang_edit}" onclick="location.href='{$edit_action}'">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'edit')"/>
						</xsl:attribute>
					</input>
				</xsl:when>
			</xsl:choose>
		</div>
	</form>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="condition_list">
	<xsl:choose>
		<xsl:when test="condition_type_list != ''">
		</xsl:when>
		<xsl:otherwise>
			<div class="pure-control-group">
				<label>
					<xsl:attribute name="title">
						<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'consequence type')"/>
				</label>
				<xsl:value-of select="condition_type_name"/>
			</div>
		</xsl:otherwise>
	</xsl:choose>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'condition degree')"/>
		</label>
		<select name="values[condition][{condition_type}][degree]"  class="pure-input-1-2" >
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select value')"/>
			</xsl:attribute>
			<xsl:apply-templates select="degree/options"/>
		</select>
	</div>
	<xsl:choose>
		<xsl:when test="condition_type_list != ''">
			<div class="pure-control-group">
				<label>
					<xsl:attribute name="title">
						<xsl:text>Konsekvenstype - tema for tilstand</xsl:text>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'consequence type')"/>
				</label>
				<select name="values[condition][{condition_type}][condition_type]"  class="pure-input-1-2" >
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select value')"/>
					</xsl:attribute>
					<xsl:apply-templates select="condition_type_list/options"/>
				</select>
			</div>
		</xsl:when>
	</xsl:choose>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Konsekvensgrad iht NS 3424</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Consequence')"/>
		</label>
		<select name="values[condition][{condition_type}][consequence]" class="pure-input-1-2" >
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select value')"/>
			</xsl:attribute>
			<xsl:apply-templates select="consequence/options"/>
		</select>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Sannsynlighet iht NS 3424</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Probability')"/>
		</label>
		<select name="values[condition][{condition_type}][probability]" class="pure-input-1-2" >
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select value')"/>
			</xsl:attribute>
			<xsl:apply-templates select="probability/options"/>
		</select>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Vekt = konfigurerbar verdi pr konsekvenstype</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'weight')"/>
		</label>
		<xsl:value-of select="weight"/>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Risiko = Konsekvensgrad x Sannsynlighetsgrad</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'risk')"/>
		</label>
		<xsl:value-of select="risk"/>
	</div>
	<div class="pure-control-group">
		<label>
			<xsl:attribute name="title">
				<xsl:text>Poeng = Tilstandsgrad x Risiko x Vekt</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'score')"/>
		</label>
		<xsl:value-of select="score"/>
	</div>
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
