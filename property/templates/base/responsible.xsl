  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="edit_contact">
				<xsl:apply-templates select="edit_contact"/>
			</xsl:when>
			<xsl:when test="list_contact">
				<xsl:apply-templates select="list_contact"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_type"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- add / edit responsibility type-->

	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td>
					<table cellpadding="2" cellspacing="2" align="left">
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
							<xsl:when test="value_id != ''">
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'id')"/>
									</td>
									<td>
										<xsl:value-of select="value_id"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<form name="form_app" method="post" action="{$form_action}">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'application')"/>
								</td>
								<td align="left">
									<select name="appname" onChange="this.form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'application')"/>
										</xsl:attribute>
										<xsl:apply-templates select="apps_list/options"/>
									</select>
								</td>
							</tr>
						</form>
						<form name="form_location" method="post" action="{$form_action}">
							<tr>
								<td>
									<input type="hidden" name="appname" value="{value_appname}"/>
									<xsl:value-of select="php:function('lang', 'location')"/>
								</td>
								<td align="left">
									<select name="location" onChange="this.form.submit();">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Select submodule')"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="php:function('lang', 'No location')"/>
										</option>
										<xsl:apply-templates select="location_list/options"/>
									</select>
								</td>
							</tr>
						</form>
					</table>
					<tr>
						<td>
							<form name="form" method="post" action="{$form_action}">
								<table cellpadding="2" cellspacing="2" align="left">
									<tr>
										<td>
											<input type="hidden" name="values[appname]" value="{value_appname}"/>
											<input type="hidden" name="values[location]" value="{value_location}"/>
											<xsl:value-of select="php:function('lang', 'category')"/>
										</td>
										<td>
											<xsl:call-template name="categories"/>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'name')"/>
										</td>
										<td>
											<input type="text" name="values[name]" value="{value_name}" size="60">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'name')"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'descr')"/>
										</td>
										<td>
											<textarea cols="60" rows="10" name="values[descr]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'descr')"/>
												</xsl:attribute>
												<xsl:value-of select="value_descr"/>
											</textarea>
										</td>
									</tr>
									<tr>
										<td class="th_text" valign="top">
											<xsl:value-of select="php:function('lang', 'details')"/>
										</td>
										<td>
											<table width="100%" cellpadding="2" cellspacing="2" align="center">
												<!--  DATATABLE 0-->
												<td>
													<div id="paging_0"/>
													<div id="datatable-container_0"/>
												</td>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<table cellpadding="2" cellspacing="2" width="50%" align="center">
												<xsl:variable name="lang_save">
													<xsl:value-of select="php:function('lang', 'save')"/>
												</xsl:variable>
												<xsl:variable name="lang_apply">
													<xsl:value-of select="php:function('lang', 'apply')"/>
												</xsl:variable>
												<xsl:variable name="lang_cancel">
													<xsl:value-of select="php:function('lang', 'cancel')"/>
												</xsl:variable>
												<tr height="50">
													<td>
														<input type="submit" name="values[save]" value="{$lang_save}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'save')"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="submit" name="values[apply]" value="{$lang_apply}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'apply')"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="submit" name="values[cancel]" value="{$lang_cancel}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'cancel')"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</td>
			</tr>
		</table>
		<!--  DATATABLE DEFINITIONS-->
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
					<!--permission:<xsl:value-of select="permission"/>, -->
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
	</xsl:template>

	<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit_role">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td>
					<table cellpadding="2" cellspacing="2" align="left">
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
					<tr>
						<td>
							<form name="form" method="post" action="{$form_action}">
								<table cellpadding="2" cellspacing="2" align="left">
									<xsl:choose>
										<xsl:when test="value_id != ''">
											<tr>
												<td valign="top">
													<xsl:value-of select="php:function('lang', 'id')"/>
												</td>
												<td>
													<xsl:value-of select="value_id"/>
												</td>
											</tr>
										</xsl:when>
									</xsl:choose>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'name')"/>
										</td>
										<td>
											<input type="text" name="values[name]" value="{value_name}" size="60">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'name')"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'descr')"/>
										</td>
										<td>
											<textarea cols="60" rows="10" name="values[remark]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'descr')"/>
												</xsl:attribute>
												<xsl:value-of select="value_remark"/>
											</textarea>
										</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="php:function('lang', 'responsibility')"/>
										</td>
										<td align="left">
											<select name="values[responsibility_id]">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Select submodule')"/>
												</xsl:attribute>
												<option value="">
													<xsl:value-of select="php:function('lang', 'select')"/>
												</option>
												<xsl:apply-templates select="responsibility_list/options"/>
											</select>
										</td>
									</tr>

									<tr>
										<td valign = 'top'>
											<xsl:value-of select="php:function('lang', 'location level')"/>
										</td>
										<td align="left">
											<table>
												<xsl:apply-templates select="level_list/checkbox"/>
											</table>
										</td>
									</tr>

									<tr>
										<td colspan="2">
											<table cellpadding="2" cellspacing="2" width="50%" align="center">
												<xsl:variable name="lang_save">
													<xsl:value-of select="php:function('lang', 'save')"/>
												</xsl:variable>
												<xsl:variable name="lang_apply">
													<xsl:value-of select="php:function('lang', 'apply')"/>
												</xsl:variable>
												<xsl:variable name="lang_cancel">
													<xsl:value-of select="php:function('lang', 'cancel')"/>
												</xsl:variable>
												<tr height="50">
													<td>
														<input type="submit" name="values[save]" value="{$lang_save}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'save')"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="submit" name="values[apply]" value="{$lang_apply}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'apply')"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="submit" name="values[cancel]" value="{$lang_cancel}">
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('lang', 'cancel')"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_contact">
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
				<td class="th_text" align="left">
					<xsl:value-of select="location_name"/>
					<xsl:choose>
						<xsl:when test="category_name != ''">
							<xsl:text>::</xsl:text>
							<xsl:value-of select="category_name"/>
						</xsl:when>
					</xsl:choose>
					<xsl:text>::</xsl:text>
					<xsl:value-of select="type_name"/>
				</td>
			</tr>
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
			<xsl:apply-templates select="table_header_contact"/>
			<xsl:choose>
				<xsl:when test="values_contact != ''">
					<xsl:apply-templates select="values_contact"/>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_contact">
		<xsl:variable name="sort_location">
			<xsl:value-of select="sort_location"/>
		</xsl:variable>
		<xsl:variable name="sort_active_from">
			<xsl:value-of select="sort_active_from"/>
		</xsl:variable>
		<xsl:variable name="sort_active_to">
			<xsl:value-of select="sort_active_to"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_contact"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{sort_ecodimb}">
					<xsl:value-of select="lang_ecodimb"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_location}">
					<xsl:value-of select="lang_location"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_item"/>
			</td>
			<td class="th_text" width="5%" align="left">
				<a href="{$sort_active_from}">
					<xsl:value-of select="lang_active_from"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="left">
				<a href="{$sort_active_to}">
					<xsl:value-of select="lang_active_to"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_created_on"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_created_by"/>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="lang_remark"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<!--
<td class="th_text" width="5%" align="center">
<xsl:value-of select="lang_delete"/>
</td>
-->
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_contact">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<td align="left">
				<xsl:value-of select="contact_name"/>
			</td>
			<td align="left">
				<xsl:value-of select="ecodimb"/>
			</td>
			<td align="left">
				<xsl:value-of select="location_code"/>
			</td>
			<td align="left">
				<xsl:value-of select="item"/>
			</td>
			<td align="left">
				<xsl:value-of select="active_from"/>
			</td>
			<td align="left">
				<xsl:value-of select="active_to"/>
			</td>
			<td align="left">
				<xsl:value-of select="created_on"/>
			</td>
			<td align="left">
				<xsl:value-of select="created_by"/>
			</td>
			<td align="left">
				<xsl:value-of select="remark"/>
			</td>
			<xsl:choose>
				<xsl:when test="link_edit != ''">
					<td align="center" title="{lang_edit_text}">
						<xsl:variable name="link_edit">
							<xsl:value-of select="link_edit"/>
						</xsl:variable>
						<a href="{link_edit}">
							<xsl:value-of select="text_edit"/>
						</a>
					</td>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="link_delete != ''">
					<td align="center" title="{lang_delete_text}">
						<xsl:variable name="link_delete">
							<xsl:value-of select="link_delete"/>
						</xsl:variable>
						<a href="{link_delete}">
							<xsl:value-of select="text_delete"/>
						</a>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_add">
		<xsl:variable name="add_action">
			<xsl:value-of select="add_action"/>
		</xsl:variable>
		<xsl:variable name="lang_add">
			<xsl:value-of select="lang_add"/>
		</xsl:variable>
		<tr>
			<td height="50">
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_statustext"/>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<xsl:choose>
				<xsl:when test="cancel_action != ''">
					<td height="50">
						<form method="post" action="{cancel_action}">
							<input type="submit" name="add" value="{lang_cancel}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"/>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</xsl:when>
			</xsl:choose>
		</tr>
	</xsl:template>

	<!-- add / edit contact-->
	<xsl:template match="edit_contact">
		<div align="left">
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<form method="post" action="{$form_action}" name="form">
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
					<tr>
						<td>
							<xsl:value-of select="lang_location"/>
						</td>
						<td>
							<xsl:value-of select="value_location_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top" width="30%">
									<xsl:value-of select="lang_id"/>
								</td>
								<td align="left">
									<xsl:value-of select="value_id"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_entry_date"/>
								</td>
								<td>
									<xsl:value-of select="value_entry_date"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="lang_responsibility"/>
						</td>
						<td>
							<input type="text" name="responsibility_id" value="{value_responsibility_id}" readonly="readonly" size="5" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_responsibility_status_text"/>
								</xsl:attribute>
							</input>
							<input size="30" type="text" name="responsibility_name" value="{value_responsibility_name}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_responsibility_status_text"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_contact"/>
						</td>
						<td>
							<input type="text" name="contact" value="{value_contact_id}" onClick="lookup_contact()" readonly="readonly" size="5" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_contact_status_text"/>
								</xsl:attribute>
							</input>
							<input size="30" type="text" name="contact_name" value="{value_contact_name}" onClick="lookup_contact()" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_contact_status_text"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<xsl:call-template name="ecodimb_form"/>
					<xsl:call-template name="location_form"/>
					<tr>
						<td>
							<xsl:value-of select="lang_active_from"/>
						</td>
						<td>
							<input type="text" id="values_active_from" name="values[active_from]" size="10" value="{value_active_from}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_from_statustext"/>
								</xsl:attribute>
							</input>
							<img id="values_active_from-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"/>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_active_to"/>
						</td>
						<td>
							<input type="text" id="values_active_to" name="values[active_to]" size="10" value="{value_active_to}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_active_to_statustext"/>
								</xsl:attribute>
							</input>
							<img id="values_active_to-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"/>
						</td>
					</tr>
					<tr>
						<td valign="top" title="{lang_remark_status_text}">
							<xsl:value-of select="lang_remark"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[remark]" onMouseout="window.status='';return true;">
								<xsl:value-of select="value_remark"/>
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td colspan="2" align="center">
							<table>
								<tr>
									<td valign="bottom">
										<xsl:variable name="lang_save">
											<xsl:value-of select="lang_save"/>
										</xsl:variable>
										<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_save_status_text"/>
											</xsl:attribute>
										</input>
									</td>
									<td valign="bottom">
										<xsl:variable name="lang_apply">
											<xsl:value-of select="lang_apply"/>
										</xsl:variable>
										<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_apply_status_text"/>
											</xsl:attribute>
										</input>
									</td>
									<td align="left" valign="bottom">
										<xsl:variable name="lang_cancel">
											<xsl:value-of select="lang_cancel"/>
										</xsl:variable>
										<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_cancel_status_text"/>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
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
	<xsl:template match="checkbox">
		<tr>
			<td value="{id}">
				<input type="checkbox" name="values[location_level][]" value="{id}">
					<xsl:attribute name="title">
						<xsl:value-of select="name"/>
					</xsl:attribute>
					<xsl:if test="selected != 0">
						<xsl:attribute name="checked" value="checked"/>
					</xsl:if>
				</input>
				<xsl:value-of select="name"/>
			</td>
		</tr>
	</xsl:template>
