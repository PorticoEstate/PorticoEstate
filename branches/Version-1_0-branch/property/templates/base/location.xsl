  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="update_cat">
				<xsl:apply-templates select="update_cat"/>
			</xsl:when>
			<xsl:when test="stop">
				<xsl:apply-templates select="stop"/>
			</xsl:when>
			<xsl:when test="summary">
				<xsl:apply-templates select="summary"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list">
		<xsl:choose>
			<xsl:when test="//lookup=1">
				<script type="text/javascript">
					function Exchange_values(thisform)
					{
						<xsl:value-of select="function_exchange_values"/>
					}
				</script>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="lookup=''">
				<xsl:apply-templates select="menu"/>
			</xsl:when>
		</xsl:choose>
		<table width="100%" cellpadding="0" cellspacing="1" align="center">
			<tr>
				<td>
					<!-- FILTER TABLE -->
					<table>
						<tr>
							<td>
								<xsl:call-template name="cat_filter"/>
							</td>
							<td align="left">
								<xsl:call-template name="filter_district"/>
							</td>
							<td>
								<xsl:call-template name="filter_part_of_town"/>
							</td>
							<xsl:choose>
								<xsl:when test="status_eco_list='' and lookup!=1">
									<td align="right">
										<xsl:call-template name="status_filter"/>
									</td>
								</xsl:when>
							</xsl:choose>
							<td align="left">
								<xsl:call-template name="owner_filter"/>
							</td>
							<td align="left">
								<xsl:call-template name="search_field"/>
							</td>
							<td align="left">
								<div id="paging"/>
							</td>
						</tr>
						<!-- /FILTER TABLE -->
					</table>
				</td>
			</tr>
			<!-- <tr>
<td colspan="{colspan}" width="100%">
<table width="100%">
<tr>


<td valign ="top">
<table>
<tr>
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
<td class="small_text" valign="top" align="left">
<xsl:variable name="link_columns"><xsl:value-of select="link_columns"/></xsl:variable>
<xsl:variable name="lang_columns_help"><xsl:value-of select="lang_columns_help"/></xsl:variable>
<xsl:variable name="lang_columns"><xsl:value-of select="lang_columns"/></xsl:variable>
<a href="javascript:var w=window.open('{$link_columns}','','left=50,top=100,width=300,height=600')"
onMouseOver="overlib('{$lang_columns_help}', CAPTION, '{$lang_columns}')"
onMouseOut="nd()">
<xsl:value-of select="lang_columns"/></a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan="{colspan}" width="100%">
<xsl:call-template name="nextmatchs"/>

</td>
</tr>-->
		</table>
		<div class="datatable-container">
			<table width="100%" class="datatable" cellpadding="2" cellspacing="2" align="center">
				<tr class="th">
					<xsl:choose>
						<xsl:when test="//lookup=1">
							<td>
							</td>
						</xsl:when>
					</xsl:choose>
					<xsl:for-each select="table_header">
						<td class="th_text" width="{with}" align="{align}">
							<xsl:choose>
								<xsl:when test="sort!=''">
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
				<xsl:call-template name="list_values"/>
				<xsl:choose>
					<xsl:when test="lookup='' and table_add !=''">
						<xsl:apply-templates select="table_add"/>
					</xsl:when>
				</xsl:choose>
			</table>
		</div>
		<xsl:call-template name="datatable-yui-definition"/>
		<xsl:choose>
			<xsl:when test="lookup=1">
				<table width="100%" cellpadding="2" cellspacing="2" align="center">
					<tr>
						<td colspan="3" width="100%">
							<xsl:call-template name="nextmatchs"/>
						</td>
					</tr>
				</table>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="list_values">
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
				<form>
					<xsl:choose>
						<xsl:when test="//lookup=1">
							<td>
								<xsl:for-each select="hidden">
									<input type="hidden" name="{name}" value="{value}"/>
								</xsl:for-each>
							</td>
						</xsl:when>
					</xsl:choose>
					<xsl:for-each select="row">
						<xsl:choose>
							<xsl:when test="link">
								<td class="small_text" align="center">
									<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;" target="{target}">
										<xsl:value-of select="text"/>
									</a>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td class="small_text" align="{align}">
									<xsl:value-of select="value"/>
								</td>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="lookup=1">
								<xsl:if test="position() = last()">
									<td class="small_text" align="center">
										<input type="button" name="select" value="{//lang_select}" onClick="{//exchange_values}" onMouseout="window.status='';return true;">
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
					</xsl:for-each>
				</form>
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
					<input type="submit" name="" value="{$lang_add}" onMouseout="window.status='';return true;">
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

	<!-- New template-->
	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			function street_lookup()
			{
				var oArgs = {<xsl:value-of select="street_link"/>};
				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
			function tenant_lookup()
			{
				var oArgs = {<xsl:value-of select="tenant_link"/>};
				var strURL = phpGWLink('index.php', oArgs);
				Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>

		<script type="text/javascript">
			function set_tab(active_tab)
			{
				document.form.active_tab.value = active_tab;			
			}

			var property_js = <xsl:value-of select="property_js"/>;
		//	var base_java_url = <xsl:value-of select="base_java_url"/>;
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

		<div class="yui-navset" id="location_edit_tabview">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form method="post" name="form" action="{$form_action}">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="100%" align="center">
							<xsl:choose>
								<xsl:when test="msgbox_data != ''">
									<tr>
										<td align="left" colspan="3">
											<xsl:call-template name="msgbox"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
						</table>
						<table cellpadding="2" cellspacing="2" width="80%" align="center">
							<xsl:choose>
								<xsl:when test="change_type_list != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_change_type"/>
										</td>
										<td valign="top">
											<xsl:variable name="lang_change_type_statustext">
												<xsl:value-of select="lang_change_type_statustext"/>
											</xsl:variable>
											<select name="change_type" class="forms" onMouseover="window.status='{$lang_change_type_statustext}'; return true;" onMouseout="window.status='';return true;">
												<option value="">
													<xsl:value-of select="lang_no_change_type"/>
												</option>
												<xsl:apply-templates select="change_type_list"/>
											</select>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="lookup_type='form'">
									<xsl:call-template name="location_form"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_view"/>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:for-each select="additional_fields">
								<tr>
									<td class="{class}" align="left" valign="top">
										<xsl:value-of select="input_text"/>
									</td>
									<td align="left">
										<xsl:choose>
											<xsl:when test="datatype ='text'">
												<textarea cols="60" rows="4" name="{input_name}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
													<xsl:value-of select="value"/>
												</textarea>
											</xsl:when>
											<xsl:when test="datatype ='date'">
												<input type="text" name="{input_name}" value="{value}" onFocus="{//dateformat_validate}" onKeyUp="{//onKeyUp}" onBlur="{//onBlur}" size="12" maxlength="10" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="descr"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<xsl:text>[</xsl:text>
												<xsl:value-of select="//lang_dateformat"/>
												<xsl:text>]</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<input type="text" name="{input_name}" value="{value}" size="{size}" onMouseout="window.status='';return true;">
													<xsl:attribute name="title">
														<xsl:value-of select="statustext"/>
													</xsl:attribute>
												</input>
											</xsl:otherwise>
										</xsl:choose>
									</td>
								</tr>
							</xsl:for-each>
							<tr>
								<td>
									<xsl:value-of select="lang_category"/>
								</td>
								<td>
									<xsl:call-template name="cat_select"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="edit_part_of_town = 1">
									<tr>
										<td>
											<xsl:value-of select="lang_part_of_town"/>
										</td>
										<td>
											<xsl:call-template name="select_part_of_town"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="edit_owner = 1">
									<tr>
										<td>
											<xsl:value-of select="lang_owner"/>
										</td>
										<td>
											<xsl:variable name="lang_owner_statustext">
												<xsl:value-of select="lang_owner_statustext"/>
											</xsl:variable>
											<select name="owner_id" class="forms" onMouseover="window.status='{$lang_owner_statustext}'; return true;" onMouseout="window.status='';return true;">
												<option value="">
													<xsl:value-of select="lang_select_owner"/>
												</option>
												<xsl:apply-templates select="owner_list"/>
											</select>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="edit_street = 1">
									<tr>
										<td>
											<a href="javascript:street_lookup()" onMouseover="window.status='{lang_select_street_help}';return true;" onMouseout="window.status='';return true;">
												<xsl:value-of select="lang_street"/>
											</a>
										</td>
										<td>
											<input type="hidden" name="street_id" value="{value_street_id}"/>
											<input size="30" type="text" name="street_name" value="{value_street_name}" onClick="street_lookup();" readonly="readonly">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_select_street_help"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
											<input size="4" type="text" name="street_number" value="{value_street_number}">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_street_num_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="edit_tenant = 1">
									<tr>
										<td>
											<a href="javascript:tenant_lookup()" onMouseover="window.status='{lang_tenant_statustext}';return true;" onMouseout="window.status='';return true;">
												<xsl:value-of select="lang_tenant"/>
											</a>
										</td>
										<td>
											<input type="hidden" name="tenant_id" value="{value_tenant_id}"/>
											<input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" onClick="tenant_lookup();" readonly="readonly">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_tenant_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
											<input size="{size_first_name}" type="text" name="first_name" value="{value_first_name}" onClick="tenant_lookup();" readonly="readonly">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_tenant_statustext"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:apply-templates select="attributes_general/attributes"/>
							<xsl:choose>
								<xsl:when test="entities_link != ''">
									<tr>
										<td valign="top">
											<xsl:value-of select="lang_related_info"/>
										</td>
										<td>
											<table width="100%" cellpadding="2" cellspacing="2" align="center">
												<xsl:apply-templates select="entities_link"/>
											</table>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
						</table>
					</div>
					<xsl:call-template name="attributes_values"/>
					<xsl:choose>
						<xsl:when test="roles != ''">
							<div id="roles">
								<table cellpadding="2" cellspacing="2" width="80%" align="left">
									<tr class="th">
										<td class="th_text">
											<xsl:value-of select="php:function('lang', 'role')"/>
										</td>
										<td class="th_text">
											<xsl:value-of select="php:function('lang', 'contact')"/>
										</td>
										<td class="th_text">
											<xsl:value-of select="php:function('lang', 'responsibility')"/>
										</td>
									</tr>
									<xsl:for-each select="roles">
										<tr>
											<td>
												<xsl:value-of select="name"/>
											</td>
											<td>
												<xsl:value-of select="responsibility_contact"/>
											</td>
											<td>
												<xsl:value-of select="responsibility_name"/>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="documents != ''">
							<div id="document">
								<!-- Some style for the expand/contract section-->
								<style>
									#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
									#treeDiv1 { background: #fff; padding:1em; margin-top:1em; }
								</style>
								<script type="text/javascript">
									var documents = <xsl:value-of select="documents"/>;
								</script>
								<!-- markup for expand/contract links -->
								<div id="expandcontractdiv">
									<a id="expand" href="#">
										<xsl:value-of select="lang_expand_all"/>
									</a>
									<xsl:text> </xsl:text>
									<a id="collapse" href="#">
										<xsl:value-of select="lang_collapse_all"/>
									</a>
								</div>
								<div id="treeDiv1"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="file_tree != ''">
							<div id="file_tree">
								<!-- Some style for the expand/contract section-->
								<style>
									#expandcontractdiv2 {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
									#treeDiv2 { background: #fff; padding:1em; margin-top:1em; }
								</style>
								<script type="text/javascript">
									var documents2 = <xsl:value-of select="file_tree"/>;
								</script>
								<!-- markup for expand/contract links -->
								<div id="expandcontractdiv2">
									<a id="expand2" href="#">
										<xsl:value-of select="lang_expand_all"/>
									</a>
									<xsl:text> </xsl:text>
									<a id="collapse2" href="#">
										<xsl:value-of select="lang_collapse_all"/>
									</a>
								</div>
								<div id="treeDiv2"/>
							</div>
						</xsl:when>
					</xsl:choose>
						<div id="related">
							<table cellpadding="2" cellspacing="2" width="80%" align="center">
								<tr>
									<td>
										<div id="datatable-container_0"/>
									</td>
								</tr>
							</table>
						</div>
					<xsl:for-each select="integration">
						<div id="{section}">
							<iframe id="{section}_content" width="100%" height="{height}">
								<p>Your browser does not support iframes.</p>
							</iframe>
						</div>
					</xsl:for-each>
				</div>
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<tr height="50">
						<xsl:choose>
							<xsl:when test="edit != ''">
								<td>
									<xsl:variable name="lang_save">
										<xsl:value-of select="lang_save"/>
									</xsl:variable>
									<input type="submit" name="save" value="{$lang_save}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_save_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="check_history != ''">
								<td>
									<xsl:variable name="lang_history">
										<xsl:value-of select="lang_history"/>
									</xsl:variable>
									<input type="submit" name="get_history" value="{$lang_history}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_history_statustext"/>
										</xsl:attribute>
									</input>
								</td>
							</xsl:when>
						</xsl:choose>
					</tr>
				</table>
			</form>
			<table>
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
									<xsl:value-of select="lang_done_statustext"/>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
			<xsl:choose>
				<xsl:when test="values != ''">
					<table width="100%" cellpadding="2" cellspacing="2" align="center">
						<xsl:call-template name="table_header_history"/>
						<xsl:call-template name="values_history"/>
					</table>
				</xsl:when>
			</xsl:choose>
		</div>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="owner_list">
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


	<!-- update_cat -->
	<xsl:template match="update_cat">
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
			<tr>
				<td align="center" colspan="2">
					<xsl:value-of select="lang_confirm_msg"/>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:variable name="update_action">
						<xsl:value-of select="update_action"/>
					</xsl:variable>
					<xsl:variable name="lang_yes">
						<xsl:value-of select="lang_yes"/>
					</xsl:variable>
					<form method="POST" action="{$update_action}">
						<input type="submit" class="forms" name="confirm" value="{$lang_yes}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_yes_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td align="right">
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<a href="{$done_action}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_no_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="lang_no"/>
					</a>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- stop -->
	<xsl:template match="stop">
		<xsl:apply-templates select="menu"/>
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
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="entities_link">
		<xsl:variable name="lang_entity_statustext">
			<xsl:value-of select="lang_entity_statustext"/>
		</xsl:variable>
		<xsl:variable name="entity_link">
			<xsl:value-of select="entity_link"/>
		</xsl:variable>
		<tr>
			<td class="small_text" align="left">
				<a href="{$entity_link}" onMouseover="window.status='{$lang_entity_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_entity"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="document_link">
		<xsl:variable name="lang_entity_statustext">
			<xsl:value-of select="lang_entity_statustext"/>
		</xsl:variable>
		<xsl:variable name="entity_link">
			<xsl:value-of select="entity_link"/>
		</xsl:variable>
		<tr>
			<td class="small_text" align="left">
				<a href="{$entity_link}" title="{$lang_entity_statustext}" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_entity"/>
				</a>
			</td>
		</tr>
	</xsl:template>


	<!-- New template-->
	<xsl:template match="summary">
		<xsl:apply-templates select="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="filter_district"/>
				</td>
				<td>
					<xsl:call-template name="filter_part_of_town"/>
				</td>
				<td align="center">
					<xsl:call-template name="owner_filter"/>
				</td>
				<td class="small_text" valign="top" align="left">
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
		</table>
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<tr class="th">
				<xsl:for-each select="table_header_summary">
					<td class="th_text" width="{with}" align="{align}">
						<xsl:value-of select="header"/>
					</td>
				</xsl:for-each>
			</tr>
			<xsl:call-template name="list_values"/>
		</table>
	</xsl:template>

	<!-- change_type_list -->
	<xsl:template match="change_type_list">
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
	<xsl:template name="table_header_history">
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
	<xsl:template name="values_history">
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
			</tr>
		</xsl:for-each>
	</xsl:template>

	<!-- New template-->
	<func:function name="phpgw:conditional">
		<xsl:param name="test"/>
		<xsl:param name="true"/>
		<xsl:param name="false"/>
		<func:result>
			<xsl:choose>
				<xsl:when test="$test">
					<xsl:value-of select="$true"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$false"/>
				</xsl:otherwise>
			</xsl:choose>
		</func:result>
	</func:function>
	<xsl:template name="datatable-yui-definition">
		<script type="text/javascript">
			var myColumnDefs = [
			<xsl:for-each select="//table_header">
				{
					key: "<xsl:value-of select="name"/>",
					label: "<xsl:value-of select="text"/>",
					resizeable:true,
					sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
					visible: <xsl:value-of select="phpgw:conditional(not(visible = 0), 'true', 'false')"/>
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
			];
		</script>
	</xsl:template>
