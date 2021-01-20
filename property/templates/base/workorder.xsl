
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
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
					<input type="submit" class="pure-button pure-button-primary forms" name="add" value="{$lang_add}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_statustext"/>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<td>
				<xsl:variable name="search_action">
					<xsl:value-of select="search_action"/>
				</xsl:variable>
				<xsl:variable name="lang_search">
					<xsl:value-of select="lang_search"/>
				</xsl:variable>
				<form method="post" action="{$search_action}">
					<input type="submit" class="pure-button pure-button-primary forms" name="search" value="{$lang_search}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_search_statustext"/>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<td>
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" class="pure-button pure-button-primary forms" name="done" value="{$lang_done}">
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
		var lang = <xsl:value-of select="php:function('js_lang', 'next', 'save', 'Select branch', 'select user', 'select category')"/>;
	</script>

	<xsl:variable name="lang_done">
		<xsl:value-of select="lang_done"/>
	</xsl:variable>
	<xsl:variable name="lang_save">
		<xsl:value-of select="lang_save"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<table cellpadding="2" cellspacing="2" align="center">
		<div id="receipt"></div>
		<div id="message" class='message'/>

		<input type="hidden" id = "lean" name="lean" value="{lean}"/>
		<!--
		<xsl:choose>
			<xsl:when test="mode='edit' and lean = 0">
				<td>
					<table>
						<tr height="50">
							<td>
								<input type="button" class="pure-button pure-button-primary" name="save" value="{$lang_save}" onClick="submit_workorder();">
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
			<xsl:when test="value_workorder_id!= 0 and mode='edit' and lean = 0">
				<td>
					<table>
						<tr>
							<td valign="top">
								<xsl:variable name="lang_calculate">
									<xsl:value-of select="lang_calculate"/>
								</xsl:variable>
								<input type="button" class="pure-button pure-button-primary" name="calculate" value="{$lang_calculate}" onClick="calculate_order();">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_calculate_statustext"/>
									</xsl:attribute>
								</input>
							</td>
							<td valign="top">
								<xsl:variable name="lang_send">
									<xsl:value-of select="lang_send"/>
								</xsl:variable>
								<input type="button" class="pure-button pure-button-primary" name="send" value="{$lang_send}" onClick="send_order()">
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
		-->
	</table>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form ENCTYPE="multipart/form-data" method="post" id='form' name="form" action="{$form_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="send_workorder" value=""/>
		<input type="hidden" name='calculate_workorder'  value=""/>
		<input type="hidden" name='validatet_category' id="validatet_category" value="{validatet_category}"/>

		<xsl:variable name="decimal_separator">
			<xsl:value-of select="decimal_separator"/>
		</xsl:variable>

		<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="floating-box">
				<div id="submitbox">
					<table>
						<tbody>
							<tr>
								<xsl:choose>
									<xsl:when test="mode='edit' and lean = 0">
										<td>
											<input type="button" class="pure-button pure-button-primary" id="save_button" name="save" value="{$lang_save}" onClick="submit_workorder();">
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
									</xsl:when>
								</xsl:choose>
								<xsl:choose>
									<xsl:when test="value_workorder_id!= 0 and mode='edit' and lean = 0">
										<td valign="top">
											<xsl:variable name="lang_calculate">
												<xsl:value-of select="lang_calculate"/>
											</xsl:variable>
											<input type="button" class="pure-button pure-button-primary" id="calculate_button" name="calculate" value="{$lang_calculate}" onClick="calculate_order();">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_calculate_statustext"/>
												</xsl:attribute>
											</input>
										</td>
										<td>
											<xsl:variable name="lang_send">
												<xsl:value-of select="lang_send"/>
											</xsl:variable>
											<input type="button" class="pure-button pure-button-primary" name="send" value="{$lang_send}" onClick="send_order();">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_send_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</xsl:when>
								</xsl:choose>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
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
										<xsl:variable name="project_link">
											<xsl:value-of select="project_link"/>&amp;id=<xsl:value-of select="value_project_id"/>
										</xsl:variable>
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
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'related')"/>
						</label>
						<div class="pure-custom pure-input-3-4">
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

					<xsl:choose>
						<xsl:when test="value_workorder_id!= 0 and mode='edit'">
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
						<label for="delivery_address">
							<xsl:value-of select="php:function('lang', 'delivery address')"/>
						</label>
						<textarea  class="pure-input-3-4" rows="6" id="delivery_address" name="values[delivery_address]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'delivery address')"/>
							</xsl:attribute>
							<xsl:value-of select="value_delivery_address"/>
						</textarea>
					</div>

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
						<select name="values[user_id]" class="pure-input-3-4">
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
						<xsl:for-each select="branch_list[selected='selected' or selected = 1]">
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
					<xsl:choose>
						<xsl:when test="value_workorder_id!= 0">
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
						<xsl:otherwise>
							<xsl:for-each select="value_origin">
								<div class="pure-control-group">
									<label for="name">
										<xsl:value-of select="descr"/>
									</label>
									<table class="pure-custom">
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
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_title"/>
						</label>
						<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
						<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
						<input type="text" name="values[title]" value="{value_title}"  class="pure-input-3-4">
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
						<textarea  class="pure-input-3-4" rows="6" name="values[descr]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_descr_statustext"/>
							</xsl:attribute>
							<xsl:value-of disable-output-escaping="yes" select="value_descr"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_status"/>
						</label>
						<xsl:call-template name="status_select">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
						</xsl:call-template>
					</div>
					<xsl:choose>
						<xsl:when test="need_approval='1'">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'approval')"/>
								</label>
								<div id="approval_container" class="pure-custom  pure-input-3-4">
								</div>
							</div>
						</xsl:when>
					</xsl:choose>
					<!--xsl:choose>
						<xsl:when test="value_workorder_id!= 0">
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
					</xsl:choose-->
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_remark"/>
						</label>
						<textarea class="pure-input-3-4" rows="6" name="values[remark]">
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
							<!--xsl:call-template name="event_form"/-->
							<xsl:call-template name="vendor_form"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="vendor_view"/>
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contract')"/>
						</label>
						<select id="vendor_contract_id" name="values[contract_id]" class="pure-input-3-4">
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:if test="count(contract_list/options) &gt; 0">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>

							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="contract_list/options"/>
						</select>
					</div>

					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'send order')"/>
						</label>
						<div class="pure-custom pure-input-3-4">
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
						<input type="text" name="values[vendor_email][]" value="{value_extra_mail_address}" class="pure-input-3-4">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'The order will also be sent to this one')"/>
							</xsl:attribute>
							<xsl:if test="mode != 'edit'">
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
					<xsl:if test="enable_order_service_id = 1">
						<div class="pure-control-group">
							<xsl:variable name="lang_service">
								<xsl:value-of select="php:function('lang', 'service')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_service"/>
							</label>
							<input type="hidden" id="service_id" name="values[service_id]"  value="{value_service_id}"/>
							<input type="text" id="service_name" name="values[service_name]" value="{value_service_name}" class="pure-input-3-4">
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<!--xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_service"/>
										</xsl:attribute-->
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</input>
							<div id="service_container"/>
						</div>
					</xsl:if>
					<div class="pure-control-group">
						<xsl:variable name="lang_dimb">
							<xsl:value-of select="php:function('lang', 'dimb')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_dimb"/>
						</label>
						<xsl:if test="mode='edit'"><!-- and project_ecodimb =''">-->
							<input type="hidden" id="ecodimb" name="values[ecodimb]"  value="{ecodimb_data/value_ecodimb}"/>
						</xsl:if>
						<input type="text" id="ecodimb_name" name="values[ecodimb_name]" value="{ecodimb_data/value_ecodimb} {ecodimb_data/value_ecodimb_descr}" class="pure-input-3-4">
							<xsl:choose>
								<xsl:when test="mode='edit'">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_dimb"/>
									</xsl:attribute>
									<xsl:choose>
										<xsl:when test="project_ecodimb !=''">
											<xsl:attribute name="disabled">
												<xsl:text>disabled</xsl:text>
											</xsl:attribute>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="disabled">
										<xsl:text>disabled</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
						</input>
						<xsl:if test="mode='edit'">
							<input type="radio" id="ecodimb_edit" onClick="$('#ecodimb_name').prop('disabled', false);">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'edit')"/>
								</xsl:attribute>
							</input>
						</xsl:if>

						<div id="ecodimb_container"/>
					</div>
					<div class="pure-control-group">
						<xsl:variable name="lang_budget_account">
							<xsl:value-of select="php:function('lang', 'budget account')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_budget_account"/>
						</label>
						<xsl:choose>
							<xsl:when test="b_account_as_listbox = 1">
								<select name="values[b_account_id]" class="pure-input-3-4">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_budget_account"/>
									</xsl:attribute>
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
									<xsl:apply-templates select="b_account_list/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<input type="hidden" id="b_account_id" name="values[b_account_id]"  value="{b_account_data/value_b_account_id}">
								</input>
								<input type="text" id="b_account_name" name="values[b_account_name]" value="{b_account_data/value_b_account_id} {b_account_data/value_b_account_name}" class="pure-input-3-4">
									<xsl:choose>
										<xsl:when test="mode='edit'">
											<xsl:attribute name="data-validation">
												<xsl:text>budget_account</xsl:text>
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
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<xsl:if test="enable_unspsc = 1">
						<div class="pure-control-group">
							<xsl:variable name="lang_unspsc_code">
								<xsl:value-of select="php:function('lang', 'unspsc code')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_unspsc_code"/>
							</label>
							<input type="hidden" id="unspsc_code" name="values[unspsc_code]"  value="{value_unspsc_code}"/>
							<input type="text" id="unspsc_code_name" name="values[unspsc_code_name]" value="{value_unspsc_code} {value_unspsc_code_name}" class="pure-input-3-4">
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<!--xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_unspsc_code"/>
										</xsl:attribute-->
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</input>
							<div id="unspsc_code_container"/>
						</div>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="collect_building_part=1">
							<div class="pure-control-group">
								<xsl:variable name="lang_building_part">
									<xsl:value-of select="php:function('lang', 'building part')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_building_part"/>
								</label>
								<select name="values[building_part]" class="pure-input-3-4">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_building_part"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_building_part"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="$lang_building_part"/>
									</option>
									<xsl:apply-templates select="building_part_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_order_dim1">
									<xsl:value-of select="php:function('lang', 'order_dim1')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_order_dim1"/>
								</label>
								<select name="values[order_dim1]">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'order_dim1')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_order_dim1"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'order_dim1')"/>
									</option>
									<xsl:apply-templates select="order_dim1_list/options"/>
								</select>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:if test="collect_tax_code=1">

						<div class="pure-control-group">
							<xsl:variable name="lang_tax_code">
								<xsl:value-of select="php:function('lang', 'tax code')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_tax_code"/>
							</label>
							<select name="values[tax_code]">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_tax_code"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_tax_code"/>
								</xsl:attribute>
								<xsl:apply-templates select="tax_code_list/options"/>
							</select>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_cat_sub"/>
						</label>
						<div class="pure-custom pure-input-3-4">
							<xsl:call-template name="cat_sub_select">
								<xsl:with-param name="id">order_cat_id</xsl:with-param>
								<xsl:with-param name="class">pure-input-1</xsl:with-param>
							</xsl:call-template>
						</div>
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
						<input type="text" data-validation="number" data-validation-allowing="float,negative" data-validation-decimal-separator="{$decimal_separator}" id="field_contract_sum" name="values[contract_sum]" value="{value_contract_sum}">
							<xsl:attribute name="data-validation">
								<xsl:text>budget</xsl:text>
							</xsl:attribute>
							<!--xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute-->
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
						<input type="text" data-validation="number" data-validation-allowing="float,negative" data-validation-decimal-separator="{$decimal_separator}" id='field_budget' name="values[budget]" value="{value_budget}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_budget_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>budget</xsl:text>
							</xsl:attribute>

							<!--xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute-->
						</input>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="lang_addition_rs"/>
						</label>
						<input type="text" data-validation="number" data-validation-allowing="float,negative" data-validation-decimal-separator="{$decimal_separator}" name="values[addition_rs]" value="{value_addition_rs}">
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
						<div class="pure-custom pure-input-3-4">
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
						<xsl:value-of select="value_calculation"/>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
						<xsl:value-of select="lang_incl_tax"/>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'sum estimated cost')"/>
						</label>
						<xsl:value-of select="value_sum_estimated_cost"/>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
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
					<!--xsl:choose>
						<xsl:when test="value_workorder_id!= 0 and mode='edit'">
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'order received')"/>
								</label>
								<xsl:variable name="lang_receive_order">
									<xsl:value-of select="php:function('lang', 'receive order')"/>
								</xsl:variable>
								<input type="button" class="pure-button pure-button-primary" name="edit" value="{$lang_receive_order}" onClick="receive_order({value_workorder_id});">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_receive_order"/>
									</xsl:attribute>
									<xsl:if test="value_order_sent != 1">
										<xsl:attribute name="disabled">
											<xsl:text>disabled</xsl:text>
										</xsl:attribute>
									</xsl:if>
								</input>
								<div  class="pure-custom">
									<table>
										<tr>
											<td id="order_received_time">
												<xsl:value-of select="value_order_received"/>
											</td>
										</tr>
										<tr>
											<td align="right" id ="current_received_amount">
												<xsl:value-of select="value_order_received_amount"/>
											</td>
										</tr>
									</table>
								</div>
								<input  class="pure-custom" type="text" id="order_received_amount" size="6"/>
							</div>
						</xsl:when>
					</xsl:choose-->
					<xsl:choose>
						<xsl:when test="value_workorder_id!= 0">
							<div class="pure-control-group">
								<label for="name">
									<xsl:choose>
										<xsl:when test="mode='edit'">
											<xsl:variable name="lang_add_invoice_statustext">
												<xsl:value-of select="php:function('lang', 'add invoice')"/>
											</xsl:variable>
											<a href="javascript:showlightbox_manual_invoice({value_workorder_id})" title="{$lang_add_invoice_statustext}">
												<xsl:value-of select="php:function('lang', 'add invoice')"/>
											</a>
										</xsl:when>
									</xsl:choose>
								</label>
								<div class="pure-custom pure-input-3-4">
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
								<div class="pure-custom pure-input-3-4">
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
					</xsl:choose>
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
						<div class="pure-custom pure-input-3-4">
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
								<select name="values[key_fetch]" class="pure-input-3-4">
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
								<select name="values[key_deliver]" class="pure-input-3-4">
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
				<xsl:when test="value_workorder_id!= 0">
					<div id="documents">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'tags')"/>
								</label>

								<select id='tags' multiple="multiple">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</xsl:attribute>
									<xsl:apply-templates select="tag_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="//lang_files"/>
								</label>
								<div class="pure-custom pure-input-3-4">
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

							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="php:function('lang', 'attachments')"/>
								</label>
								<div class="pure-custom pure-input-3-4">
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

							<div class="pure-control-group ">
								<label for="name">
								</label>
								<div class="wrapperForGlider" style="display:none;">
									<div class="glider-contain">
										<div class="glider">
											<xsl:for-each select="image_list">
												<div>
													<img data-src="{image_url}" alt="{image_name}"/>
												</div>
											</xsl:for-each>
										</div>
										<input type="button" role="button"  aria-label="Previous" class="glider-prev" value="«"></input>
										<input type="button" role="button" aria-label="Next" class="glider-next" value="»"></input>
										<div role="tablist" class="dots"></div>
									</div>
								</div>
							</div>

						</fieldset>
					</div>
					<div id="history">
						<fieldset>
							<div style="width:90%; float: right;">
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
				var lang = <xsl:value-of select="php:function('js_lang', 'please enter either a budget or contrakt sum', 'next', 'save')"/>;
				var check_for_budget = <xsl:value-of select="check_for_budget"/>;
				var local_value_budget = <xsl:value-of select="local_value_budget"/>;
				var accumulated_budget_amount = <xsl:value-of select="accumulated_budget_amount"/>;
				var project_ecodimb = '<xsl:value-of select="project_ecodimb"/>';
				var base_java_url = <xsl:value-of select="base_java_url"/>;
				var location_item_id = '<xsl:value-of select="location_item_id"/>';
				var order_id = '<xsl:value-of select="value_workorder_id"/>';
				var project_id = '<xsl:value-of select="value_project_id"/>';
			</script>
		</div>
		<div class="proplist-col">
			<xsl:choose>
				<xsl:when test="mode='edit'">
					<input type="hidden" name="values[save]" value="1"/>
					<input type="button" class="pure-button pure-button-primary" id="save_button_bottom" name="save" value="{$lang_save}" onClick="submit_workorder();">
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

	<div>
		<dl>
			<dt>
				<xsl:value-of select="message"/>
			</dt>
		</dl>
		<dl>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</xsl:when>
			</xsl:choose>
		</dl>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" id="add_invoice" name="form" action="{$form_action}" ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned">
			<div id="invoice">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'location')"/>
					</label>
					<input name="values[location_code]" value="{location_code}" class="forms">
						<xsl:attribute name="readonly">
							<xsl:text>readonly</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<!--<xsl:call-template name="b_account_form"/>-->
				<div class="pure-control-group">
					<xsl:variable name="lang_budget_account">
						<xsl:value-of select="php:function('lang', 'budget account')"/>
					</xsl:variable>
					<label>
						<xsl:value-of select="$lang_budget_account"/>
					</label>
					<xsl:choose>
						<xsl:when test="b_account_as_listbox = 1">
							<select name="values[b_account_id]" class="pure-input-3-4">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_budget_account"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_budget_account"/>
								</xsl:attribute>
								<xsl:apply-templates select="b_account_list/options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<input type="hidden" id="b_account_id" name="values[b_account_id]"  value="{b_account_data/value_b_account_id}"/>
							<input type="text" id="b_account_name" name="values[b_account_name]" value="{b_account_data/value_b_account_id} {b_account_data/value_b_account_name}" class="pure-input-3-4">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_budget_account"/>
								</xsl:attribute>
							</input>
							<div id="b_account_container"/>
						</xsl:otherwise>
					</xsl:choose>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'external project')"/>
					</label>
					<input type="hidden" id="external_project_id" name="values[external_project_id]"  value="{value_external_project_id}"/>
					<input type="text" id="external_project_name" name="values[external_project_name]" value="{value_external_project_name}" class="pure-input-3-4"/>
					<div id="external_project_container"/>
				</div>


				<!--<xsl:call-template name="external_project_form"/>-->
				<!--<xsl:call-template name="ecodimb_form"/>-->
				<div class="pure-control-group">
					<xsl:variable name="lang_dimb">
						<xsl:value-of select="php:function('lang', 'dimb')"/>
					</xsl:variable>
					<label>
						<xsl:value-of select="$lang_dimb"/>
					</label>
					<xsl:if test="mode='edit' and project_ecodimb =''">
						<input type="hidden" id="ecodimb" name="values[ecodimb]"  value="{ecodimb_data/value_ecodimb}"/>
					</xsl:if>
					<input type="text" id="ecodimb_name" name="values[ecodimb_name]" value="{ecodimb_data/value_ecodimb} {ecodimb_data/value_ecodimb_descr}" class="pure-input-3-4">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="$lang_dimb"/>
						</xsl:attribute>
						<xsl:choose>
							<xsl:when test="project_ecodimb !=''">
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
							</xsl:when>
						</xsl:choose>
					</input>
					<div id="ecodimb_container"/>
				</div>
				<xsl:call-template name="vendor_form"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'contract')"/>
					</label>
					<select id="vendor_contract_id" name="values[contract_id]" class="pure-input-3-4">
						<xsl:if test="count(contract_list/options) &gt; 0">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<option value="">
							<xsl:value-of select="php:function('lang', 'select')"/>
						</option>
						<xsl:apply-templates select="contract_list/options"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'janitor')"/>
					</label>
					<select name="values[janitor]" class="forms">
						<option value="">
							<xsl:value-of select="php:function('lang', 'no janitor')"/>
						</option>
						<xsl:apply-templates select="janitor_list/options_lid"/>
					</select>
				</div>
				<!--				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'supervisor')"/>
					</label>
					<select name="values[supervisor]" class="forms">
						<option value="">
							<xsl:value-of select="php:function('lang', 'no supervisor')"/>
						</option>
						<xsl:apply-templates select="supervisor_list/options_lid"/>
					</select>
				</div>-->
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'B - responsible')"/>
					</label>
					<select name="values[budget_responsible]" class="forms">
						<option value="">
							<xsl:value-of select="php:function('lang', 'Select B-Responsible')"/>
						</option>
						<xsl:apply-templates select="budget_responsible_list/options_lid"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'order id')"/>
					</label>
					<input type="text" name="values[order_id]" value="{value_order_id}" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Order # that initiated the invoice')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'tax code')"/>
					</label>
					<select name="values[tax_code]" class="forms" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'tax code')"/>
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'select')"/>
						</option>
						<xsl:apply-templates select="tax_code_list/options"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<!--<xsl:value-of select="php:function('lang', 'art')"/>-->
						<xsl:value-of select="php:function('lang', 'Type invoice II')"/>

					</label>
					<select name="values[artid]" class="forms" >
						<xsl:attribute name="required">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<!--<xsl:value-of select="php:function('lang', 'You have to select type of invoice')"/>-->
							<xsl:value-of select="php:function('lang', 'Select the type  invoice. To do not use type -  select NO TYPE')"/>
						</xsl:attribute>
						<option value="">
							<!--<xsl:value-of select="php:function('lang', 'Select Invoice Type')"/>-->
							<xsl:value-of select="php:function('lang', 'No type')"/>
						</option>
						<xsl:apply-templates select="art_list/options"/>
					</select>
				</div>
				<!--				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Type invoice II')"/>
					</label>
					<select name="values[typeid]" class="forms">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Select the type  invoice. To do not use type -  select NO TYPE')"/>
						</xsl:attribute>
						<option value="">
							<xsl:value-of select="php:function('lang', 'No type')"/>
						</option>
						<xsl:apply-templates select="type_list/options"/>
					</select>
				</div>-->
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'voucher id')"/>
					</label>
					<input type="text" name="values[voucher_out_id]" value="{value_voucher_out_id}">
						<xsl:attribute name="required">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'voucher id')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Invoice Number')"/>
					</label>
					<input type="text" name="values[invoice_id]" value="{value_invoice_id}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Enter Invoice Number')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'KID nr')"/>
					</label>
					<input type="text" name="values[kidnr]" value="{value_kidnr}" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Enter Kid nr')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'amount')"/>
					</label>
					<input type="text" name="values[amount]" value="{value_amount}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'amount of the invoice')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'invoice date')"/>
					</label>
					<input type="text" id="invoice_date" name="values[invoice_date]" size="10" value="{value_invoice_date}" readonly="readonly" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Enter the invoice date')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'payment date')"/>
					</label>
					<input type="text" id="payment_date" name="values[payment_date]" size="10" value="{value_payment_date}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'payment date')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'paid')"/>
					</label>
					<input type="text" id="paid_date" name="values[paid_date]" size="10" value="{value_paid_date}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'paid')"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'remark')"/>
					</label>
					<textarea class="pure-input-3-4" rows="10" name="values[merknad]">
						<xsl:value-of select="value_merknad"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'upload file')"/>
					</label>
					<input type="file" id="file" name="file" class="pure-input-3-4">
					</input>
				</div>
			</div>
			<div class="pure-control-group">
				<xsl:variable name="lang_add">
					<xsl:value-of select="php:function('lang', 'add')"/>
				</xsl:variable>
				<input type="submit" class="pure-button pure-button-primary" name="add" value="{$lang_add}" title='{$lang_add}'>
				</input>
				<xsl:variable name="cancel_action">
					<xsl:value-of select="cancel_action"/>
				</xsl:variable>
			</div>
		</form>
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
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="lastname"/>
		<xsl:text>, </xsl:text>
		<xsl:value-of disable-output-escaping="yes" select="firstname"/>
	</option>
</xsl:template>

