
<!-- $Id$ -->

<xsl:template match="data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="bulk_update_status">
			<xsl:apply-templates select="bulk_update_status"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<!-- add / edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<style type="text/css">
		#floating-box {
		position: relative;
		z-index: 10;
		}
		#submitbox {
		display: none;
		}
	</style>
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
		var project_type_id = '<xsl:value-of select="project_type_id"/>';
		var project_id = '<xsl:value-of select="value_project_id"/>';
		var location_item_id = '<xsl:value-of select="location_item_id"/>';
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'next', 'save')"/>;
	</script>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<div id="message" class='message'/>

	<form ENCTYPE="multipart/form-data" method="post" id="form" name="form" action="{form_action}" class= "pure-form pure-form-aligned">
		<xsl:variable name="decimal_separator">
			<xsl:value-of select="decimal_separator"/>
		</xsl:variable>
		<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
		<input type="hidden" name='validatet_category' id="validatet_category" value="1"/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="floating-box">
				<div id="submitbox">
					<table>
						<tbody>
							<tr>
								<xsl:choose>
									<xsl:when test="value_project_id &gt; 0  and mode='edit'">
										<td valign="top">
											<xsl:variable name="lang_add_sub_entry">
												<xsl:value-of select="lang_add_sub_entry"/>
											</xsl:variable>
											<input type="button" class="pure-button pure-button-primary" id="add_sub_entry" name="add_sub_entry" value="{$lang_add_sub_entry}" onClick="addSubEntry();">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_add_sub_entry_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<xsl:variable name="lang_save">
											<xsl:value-of select="lang_save"/>
										</xsl:variable>
										<td>
											<input type="hidden" name='save'  value=""/>
											<input type="button" id="submitform" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onClick="validate_submit();">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_save_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</xsl:when>
									<xsl:when test="mode='view'">
										<xsl:variable name="lang_edit">
											<xsl:value-of select="lang_edit"/>
										</xsl:variable>
										<td>
											<input type="button" id="editform" class="pure-button pure-button-primary" name="edit" value="{$lang_edit}" onClick="document.edit_form.submit();">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_edit_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</xsl:when>
								</xsl:choose>
								<xsl:variable name="lang_done">
									<xsl:value-of select="lang_done"/>
								</xsl:variable>
								<td>
									<input type="button" id="cancelform" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_done_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div id="general">
				<fieldset>
					<xsl:choose>
						<xsl:when test="value_project_id &gt; 0">
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<div class="pure-control-group">
										<label for="name" title="{lang_copy_project_statustext}">
											<xsl:value-of select="lang_copy_project"/>
										</label>
										<input type="checkbox" name="values[copy_project]" value="True">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_copy_project_statustext"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_project_id"/>
								</label>
								<div class="pure-custom">
									<xsl:value-of select="value_project_id"/>
								</div>
							</div>
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<div class="pure-control-group">
										<label for="name">
											<a href="{link_select_request}" title="{lang_select_request_statustext}">
												<xsl:value-of select="php:function('lang', 'select request')"/>
											</a>
										</label>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'related')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_6'">
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
						</xsl:when>
						<xsl:otherwise>
							<xsl:for-each select="value_origin">
								<div class="pure-control-group">
									<label for="name">
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
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'project type')"/>
						</label>
						<select name="values[project_type_id]" class="pure-input-1-2">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'project type')"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="project_types/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_name"/>
						</label>
						<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
						<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
						<input type="text" name="values[name]" value="{value_name}" class="pure-input-1-2">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_name_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:text>Please enter a project NAME !</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea  class="pure-input-1-2" rows="6" name="values[descr]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_descr_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_coordinator"/>
						</label>
						<xsl:call-template name="user_id_select"/>
					</div>
					<!--xsl:call-template name="contact_form"/-->
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_category"/>
						</label>
						<xsl:call-template name="categories"/>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_status"/>
						</label>
						<xsl:call-template name="status_select"/>
					</div>
					<xsl:choose>
						<xsl:when test="value_project_id &gt; 0 and mode='edit'">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_confirm_status"/>
								</label>
								<input type="checkbox" name="values[confirm_status]" value="True">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_confirm_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<!--xsl:choose>
						<xsl:when test="need_approval='1' and mode='edit'">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_ask_approval"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="value_approval_mail_address">
										<div>
											<input type="checkbox" name="values[approval][{id}]" value="True">
												<xsl:attribute name="title">
													<xsl:value-of select="//lang_ask_approval_statustext"/>
												</xsl:attribute>
											</input>
											<input type="text" name="values[mail_address][{id}]" value="{address}">
												<xsl:attribute name="title">
													<xsl:value-of select="//lang_ask_approval_statustext"/>
												</xsl:attribute>
											</input>
											<xsl:if test="default = '1'">
												<xsl:text>&lt;=</xsl:text>
											</xsl:if>
										</div>
									</xsl:for-each>
								</div>
							</div>
						</xsl:when>
					</xsl:choose-->
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_remark"/>
						</label>
						<textarea  class="pure-input-1-2" rows="6" name="values[remark]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_remark_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>
						</textarea>
					</div>
					<xsl:apply-templates select="custom_attributes/attributes"/>
				</fieldset>
			</div>

			<div id="location">
				<fieldset>
					<xsl:choose>
						<xsl:when test="mode='edit'">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'inherit location')"/>
								</label>
								<input type="checkbox" name="values[inherit_location]" value="1">
									<xsl:if test="inherit_location = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'inherit location')"/>
									</xsl:attribute>
								</input>
							</div>
							<xsl:call-template name="location_form"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view"/>
							<input type="hidden" id="street_name">
								<xsl:attribute name="value">
									<xsl:value-of select="street_name"/>
								</xsl:attribute>
							</input>
							<input type="hidden" id="street_number">
								<xsl:attribute name="value">
									<xsl:value-of select="street_number"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="suppressmeter =''">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_power_meter"/>
								</label>
								<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" class="pure-input-1-2">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_power_meter_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label for="delivery_address">
							<xsl:value-of select="php:function('lang', 'delivery address')"/>
						</label>
						<textarea  class="pure-input-1-2" rows="6" id="delivery_address" name="values[delivery_address]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'delivery address')"/>
							</xsl:attribute>
							<xsl:value-of select="value_delivery_address"/>
						</textarea>
					</div>
					<div id="gmap-container" class="pure-control-group" style="display:none">
						<label>
							<a href="" id="googlemaplink" style="color:#0000FF;text-align:left" target="_new">Vis større kart</a>
						</label>
						<iframe width="500" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="googlemapiframe" src="">
						</iframe>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'other projects')"/>
						</label>
						<div class="pure-custom">
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_7'">
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

			<div id="budget">
				<fieldset>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_start_date"/>
						</label>
						<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_start_date_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_end_date"/>
						</label>
						<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_end_date_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select an end date!')"/>

							</xsl:attribute>
						</input>
					</div>
					<!--xsl:call-template name="external_project_form"/-->
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external project')"/>
						</label>
						<input type="hidden" id="external_project_id" name="values[external_project_id]"  value="{value_external_project_id}"/>
						<input type="text" id="external_project_name" name="values[external_project_name]" value="{value_external_project_name}" class="pure-input-1-2"/>
						<div id="external_project_container"/>
					</div>

					<!--xsl:choose>
						<xsl:when test="ecodimb_data!=''">
							<xsl:call-template name="ecodimb_form"/>
						</xsl:when>
					</xsl:choose-->


					<div class="pure-control-group">
						<xsl:variable name="lang_dimb">
							<xsl:value-of select="php:function('lang', 'dimb')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_dimb"/>
						</label>
						<xsl:if test="mode='edit'">
							<input type="hidden" id="ecodimb" name="values[ecodimb]"  value="{ecodimb_data/value_ecodimb}"/>
						</xsl:if>
						<input type="text" id="ecodimb_name" name="values[ecodimb_name]" value="{ecodimb_data/value_ecodimb} {ecodimb_data/value_ecodimb_descr}" class="pure-input-1-2">
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_dimb"/>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
						</input>
						<div id="ecodimb_container"/>
					</div>
					<xsl:if test="b_account_data =''">
						<div class="pure-control-group">
							<xsl:variable name="lang_budget_account">
								<xsl:value-of select="php:function('lang', 'budget account group')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_budget_account"/>
							</label>
							<input type="hidden" id="b_account_group" name="values[b_account_group]"  value="{b_account_group_data/value_b_account_id}"/>
							<input type="text" id="b_account_group_name" name="values[b_account_group_name]" value="{b_account_group_data/value_b_account_name}" class="pure-input-1-2">
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_budget_account"/>
										</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</input>
							<div id="b_account_group_container"/>
						</div>
					</xsl:if>

					<xsl:if test="b_account_data !=''">
						<div class="pure-control-group">
							<xsl:variable name="lang_budget_account">
								<xsl:value-of select="php:function('lang', 'budget account')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_budget_account"/>
							</label>
							<input type="hidden" id="b_account_id" name="values[b_account_id]"  value="{b_account_data/value_b_account_id}"/>
							<input type="text" id="b_account_name" name="values[b_account_name]" value="{b_account_data/value_b_account_id} {b_account_data/value_b_account_name}" class="pure-input-1-2">
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_budget_account"/>
										</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</input>
							<div id="b_account_container"/>
						</div>
					</xsl:if>

					<!--xsl:choose>
						<xsl:when test="b_account_data!=''">
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:call-template name="b_account_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="b_account_view"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
					</xsl:choose-->
					<xsl:if test="value_project_id &gt; 0 and mode='edit'">
						<div class="pure-control-group">
							<label for="name">
								<xsl:value-of select="php:function('lang', 'move')"/>
							</label>
							<input type="text" data-validation="number" name="values[new_project_id]" value="">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'move budget and orders to another project')"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
							</input>
						</div>
					</xsl:if>
					<div class="pure-control-group">
						<xsl:variable name="lang_budget">
							<xsl:value-of select="php:function('lang', 'budget')"/>
						</xsl:variable>
						<label for="name">
							<xsl:value-of select="$lang_budget"/>
						</label>
						<div class="pure-custom">
							<div>
								<input data-validation="number" data-validation-allowing="float,negative" data-validation-decimal-separator="{$decimal_separator}" type="text" name="values[budget]" value="{value_budget}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter the budget')"/>
									</xsl:attribute>
									<xsl:choose>
										<xsl:when  test="not(value_project_id &gt; 0) and mode='edit'">
											<xsl:attribute name="data-validation">
												<xsl:text>required</xsl:text>
											</xsl:attribute>
											<xsl:attribute name="data-validation-error-msg">
												<xsl:value-of select="$lang_budget"/>
											</xsl:attribute>
										</xsl:when>
										<xsl:when  test="value_project_id &gt; 0 and not(check_for_budget &gt; 0) and mode='edit'">
											<xsl:attribute name="data-validation">
												<xsl:text>required</xsl:text>
											</xsl:attribute>
											<xsl:attribute name="data-validation-error-msg">
												<xsl:value-of select="$lang_budget"/>
											</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="data-validation-optional">
												<xsl:text>true</xsl:text>
											</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
								</input>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								<select name="values[budget_year]">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'year')"/>
									</xsl:attribute>
									<option value="0">
										<xsl:value-of select="php:function('lang', 'year')"/>
									</option>
									<xsl:apply-templates select="year_list/options"/>
								</select>
								<xsl:choose>
									<xsl:when test="project_type_id ='3'">
										<input type="checkbox" name="values[budget_reset_buffer]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'delete')"/>
												<xsl:text> </xsl:text>
												<xsl:value-of select="php:function('lang', 'buffer')"/>
												<xsl:text> </xsl:text>
												<xsl:value-of select="php:function('lang', 'budget')"/>
											</xsl:attribute>
										</input>
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="project_type_id !='3'">
										<select name="values[budget_periodization]">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'periodization')"/>
											</xsl:attribute>
											<option value="0">
												<xsl:value-of select="php:function('lang', 'periodization')"/>
											</option>
											<xsl:apply-templates select="periodization_list/options"/>
										</select>
										<input type="checkbox" name="values[budget_periodization_all]" value="True">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'all')"/>
												<xsl:text> </xsl:text>
												<xsl:value-of select="php:function('lang', 'periods')"/>
											</xsl:attribute>
										</input>
										<input type="checkbox" name="values[budget_periodization_activate]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'activate')"/>
											</xsl:attribute>
											<xsl:attribute name="checked" value="checked"/>
										</input>
									</xsl:when>
								</xsl:choose>
							</div>
						</div>
					</div>
					<xsl:choose>
						<xsl:when test="value_project_id > 0 ">
							<div class="pure-control-group">

								<label for="name">
									<xsl:value-of select="php:function('lang', 'budget')"/>
								</label>
								<div class="pure-custom">
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
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_project_id > 0 and mode='edit'">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'transfer')"/>
								</label>
								<div class="pure-custom">
									<table>
										<tr>
											<td>
												<xsl:value-of select="php:function('lang', 'amount')"/>
											</td>
											<td>
												<xsl:value-of select="php:function('lang', 'project')"/>
											</td>
											<td>
												<xsl:value-of select="php:function('lang', 'remark')"/>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" name="values[transfer_amount]" value="">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'amount to transfer')"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" name="values[transfer_target]" value="">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'target project')"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" name="values[transfer_remark]" value="">
													<xsl:attribute name="title">
														<xsl:value-of select="php:function('lang', 'remark')"/>
													</xsl:attribute>
												</input>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="project_type_id !='3'">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_reserve"/>
								</label>
								<input data-validation="number" data-validation-allowing="float,negative" data-validation-decimal-separator="{$decimal_separator}" type="text" name="values[reserve]" value="{value_reserve}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_reserve_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-optional">
										<xsl:text>true</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_sum"/>
								</label>
								<xsl:value-of select="value_sum"/>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_remainder"/>
								</label>
								<xsl:value-of select="value_remainder"/>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_reserve_remainder"/>
								</label>
								<xsl:value-of select="value_reserve_remainder"/>
								<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
								<xsl:text> % )</xsl:text>
							</div>
						</xsl:when>
						<xsl:otherwise>
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'year')"/>
						</label>
						<xsl:choose>
							<xsl:when test="value_project_id = 0">
								<xsl:value-of select="lang_no_workorders"/>
							</xsl:when>
							<xsl:otherwise>
								<select id = "order_time_span" name="order_time_span">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</xsl:attribute>
									<option value="0">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</option>
									<xsl:apply-templates select="order_time_span/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<xsl:if test="value_project_id > 0">

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
						<div class="pure-control-group">
							<label for="name">
								<xsl:value-of select="php:function('lang', 'invoice')"/>
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
						
						<div class="pure-control-group">
							<label for="name">
								<xsl:value-of select="php:function('lang', 'attachments')"/>
							</label>
							<div class="pure-custom">

								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_8'">
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

					</xsl:if>
				</fieldset>
			</div>

			<div id="coordination">
				<fieldset>
					<xsl:variable name="lang_contact_statustext">
						<xsl:value-of select="php:function('lang', 'click this link to select')"/>
					</xsl:variable>
					<div class="pure-control-group">
						<label for="name">
							<a href="javascript:notify_contact_lookup()" title="{$lang_contact_statustext}">
								<xsl:value-of select="php:function('lang', 'contact')"/>
							</a>
						</label>
						<input type="hidden" id="notify_contact" name="notify_contact" value="" title="{$lang_contact_statustext}"></input>
						<input type="hidden" name="notify_contact_name" value="" onClick="notify_contact_lookup();" readonly="readonly" title="{$lang_contact_statustext}"/>
					</div>
					<label for="name">
						<xsl:value-of select="php:function('lang', 'notify')"/>
					</label>
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
					<xsl:choose>
						<xsl:when test="suppresscoordination =''">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_branch"/>
								</label>
								<xsl:variable name="lang_branch_statustext">
									<xsl:value-of select="lang_branch_statustext"/>
								</xsl:variable>
								<select name="values[branch][]" multiple="multiple" title="{$lang_branch_statustext}" class="pure-input-1-2">
									<xsl:apply-templates select="branch_list"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_other_branch"/>
								</label>
								<input type="text" name="values[other_branch]" value="{value_other_branch}" class="pure-input-1-2">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_other_branch_statustext"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_key_fetch"/>
								</label>
								<xsl:variable name="lang_key_fetch_statustext">
									<xsl:value-of select="lang_key_fetch_statustext"/>
								</xsl:variable>
								<select name="values[key_fetch]" title="{$lang_key_fetch_statustext}" class="pure-input-1-2">
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
								<select name="values[key_deliver]" class="pure-input-1-2">
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
								<xsl:variable name="lang_key_responsible_statustext">
									<xsl:value-of select="lang_key_responsible_statustext"/>
								</xsl:variable>
								<select name="values[key_responsible]" class="pure-input-1-2">
									<option value="">
										<xsl:value-of select="lang_no_key_responsible"/>
									</option>
									<xsl:apply-templates select="key_responsible_list"/>
								</select>
							</div>
						</xsl:when>
					</xsl:choose>
				</fieldset>
			</div>

			<xsl:choose>
				<xsl:when test="value_project_id &gt; 0">
					<div id="documents">
						<fieldset>
							<label for="name">
								<xsl:value-of select="php:function('lang', 'files')"/>
							</label>
							<div>
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

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'upload files')"/>
								</label>

								<xsl:call-template name="multi_upload_file_inline">
									<xsl:with-param name="class">pure-input-3-4 pure-custom</xsl:with-param>
									<xsl:with-param name="multi_upload_action">
										<xsl:value-of select="multi_upload_action"/>
									</xsl:with-param>
								</xsl:call-template>
							</div>

						</fieldset>
					</div>
					<div id="history">
						<fieldset>
							<div>
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
						</fieldset>
					</div>
				</xsl:when>
			</xsl:choose>
			<xsl:call-template name="attributes_values"/>
		</div>

		<!--/div-->
	</form>
	
	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form name="done_form" id="done_form" method="post" action="{$done_action}"></form>
			
	<xsl:variable name="edit_action">
		<xsl:value-of select="edit_action"/>
	</xsl:variable>
	<form name="edit_form" id="edit_form" method="post" action="{$edit_action}"></form>
							
	<!-- AQUI VA EL SCRIPT -->
	<xsl:choose>
		<xsl:when test="mode='edit'">
			<xsl:variable name="add_sub_entry_action">
				<xsl:value-of select="add_sub_entry_action"/>
			</xsl:variable>
			<form method="post" name="add_sub_entry_form" action="{$add_sub_entry_action}">
			</form>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<!-- New template-->
<xsl:template match="branch_list">
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
<xsl:template match="key_responsible_list">
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

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="bulk_update_status">
	<input type="hidden" name="tab" value=""/>
	<form name="form" id="form" method="post" action="{update_action}" class= "pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'start date')"/>
						</label>
						<input type="text" id="values_start_date" name="start_date" size="10" value="{start_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_start_date_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'end date')"/>
						</label>
						<input type="text" id="values_end_date" name="end_date" size="10" value="{end_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_end_date_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'dimb')"/>
						</label>
						<select name="ecodimb">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="ecodimb_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_coordinator"/>
						</label>
						<select name="coordinator">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select user')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="new_coordinator">
							<xsl:value-of select="lang_new_coordinator"/>
						</label>
						<select name="new_coordinator">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select user')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:for-each select="user_list/options">
								<option value="{id}">
									<xsl:value-of disable-output-escaping="yes" select="name"/>
								</option>
							</xsl:for-each>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'type')"/>
						</label>
						<select name="type" onChange="this.form.submit();">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select type')"/>
							</xsl:attribute>
							<xsl:apply-templates select="type_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'status filter')"/>
						</label>
						<select name="status_filter">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select status')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select status')"/>
							</option>
							<xsl:apply-templates select="status_list_filter/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'status new')"/>
						</label>
						<select name="status_new">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select status')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select status')"/>
							</option>
							<xsl:apply-templates select="status_list_new/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'transfer budget')"/>
						</label>
						<select name="transfer_budget">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'transfer budget')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select year')"/>
							</option>
							<xsl:apply-templates select="year_list/options"/>
						</select>
					</div>
					<xsl:choose>
						<xsl:when test="check_paid = 1">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'paid')"/>
								</label>
								<input type="checkbox" name="paid" value="True">
									<xsl:if test="paid = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'workorder')"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="check_closed_orders = 1">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'closed')"/>
								</label>
								<input type="checkbox" name="closed_orders" value="True">
									<xsl:if test="closed_orders = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'projekt')"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label for="name"></label>
						<input type="submit" class="pure-button pure-button-primary" name="get_list">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'get list')"/>
							</xsl:attribute>
						</input>
						<input type="submit" class="pure-button pure-button-primary" name="execute" onClick="onActionsClick()">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'count')"/>
						</label>
						<xsl:value-of select="total_records"/>
					</div>
					<div class="pure-control-group">
						<label for="name"></label>
						<div class="pure-custom">
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
							<input type="hidden" id="id_to_update" name="id_to_update" value=""/>
							<input type="hidden" id="new_budget" name="new_budget" value=""/>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
	</form>
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
