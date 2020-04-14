
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="empty">
			<xsl:apply-templates select="empty"/>
		</xsl:when>
		<xsl:when test="summary">
			<xsl:apply-templates select="summary"/>
		</xsl:when>
		<xsl:when test="edit_inventory">
			<xsl:apply-templates select="edit_inventory"/>
		</xsl:when>
		<xsl:when test="add_inventory">
			<xsl:apply-templates select="add_inventory"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="summary">
	<div class="content">
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

					<div id="main">

						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'summary')"/>
							</legend>

							<div class="pure-control-group">
								<label for='location_name'>
									<xsl:value-of select="php:function('lang', 'location')"/>
								</label>
								<input type="hidden" id="location_code" name="location_code" />
								<input type="text" id="location_name" name="location_name" class="pure-input-3-4">
									<xsl:attribute name="required">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>
								<div id="location_container"/>
							</div>
						</fieldset>
					</div>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'create summary')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="save">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<!-- edit inventory -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit_inventory">
	<fieldset>
		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uientity.edit_inventory')" />
		</xsl:variable>
		<xsl:variable name="lang_inventory">
			<xsl:value-of select="php:function('lang', 'inventory')" />
		</xsl:variable>

		<form name="form" id="edit_inventory" action="{$action_url}" method="post" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<fieldset>
				<legend>
					<xsl:value-of select="system_location/descr"/>
					<xsl:text>::</xsl:text>
					<xsl:value-of select="php:function('lang', 'edit inventory')" />
				</legend>

				<xsl:call-template name="location_view"/>

				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'id')" />
					</label>
					<xsl:value-of select="item_id"/>
					<input type="hidden" name="location_id" value="{location_id}"/>
					<input type="hidden" name="id" value="{item_id}"/>
					<input type="hidden" name="inventory_id" value="{inventory_id}"/>
				</div>

				<div class="pure-control-group">
					<label for="unit_id">
						<xsl:value-of select="php:function('lang', 'unit')" />
					</label>
					<select id = 'unit_id' name="values[unit_id]" class="forms">
						<xsl:if test="lock_unit = 1">
							<xsl:attribute name="disabled" value="disabled"/>
						</xsl:if>
						<xsl:apply-templates select="unit_list/options"/>
					</select>
				</div>

				<div class="pure-control-group">
					<label for="old_inventory">
						<xsl:value-of select="$lang_inventory"/>
					</label>
					<xsl:value-of select="value_inventory"/>
				</div>

				<div class="pure-control-group">
					<label for="inventory">
						<xsl:value-of select="php:function('lang', 'new')" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="$lang_inventory"/>
					</label>
					<input type="text" id = 'inventory' name="values[inventory]" value="{value_inventory}" size="12">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_inventory_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'bookable')" />
					</label>
					<input type="checkbox" name="values[bookable]" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'bookable')"/>
						</xsl:attribute>
						<xsl:if test="bookable = '1'">
							<xsl:attribute name="checked">
								<xsl:text>checked</xsl:text>
							</xsl:attribute>
						</xsl:if>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'active from')"/>
					</label>
					<input type="text" id="active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_active_from_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'active to')"/>
					</label>
					<input type="text" id="active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_active_to_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'remark')" />
					</label>
					<textarea cols="60" rows="4" name="values[remark]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'remark')"/>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>
					</textarea>
				</div>

				<div class="pure-g">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="hidden" name="values[save]" value="1"/>
					<input type="submit" name="send" class="pure-button pure-button-primary" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save values and exit')"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onClick="parent.TINY.box.hide();">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Back to the list')"/>
						</xsl:attribute>
					</input>
				</div>
			</fieldset>
		</form>
	</fieldset>
</xsl:template>

<!-- add inventory -->
<xsl:template xmlns:php="http://php.net/xsl" match="add_inventory">
	<fieldset>
		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uientity.add_inventory')" />
		</xsl:variable>

		<form name="form" id="form" action="{$action_url}" method="post" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<fieldset>
				<xsl:call-template name="location_form"/>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'id')" />
					</label>
					<xsl:value-of select="item_id"/>
					<input type="hidden" name="location_id" value="{location_id}"/>
					<input type="hidden" name="id" value="{item_id}"/>
				</div>

				<div class="pure-control-group">
					<label for="unit_id">
						<xsl:value-of select="php:function('lang', 'unit')" />
					</label>
					<select id = 'unit_id' name="values[unit_id]" class="forms">
						<xsl:if test="lock_unit = 1">
							<xsl:attribute name="disabled" value="disabled"/>
						</xsl:if>
						<xsl:apply-templates select="unit_list/options"/>
					</select>
				</div>

				<div class="pure-control-group">
					<label for="inventory">
						<xsl:value-of select="php:function('lang', 'inventory')" />
					</label>
					<input type="text" id = 'inventory' name="values[inventory]" value="{value_inventory}" size="12">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_inventory_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'bookable')" />
					</label>
					<input type="checkbox" name="values[bookable]" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'bookable')"/>
						</xsl:attribute>
						<xsl:if test="bookable = '1'">
							<xsl:attribute name="checked">
								<xsl:text>checked</xsl:text>
							</xsl:attribute>
						</xsl:if>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'active from')"/>
					</label>
					<input type="text" id="active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_active_from_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'active to')"/>
					</label>
					<input type="text" id="active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_active_to_statustext"/>
						</xsl:attribute>
					</input>
				</div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'remark')" />
					</label>
					<textarea cols="60" rows="4" name="values[remark]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'remark')"/>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>
					</textarea>
				</div>

				<div class="pure-g">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save values')"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onClick="parent.TINY.box.hide();">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Back to the list')"/>
						</xsl:attribute>
					</input>
				</div>
			</fieldset>
		</form>
	</fieldset>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- add / edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		var location_id = '<xsl:value-of select="value_location_id"/>';
		var item_id = '<xsl:value-of select="value_id"/>';
		var get_files_java_url = <xsl:value-of select="get_files_java_url"/>;
		function set_tab(active_tab)
		{
			document.form.active_tab.value = active_tab;
		}
		<xsl:choose>
			<xsl:when test="mode = 'edit'">
				<xsl:value-of select="lookup_functions"/>
			</xsl:when>
		</xsl:choose>
	</script>

	<div id="entity_edit_tabview">
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
		<form id="form" name="form" action="{$form_action}" method="post" ENCTYPE="multipart/form-data" class= "pure-form pure-form-aligned">
			<input type="hidden" name="active_tab" value="{active_tab}"/>
			<table cellpadding="2" cellspacing="2" width="80%" align="left">
				<xsl:choose>
					<xsl:when test="value_id !=''">
						<tr>
							<td class="th_text" valign="top">
								<a href="{link_pdf}" target="_blank">PDF</a>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:if test="cat_list=''">
					<xsl:if test="mode = 'edit'">
						<tr>
							<td colspan="2" align="left">
								<xsl:call-template name="table_apply">
									<xsl:with-param	name="lean" select="lean"/>
									<xsl:with-param	name="cat_list" select="cat_list"/>
								</xsl:call-template>
							</td>
						</tr>
					</xsl:if>
				</xsl:if>
			</table>
			<table class="pure-table pure-table-bordered"  width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:for-each select="origin_list">
					<tr>
						<td class="th_text">
							<xsl:value-of select="name"/>
						</td>
						<td class="th_text">
							<a href="{link}" title="{statustext}">
								<xsl:value-of select="id"/>
							</a>
						</td>
					</tr>
				</xsl:for-each>
				<xsl:choose>
					<xsl:when test="value_ticket_id!=''">
						<tr>
							<td>
								<xsl:value-of select="lang_ticket"/>
							</td>
							<td class="th_text" align="left">
								<xsl:for-each select="value_ticket_id">
									<xsl:variable name="link_ticket">
										<xsl:value-of select="//link_ticket"/>&amp;id=<xsl:value-of select="id"/>
									</xsl:variable>
									<a href="{$link_ticket}" onMouseover="window.status='{//lang_ticket_statustext}';return true;" onMouseout="window.status='';return true;">
										<xsl:value-of select="id"/>
									</a>
									<xsl:text> </xsl:text>
								</xsl:for-each>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_entity"/>
					</td>
					<td class="th_text">
						<xsl:value-of select="entity_name"/>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_category"/>
						<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
						<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
					</td>
					<td class="th_text">
						<xsl:choose>
							<xsl:when test="cat_list=''">
								<xsl:value-of select="category_name"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:call-template name="cat_select"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="value_id!=''">
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_id"/>
							</td>
							<td>
								<xsl:value-of select="value_num"/>
								<input type="hidden" id="location_code" name="location_code" value="{location_code}"/>
								<input type="hidden" name="lookup_tenant" value="{lookup_tenant}"/>
								<input type="hidden" name="values[id]" value="{value_id}"/>
								<input type="hidden" name="values[num]" value="{value_num}"/>
							</td>
						</tr>
						<xsl:for-each select="value_origin">
							<tr>
								<td class="th_text" valign="top">
									<xsl:value-of select="descr"/>
								</td>
								<td>
									<table>
										<xsl:for-each select="data">
											<tr>
												<td class="th_text" align="left">
													<a href="{link}" title="{statustext}">
														<xsl:value-of select="id"/>
													</a>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<xsl:for-each select="value_origin">
							<tr>
								<td class="th_text" valign="top">
									<xsl:value-of select="descr"/>
								</td>
								<td>
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
								</td>
							</tr>
						</xsl:for-each>
					</xsl:otherwise>
				</xsl:choose>
			</table>

			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<xsl:choose>
					<xsl:when test="location_data2!=''">
						<div id="location">
							<fieldset>
								<xsl:choose>
									<xsl:when test="mode='edit'">
										<div class="pure-control-group">
											<xsl:variable name="lang_entity_group">
												<xsl:value-of select="php:function('lang', 'entity group')"/>
											</xsl:variable>
											<label>
												<xsl:value-of select="$lang_entity_group"/>
											</label>
											<select name="values[entity_group_id]" title="$lang_entity_group">
												<xsl:apply-templates select="entity_group_list/options"/>
											</select>
											<xsl:value-of select="entity_group_name"/>
										</div>

										<xsl:choose>
											<xsl:when test="org_unit='1'">
												<div class="pure-control-group">
													<label>
														<xsl:value-of select="php:function('lang', 'department')"/>
													</label>
													<div class="pure-custom">
														<div class="autocomplete">
															<input id="org_unit_id" name="org_unit_id" type="hidden" value="{value_org_unit_id}">
															</input>
															<input id="org_unit_name" name="org_unit_name" type="text" value="{value_org_unit_name}" title="{value_org_unit_name_path}" size='60'>
																<xsl:choose>
																	<xsl:when test="disabled!=''">
																		<xsl:attribute name="disabled">
																			<xsl:text>disabled</xsl:text>
																		</xsl:attribute>
																	</xsl:when>
																</xsl:choose>
															</input>
															<div id="org_unit_container"/>
														</div>
													</div>
												</div>
											</xsl:when>
										</xsl:choose>
										<xsl:call-template name="location_form2"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:call-template name="location_view2"/>
									</xsl:otherwise>
								</xsl:choose>
								<xsl:apply-templates select="attributes_general/attributes"/>
							</fieldset>
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

				<xsl:choose>
					<xsl:when test="attributes_group!=''">
						<xsl:call-template name="attributes_values"/>
					</xsl:when>
				</xsl:choose>

				<xsl:choose>
					<xsl:when test="files!='' or  fileupload = 1 and value_id!=''">
						<div id="files">
							<script type="text/javascript">
								var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
							</script>
							<fieldset>
								<div class="pure-control-group">
									<xsl:value-of select="//lang_files"/>
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

							<div class="pure-control-group ">
								<label for="name">
								</label>
								<div class="wrapperForGlider" style="display:none;">
									<div class="glider-contain">
										<div class="glider">
											<xsl:for-each select="content_images">
												<xsl:if test="img_url">
													<div>
														<img data-src="{img_url}" alt="{file_name}"/>
													</div>
												</xsl:if>
											</xsl:for-each>
										</div>
										<input type="button" role="button"  aria-label="Previous" class="glider-prev" value="«"></input>
										<input type="button" role="button" aria-label="Next" class="glider-next" value="»"></input>
										<div role="tablist" class="dots"></div>
									</div>
								</div>
							</div>

								<xsl:choose>
									<xsl:when test="value_id!='' and fileupload = 1 and mode = 'edit'">
										<xsl:call-template name="file_upload"/>
									</xsl:when>
								</xsl:choose>
							</fieldset>
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:for-each select="integration">
					<div id="{section}">
						<iframe id="{section}_content" width="100%" height="{height}">
							<p>Your browser does not support iframes.</p>
						</iframe>
					</div>
				</xsl:for-each>

				<xsl:choose>
					<xsl:when test="value_id!='' and documents =1">
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
					</xsl:when>
				</xsl:choose>

				<xsl:choose>
					<xsl:when test="value_id !='' and enable_bulk = 0">
						<div id="related">
							<div class="pure-control-group">
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
					<xsl:when test="enable_bulk = 1">
						<div id="inventory">
							<fieldset>
								<div class="pure-control-group">
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
								<xsl:choose>
									<xsl:when test="value_id!='' and mode = 'edit'">
										<xsl:variable name="lang_add_inventory">
											<xsl:value-of select="php:function('lang', 'add inventory')"/>
										</xsl:variable>
										<a href="javascript:showlightbox_add_inventory({value_location_id},{value_id})" title="{$lang_add_inventory}">
											<xsl:value-of select="$lang_add_inventory"/>
										</a>
									</xsl:when>
								</xsl:choose>
							</fieldset>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<xsl:choose>
				<xsl:when test="mode = 'edit'">
					<xsl:call-template name="table_apply">
						<xsl:with-param	name="lean" select="lean"/>
						<xsl:with-param	name="cat_list" select="cat_list"/>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="lang_edit">
						<xsl:value-of select="php:function('lang', 'edit')" />
					</xsl:variable>
					<xsl:variable name="lang_new_entity">
						<xsl:value-of select="php:function('lang', 'new')" />
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="edit_entity" value="{$lang_edit}" title = "{$lang_edit}"  onClick="document.load_edit_form.submit();"/>
					<!--input type="button" class="pure-button pure-button-primary" name="new_entity" value="{$lang_new_entity}" title = "{$lang_new_entity}" onClick="document.new_form.submit();"/-->
					<!--input class="pure-button pure-button-primary" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/-->
				</xsl:otherwise>
			</xsl:choose>
		</form>

		<xsl:variable name="edit_params">
			<xsl:text>menuaction:property.uientity.edit, id:</xsl:text>
			<xsl:value-of select="value_id"/>
			<xsl:text>, location_id:</xsl:text>
			<xsl:value-of select="value_location_id"/>
		</xsl:variable>
		<xsl:variable name="edit_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $edit_params )" />
		</xsl:variable>

		<form name="load_edit_form" id="load_edit_form" action="{$edit_url}" method="post">
		</form>



		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
		<xsl:choose>
			<xsl:when test="value_id!='' and lean !=1">
				<div class="pure-g">
					<xsl:choose>
						<xsl:when test="start_ticket!=''">
							<xsl:variable name="ticket_link">
								<xsl:value-of select="ticket_link"/>
							</xsl:variable>
							<form method="post" action="{$ticket_link}">
								<xsl:variable name="lang_start_ticket">
									<xsl:value-of select="lang_start_ticket"/>
								</xsl:variable>
								<input type="submit" name="location" class="pure-button pure-button-primary" value="{$lang_start_ticket}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_start_ticket_statustext"/>
									</xsl:attribute>
								</input>
							</form>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="start_project!=''">
							<xsl:variable name="project_link">
								<xsl:value-of select="project_link"/>
							</xsl:variable>
							<form method="post" action="{$project_link}">
								<xsl:variable name="lang_start_project">
									<xsl:value-of select="php:function('lang', 'generate new project')"/>
								</xsl:variable>
								<input type="submit" name="location" class="pure-button pure-button-primary" value="{$lang_start_project}">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')"/>
									</xsl:attribute>
								</input>
							</form>

							<xsl:variable name="add_to_project_link">
								<xsl:value-of select="add_to_project_link"/>
							</xsl:variable>
							<form method="post" action="{$add_to_project_link}">
								<xsl:variable name="lang_add_to_project">
									<xsl:value-of select="php:function('lang', 'add to project')"/>
								</xsl:variable>
								<input type="submit" name="location" class="pure-button pure-button-primary" value="{$lang_add_to_project}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')"/>
									</xsl:attribute>
								</input>
							</form>
						</xsl:when>
					</xsl:choose>
				</div>
			</xsl:when>
		</xsl:choose>
	</div>
	<div id="lightbox-placeholder" style="background-color:#000000;color:#FFFFFF;display:none">
		<div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center">
			<xsl:value-of select="php:function('lang', 'fileuploader')"/>
		</div>
		<div class="bd" style="text-align:center;"> </div>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="table_apply">
	<xsl:param name="lean" />
	<xsl:param name="cat_list" />
	<div class="proplist-col">
		<table>
			<tr>
				<xsl:if test="$cat_list =''">
					<xsl:if test="$lean!=1">
						<td valign="bottom">
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'save')"/>
							</xsl:variable>
							<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'save values and exit')"/>
								</xsl:attribute>
							</input>
						</td>
					</xsl:if>
				</xsl:if>
				<td valign="bottom">
					<xsl:variable name="lang_apply">
						<xsl:value-of select="php:function('lang', 'apply')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save and stay in form')"/>
						</xsl:attribute>
					</input>
				</td>
				<xsl:if test="$lean!=1">
					<td align="right" valign="bottom">
						<xsl:variable name="lang_cancel">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:variable>
						<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Back to the list')"/>
							</xsl:attribute>
						</input>
					</td>
				</xsl:if>
			</tr>
		</table>
	</div>

</xsl:template>

<!-- emtpy -->
<xsl:template match="empty">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td>
				<xsl:call-template name="cat_filter"/>
			</td>
		</tr>
		<tr>
			<td colspan="4" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:call-template name="table_header_entity"/>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="attributes_header">
	<tr class="th">
		<td class="th_text" width="15%" align="left">
			<xsl:value-of select="lang_name"/>
		</td>
		<td class="th_text" width="55%" align="right">
			<xsl:value-of select="lang_value"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template name="target">
	<xsl:choose>
		<xsl:when test="value_target!=''">
			<xsl:for-each select="value_target">
				<tr>
					<td class="th_text" valign="top">
						<xsl:value-of select="//lang_target"/>
					</td>
					<td>
						<table>
							<xsl:for-each select="data">
								<tr>
									<td class="th_text" align="left">
										<a href="{link}" title="{//lang_target_statustext}">
											<xsl:value-of select="type"/>
											<xsl:text> #</xsl:text>
											<xsl:value-of select="id"/>
										</a>
										<xsl:text> </xsl:text>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</td>
				</tr>
			</xsl:for-each>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="controller_integration">
	<xsl:param name="controller" />

</xsl:template>
