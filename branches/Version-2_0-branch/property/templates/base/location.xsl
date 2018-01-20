  <!-- $Id$ -->
	<xsl:template match="data">
		<xsl:call-template name="jquery_phpgw_i18n"/>
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
		function street_lookup()
		{
		var oArgs = {<xsl:value-of select="street_link"/>};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
		function tenant_lookup()
		{
		var oArgs = {<xsl:value-of select="tenant_link"/>};
		var strURL = phpGWLink('index.php', oArgs);
		TINY.box.show({iframe:strURL, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
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
	<div id="location_edit_tabview">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" name="form" id="form" action="{$form_action}" class="pure-form pure-form-aligned">
			<input type="hidden" name="active_tab" value="{active_tab}"/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="general">
					<fieldset>
						<xsl:choose>
							<xsl:when test="change_type_list != ''">
								<div class="pure-control-group">
									<label for="name">
										<xsl:value-of select="lang_change_type"/>
									</label>
									<xsl:variable name="lang_change_type_statustext">
										<xsl:value-of select="lang_change_type_statustext"/>
									</xsl:variable>
									<select name="change_type" class="forms" title="{$lang_change_type_statustext}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_change_type_statustext"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="lang_no_change_type"/>
										</option>
										<xsl:apply-templates select="change_type_list"/>
									</select>
								</div>
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
							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="input_text"/>
								</label>
								<xsl:choose>
									<xsl:when test="datatype ='text'">
										<textarea cols="60" rows="4" name="{input_name}">
											<xsl:attribute name="title">
												<xsl:value-of select="statustext"/>
											</xsl:attribute>
											<xsl:value-of select="value"/>
										</textarea>
									</xsl:when>
									<xsl:when test="datatype ='date'">
										<input type="text" name="{input_name}" value="{value}" onFocus="{//dateformat_validate}" onKeyUp="{//onKeyUp}" onBlur="{//onBlur}" size="12" maxlength="10">
											<xsl:attribute name="title">
												<xsl:value-of select="descr"/>
											</xsl:attribute>
										</input>
										<xsl:text>[</xsl:text>
										<xsl:value-of select="//lang_dateformat"/>
										<xsl:text>]</xsl:text>
									</xsl:when>
									<xsl:otherwise>
										<input type="text" name="{input_name}" value="{value}" size="{size}">
											<xsl:attribute name="title">
												<xsl:value-of select="statustext"/>
											</xsl:attribute>
											<xsl:if test="required = 1">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="input_text"/>
												</xsl:attribute>
											</xsl:if>
										</input>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</xsl:for-each>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_category"/>
							</label>
							<xsl:call-template name="cat_select"/>
						</div>
						<xsl:choose>
							<xsl:when test="edit_part_of_town = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_part_of_town"/>
									</label>
									<xsl:variable name="lang_town_statustext">
										<xsl:value-of select="lang_town_statustext"/>
									</xsl:variable>
									<select name="part_of_town_id" title="{$lang_town_statustext}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="lang_no_part_of_town"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="lang_no_part_of_town"/>
										</option>
										<xsl:apply-templates select="part_of_town_list"/>
									</select>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="edit_owner = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_owner"/>
									</label>
									<xsl:variable name="lang_owner_statustext">
										<xsl:value-of select="lang_owner_statustext"/>
									</xsl:variable>
									<select name="owner_id" class="forms" title="{$lang_owner_statustext}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_owner_statustext"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="lang_select_owner"/>
										</option>
										<xsl:apply-templates select="owner_list"/>
									</select>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="edit_street = 1">
								<div class="pure-control-group">
									<label>
										<a href="javascript:street_lookup()" title="{lang_select_street_help}">
											<xsl:value-of select="lang_street"/>
										</a>
									</label>
									<input type="hidden" name="street_id" value="{value_street_id}"/>
									<input size="30" type="text" name="street_name" value="{value_street_name}" onClick="street_lookup();" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_select_street_help"/>
										</xsl:attribute>
									</input>
									<input size="4" type="text" name="street_number" value="{value_street_number}">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_street_num_statustext"/>
										</xsl:attribute>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="edit_tenant = 1">
								<div class="pure-control-group">
									<label>
										<a href="javascript:tenant_lookup()" title="{lang_tenant_statustext}">
											<xsl:value-of select="lang_tenant"/>
										</a>
									</label>
									<input type="hidden" name="tenant_id" value="{value_tenant_id}"/>
									<input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" onClick="tenant_lookup();" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_tenant_statustext"/>
										</xsl:attribute>
									</input>
									<input size="{size_first_name}" type="text" name="first_name" value="{value_first_name}" onClick="tenant_lookup();" readonly="readonly">
										<xsl:attribute name="title">
											<xsl:value-of select="lang_tenant_statustext"/>
										</xsl:attribute>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<xsl:apply-templates select="attributes_general/attributes"/>
						<xsl:choose>
							<xsl:when test="entities_link != ''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_related_info"/>
									</label>
									<div class="pure-custom">
										<xsl:apply-templates select="entities_link"/>
									</div>
								</div>
							</xsl:when>
						</xsl:choose>
					</fieldset>
				</div>
				<xsl:call-template name="attributes_values"/>
				<xsl:choose>
					<xsl:when test="roles != ''">
						<div id="roles">
							<fieldset>
								<table class="display cell-border compact responsive no-wrap dataTable dtr-inline">
									<thead>
										<tr role="row">
											<td>
												<xsl:value-of select="php:function('lang', 'role')"/>
											</td>
											<td>
												<xsl:value-of select="php:function('lang', 'contact')"/>
											</td>
											<td>
												<xsl:value-of select="php:function('lang', 'responsibility')"/>
											</td>
										</tr>
									</thead>
									<tbody>
										<xsl:for-each select="roles">
											<tr class="odd">
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
									</tbody>
									<tfoot>
										<tr>
											<th></th>
											<th></th>
											<th></th>
										</tr>
									</tfoot>
								</table>
							</fieldset>
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="documents != ''">
						<div id="document">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Doc type')" />
								</label>
								<select id="doc_type" name="doc_type">
									<xsl:apply-templates select="doc_type_filter/options"/>
								</select>
							</div>
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
					</xsl:when>
				</xsl:choose>
				<div id="related">
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
				<xsl:choose>
					<xsl:when test="check_history != ''">
						<script type="text/javascript">
							link_history = <xsl:value-of select="link_history"/>;
						</script>
						<div id="history">
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
					</xsl:when>
				</xsl:choose>
				<xsl:if test="controller=1">
					<div id="controller">
						<script type="text/javascript">
							lookup_control_responsible = function()
							{
							var oArgs = {menuaction:'property.uilookup.phpgw_user', column:'control_responsible', acl_app:'controller', acl_location: '.checklist', acl_required:4};
							var requestUrl = phpGWLink('index.php', oArgs);
							TINY.box.show({iframe:requestUrl, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
							}

							lookup_control = function()
							{
							var oArgs = {menuaction:'controller.uilookup.control'};
							var requestUrl = phpGWLink('index.php', oArgs);
							TINY.box.show({iframe:requestUrl, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
							}

							var location_id = <xsl:value-of select="location_id"/>;
							var item_id = <xsl:value-of select="item_id"/>;

						</script>
						<div id="controller_receipt"></div>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'new')" />
							</legend>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'user')" />
								</label>
								<input type="text" name="control_responsible" id="control_responsible" value="" onClick="lookup_control_responsible();" readonly="readonly" size="6">
								</input>
								<input size="30" type="text" name="control_responsible_user_name" id="control_responsible_user_name" value="" onClick="lookup_control_responsible();" readonly="readonly">
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'controller')" />
								</label>
								<input type="text" name="control_id" id="control_id" value="" onClick="lookup_control();" readonly="readonly" size="6">
								</input>
								<input type="text" name="control_name" id="control_name" value="" onClick="lookup_control();" readonly="readonly" size="30">
								</input>
							</div>
							<xsl:variable name="lang_add">
								<xsl:value-of select="php:function('lang', 'add')"/>
							</xsl:variable>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'start date')" />
								</label>

								<input type="text" name="control_start_date" id="control_start_date" value=""  readonly="readonly" size="10">
								</input>
							</div>
							<div class="pure-control-group">

								<label>
									<xsl:value-of select="php:function('lang', 'repeat type')" />
								</label>
								<select id="repeat_type" name="repeat_type">
									<option value="">
										<xsl:value-of select="php:function('lang', 'select')"/>
									</option>
									<xsl:apply-templates select="repeat_types/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'interval')" />
								</label>
								<input type="text" name="repeat_interval" id="repeat_interval" value="0" size="2">
								</input>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'controle time')" />
								</label>
								<input type="text" name="controle_time" id="controle_time" value="" size="">
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'service time')" />
								</label>
								<input type="text" name="service_time" id="service_time" value="" size="">
								</input>
							</div>
						</fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'controller')" />
							</label>
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

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'checklist')" />
							</label>
							<select id = "check_lst_time_span" name="check_lst_time_span">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</xsl:attribute>
								<option value="0">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="check_lst_time_span/options"/>
							</select>
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
								<xsl:value-of select="php:function('lang', 'cases')" />
							</label>
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
						<xsl:call-template name="controller_integration">
							<xsl:with-param name="controller" select ='controller'/>
						</xsl:call-template>
					</div>
				</xsl:if>
				<xsl:for-each select="integration">
					<div id="{section}">
						<fieldset>
							<iframe id="{section}_content" width="100%" height="{height}">
								<p>Your browser does not support iframes.</p>
							</iframe>
						</fieldset>
					</div>
				</xsl:for-each>
			</div>
			<div class="proplist-col">
				<xsl:choose>
					<xsl:when test="edit != ''">
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="save" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"/>
							</xsl:attribute>
						</input>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
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
	<div>
		<a href="{$entity_link}" onMouseover="window.status='{$lang_entity_statustext}';return true;" onMouseout="window.status='';return true;">
			<xsl:value-of select="text_entity"/>
		</a>
	</div>
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

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<xsl:template name="controller_integration">
	<xsl:param name="controller" />

</xsl:template>
