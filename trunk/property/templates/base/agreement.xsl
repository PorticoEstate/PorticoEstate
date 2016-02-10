  <!-- $Id$ -->
	<xsl:template match="data">
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
			<xsl:when test="add_activity">
				<xsl:apply-templates select="add_activity"/>
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
					<input type="text" disabled="disabled" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_name_statustext"/>
						</xsl:attribute>
					</input>
                            </div>
                            <div class="pure-control-group">
                                    <label>
					<xsl:value-of select="lang_descr"/>
                                    </label>
					<textarea cols="60" disabled="disabled" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
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
                                    <!--xsl:call-template name="table_header"/>
				<xsl:choose>
					<xsl:when test="values != ''">
						<xsl:call-template name="values4"/>
					</xsl:when>
                                    </xsl:choose-->
                                    <div class="pure-control-group">
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
                                                    <input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"/>
							</xsl:attribute>
						</input>
						<xsl:variable name="lang_apply">
							<xsl:value-of select="lang_apply"/>
						</xsl:variable>
                                                    <input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_apply_statustext"/>
							</xsl:attribute>
						</input>
						<xsl:variable name="lang_cancel">
							<xsl:value-of select="lang_cancel"/>
						</xsl:variable>
                                                    <input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
					<xsl:when test="//acl_manage != '' and total_cost!=''">
						<td align="center">
							<input type="hidden" name="values[activity_id][{activity_id}]" value="{activity_id}"/>
							<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"/>
							<input type="checkbox" name="values[select][{activity_id}]" value="{cost}" onMouseout="window.status='';return true;">
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
					<xsl:when test="//acl_manage != '' and total_cost!=''">
						<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"/>
						<input type="hidden" name="values[m_cost][{activity_id}]" value="{m_cost}"/>
						<input type="hidden" name="values[w_cost][{activity_id}]" value="{w_cost}"/>
						<input type="hidden" name="values[total_cost][{activity_id}]" value="{total_cost}"/>
						<input type="hidden" name="values[select][0]" value="{activity_id}"/>
					</xsl:when>
				</xsl:choose>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="values3">
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
				<td class="small_text" align="left">
					<xsl:value-of select="activity_id"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="num"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="descr"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="unit"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="m_cost"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="w_cost"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="total_cost"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="this_index"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="index_count"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="index_date"/>
				</td>
				<xsl:choose>
					<xsl:when test="acl_read != ''">
						<td align="center">
							<xsl:variable name="link_view">
								<xsl:value-of select="link_view"/>
							</xsl:variable>
							<xsl:variable name="lang_view_statustext">
								<xsl:value-of select="lang_view_statustext"/>
							</xsl:variable>
							<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_view"/>
							</a>
						</td>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="acl_edit != ''">
						<td align="center">
							<xsl:variable name="link_edit">
								<xsl:value-of select="link_edit"/>
							</xsl:variable>
							<xsl:variable name="lang_edit_statustext">
								<xsl:value-of select="lang_edit_statustext"/>
							</xsl:variable>
							<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_edit"/>
							</a>
						</td>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="acl_delete != ''">
						<td align="center">
							<xsl:variable name="link_delete">
								<xsl:value-of select="link_delete"/>
							</xsl:variable>
							<xsl:variable name="lang_delete_statustext">
								<xsl:value-of select="lang_delete_statustext"/>
							</xsl:variable>
							<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
								<xsl:value-of select="text_delete"/>
							</a>
						</td>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="acl_manage != '' and total_cost!=''">
						<td align="center">
							<input type="hidden" name="values[id][{activity_id}]" value="{index_count}"/>
							<input type="hidden" name="values[m_cost][{activity_id}]" value="{m_cost}"/>
							<input type="hidden" name="values[w_cost][{activity_id}]" value="{w_cost}"/>
							<input type="hidden" name="values[total_cost][{activity_id}]" value="{total_cost}"/>
							<input type="checkbox" name="values[select][]" value="{activity_id}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_select_statustext"/>
								</xsl:attribute>
							</input>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<!-- New template-->
	<xsl:template name="values4">
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
				<td class="small_text" align="left">
					<xsl:value-of select="id"/>
				</td>
				<td class="small_text" align="left">
					<xsl:value-of select="num"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="base_descr"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="descr"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="unit"/>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="ns3420"/>
				</td>
				<td align="center">
					<input type="checkbox" name="values[select][]" value="{id}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_select_statustext"/>
						</xsl:attribute>
					</input>
				</td>
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
					<input class="pure-button pure-button-primary" type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
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
											
												<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_name_statustext"/>
														<xsl:text>'; return true;</xsl:text>
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
											
												<textarea cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
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
												<select name="values[group_id]" class="forms" onMouseover="window.status='{$lang_agreement_group_statustext}'; return true;" onMouseout="window.status='';return true;">
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
											
												<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_start_date_statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="lang_end_date"/>
											</label>
											
												<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_end_date_statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="lang_termination_date"/>
											</label>
											
												<input type="text" id="values_termination_date" name="values[termination_date]" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_termination_date_statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											
										</div>
										<xsl:choose>
											<xsl:when test="files!=''">
												<!-- <xsl:call-template name="file_list"/> -->
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
														<select name="values[member_of][]" disabled="disabled" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
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
												<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_save_statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<!-- </td><td valign="bottom">  -->
												<xsl:variable name="lang_apply">
													<xsl:value-of select="lang_apply"/>
												</xsl:variable>
												<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_apply_statustext"/>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
												<!-- </td><td align="right" valign="bottom">-->
												<xsl:variable name="lang_cancel">
													<xsl:value-of select="lang_cancel"/>
												</xsl:variable>
												<input type="button" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_cancel_statustext"/>
														<xsl:text>'; return true;</xsl:text>
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
										<!--tr>
											<td class="center" align="right" colspan="10">
												<div id="datatable-buttons_0"/>
											</td>
										</tr-->
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
										<input class="mybottonsUpdates calendar-opt" type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_date_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
										<div style="width:25px;height:15px;position:relative;float:left;"></div>
                                                                                <input id="new_index" class="mybottonsUpdates" type="inputText" name="values[new_index]" size="12"/>
                                                                                <input id="hd_values[update]" class="" type="hidden" name="values[update]" value="Update"/>
                                                                                <input type="button" name="" value="Update" id="values[update]" onClick="onUpdateClickAlarm('update');"/>
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
						permission:<xsl:value-of select="permission"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
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

	<!-- add item / edit item -->
	<xsl:template match="edit_item">
		<script type="text/javascript">
			self.name="first_Window";
			<xsl:value-of select="lookup_functions"/>
		</script>
		<!--  DATATABLE DEFINITIONS-->
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
									<xsl:text> [</xsl:text>
									<xsl:value-of select="value_num"/>
									<xsl:text>] </xsl:text>
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
							<input type="text" name="values[m_cost]" value="{value_m_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_m_cost_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
                                            </div>
                                            <div class="pure-control-group">
                                                    <label>
							<xsl:value-of select="lang_w_cost"/>
                                                    </label>
							<input type="text" name="values[w_cost]" value="{value_w_cost}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_w_cost_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
                                                            <input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
   
							<xsl:variable name="lang_apply">
								<xsl:value-of select="lang_apply"/>
							</xsl:variable>
                                                            <input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_apply_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
    
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="lang_cancel"/>
							</xsl:variable>
                                                            <input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
										<input type="text" id="values_date" class="calendar-opt" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_date_statustext"/>
												<xsl:text>'; return true;</xsl:text>
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
                                                    <!--
    <table width="100%" cellpadding="2" cellspacing="2" align="center">
    <xsl:call-template name="table_header"/>
    <xsl:call-template name="values2"/>
    </table>
    <table width="70%" cellpadding="2" cellspacing="2" align="center">
    <xsl:choose>
    <xsl:when test="table_update!=''">
    <xsl:apply-templates select="table_update"/>
    </xsl:when>
    </xsl:choose>
    <tr>
    <td></td><td></td>
    <td class="small_text" align="left">
    <a href="{delete_action}" onMouseover="window.status='{lang_delete_last_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_delete_last"/></a>
								</td>
							</tr>

							</table>
							-->
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
				<input type="text" name="values[new_index]" size="12" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_new_index_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td>
				<input type="text" id="values_date" name="values[date]" size="10" value="{date}" readonly="readonly" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_date_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td height="50">
				<xsl:variable name="lang_update">
					<xsl:value-of select="lang_update"/>
				</xsl:variable>
				<input type="submit" name="values[update]" value="{$lang_update}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_update_statustext"/>
						<xsl:text>'; return true;</xsl:text>
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
									<xsl:value-of select="lang_status"/>
							</label>
								<xsl:for-each select="status_list">
									<xsl:choose>
										<xsl:when test="selected='selected'">
												<xsl:value-of select="name"/>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
						</div>
						<div class="pure-control-group">
							<label>
									<xsl:value-of select="lang_descr"/>
							</label>
									<textarea disabled="disabled" cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
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
										<xsl:when test="selected='selected'">
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
										<xsl:when test="selected='selected'">
												<xsl:value-of select="name"/>
										</xsl:when>
									</xsl:choose>
								</xsl:for-each>
						</div>
						<div class="pure-control-group">
							<label>
									<xsl:value-of select="lang_start_date"/>
							</label>
									<input type="text" id="start_date" name="start_date" size="10" value="{value_start_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
						</div>
						<div class="pure-control-group">
							<label>
									<xsl:value-of select="lang_end_date"/>
							</label>
									<input type="text" id="end_date" name="end_date" size="10" value="{value_end_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
						</div>
						<div class="pure-control-group">
							<label>
									<xsl:value-of select="lang_termination_date"/>
							</label>
                                                                    
									<input type="text" id="termination_date" name="termination_date" size="10" value="{value_termination_date}" readonly="readonly" onMouseout="window.status='';return true;"/>
                                                                    
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
									<!--td colspan="2" width="50%" align="left"-->
											<xsl:apply-templates select="attributes_view"/>
									<!--/td-->
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
											<select disabled="disabled" name="values[member_of][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_member_of_statustext}'; return true;" onMouseout="window.status='';return true;">
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
							<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
		</div>
	</div>
	<div class="proplist-col">
		<table cellpadding="2" cellspacing="2" align="left">
			<xsl:variable name="edit_url">
				<xsl:value-of select="edit_url"/>
			</xsl:variable>
			<form name="form" method="post" action="{$edit_url}">
				<tr>
					<td align="left" valign="bottom">
						<xsl:variable name="lang_cancel">
							<xsl:value-of select="lang_cancel"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						footer:<xsl:value-of select="footer"/>
						}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>

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
						<input type="submit" name="cancel"  class="pure-button pure-button-primary" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>

		</script>
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
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_attribtext}';return true;" onMouseout="window.status='';return true;">
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_column_name_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_input_text_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_statustext_attribtext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_precision_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_scale_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_default_statustext"/>
									<xsl:text>'; return true;</xsl:text>
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
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[list]" value="1" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_list_statustext"/>
											<xsl:text>'; return true;</xsl:text>
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
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[search]" value="1" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_include_search_statustext"/>
											<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_attribtext"/>
									<xsl:text>'; return true;</xsl:text>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_attribtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
</xsl:template>

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
<xsl:template match="agreement_group_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
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
