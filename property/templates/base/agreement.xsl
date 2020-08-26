  <!-- $Id$ -->
	<xsl:template match="data">
		<xsl:call-template name="jquery_phpgw_i18n"/>
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="edit_item">
				<xsl:apply-templates select="edit_item"/>
			</xsl:when>
			<xsl:when test="view_item">
				<xsl:apply-templates select="view_item"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="add_activity">
				<xsl:apply-templates select="add_activity"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->

	<xsl:template match="add_activity">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<form class="pure-form pure-form-aligned">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_id"/>
						</label>
						<xsl:value-of select="value_agreement_id"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_name"/>
						</label>
						<input type="text" disabled="disabled" name="values[name]" value="{value_name}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_name_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea cols="60" disabled="disabled" rows="6" name="values[descr]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_descr_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
				</form>
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<form name="form2" method="post" class="pure-form pure-form-aligned" action="{$add_action}" >
					<div class="pure-control-group">
						<div class="pure-custom" style="display:inherit !important;">
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
					<div class="pure-control-group">
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"/>
							</xsl:attribute>
						</input>
						<xsl:variable name="lang_apply">
							<xsl:value-of select="lang_apply"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_apply_statustext"/>
							</xsl:attribute>
						</input>
						<xsl:variable name="lang_cancel">
							<xsl:value-of select="lang_cancel"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_cancel_statustext"/>
							</xsl:attribute>
						</input>
					</div>
				</form>
			</div>
		</div>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_add">
		<div class="pure-control-group">

			<xsl:variable name="add_action">
				<xsl:value-of select="add_action"/>
			</xsl:variable>
			<xsl:variable name="lang_add">
				<xsl:value-of select="lang_add"/>
			</xsl:variable>
			<form method="post" action="{$add_action}">
				<input class="pure-button pure-button-primary" type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</form>
		</div>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<div class="pure-control-group">
					<form ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned" id="form" method="post" name="form" action="{$edit_url}">
						<fieldset>
							<xsl:choose>
								<xsl:when test="value_agreement_id!=''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="lang_id"/>
										</label>
										<xsl:value-of select="value_agreement_id"/>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_name"/>
								</label>

								<input type="text" name="values[name]" value="{value_name}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_name_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contract')"/>
								</label>
								<input type="text" name="values[contract_id]" value="{value_contract_id}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'contract')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_status"/>
								</label>
								<xsl:call-template name="status_select"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_descr"/>
								</label>
								<textarea cols="60" rows="6" name="values[descr]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_descr_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:value-of select="value_descr"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_category"/>
								</label>
								<xsl:call-template name="cat_select"/>
							</div>
							<xsl:call-template name="vendor_form"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_agreement_group"/>
								</label>

								<xsl:variable name="lang_agreement_group_statustext">
									<xsl:value-of select="lang_agreement_group_statustext"/>
								</xsl:variable>
								<select name="values[group_id]" class="forms" title="{$lang_agreement_group_statustext}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="lang_no_agreement_group"/>
									</option>
									<xsl:apply-templates select="agreement_group_list"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
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
								<label>
									<xsl:value-of select="lang_end_date"/>
								</label>

								<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_end_date_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_termination_date"/>
								</label>

								<input type="text" id="values_termination_date" name="values[termination_date]" size="10" value="{value_termination_date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_termination_date_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<xsl:choose>
								<xsl:when test="files!=''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="//lang_files"/>
										</label>
										<!-- DataTable 2 EDIT-->
										<!--div id="datatable-container_2"/-->
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
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="fileupload = 1">
									<xsl:call-template name="file_upload"/>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="member_of_list != ''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="lang_member_of"/>
										</label>
										<xsl:variable name="lang_member_of_statustext">
											<xsl:value-of select="lang_member_of_statustext"/>
										</xsl:variable>
										<select name="values[member_of][]" disabled="disabled" class="forms" multiple="multiple" title="{$lang_member_of_statustext}">
											<xsl:apply-templates select="member_of_list"/>
										</select>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="attributes_group != ''">
									<xsl:call-template name="attributes_values"/>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<xsl:variable name="lang_save">
									<xsl:value-of select="lang_save"/>
								</xsl:variable>
								<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_save_statustext"/>
									</xsl:attribute>
								</input>
								<!-- </td><td valign="bottom">  -->
								<xsl:variable name="lang_apply">
									<xsl:value-of select="lang_apply"/>
								</xsl:variable>
								<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_apply_statustext"/>
									</xsl:attribute>
								</input>
								<!-- </td><td align="right" valign="bottom">-->
								<xsl:variable name="lang_cancel">
									<xsl:value-of select="lang_cancel"/>
								</xsl:variable>
								<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onClick="document.cancel_form.submit();">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_cancel_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
					</form>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<form name="cancel_form" id="cancel_form" method="post" action="{$cancel_url}"></form>
				</div>
				<div class="pure-control-group">
					<form method="post" name="alarm" action="{$edit_url}">
						<input type="hidden" name="values[entity_id]" value="{value_agreement_id}"/>
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_alarm"/>
								</label>
							</div>
							<!-- DataTable 0  EDIT-->
							<div class="pure-control-group">

								<!--div id="datatable-container_0"/-->
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
							<div class="pure-control-group">
								<xsl:value-of select="alarm_data/add_alarm/lang_add_alarm"/>
								<xsl:text> : </xsl:text>
								<xsl:value-of select="alarm_data/add_alarm/lang_day_statustext"/>
								<xsl:value-of select="alarm_data/add_alarm/lang_hour_statustext"/>
								<xsl:value-of select="alarm_data/add_alarm/lang_minute_statustext"/>
								<xsl:value-of select="alarm_data/add_alarm/lang_user"/>
							</div>
							<div class="pure-control-group">

								<!--div id="datatable-buttons_1"/-->
								<select name="values[alarm_data/add_alarm/day_list]" class="form" title="{lang_days_statustext}" id="day_list">
									<xsl:apply-templates select="alarm_data/add_alarm/day_list"/>
								</select>

								<select name="values[alarm_data/add_alarm/hour_list]" class="form" title="{alarm_data/add_alarm/lang_hour_statustext}" id="hour_list">
									<xsl:apply-templates select="alarm_data/add_alarm/hour_list"/>
								</select>

								<select name="values[alarm_data/add_alarm/minute_list]" class="form" title="{alarm_data/add_alarm/lang_minute_statustext}" id="minute_list">
									<xsl:apply-templates select="alarm_data/add_alarm/minute_list"/>
								</select>

								<select name="values[alarm_data/add_alarm/user_list]" class="form" title="{alarm_data/add_alarm/lang_user}" id="user_list">
									<xsl:apply-templates select="alarm_data/add_alarm/user_list"/>
								</select>
								<input type="hidden" id="agreementid" name="agreementid" value="{value_agreement_id}" />
								<input type="button" name="" value="Add" id="values[add_alarm]" onClick="onAddClick_Alarm('add_alarm');"/>
							</div>
							<!-- <xsl:call-template name="alarm_form"/>  -->
						</fieldset>
					</form>
				</div>
			</div>
			<div id="items">
				<xsl:choose>
					<xsl:when test="table_update!=''">
						<xsl:variable name="update_action">
							<xsl:value-of select="update_action"/>
						</xsl:variable>
						<form method="post" name="form2" action="{$update_action}">
							<input type="hidden" name="values[agreement_id]" value="{value_agreement_id}"/>
							<div class="pure-control-group">
								<xsl:for-each select="set_column">

								</xsl:for-each>
								<xsl:variable name="link_download">
									<xsl:value-of select="link_download"/>
								</xsl:variable>
								<xsl:variable name="lang_download_help">
									<xsl:value-of select="lang_download_help"/>
								</xsl:variable>
								<xsl:variable name="lang_download">
									<xsl:value-of select="lang_download"/>
								</xsl:variable>
								<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
									<xsl:value-of select="lang_download"/>
								</a>
							</div>
							<!-- DataTable 1 EDIT_ITEMS-->
							<div class="pure-control-group">

								<div id="paging_1"> </div>
								<!--div id="datatable-container_1"/-->
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
								<div id="contextmenu_1"/>
							</div>
							<br/>
							<div class="pure-control-group">
								<!-- Buttons 2 -->
								<div id="datatable-buttons_2" class="div-buttons">
									<input class="mybottonsUpdates calendar-opt" type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_date_statustext"/>
										</xsl:attribute>
									</input>
									<div style="width:25px;height:15px;position:relative;float:left;"></div>
									<input id="new_index" class="mybottonsUpdates" type="inputText" name="values[new_index]" size="12"/>
									<input id="hd_values[update]" class="" type="hidden" name="values[update]" value="Update"/>
									<input type="button" name="" value="Update" id="values[update]" onClick="onUpdateClickIndex('update');"/>
								</div>
							</div>		<!-- <xsl:apply-templates select="table_update"/>  -->
						</form>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_agreement_id!=''">
						<!--table width="100%" cellpadding="2" cellspacing="2" align="center"-->
						<xsl:apply-templates select="table_add"/>
						<!--/table-->
					</xsl:when>
				</xsl:choose>
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
			height:50px;
			}
		</style>
		<script type="text/javascript">
			var base_java_url = <xsl:value-of select="base_java_url"/>;
		</script>
	</xsl:template>

	<!-- add item / edit item -->
	<xsl:template match="edit_item">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
			var base_java_url = <xsl:value-of select="base_java_url"/>;
		</script>
		<xsl:variable name="edit_url">
			<xsl:value-of select="edit_url"/>
		</xsl:variable>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<div align="left">
					<form name="form" class="pure-form pure-form-aligned" method="post" action="{$edit_url}">
						<dl>
							<xsl:choose>
								<xsl:when test="msgbox_data != ''">
									<dt>
										<xsl:call-template name="msgbox"/>
									</dt>
								</xsl:when>
							</xsl:choose>
						</dl>
						<xsl:choose>
							<xsl:when test="value_agreement_id!=''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_agreement"/>
									</label>
									<xsl:value-of select="value_agreement_id"/>
									<xsl:text> [</xsl:text>
									<xsl:value-of select="agreement_name"/>
									<xsl:text>] </xsl:text>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="value_id!=''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_id"/>
									</label>
									<xsl:value-of select="value_id"/>
									<xsl:if test="value_num !=''">
										<xsl:text> [</xsl:text>
										<xsl:value-of select="value_num"/>
										<xsl:text>] </xsl:text>
									</xsl:if>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_descr"/>
									</label>
									<xsl:value-of select="activity_descr"/>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_m_cost"/>
							</label>
							<input type="text" name="values[m_cost]" value="{value_m_cost}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_m_cost_statustext"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_w_cost"/>
							</label>
							<input type="text" name="values[w_cost]" value="{value_w_cost}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_w_cost_statustext"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_total_cost"/>
							</label>
							<xsl:value-of select="value_total_cost"/>
						</div>
						<xsl:choose>
							<xsl:when test="attributes_values != ''">
								<div class="pure-control-group">
									<xsl:call-template name="attributes_form"/>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<input type="hidden" name="values[index_count]" value="{index_count}"/>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_save_statustext"/>
								</xsl:attribute>
							</input>
							<xsl:variable name="lang_apply">
								<xsl:value-of select="lang_apply"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_apply_statustext"/>
								</xsl:attribute>
							</input>
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"/>
								</xsl:attribute>
							</input>
						</div>
					</form>
					<xsl:choose>
						<xsl:when test="values != ''">
							<xsl:variable name="update_action">
								<xsl:value-of select="update_action"/>
							</xsl:variable>
							<form method="post" name="form2" action="{$update_action}">
								<input type="hidden" name="values[agreement_id]" value="{value_agreement_id}"/>
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
								<fieldset>
									<div class="pure-control-group">
										<label>
											<br/>
										</label>
									</div>
									<!-- DataTable 0 EDIT_ITEM-->
									<div class="pure-control-group">
										<!--div id="datatable-container_0"></div-->
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
									<div class="pure-control-group">
										<label>
											<br/>
										</label>
									</div>
									<div class="pure-control-group">
										<div id="datatable-buttons_0" class="div-buttons">
											<input type="text" id="values_date" class="calendar-opt" name="values[date]" size="10" value="{date}" readonly="readonly">
												<xsl:attribute name="title">

													<xsl:value-of select="lang_date_statustext"/>

												</xsl:attribute>
											</input>
											<div style="width:25px;height:15px;position:relative;float:left;"></div>
											<input type="hidden" id="agreementid" name="agreementid" value="{value_agreement_id}" />
											<input id="new_index" class="mybottonsUpdates" type="inputText" name="values[new_index]" size="12"/>
											<input id="hd_values[update]" class="" type="hidden" name="values[update]" value="Update"/>
											<input type="button" name="" value="Update" id="values[update]" onClick="onUpdateClickItems('update_item');"/>
											<input type="button" name="" value="delete las index" id="values[delete]" onClick="onActionsClickDeleteLastIndex('delete_item');"/>
										</div>
									</div>
								</fieldset>

							</form>
						</xsl:when>
					</xsl:choose>
				</div>
			</div>
		</div>
	</xsl:template>

<!-- New template-->
	<xsl:template match="table_update">
		<tr>
			<td>
				<xsl:value-of select="lang_new_index"/>
				<input type="text" name="values[new_index]" size="12">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_new_index_statustext"/>
					</xsl:attribute>
				</input>
			</td>
			<td>
				<input type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_date_statustext"/>
					</xsl:attribute>
				</input>
			</td>
			<td height="50">
				<xsl:variable name="lang_update">
					<xsl:value-of select="lang_update"/>
				</xsl:variable>
				<input type="submit" name="values[update]" value="{$lang_update}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_update_statustext"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>

<!-- view -->
	<xsl:template match="view">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div class="yui-content">
				<div id="general">
					<div class="pure-control-group">
						<form ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned" id="form" method="post" name="form" action="">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_id"/>
								</label>
								<xsl:value-of select="value_agreement_id"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_name"/>
								</label>
								<xsl:value-of select="value_name"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contract')"/>
								</label>
								<xsl:value-of select="value_contract_id"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_status"/>
								</label>
								<xsl:for-each select="status_list">
									<xsl:choose>
										<xsl:when test="selected='selected' or selected = 1">
											<xsl:value-of select="name"/>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_descr"/>
								</label>
								<textarea disabled="disabled" cols="60" rows="6" name="values[descr]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_descr_statustext"/>
									</xsl:attribute>
									<xsl:value-of select="value_descr"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_category"/>
								</label>
								<xsl:for-each select="cat_list">
									<xsl:choose>
										<xsl:when test="selected='selected' or selected = 1">
											<xsl:value-of select="name"/>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</div>
							<xsl:call-template name="vendor_view"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_agreement_group"/>
								</label>
								<xsl:for-each select="agreement_group_list">
									<xsl:choose>
										<xsl:when test="selected='selected' or selected = 1">
											<xsl:value-of select="name"/>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_start_date"/>
								</label>
								<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_end_date"/>
								</label>
								<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_termination_date"/>
								</label>
								<input type="text" id="termination_date" name="termination_date" size="10" value="{value_termination_date}" readonly="readonly"/>
							</div>
							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list_view"/> -->
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="//lang_files"/>
										</label>

										<!-- DataTable 2 VIEW-->
										<!--div id="datatable-container_2"></div-->
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
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="attributes_view != ''">
									<div class="pure-control-group">
										<xsl:apply-templates select="attributes_view"/>
									</div>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="member_of_list != ''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="lang_member_of"/>
										</label>
										<xsl:variable name="lang_member_of_statustext">
											<xsl:value-of select="lang_member_of_statustext"/>
										</xsl:variable>
										<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" title="{$lang_member_of_statustext}">
											<xsl:apply-templates select="member_of_list"/>
										</select>
									</div>
								</xsl:when>
							</xsl:choose>
						</form>
					</div>
					<div class="pure-control-group">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_alarm"/>
								</label>
							</div>
							<div class="pure-control-group">
								<!--  DataTable 0 VIEW -->
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
						</fieldset>
					</div>
				</div>
				<div id="items">
					<xsl:choose>
						<xsl:when test="values!=''">
							<div class="pure-control-group">
								<xsl:variable name="link_download">
									<xsl:value-of select="link_download"/>
								</xsl:variable>
								<xsl:variable name="lang_download_help">
									<xsl:value-of select="lang_download_help"/>
								</xsl:variable>
								<xsl:variable name="lang_download">
									<xsl:value-of select="lang_download"/>
								</xsl:variable>
								<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')">
									<xsl:value-of select="lang_download"/>
								</a>
								<xsl:text> </xsl:text>
								<xsl:value-of select="lang_total_records"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="num_records"/>
							</div>
							<div class="pure-control-group">
								<div id="paging_1"> </div>
								<!--div id="datatable-container_1"/-->
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
								<div id="contextmenu_1"/>
							</div>
						</xsl:when>
					</xsl:choose>
				</div>
			</div>
		</div>
		<div class="proplist-col">
			<table cellpadding="2" cellspacing="2" align="left">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<form name="form" method="post" action="{$cancel_url}">
					<tr>
						<td align="left" valign="bottom">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
			</table>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var base_java_url = <xsl:value-of select="base_java_url"/>;
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
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_agreement_id!=''">
						<tr>
							<td align="left">
								<xsl:value-of select="lang_agreement"/>
							</td>
							<td align="left">
								<xsl:value-of select="value_agreement_id"/>
								<xsl:text> [</xsl:text>
								<xsl:value-of select="agreement_name"/>
								<xsl:text>] </xsl:text>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="value_id!=''">
						<tr>
							<td align="left">
								<xsl:value-of select="lang_id"/>
							</td>
							<td align="left">
								<xsl:value-of select="value_id"/>
								<xsl:text> [</xsl:text>
								<xsl:value-of select="value_num"/>
								<xsl:text>] </xsl:text>
							</td>
						</tr>
						<tr>
							<td align="left">
								<xsl:value-of select="lang_descr"/>
							</td>
							<td align="left">
								<xsl:value-of select="activity_descr"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_m_cost"/>
					</td>
					<td>
						<xsl:value-of select="value_m_cost"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_w_cost"/>
					</td>
					<td>
						<xsl:value-of select="value_w_cost"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_total_cost"/>
					</td>
					<td>
						<xsl:value-of select="value_total_cost"/>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="attributes_view != ''">
						<tr>
							<td colspan="2" width="50%" align="left">
								<xsl:apply-templates select="attributes_view"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="values != ''">
						<xsl:variable name="update_action">
							<xsl:value-of select="update_action"/>
						</xsl:variable>
						<br/>
						<!-- DataTable 0  VIEW_ITEMS-->
						<tr>
							<td colspan="2" width="50%" align="left">
								<br/>
								<!--div id="datatable-container_0"></div-->
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
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
			<xsl:variable name="edit_url">
				<xsl:value-of select="edit_url"/>
			</xsl:variable>
			<form name="form" method="post" action="{$edit_url}">
				<table width="80%" cellpadding="2" cellspacing="2" align="center">
					<tr height="50">
						<td align="left" valign="bottom">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
							<input type="submit" name="cancel"  class="pure-button pure-button-primary" value="{$lang_cancel}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var base_java_url = <xsl:value-of select="base_java_url"/>;
		</script>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="member_of_list">
		<xsl:variable name="id">
			<xsl:value-of select="cat_id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected' or selected = 1">
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
	<xsl:template match="agreement_group_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected = 1">
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
