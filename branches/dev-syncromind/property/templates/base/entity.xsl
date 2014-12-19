  <!-- $Id$ -->
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

	<xsl:template match="data">
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
		<xsl:call-template name="jquery_phpgw_i18n"/>
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

		<form name="form" id="edit_inventory" action="{$action_url}" method="post" class= "pure-form-aligned">

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
				<div>
					<fieldset>
						<xsl:call-template name="location_form"/>
						<div class="pure-control-group">
							<label><xsl:value-of select="php:function('lang', 'id')" /></label>
							<xsl:value-of select="item_id"/>
							<input type="hidden" name="location_id" value="{location_id}"/>
							<input type="hidden" name="id" value="{item_id}"/>					
						</div>

						<div class="pure-control-group">
							<label for="unit_id"><xsl:value-of select="php:function('lang', 'unit')" /></label>
							<select id = 'unit_id' name="values[unit_id]" class="forms">
								<xsl:if test="lock_unit = 1">
									<xsl:attribute name="disabled" value="disabled"/>
								</xsl:if>
								<xsl:apply-templates select="unit_list/options"/>
							</select>				
						</div>

						<div class="pure-control-group">
							<label for="inventory"><xsl:value-of select="php:function('lang', 'inventory')" /></label>
							<input type="text" id = 'inventory' name="values[inventory]" value="{value_inventory}" size="12">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_inventory_statustext"/>
								</xsl:attribute>
							</input>				
						</div>

						<div class="pure-control-group">
							<label><xsl:value-of select="php:function('lang', 'bookable')" /></label>
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
							<label><xsl:value-of select="php:function('lang', 'active from')"/></label>
							<input type="text" id="active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_from_statustext"/>
								</xsl:attribute>
							</input>				
						</div>

						<div class="pure-control-group">
							<label><xsl:value-of select="php:function('lang', 'active to')"/></label>
							<input type="text" id="active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_to_statustext"/>
								</xsl:attribute>
							</input>			
						</div>

						<div class="pure-control-group">
							<label><xsl:value-of select="php:function('lang', 'remark')" /></label>
							<textarea cols="60" rows="4" name="values[remark]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'remark')"/>
								</xsl:attribute>
								<xsl:value-of select="value_remark"/>
							</textarea>			
						</div>
					</fieldset>
				</div>
				<div class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
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
			</form>
		</fieldset>

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
			var base_java_url = <xsl:value-of select="base_java_url"/>;
		</script>
		
		<div id="entity_edit_tabview">
			
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			
			<form id="form" name="form" action="{$form_action}" method="post" ENCTYPE="multipart/form-data" class= "pure-form pure-form-aligned">
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
									<xsl:call-template name="table_apply">
										<xsl:with-param	name="lean" select="lean"/>
									</xsl:call-template>
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
				
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<xsl:choose>
						<xsl:when test="location_data!=''">
							<div id="location">
								<fieldset>
									<xsl:choose>
										<xsl:when test="mode='edit'">
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
											<xsl:call-template name="location_form"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:call-template name="location_view"/>
										</xsl:otherwise>
									</xsl:choose>
									<xsl:apply-templates select="attributes_general/attributes"/>
								</fieldset>
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
								<fieldset>
									<div class="pure-control-group">
										<xsl:value-of select="//lang_files"/>
										<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_0'">
													<xsl:call-template name="table_setup">
													  <xsl:with-param name="container" select ='container'/>
													  <xsl:with-param name="requestUrl" select ='requestUrl' />
													  <xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
													</xsl:call-template>
												</xsl:if>
										</xsl:for-each>
									</div>
									<xsl:choose>
										<xsl:when test="cat_list='' and fileupload = 1 and mode = 'edit'">
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
						<xsl:when test="documents != ''">
							<div id="document">
								<!-- Some style for the expand/contract section-->
								<style>
									#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
									#treeDiv1 { background: #fff; padding:1em; margin-top:1em; }
								</style>
								<script type="text/javascript">
									var documents = <xsl:value-of select="documents"/>;
									var requestUrlDoc = <xsl:value-of select="requestUrlDoc"/>;
								</script>
								<fieldset>
								<!-- markup for expand/contract links -->
								<div id="treecontrol">
									<a id="collapse" title="Collapse the entire tree below" href="#"><xsl:value-of select="php:function('lang', 'collapse all')"/></a>
									<xsl:text> | </xsl:text>
									<a id="expand" title="Expand the entire tree below" href="#"><xsl:value-of select="php:function('lang', 'expand all')"/></a>
								</div>
								<div id="treeDiv1"></div>
								</fieldset>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="value_id !='' and enable_bulk = ''">
							<div id="related">
								<fieldset>
									<div class="pure-control-group">
										<xsl:for-each select="datatable_def">
												<xsl:if test="container = 'datatable-container_1'">
													<xsl:call-template name="table_setup">
													  <xsl:with-param name="container" select ='container'/>
													  <xsl:with-param name="requestUrl" select ='requestUrl' />
													  <xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
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
													</xsl:call-template>
												</xsl:if>
										</xsl:for-each>
									</div>
								</fieldset>
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
						</xsl:call-template>
					</xsl:when>
				</xsl:choose>
			</form>
			<xsl:variable name="cancel_url">
				<xsl:value-of select="cancel_url"/>
			</xsl:variable>
			<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
			<xsl:choose>
				<xsl:when test="value_id!='' and lean !=1">
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
		<xsl:param name="lean" />
		
		<div class="proplist-col">
		<table>
			<tr>
				<xsl:choose>
					<xsl:when test="$lean!=1">
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
					</xsl:when>
				</xsl:choose>
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
				<xsl:choose>
					<xsl:when test="$lean!=1">
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
					</xsl:when>
				</xsl:choose>
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

	<xsl:template name="table_setup">
		<xsl:param name="container" />
		<xsl:param name="requestUrl" />
		<xsl:param name="ColumnDefs" />
		<table id="{$container}" class="display cell-border compact responsive no-wrap" width="100%">
			<thead>
				<tr>
					<xsl:for-each select="$ColumnDefs">
						<xsl:choose>
							<xsl:when test="hidden">
								<xsl:if test="hidden =0">
									<th>
										<xsl:value-of select="label"/>
									</th>
									</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<th>
									<xsl:value-of select="label"/>
								</th>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</tr>
			</thead>
		</table>
		<script>
			JqueryPortico.inlineTablesDefined += 1;
			var PreColumns = [
					<xsl:for-each select="$ColumnDefs">
					{
						data:			"<xsl:value-of select="key"/>",
						class:			"<xsl:value-of select="className"/>",
						orderable:		<xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:choose>
							<xsl:when test="hidden">
								<xsl:if test="hidden =0">
									visible			:true,
								</xsl:if>
								<xsl:if test="hidden =1">
									class:			'none',
									visible			:false,
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
									visible			:true,
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="formatter">
						 render: function (dummy1, dummy2, oData) {
								try {
									var ret = <xsl:value-of select="formatter"/>("<xsl:value-of select="key"/>", oData);
								}
								catch(err) {
									return err.message;
								}
								return ret;
							 },

						</xsl:if>
						defaultContent:	"<xsl:value-of select="defaultContent"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
	<![CDATA[
			columns = [];

			for(i=0;i < PreColumns.length;i++)
			{
				if ( PreColumns[i]['visible'] == true )
				{
					columns.push(PreColumns[i]);
				}
			}
	]]>
			<xsl:variable name="num">
				<xsl:number count="*"/>
			</xsl:variable>
			var options = {disablePagination:true, disableFilter:true};
			var oTable<xsl:number value="($num - 1)"/> = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns, options);

		</script>
	</xsl:template>