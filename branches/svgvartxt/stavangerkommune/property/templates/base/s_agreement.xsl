  <!-- $Id$ -->
	<xsl:template name="app_data">
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
							<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;">
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
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;">
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
							<input type="checkbox" name="values[select][{item_id}]" value="{cost}" onMouseout="window.status='';return true;">
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
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;">
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
		<tr>
			<td height="50">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
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
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<div class="yui-navset" id="edit_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div class="yui-content">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<div id="general">
					<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$edit_url}">
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
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
								<xsl:when test="value_s_agreement_id!=''">
									<tr>
										<td align="left">
											<xsl:value-of select="lang_id"/>
										</td>
										<td align="left">
											<xsl:value-of select="value_s_agreement_id"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_name"/>
								</td>
								<td>
									<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_name_statustext"/>
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
									<textarea cols="60" rows="6" name="values[descr]">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_descr_statustext"/>
										</xsl:attribute>
										<xsl:value-of select="value_descr"/>
									</textarea>
								</td>
							</tr>
							<tr>
								<td align="left">
									<xsl:value-of select="lang_category"/>
								</td>
								<td align="left">
									<xsl:call-template name="cat_select"/>
								</td>
							</tr>
							<xsl:call-template name="vendor_form"/>
							<xsl:choose>
								<xsl:when test="member_of_list2 != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'member of')"/>
										</td>
										<td valign="top">
											<div id="member_of">
												<xsl:apply-templates select="member_of_list2"/>
											</div>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_budget"/>
								</td>
								<td>
									<input type="text" name="values[budget]" value="{value_budget}" onMouseout="window.status='';return true;"><xsl:attribute name="title"><xsl:value-of select="lang_budget_statustext"/></xsl:attribute></input><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_year"/>
								</td>
								<td valign="top">
									<select name="values[year]" class="forms" title="{lang_year_statustext}">
										<xsl:apply-templates select="year"/>
									</select>
								</td>
							</tr>
							<xsl:call-template name="ecodimb_form"/>
							<xsl:call-template name="b_account_form"/>
							<tr>
								<td>
									<xsl:value-of select="lang_category"/>
								</td>
								<td>
									<xsl:call-template name="categories"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_start_date"/>
								</td>
								<td>
									<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_start_date_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_end_date"/>
								</td>
								<td>
									<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_end_date_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_termination_date"/>
								</td>
								<td>
									<input type="text" id="values_termination_date" name="values[termination_date]" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_termination_date_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td width="19%" align="left" valign="top">
									<xsl:value-of select="lang_budget"/>
								</td>
								<td>
									<!-- DataTable 2 EDIT -->
									<div id="datatable-container_3"/>
								</td>
							</tr>
						</table>
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
							<tr>
								<td>
									<xsl:call-template name="attributes_values"/>
								</td>
							</tr>
						</table>
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td width="19%" align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<!-- DataTable 2 EDIT -->
											<div id="datatable-container_2"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="fileupload = 1">
									<xsl:call-template name="file_upload"/>
								</xsl:when>
							</xsl:choose>
						</table>
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
							<tr height="50">
								<td valign="bottom">
									<xsl:variable name="lang_save">
										<xsl:value-of select="lang_save"/>
									</xsl:variable>
									<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_save_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
									<!-- </td> 
<td valign="bottom">-->
									<xsl:variable name="lang_apply">
										<xsl:value-of select="lang_apply"/>
									</xsl:variable>
									<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_apply_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
									<!-- </td> 
<td valign="bottom">-->
									<xsl:variable name="lang_cancel">
										<xsl:value-of select="lang_cancel"/>
									</xsl:variable>
									<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_cancel_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</form>
					<form method="post" name="alarm" action="{$edit_url}">
						<input type="hidden" name="values[entity_id]" value="{value_s_agreement_id}"/>
						<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
							<tr>
								<td width="79%" class="center" align="left">
									<br/>
								</td>
							</tr>
							<tr>
								<td width="79%" class="center" align="left">
									<xsl:value-of select="lang_alarm"/>
								</td>
							</tr>
							<!-- DataTable 0 EDIT -->
							<tr>
								<td class="center" align="left" colspan="10">
									<div id="datatable-container_0"/>
								</td>
							</tr>
							<tr>
								<td class="center" align="right" colspan="10">
									<div id="datatable-buttons_0"/>
								</td>
							</tr>
							<tr>
								<td class="center" align="left" colspan="10">
									<xsl:value-of select="alarm_data/add_alarm/lang_add_alarm"/>
									<xsl:text> : </xsl:text>
									<xsl:value-of select="alarm_data/add_alarm/lang_day_statustext"/>
									<xsl:value-of select="alarm_data/add_alarm/lang_hour_statustext"/>
									<xsl:value-of select="alarm_data/add_alarm/lang_minute_statustext"/>
									<xsl:value-of select="alarm_data/add_alarm/lang_user"/>
								</td>
							</tr>
							<tr>
								<td class="center" align="left" colspan="10">
									<div id="datatable-buttons_1"/>
								</td>
							</tr>
							<!-- <xsl:call-template name="alarm_form"/>  -->
						</table>
					</form>
				</div>
				<div id="items">
					<script type="text/javascript">
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
					</script>
					<table>
						<xsl:choose>
							<xsl:when test="value_s_agreement_id!=''">
								<tr>
									<td>
										<form ENCTYPE="multipart/form-data" method="post" name="form" action="{link_import}">
											<input type="hidden" name="id" value="{value_s_agreement_id}"/>
											<table cellpadding="2" cellspacing="2" width="90%" align="left">
												<tr>
													<td valign="top" title="{lang_detail_import_statustext}" style="cursor: help;">
														<xsl:value-of select="lang_import_detail"/>
													</td>
													<td>
														<input type="file" name="importfile" size="40" onMouseout="window.status='';return true;">
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
														<input type="submit" name="detail_import" value="{$lang_import}" onMouseout="window.status='';return true;">
															<xsl:attribute name="onMouseover">
																<xsl:text>window.status='</xsl:text>
																<xsl:value-of select="lang_detail_import_statustext"/>
																<xsl:text>'; return true;</xsl:text>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</table>
										</form>
									</td>
									<td class="small_text" valign="bottom" align="center">
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
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
					</table>
					<xsl:choose>
						<xsl:when test="table_update!=''">
							<xsl:variable name="update_action">
								<xsl:value-of select="update_action"/>
							</xsl:variable>
							<form method="post" name="form2" action="{$update_action}">
								<input type="hidden" name="values[agreement_id]" value="{value_s_agreement_id}"/>
								<!-- DataTable 1 EDIT -->
								<div id="paging_1"> </div>
								<div id="datatable-container_1"/>
								<div id="contextmenu_1"/>
								<div style="height:15px;"/>
								<div id="datatable-buttons_2" class="div-buttons">
									<input type="text" id="values_date" class="calendar-opt" name="values[date]" size="10" value="{date}" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_date_statustext"/>
										</xsl:attribute>
									</input>
									<div style="width:25px;height:15px;position:relative;float:left;"/>
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
										position:relative;
										float:left;
										width:750px;
										height:100px;
									}
								</style>
							</form>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_s_agreement_id!=''">
							<table width="100%" cellpadding="2" cellspacing="2" align="center">
								<xsl:apply-templates select="table_add"/>
							</table>
						</xsl:when>
					</xsl:choose>
				</div>
			</div>
		</div>
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
		<div class="yui-navset" id="edit_tabview">
			<div class="yui-content">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<form name="form" method="post" action="{$edit_url}">
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
							<xsl:when test="value_s_agreement_id!=''">
								<tr>
									<td align="left">
										<xsl:value-of select="lang_agreement"/>
									</td>
									<td align="left">
										<xsl:value-of select="value_s_agreement_id"/>
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
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<xsl:call-template name="location_form"/>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_cost"/>
							</td>
							<td>
								<input type="text" name="values[cost]" value="{value_cost}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_cost_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<xsl:choose>
							<xsl:when test="attributes_group != ''">
								<tr>
									<td colspan="2">
										<xsl:call-template name="attributes_values"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<tr height="50">
							<td valign="bottom">
								<xsl:variable name="lang_save">
									<xsl:value-of select="lang_save"/>
								</xsl:variable>
								<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_save_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</td>
							<td valign="bottom">
								<xsl:variable name="lang_apply">
									<xsl:value-of select="lang_apply"/>
								</xsl:variable>
								<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_apply_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</td>
							<td align="right" valign="bottom">
								<xsl:variable name="lang_cancel">
									<xsl:value-of select="lang_cancel"/>
								</xsl:variable>
								<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_cancel_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</table>
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
							<table cellpadding="2" cellspacing="2" width="79%" align="center" border="0">
								<tr>
									<td>
										<br/>
									</td>
								</tr>
								<!-- DataTable 0 EDIT_ITEM-->
								<div id="contextmenu_0"/>
								<tr>
									<td class="center" align="left" colspan="10">
										<div id="datatable-container_0"/>
									</td>
								</tr>
								<tr>
									<td>
										<br/>
									</td>
								</tr>
								<tr>
									<td class="center" align="left" colspan="10">
										<div id="datatable-buttons_0" class="div-buttons">
											<input type="text" class="calendar-opt" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_date_statustext"/>
												</xsl:attribute>
											</input>
											<div style="width:25px;height:15px;position:relative;float:left;"/>
										</div>
									</td>
								</tr>
							</table>
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
					<input type="text" id="values_date" name="values[date]" class="actionsFilter" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
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
		<div align="left">
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
			<table cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td>
						<table cellpadding="2" cellspacing="2" width="79%" align="center">
							<tr>
								<td align="left">
									<xsl:value-of select="lang_id"/>
								</td>
								<td align="left">
									<xsl:value-of select="value_s_agreement_id"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_name"/>
								</td>
								<td>
									<xsl:value-of select="value_name"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"/>
								</td>
								<td>
									<textarea disabled="disabled" cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
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
								<td align="left">
									<xsl:value-of select="lang_category"/>
								</td>
								<xsl:for-each select="cat_list">
									<xsl:choose>
										<xsl:when test="selected='selected'">
											<td>
												<xsl:value-of select="name"/>
											</td>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
							</tr>
							<xsl:call-template name="vendor_view"/>
							<xsl:call-template name="b_account_view"/>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_start_date"/>
								</td>
								<td>
									<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_end_date"/>
								</td>
								<td>
									<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_termination_date"/>
								</td>
								<td>
									<input type="text" id="termination_date" name="termination_date" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
								</td>
							</tr>
							<tr>
								<td width="19%" align="left" valign="top">
									<xsl:value-of select="lang_budget"/>
								</td>
								<td>
									<div id="datatable-container_3"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="files!=''">
									<!-- <xsl:call-template name="file_list_view"/>-->
									<tr>
										<td width="19%" align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<!-- DataTable 2 VIEW -->
											<div id="datatable-container_2"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
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
								<xsl:when test="member_of_list != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_member_of"/>
										</td>
										<!--<td valign="top">
<xsl:for-each select="member_of_list[selected='selected']" >
<xsl:value-of select="name"/>
<xsl:if test="position() != last()">, </xsl:if>
</xsl:for-each>
</td>-->
										<td>
											<xsl:variable name="lang_member_of_statustext">
												<xsl:value-of select="lang_member_of_statustext"/>
											</xsl:variable>
											<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
												<xsl:apply-templates select="member_of_list"/>
											</select>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table align="center">
							<tr>
								<td class="th_text" align="left" colspan="4">
									<xsl:value-of select="lang_alarm"/>
								</td>
							</tr>
							<!--  DataTable 0 VIEW -->
							<tr>
								<td align="left" colspan="4">
									<div id="datatable-container_0"/>
								</td>
							</tr>
							<!-- <xsl:call-template name="alarm_view"/> -->
						</table>
					</td>
				</tr>
			</table>
			<br/>
			<br/>
			<xsl:choose>
				<xsl:when test="values!=''">
					<table width="79%" cellpadding="2" cellspacing="2" align="center">
						<tr>
							<td align="center">
								<xsl:value-of select="lang_total_records"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="total_records"/>
							</td>
						</tr>
						<!--  DataTable 1 VIEW -->
						<tr>
							<td>
								<div id="paging_1"> </div>
								<div id="datatable-container_1"/>
								<div id="contextmenu_1"/>
							</td>
						</tr>
						<!--
<xsl:call-template name="table_header"/>
<xsl:call-template name="values"/>
-->
					</table>
				</xsl:when>
			</xsl:choose>
			<table width="80%" cellpadding="2" cellspacing="2" align="center">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<form name="form" method="post" action="{$edit_url}">
					<tr height="50">
						<td align="left" valign="bottom">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
							<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
		<div align="left">
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
					<xsl:when test="value_s_agreement_id!=''">
						<tr>
							<td align="left">
								<xsl:value-of select="lang_agreement"/>
							</td>
							<td align="left">
								<xsl:value-of select="value_s_agreement_id"/>
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
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:call-template name="location_view"/>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_cost"/>
					</td>
					<td>
						<xsl:value-of select="value_cost"/>
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
			</table>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:variable name="update_action">
						<xsl:value-of select="update_action"/>
					</xsl:variable>
					<table width="79%" cellpadding="2" cellspacing="2" align="center">
						<!--  DataTable 0 VIEW ITEM -->
						<tr>
							<td align="left" colspan="4">
								<div id="datatable-container_0"/>
								<div id="contextmenu_0"/>
							</td>
						</tr>
						<!--
<xsl:call-template name="table_header"/>
<xsl:call-template name="values2"/>
-->
					</table>
				</xsl:when>
			</xsl:choose>
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
							<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</table>
			</form>
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
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
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
					<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
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
							<a href="{$link_up}" onMouseover="window.status='{$lang_up_text}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_up"/>
							</a>
							<xsl:text> | </xsl:text>
							<xsl:variable name="link_down">
								<xsl:value-of select="link_down"/>
							</xsl:variable>
							<a href="{$link_down}" onMouseover="window.status='{$lang_down_text}';return true;" onMouseout="window.status='';return true;">
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
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_attribtext}';return true;" onMouseout="window.status='';return true;">
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
							<input type="text" name="values[column_name]" value="{value_column_name}" maxlength="20" onMouseout="window.status='';return true;">
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
							<input type="text" name="values[input_text]" value="{value_input_text}" size="60" maxlength="50" onMouseout="window.status='';return true;">
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
							<textarea cols="60" rows="10" name="values[statustext]" onMouseout="window.status='';return true;">
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
							<select name="values[column_info][type]" class="forms" onMouseover="window.status='{$lang_datatype_statustext}'; return true;" onMouseout="window.status='';return true;">
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
							<input type="text" name="values[column_info][precision]" value="{value_precision}" onMouseout="window.status='';return true;">
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
							<input type="text" name="values[column_info][scale]" value="{value_scale}" onMouseout="window.status='';return true;">
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
							<input type="text" name="values[column_info][default]" value="{value_default}" onMouseout="window.status='';return true;">
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
							<select name="values[column_info][nullable]" class="forms" onMouseover="window.status='{$lang_nullable_statustext}'; return true;" onMouseout="window.status='';return true;">
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
									<input type="checkbox" name="values[list]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_list_statustext"/>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[list]" value="1" onMouseout="window.status='';return true;">
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
									<input type="checkbox" name="values[search]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_include_search_statustext"/>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[search]" value="1" onMouseout="window.status='';return true;">
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
									<input type="checkbox" name="values[history]" value="1" checked="checked" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_history_statustext"/>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[history]" value="1" onMouseout="window.status='';return true;">
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
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
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
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
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
			<xsl:when test="selected='selected'">
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
			<xsl:when test="selected='selected'">
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
