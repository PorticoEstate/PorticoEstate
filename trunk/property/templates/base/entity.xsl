  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="empty">
				<xsl:apply-templates select="empty"/>
			</xsl:when>
			<xsl:when test="edit_inventory">
				<xsl:apply-templates select="edit_inventory"/>
			</xsl:when>
			<xsl:when test="add_inventory">
				<xsl:apply-templates select="add_inventory"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>


	<!-- edit inventory -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit_inventory">
		<script type="text/javascript">
			function edit_inventory()
			{
				var location_id = '<xsl:value-of select="location_id"/>';
				var item_id = '<xsl:value-of select="item_id"/>';
				document.form.submit();
		//		parent.refresh_inventory(location_id, item_id);
				parent.TINY.box.hide();
			}
		</script>

	 <div align = 'left'>

		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uientity.edit_inventory')" />
		</xsl:variable>
		<xsl:variable name="lang_inventory">
				<xsl:value-of select="php:function('lang', 'inventory')" />
		</xsl:variable>

		<form name="form" id="edit_inventory" action="{$action_url}" method="post">

	 <fieldset>
		<legend>
			<xsl:value-of select="system_location/descr"/>
			<xsl:text>::</xsl:text>			
			<xsl:value-of select="php:function('lang', 'edit inventory')" />
		</legend>

			<div id="receipt"></div>
			<table>
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
					<td>
					<label><xsl:value-of select="php:function('lang', 'id')" /></label>
					</td>
					<td>
						<xsl:value-of select="item_id"/>
						<input type="hidden" name="location_id" value="{location_id}"/>
						<input type="hidden" name="id" value="{item_id}"/>
						<input type="hidden" name="inventory_id" value="{inventory_id}"/>
					</td>
				</tr>

				<xsl:call-template name="location_view"/>
				<tr>
					<td>
						<label for="unit_id"><xsl:value-of select="php:function('lang', 'unit')" /></label>
					</td>
					<td>
						<select id = 'unit_id' name="values[unit_id]" class="forms">
							<xsl:if test="lock_unit = 1">
								<xsl:attribute name="disabled" value="disabled"/>
							</xsl:if>
							<xsl:apply-templates select="unit_list/options"/>
						</select>
					</td>
					</tr>
					<tr>
					<td>
						<label for="old_inventory">
							<xsl:value-of select="$lang_inventory"/>
						</label>
					</td>
					<td>
						<xsl:value-of select="value_inventory"/>
					</td>
					</tr>
					<tr>
					<td>
						<label for="inventory">
							<xsl:value-of select="php:function('lang', 'new')" />
							<xsl:text> </xsl:text>
							<xsl:value-of select="$lang_inventory"/>
						</label>
					</td>
					<td>

						<input type="text" id = 'inventory' name="values[inventory]" value="{value_inventory}" size="12">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_inventory_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>

					<tr>
					<td>
						<label><xsl:value-of select="php:function('lang', 'bookable')" /></label>
					</td>
					<td>
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
					</td>
					</tr>
					<tr>
					<td>
						<label>
							<xsl:value-of select="php:function('lang', 'active from')"/>
						</label>
					</td>
					<td>
						<input type="text" id="active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_active_from_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
					<tr>
					<td>
					<label>
						<xsl:value-of select="php:function('lang', 'active to')"/>
					</label>
					</td>
					<td>
						<input type="text" id="active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_active_to_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
					<tr>

					<td>
						<label><xsl:value-of select="php:function('lang', 'remark')" /></label>
					</td>
					<td>
						<textarea cols="60" rows="4" name="values[remark]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>
						</textarea>
					</td>
					</tr>
			</table>
			 </fieldset>
		<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="hidden" name="values[save]" value="1"/>
					<input type="submit" name="send" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save values and exit')"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:variable>
					<input type="button" name="values[cancel]" value="{$lang_cancel}" onClick="parent.TINY.box.hide();">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_cancel"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>

			</form>


	 </div>
	</xsl:template>


	<!-- add inventory -->
	<xsl:template xmlns:php="http://php.net/xsl" match="add_inventory">

	 <div align = 'left'>

		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uientity.add_inventory')" />
		</xsl:variable>

		<form name="form" id="form" action="{$action_url}" method="post">

	 <fieldset>
		<legend>
			<xsl:value-of select="system_location/descr"/>
			<xsl:text>::</xsl:text>			
			<xsl:value-of select="php:function('lang', 'add inventory')" />
		</legend>

			<table>
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
					<td>
					<label><xsl:value-of select="php:function('lang', 'id')" /></label>
					</td>
					<td>
						<xsl:value-of select="item_id"/>
						<input type="hidden" name="location_id" value="{location_id}"/>
						<input type="hidden" name="id" value="{item_id}"/>
					</td>
				</tr>

				<xsl:call-template name="location_form"/>
				<tr>
					<td>
						<label for="unit_id"><xsl:value-of select="php:function('lang', 'unit')" /></label>
					</td>
					<td>
						<select id = 'unit_id' name="values[unit_id]" class="forms">
							<xsl:if test="lock_unit = 1">
								<xsl:attribute name="disabled" value="disabled"/>
							</xsl:if>
							<xsl:apply-templates select="unit_list/options"/>
						</select>
					</td>
					</tr>
					<tr>
					<td>
						<label for="inventory"><xsl:value-of select="php:function('lang', 'inventory')" /></label>
					</td>
					<td>

						<input type="text" id = 'inventory' name="values[inventory]" value="{value_inventory}" size="12">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_inventory_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
<!--
					<tr>
					<td>
						<label ><xsl:value-of select="php:function('lang', 'write off')" /></label>
					</td>
					<td>
						<input type="text" name="values[write_off]" value="{value_write_off}" size="12">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_write_off_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
-->
					<tr>
					<td>
						<label><xsl:value-of select="php:function('lang', 'bookable')" /></label>
					</td>
					<td>
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
					</td>
					</tr>
					<tr>
					<td>
						<label>
							<xsl:value-of select="php:function('lang', 'active from')"/>
						</label>
					</td>
					<td>
						<input type="text" id="active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_active_from_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
					<tr>
					<td>
					<label>
						<xsl:value-of select="php:function('lang', 'active to')"/>
					</label>
					</td>
					<td>
						<input type="text" id="active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_active_to_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					</tr>
					<tr>

					<td>
						<label><xsl:value-of select="php:function('lang', 'remark')" /></label>
					</td>
					<td>
						<textarea cols="60" rows="4" name="values[remark]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>
						</textarea>
					</td>
					</tr>
			</table>
			 </fieldset>
		<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save values and exit')"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:variable>
					<input type="button" name="values[cancel]" value="{$lang_cancel}" onClick="parent.TINY.box.hide();">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Back to the list')"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>

			</form>



	 </div>
	</xsl:template>

	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"/>
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<xsl:choose>
			<xsl:when test="mode = 'edit'">
				<script type="text/javascript">
					self.name="first_Window";
					<xsl:value-of select="lookup_functions"/>
				</script>
			</xsl:when>
		</xsl:choose>
		<script type="text/javascript">
			function set_tab(active_tab)
			{
				document.form.active_tab.value = active_tab;			
			}

			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
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
		<div class="yui-navset" id="entity_edit_tabview">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form id="form" name="form" action="{$form_action}" method="post" ENCTYPE="multipart/form-data">
				<input type="hidden" name="active_tab" value="{active_tab}"/>
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
					<xsl:choose>
						<xsl:when test="value_id !=''">
							<tr>
								<td class="th_text" valign="top">
									<a href="{link_pdf}" target="_blank">PDF</a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="mode = 'edit'">
							<tr>
								<td colspan="2" align="center">
									<xsl:call-template name="table_apply"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
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
										<xsl:variable name="link_ticket"><xsl:value-of select="//link_ticket"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
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
									<input type="hidden" name="location_code" value="{location_code}"/>
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
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<xsl:choose>
						<xsl:when test="location_data!=''">
							<div id="location">
								<table>
									<xsl:choose>
										<xsl:when test="mode='edit'">
											<xsl:call-template name="location_form"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:call-template name="location_view"/>
										</xsl:otherwise>
									</xsl:choose>
									<xsl:apply-templates select="attributes_general/attributes"/>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:call-template name="attributes_values"/>
					<xsl:choose>
						<xsl:when test="files!='' or fileupload = 1">
							<div id="files">
								<script type="text/javascript">
									var fileuploader_action = <xsl:value-of select="fileuploader_action"/>;
								</script>
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<div id="datatable-container_0"/>
										</td>
									</tr>
									<xsl:choose>
										<xsl:when test="cat_list='' and fileupload = 1 and mode = 'edit'">
											<xsl:call-template name="file_upload"/>
										</xsl:when>
									</xsl:choose>
								</table>
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
					<!--
<xsl:choose>
<xsl:when test="integration!=''">
<div id="integration">
<iframe id = "integration_content" width="100%" height="500">
<p>Your browser does not support iframes.</p>
</iframe>
</div>

<div id="test" >
<div class="hd" style="background-color:#000000;color:#FFFFFF; border:0; text-align:center"> Kart </div>
<div class="bd" style="text-align:center;"> </div>
</div>
</xsl:when>
</xsl:choose>
-->
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
										<xsl:value-of select="php:function('lang', 'expand all')"/>
									</a>
									<xsl:text> </xsl:text>
									<a id="collapse" href="#">
										<xsl:value-of select="php:function('lang', 'collapse all')"/>
									</a>
								</div>
								<div id="treeDiv1"/>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="value_id !='' and enable_bulk = ''">
							<div id="related">
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<tr>
										<td valign='top'>
											<!--<xsl:value-of select="php:function('lang', 'started from')"/>-->
										</td>
										<td>
											<div id="datatable-container_1"/>
										</td>
									</tr>
									<tr>
										<td valign='top'>
											<!--<xsl:value-of select="php:function('lang', 'used in')"/>-->
										</td>
										<td>
											<div id="datatable-container_2"/>
										</td>
									</tr>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="enable_bulk = 1">
							<div id="inventory">
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<tr>
										<td align="left" valign="top">
											<xsl:value-of select="php:function('lang', 'inventory')"/>
										</td>
										<td>
											<div id="datatable-container_3"/>
										</td>
									</tr>
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
								</table>
							</div>
						</xsl:when>
					</xsl:choose>

				</div>
				<xsl:choose>
					<xsl:when test="mode = 'edit'">
						<table cellpadding="2" cellspacing="2" width="80%" align="center">
							<tr height="50">
								<td colspan="2" align="center">
									<xsl:call-template name="table_apply"/>
								</td>
							</tr>
						</table>
					</xsl:when>
				</xsl:choose>
			</form>
			<xsl:choose>
				<xsl:when test="value_id!=''">
					<table cellpadding="2" cellspacing="2" width="80%" align="center">
						<tr>
							<xsl:choose>
								<xsl:when test="start_ticket!=''">
									<td valign="top">
										<xsl:variable name="ticket_link">
											<xsl:value-of select="ticket_link"/>
										</xsl:variable>
										<form method="post" action="{$ticket_link}">
											<xsl:variable name="lang_start_ticket">
												<xsl:value-of select="lang_start_ticket"/>
											</xsl:variable>
											<input type="submit" name="location" value="{$lang_start_ticket}">
												<xsl:attribute name="title">
													<xsl:value-of select="lang_start_ticket_statustext"/>
												</xsl:attribute>
											</input>
										</form>
									</td>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="start_project!=''">
									<td valign="top">
										<xsl:variable name="project_link">
											<xsl:value-of select="project_link"/>
										</xsl:variable>
										<form method="post" action="{$project_link}">
											<xsl:variable name="lang_start_project">
												<xsl:value-of select="php:function('lang', 'generate new project')"/>
											</xsl:variable>
											<input type="submit" name="location" value="{$lang_start_project}">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')"/>
												</xsl:attribute>
											</input>
										</form>
									</td>
									<td valign="top">
										<xsl:variable name="add_to_project_link">
											<xsl:value-of select="add_to_project_link"/>
										</xsl:variable>
										<form method="post" action="{$add_to_project_link}">
											<xsl:variable name="lang_add_to_project">
												<xsl:value-of select="php:function('lang', 'add to project')"/>
											</xsl:variable>
											<input type="submit" name="location" value="{$lang_add_to_project}" onMouseout="window.status='';return true;">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')"/>
												</xsl:attribute>
											</input>
										</form>
									</td>
								</xsl:when>
							</xsl:choose>
						</tr>
					</table>
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
		<table>
			<tr>
				<td valign="bottom">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save values and exit')"/>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply">
						<xsl:value-of select="php:function('lang', 'apply')"/>
					</xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save and stay in form')"/>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Back to the list')"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
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
