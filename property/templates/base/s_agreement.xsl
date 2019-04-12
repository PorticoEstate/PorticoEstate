
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:choose>
		<xsl:when test="add">
			<xsl:apply-templates select="add"/>
		</xsl:when>
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
		<xsl:when test="list_attribute">
			<xsl:apply-templates select="list_attribute"/>
		</xsl:when>
		<xsl:when test="edit_attrib">
			<xsl:apply-templates select="edit_attrib"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="list">
	<xsl:apply-templates select="menu"/>
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
				<xsl:when test="member_of_list != ''">
					<td align="left">
						<xsl:call-template name="filter_member_of"/>
					</td>
				</xsl:when>
			</xsl:choose>
			<td align="left">
				<xsl:call-template name="cat_filter"/>
			</td>
			<td align="left">
				<xsl:call-template name="filter_vendor"/>
			</td>
			<td align="right">
				<xsl:call-template name="search_field"/>
			</td>
			<td valign="top">
				<table>
					<tr>
						<td class="small_text" valign="top" align="left">
							<xsl:variable name="link_columns">
								<xsl:value-of select="link_columns"/>
							</xsl:variable>
							<xsl:variable name="lang_columns_help">
								<xsl:value-of select="lang_columns_help"/>
							</xsl:variable>
							<xsl:variable name="lang_columns">
								<xsl:value-of select="lang_columns"/>
							</xsl:variable>
							<a href="javascript:var w=window.open('{$link_columns}','','left=50,top=100,width=300,height=600')" onMouseOver="overlib('{$lang_columns_help}', CAPTION, '{$lang_columns}')" onMouseOut="nd()">
								<xsl:value-of select="lang_columns"/>
							</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="8" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:call-template name="table_header"/>
		<xsl:call-template name="values"/>
		<xsl:choose>
			<xsl:when test="table_add!=''">
				<xsl:apply-templates select="table_add"/>
			</xsl:when>
		</xsl:choose>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template name="table_header">
	<tr class="th">
		<xsl:for-each select="table_header">
			<td class="th_text" width="{with}" align="{align}">
				<xsl:choose>
					<xsl:when test="sort_link!=''">
						<a href="{sort}" onMouseover="window.status='{header}';return true;">
							<xsl:value-of select="header"/>
						</a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="header"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
		</xsl:for-each>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template name="values">
	<xsl:for-each select="values">
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
			<xsl:for-each select="row">
				<xsl:choose>
					<xsl:when test="link">
						<td class="small_text" align="center">
							<a href="{link}" onMouseover="window.status='{statustext}';return true;">
								<xsl:value-of select="text"/>
							</a>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td class="small_text" align="left">
							<xsl:value-of select="value"/>
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:choose>
				<xsl:when test="//acl_manage != '' and cost!=''">
					<td align="center">
						<input type="hidden" name="values[item_id][{item_id}]" value="{item_id}"/>
						<input type="hidden" name="values[id][{item_id}]" value="{index_count}"/>
						<input type="checkbox" name="values[select][{item_id}]" value="{cost}">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:for-each>
</xsl:template>

<!-- New template-->
<xsl:template name="values2">
	<xsl:for-each select="values">
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
			<xsl:for-each select="row">
				<xsl:choose>
					<xsl:when test="link">
						<td class="small_text" align="center">
							<a href="{link}" onMouseover="window.status='{statustext}';return true;">
								<xsl:value-of select="text"/>
							</a>
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td class="small_text" align="left">
							<xsl:value-of select="value"/>
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:choose>
				<xsl:when test="//acl_manage != '' and cost!=''">
					<input type="hidden" name="values[item_id][{item_id}]" value="{item_id}"/>
					<input type="hidden" name="values[id][{item_id}]" value="{index_count}"/>
					<input type="hidden" name="values[select][{item_id}]" value="{cost}"/>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:for-each>
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
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_add_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</form>
	</div>
</xsl:template>

<!-- add / edit -->
<xsl:template match="edit" xmlns:php="http://php.net/xsl">
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
			<form ENCTYPE="multipart/form-data" method="post" name="form" id="form" action="{$edit_url}" class="pure-form pure-form-aligned">
				<xsl:choose>
					<xsl:when test="value_s_agreement_id!=''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_id"/>
							</label>
							<xsl:value-of select="value_s_agreement_id"/>
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
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'please enter a name !')"/>
						</xsl:attribute>

					</input>
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
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'please enter a description!')"/>
						</xsl:attribute>
						<xsl:value-of select="value_descr"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_category"/>
					</label>
					<select id="cat_id" name="values[cat_id]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Select the category the s_agreement belongs to. To do not use a category select NO CATEGORY')"/>
						</xsl:attribute>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please select a category !')"/>
						</xsl:attribute>

						<option value=''>
							<xsl:value-of select="php:function('lang', 'no category')"/>
						</option>
						<xsl:apply-templates select="cat_list/options"/>
					</select>
				</div>
				<xsl:call-template name="vendor_form"/>
				<xsl:choose>
					<xsl:when test="member_of_list2 != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'member of')"/>
							</label>
							<label style="vertical-align:top;">
								<div id="member_of">
									<xsl:apply-templates select="member_of_list2"/>
								</div>
							</label>
						</div>
					</xsl:when>
				</xsl:choose>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_budget"/>
					</label>
					<input id="field_budget" type="text" name="values[budget]" value="{value_budget}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_budget_statustext"/>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_year"/>
					</label>
					<select name="values[year]" class="forms" title="{lang_year_statustext}">
						<xsl:apply-templates select="year"/>
					</select>
				</div>
				<xsl:call-template name="ecodimb_form"/>
				<xsl:call-template name="b_account_form"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_category"/>
					</label>
					<xsl:call-template name="categories"/>
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
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'start date')"/>
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
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'end date')"/>
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
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_budget"/>
					</label>
					<!-- DataTable 2 EDIT -->
					<!--div id="datatable-container_3"/-->
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
				<div class="pure-control-group">
					<xsl:call-template name="attributes_values"/>
				</div>
				<xsl:choose>
					<xsl:when test="files!=''">
						<!-- <xsl:call-template name="file_list"/> -->
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="//lang_files"/>
							</label>
							<!-- DataTable 2 EDIT -->
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
				<div class="pure-control-group">
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
                                                                        
					<xsl:variable name="lang_apply">
						<xsl:value-of select="lang_apply"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
                                                                        
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onClick="document.cancel_form.submit();">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
			</form>
			<xsl:variable name="cancel_url">
				<xsl:value-of select="cancel_url"/>
			</xsl:variable>
			<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
			<form method="post" name="alarm" action="{$edit_url}">
				<input type="hidden" name="values[entity_id]" value="{value_s_agreement_id}"/>
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_alarm"/>
						</label>
					</div>
					<!-- DataTable 0 EDIT -->
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
						<input type="hidden" id="agreementid" name="agreementid" value="{value_s_agreement_id}" />
						<input type="button" name="" value="Add" id="values[add_alarm]" onClick="onAddClick_Alarm('add_alarm');"/>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="items">
			<!--script type="text/javascript">
						var property_js = <xsl:value-of select="property_js"/>;
						var base_java_url = <xsl:value-of select="base_java_url"/>;
						var datatable = new Array();
						var myColumnDefs = new Array();
						var myButtons = new Array();
						var td_count = <xsl:value-of select="td_count"/>;

						<xsl:for-each select="datatable">
							datatable[<xsl:value-of select="name"/>] = [
								{
									values:<xsl:value-of select="values"/>,
									total_records: <xsl:value-of select="total_records"/>,
									is_paginator:  <xsl:value-of select="is_paginator"/>,
									permission:<xsl:value-of select="permission"/>,
									footer:<xsl:value-of select="footer"/>
								}
							]
						</xsl:for-each>

						<xsl:for-each select="myColumnDefs">
							myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
						</xsl:for-each>
						<xsl:for-each select="myButtons">
							myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
						</xsl:for-each>
			</script-->
			<xsl:choose>
				<xsl:when test="value_s_agreement_id!=''">
					<div class="pure-control-group">
						<form ENCTYPE="multipart/form-data" method="post" name="form" action="{link_import}">
							<input type="hidden" name="id" value="{value_s_agreement_id}"/>
							<div class="pure-control-group">
								<label title="{lang_detail_import_statustext}" style="cursor: help;">
									<xsl:value-of select="lang_import_detail"/>
								</label>
								<input type="file" name="importfile" size="40">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_detail_import_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
								<xsl:text> </xsl:text>
								<xsl:variable name="lang_import">
									<xsl:value-of select="lang_import"/>
								</xsl:variable>
								<input type="submit" name="detail_import" value="{$lang_import}">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_detail_import_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</div>
						</form>
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
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_update!=''">
					<xsl:variable name="update_action">
						<xsl:value-of select="update_action"/>
					</xsl:variable>
					<form method="post" name="form2" action="{$update_action}">
						<div class="pure-control-group">
							<input type="hidden" name="values[agreement_id]" value="{value_s_agreement_id}"/>
							<!-- DataTable 1 EDIT -->
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
							<div id="datatable-buttons_2" class="div-buttons">
								<input type="text" id="values_date" class="calendar-opt" name="values[date]" size="10" value="{date}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_date_statustext"/>
									</xsl:attribute>
								</input>
								<div style="width:25px;height:15px;position:relative;float:left;"></div>
								<input id="new_index" class="mybottonsUpdates" type="text" name="values[new_index]" size="12"/>
								<input id="hd_values[update]" class="" type="hidden" name="values[update]" value="Update"/>
								<input type="button" name="" value="Update" id="values[update]" onClick="onUpdateClickAlarm('update');"/>
							</div>
						</div>
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
					</form>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_s_agreement_id!=''">
					<!--table width="100%" cellpadding="2" cellspacing="2" align="center"-->
					<xsl:apply-templates select="table_add"/>
					<!--/table-->
				</xsl:when>
			</xsl:choose>
		</div>
	</div>
	<xsl:variable name="lang_budget_validation">
		<xsl:value-of select="php:function('lang', 'budget info')" />
	</xsl:variable>

	<script type="text/javascript">
		$('#b_account_id').attr("data-validation","budget").attr("data-validation-error-msg", "<xsl:value-of select="$lang_budget_validation" />");
		$('#field_ecodimb').attr("data-validation","budget").attr("data-validation-error-msg", "<xsl:value-of select="$lang_budget_validation" />");
		$('#global_category_id').attr("data-validation","budget").attr("data-validation-error-msg", "<xsl:value-of select="$lang_budget_validation" />");
		$('#field_budget').attr("data-validation","budget").attr("data-validation-error-msg", "<xsl:value-of select="$lang_budget_validation" />");

	</script>

</xsl:template>

<!-- add item / edit item -->
<xsl:template match="edit_item">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<script type="text/javascript">
		var property_js = <xsl:value-of select="property_js"/>;
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var datatable = new Array();
		var myColumnDefs = new Array();
		var myButtons = new Array();

		<xsl:for-each select="datatable">
			datatable[<xsl:value-of select="name"/>] = [
			{
			values:<xsl:value-of select="values"/>,
			total_records: <xsl:value-of select="total_records"/>,
			is_paginator:  <xsl:value-of select="is_paginator"/>,
			permission:<xsl:value-of select="permission"/>,
			footer:<xsl:value-of select="footer"/>
			}
			]
		</xsl:for-each>

		<xsl:for-each select="myColumnDefs">
			myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
		</xsl:for-each>
		<xsl:for-each select="myButtons">
			myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
		</xsl:for-each>
	</script>
	<div id="tab-content">
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<div id="general">
			<xsl:variable name="edit_url">
				<xsl:value-of select="edit_url"/>
			</xsl:variable>
			<form name="form" method="post" class="pure-form pure-form-aligned" action="{$edit_url}">
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
					<xsl:when test="value_s_agreement_id!=''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_agreement"/>
							</label>
							<xsl:value-of select="value_s_agreement_id"/>
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
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:call-template name="location_form"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_cost"/>
					</label>
					<input type="text" name="values[cost]" value="{value_cost}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cost_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
				<xsl:choose>
					<xsl:when test="attributes_group != ''">
						<div class="pure-control-group">
							<xsl:call-template name="attributes_values"/>
						</div>
					</xsl:when>
				</xsl:choose>
				<div class="pure-control-group">
					<xsl:variable name="lang_save">
						<xsl:value-of select="lang_save"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_apply">
						<xsl:value-of select="lang_apply"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<input type="submit"  class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
			</form>
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
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:variable name="update_action">
						<xsl:value-of select="update_action"/>
					</xsl:variable>
					<form method="post" name="form2" action="{$update_action}">
						<input type="hidden" name="values[agreement_id]" value="{value_s_agreement_id}"/>
						<input type="hidden" name="values[item_id]" value="{value_id}"/>
						<fieldset>
							<div id="contextmenu_0"/>
							<div class="pure-control-group">
								<!--div id="datatable-container_0"/></div-->
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
								<div id="datatable-buttons_0" class="div-buttons">
									<input type="text" class="calendar-opt" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_date_statustext"/>
										</xsl:attribute>
									</input>
									<div style="width:25px;height:15px;position:relative;float:left;"></div>
									<input type="hidden" id="agreementid" name="agreementid" value="{value_s_agreement_id}" />
									<input id="new_index" class="mybottonsUpdates" type="inputText" name="values[new_index]" size="12"/>
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
</xsl:template>

<!-- New template-->
<xsl:template match="table_update">
	<tr>
		<td>
			<div id="datatable-buttons_0">
				<input type="text" id="values_date" name="values[date]" class="actionsFilter" size="10" value="{date}" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_date_statustext"/>
					</xsl:attribute>
				</input>
			</div>
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
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
				{
				values:<xsl:value-of select="values"/>,
				total_records: <xsl:value-of select="total_records"/>,
				is_paginator:  <xsl:value-of select="is_paginator"/>,
				permission:<xsl:value-of select="permission"/>,
				footer:<xsl:value-of select="footer"/>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<!--div class="yui-content"-->
		<div id="general">
			<form ENCTYPE="multipart/form-data" method="post" name="form" action="" class="pure-form pure-form-aligned">
				<div class="pure-control-group">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_id"/>
						</label>
						<xsl:value-of select="value_s_agreement_id"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_name"/>
						</label>
						<xsl:value-of select="value_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea disabled="disabled" cols="60" rows="6" name="values[descr]">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_statustext"/>
								<xsl:text>'; return true;</xsl:text>
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
								<xsl:when test="selected='1'">
									<td>
										<xsl:value-of select="name"/>
									</td>
								</xsl:when>
							</xsl:choose>
						</xsl:for-each>
					</div>
					<xsl:call-template name="vendor_view"/>
					<xsl:call-template name="b_account_view"/>
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
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_budget"/>
						</label>
						<!--div id="datatable-container_3"/-->
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
						<xsl:when test="files!=''">
							<!-- <xsl:call-template name="file_list_view"/>-->
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="//lang_files"/>
								</label>
								<!-- DataTable 2 VIEW -->
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
						<xsl:when test="attributes_group != ''">
							<div clas="pure-control-group">
								<br></br>
								<xsl:call-template name="attributes_values"/>
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
								<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;">
									<xsl:apply-templates select="member_of_list"/>
								</select>
							</div>
						</xsl:when>
					</xsl:choose>
				</div>
				<fieldset  style="border: 1px solid ! important;">
					<div class="pure-control-group">
                                                            
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_alarm"/>
							</label>
						</div>
						<!--  DataTable 0 VIEW -->
						<div class="pure-control-group">
							<!--div id="datatable-container_0"/-->
							<div class="pure-custom" style="display:inline" >
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
						<!-- <xsl:call-template name="alarm_view"/> -->
                                                            
					</div>
				</fieldset>
			</form>
		</div>
		<div id="items">
			<xsl:choose>
				<xsl:when test="values!=''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_total_records"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="total_records"/>
						</label>
					</div>
					<!--  DataTable 1 VIEW -->
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
		<!--table width="80%" cellpadding="2" cellspacing="2" align="center">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<form name="form" method="post" action="{$edit_url}">
					<tr height="50">
						<td align="left" valign="bottom">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
							<input type="submit" name="values[cancel]" value="{$lang_cancel}">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
		</table-->
		<!--/div-->
	</div>
	<div class="proplist-col">
		<table width="80%" cellpadding="2" cellspacing="2" align="left">
			<xsl:variable name="edit_url">
				<xsl:value-of select="edit_url"/>
			</xsl:variable>
			<form name="form" method="post" action="{$edit_url}">
				<tr>
					<td align="left" valign="bottom">
						<xsl:variable name="lang_cancel">
							<xsl:value-of select="lang_cancel"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</form>
		</table>
	</div>
</xsl:template>

<!-- view item -->
<xsl:template match="view_item">
	<div id="tab-content">
		<xsl:value-of disable-output-escaping="yes" select="tabs"/>
		<div id="general">
			<script type="text/javascript">
				var property_js = <xsl:value-of select="property_js"/>;
				var base_java_url = <xsl:value-of select="base_java_url"/>;
				var datatable = new Array();
				var myColumnDefs = new Array();
				var myButtons = new Array();

				<xsl:for-each select="datatable">
					datatable[<xsl:value-of select="name"/>] = [
					{
					values:<xsl:value-of select="values"/>,
					total_records: <xsl:value-of select="total_records"/>,
					is_paginator:  <xsl:value-of select="is_paginator"/>,
					permission:<xsl:value-of select="permission"/>,
					footer:<xsl:value-of select="footer"/>
					}
					]
				</xsl:for-each>

				<xsl:for-each select="myColumnDefs">
					myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
				</xsl:for-each>
				<xsl:for-each select="myButtons">
					myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
				</xsl:for-each>
			</script>
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<form method="post" class="pure-form pure-form-aligned"  name="form">
				<xsl:choose>
					<xsl:when test="value_s_agreement_id!=''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_agreement"/>
							</label>
							<xsl:value-of select="value_s_agreement_id"/>
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
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:call-template name="location_view"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_cost"/>
					</label>
					<xsl:value-of select="value_cost"/>
				</div>
				<xsl:choose>
					<xsl:when test="attributes_group != ''">
						<div clas="pure-control-group">
							<br></br>
							<!--xsl:apply-templates select="attributes_view"/-->
							<xsl:call-template name="attributes_values"/>
						</div>
					</xsl:when>
				</xsl:choose>
			</form>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:variable name="update_action">
						<xsl:value-of select="update_action"/>
					</xsl:variable>
					<fieldset>
						<!--  DataTable 0 VIEW ITEM -->
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
							<div id="contextmenu_0"/>
						</div>
						<!--
						<xsl:call-template name="table_header"/>
						<xsl:call-template name="values2"/>
						-->
					</fieldset>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="edit_url">
				<xsl:value-of select="edit_url"/>
			</xsl:variable>
			<form name="form" method="post" class="pure-form pure-form-aligned" action="{$edit_url}">
				<div class="pure-control-group">
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="cancel" value="{$lang_cancel}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_add2">
	<tr>
		<td height="50">
			<xsl:variable name="add_action">
				<xsl:value-of select="add_action"/>
			</xsl:variable>
			<xsl:variable name="lang_add">
				<xsl:value-of select="lang_add"/>
			</xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_standardtext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</form>
		</td>
		<td height="50">
			<xsl:variable name="done_action">
				<xsl:value-of select="done_action"/>
			</xsl:variable>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<form method="post" action="{$done_action}">
				<input type="submit" name="add" value="{$lang_done}">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_standardtext"/>
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
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:apply-templates select="table_header_attrib"/>
		<xsl:apply-templates select="values_attrib"/>
		<xsl:apply-templates select="table_add2"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_attrib">
	<xsl:variable name="sort_sorting">
		<xsl:value-of select="sort_sorting"/>
	</xsl:variable>
	<xsl:variable name="sort_id">
		<xsl:value-of select="sort_id"/>
	</xsl:variable>
	<xsl:variable name="sort_name">
		<xsl:value-of select="sort_name"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="10%" align="left">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="1%" align="center">
			<xsl:value-of select="lang_datatype"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<a href="{$sort_sorting}">
				<xsl:value-of select="lang_sorting"/>
			</a>
		</td>
		<td class="th_text" width="1%" align="center">
			<xsl:value-of select="lang_search"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_delete"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values_attrib">
	<xsl:variable name="lang_up_text">
		<xsl:value-of select="lang_up_text"/>
	</xsl:variable>
	<xsl:variable name="lang_down_text">
		<xsl:value-of select="lang_down_text"/>
	</xsl:variable>
	<xsl:variable name="lang_attribute_attribtext">
		<xsl:value-of select="lang_delete_attribtext"/>
	</xsl:variable>
	<xsl:variable name="lang_edit_attribtext">
		<xsl:value-of select="lang_edit_attribtext"/>
	</xsl:variable>
	<xsl:variable name="lang_delete_attribtext">
		<xsl:value-of select="lang_delete_attribtext"/>
	</xsl:variable>
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
			<xsl:value-of select="column_name"/>
		</td>
		<td align="left">
			<xsl:value-of select="input_text"/>
		</td>
		<td align="left">
			<xsl:value-of select="datatype"/>
		</td>
		<td>
			<table align="left">
				<tr>
					<td>
						<xsl:value-of select="sorting"/>
					</td>
					<td align="left">
						<xsl:variable name="link_up">
							<xsl:value-of select="link_up"/>
						</xsl:variable>
						<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;">
							<xsl:value-of select="text_up"/>
						</a>
						<xsl:text> | </xsl:text>
						<xsl:variable name="link_down">
							<xsl:value-of select="link_down"/>
						</xsl:variable>
						<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;">
							<xsl:value-of select="text_down"/>
						</a>
					</td>
				</tr>
			</table>
		</td>
		<td align="center">
			<xsl:value-of select="search"/>
		</td>
		<td align="center">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>
			</xsl:variable>
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_attribtext}';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td align="center">
			<xsl:variable name="link_delete">
				<xsl:value-of select="link_delete"/>
			</xsl:variable>
			<a href="{$link_delete}" title="$lang_delete_attribtext">
				<xsl:value-of select="text_delete"/>
			</a>
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
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form method="post" action="{$form_action}">
				<tr>
					<td valign="top">
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<xsl:value-of select="lang_id"/>
							</xsl:when>
							<xsl:otherwise>
							</xsl:otherwise>
						</xsl:choose>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="value_id != ''">
								<xsl:value-of select="value_id"/>
							</xsl:when>
							<xsl:otherwise>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_column_name"/>
					</td>
					<td>
						<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_column_name_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_input_text"/>
					</td>
					<td>
						<input type="text" name="values[input_text]" value="{value_input_text}" size="60" maxlength="50">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_input_text_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_statustext"/>
					</td>
					<td>
						<textarea cols="60" rows="10" name="values[statustext]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_statustext_attribtext"/>
							</xsl:attribute>
							<xsl:value-of select="value_statustext"/>
						</textarea>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_datatype"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_datatype_statustext">
							<xsl:value-of select="lang_datatype_statustext"/>
						</xsl:variable>
						<select name="values[column_info][type]" class="forms" onMouseover="window.status='{$lang_datatype_statustext}'; return true;">
							<option value="">
								<xsl:value-of select="lang_no_datatype"/>
							</option>
							<xsl:apply-templates select="datatype_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_precision"/>
					</td>
					<td>
						<input type="text" name="values[column_info][precision]" value="{value_precision}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_precision_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_scale"/>
					</td>
					<td>
						<input type="text" name="values[column_info][scale]" value="{value_scale}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_scale_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_default"/>
					</td>
					<td>
						<input type="text" name="values[column_info][default]" value="{value_default}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_default_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_nullable"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_nullable_statustext">
							<xsl:value-of select="lang_nullable_statustext"/>
						</xsl:variable>
						<select name="values[column_info][nullable]" class="forms" onMouseover="window.status='{$lang_nullable_statustext}'; return true;">
							<option value="">
								<xsl:value-of select="lang_select_nullable"/>
							</option>
							<xsl:apply-templates select="nullable_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_list"/>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="value_list = 1">
								<input type="checkbox" name="values[list]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_list_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[list]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_list_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_include_search"/>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="value_search = 1">
								<input type="checkbox" name="values[search]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_include_search_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[search]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_include_search_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_history"/>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="value_history = 1">
								<input type="checkbox" name="values[history]" value="1" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_history_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[history]" value="1">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_history_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="multiple_choice != ''">
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'include as filter')"/>
							</td>
							<td>
								<input type="checkbox" name="values[table_filter]" value="1">
									<xsl:if test="value_table_filter = 1">
										<xsl:attribute name="checked">
											<xsl:text>checked</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'check to act as filter in list')"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_choice"/>
							</td>
							<td align="right">
								<xsl:call-template name="choice"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr height="50">
					<td>
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_attribtext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_attribtext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</div>
</xsl:template>

<!-- New template-->
<!-- datatype_list -->
<xsl:template match="datatype_list">
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
<!-- nullable_list -->
<xsl:template match="nullable_list">
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
<xsl:template match="year">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected='selected' or selected = 1">
			<option value="{$id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$id}">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
<!-- New template-->

<xsl:template match="member_of_list2">
	<input type="checkbox" name="values[member_of][]" value="{cat_id}">
		<xsl:if test="selected != ''">
			<xsl:attribute name="checked" value="checked"/>
		</xsl:if>
	</input>
	<xsl:value-of disable-output-escaping="yes" select="name"/>
	<br/>
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
