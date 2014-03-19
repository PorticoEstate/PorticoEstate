  <!-- $Id$ -->
	<!--
Function
phpgw:conditional( expression $test, mixed $true, mixed $false )
Evaluates test expression and returns the contents in the true variable if
the expression is true and the contents of the false variable if its false

Returns mixed
-->
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
<xsl:template name="app_data">
    <xsl:choose>
	<xsl:when test="edit">
	    <xsl:apply-templates select="edit"/>
	</xsl:when>
    </xsl:choose>
</xsl:template>

<!-- New template-->
<!-- add / edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
    <script type="text/javascript">
	self.name="first_Window";
	<xsl:value-of select="lookup_functions"/>
	function add_sub_entry()
	{
	document.add_sub_entry_form.submit();
	}
	var project_type_id = '<xsl:value-of select="project_type_id"/>';
	var project_id = '<xsl:value-of select="value_project_id"/>';

	function set_tab(tab)
	{
	document.form.tab.value = tab;			
	}
    </script>
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
	<xsl:choose>
	    <xsl:when test="value_project_id &gt; 0  and mode='edit'">
		<td valign="top">
		    <xsl:variable name="lang_add_sub_entry">
			<xsl:value-of select="lang_add_sub_entry"/>
		    </xsl:variable>
		    <input type="button" name="add_sub_entry" value="{$lang_add_sub_entry}" onClick="add_sub_entry()">
			<xsl:attribute name="title">
			    <xsl:value-of select="lang_add_sub_entry_statustext"/>
			</xsl:attribute>
		    </input>
		</td>
	    </xsl:when>
	</xsl:choose>
    </table>
    <form ENCTYPE="multipart/form-data" method="post" name="form" action="{form_action}">
	<input type="hidden" name="tab" value=""/>
	<div class="yui-navset" id="project_tabview">
	    <xsl:value-of disable-output-escaping="yes" select="tabs"/>
	    <div class="yui-content">
		<div id="general">
		    <table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
			    <xsl:when test="value_project_id &gt; 0">
				<xsl:choose>
				    <xsl:when test="mode='edit'">
					<tr>
					    <td title="{lang_copy_project_statustext}">
						<xsl:value-of select="lang_copy_project"/>
					    </td>
					    <td>
						<input type="checkbox" name="values[copy_project]" value="True">
						    <xsl:attribute name="title">
							<xsl:value-of select="lang_copy_project_statustext"/>
						    </xsl:attribute>
						</input>
					    </td>
					</tr>
				    </xsl:when>
				</xsl:choose>
				<tr>
				    <td>
					<xsl:value-of select="lang_project_id"/>
				    </td>
				    <td>
					<xsl:value-of select="value_project_id"/>
				    </td>
				</tr>
				<xsl:choose>
				    <xsl:when test="mode='edit'">
					<tr>
					    <td valign="top">
						<a href="{link_select_request}" title="{lang_select_request_statustext}">
						    <xsl:value-of select="php:function('lang', 'select request')"/>
						</a>
					    </td>
					</tr>
				    </xsl:when>
				</xsl:choose>
				<tr>
				    <td width="19%" align="left" valign="top">
					<xsl:value-of select="php:function('lang', 'related')"/>
				    </td>
				    <td>
					<div id="paging_6"> </div>
					<div id="datatable-container_6"/>
				    </td>
				</tr>

				<!--	<xsl:for-each select="value_origin">
					<xsl:variable name="origin_location">
						<xsl:value-of select="location"/>
					</xsl:variable>
					<tr>
						<td valign="top">
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
											<xsl:choose>
												<xsl:when test="$origin_location ='.project.request'">
													<input type="checkbox" name="values[delete_request][]" value="{id}" onMouseout="window.status='';return true;">
													  <xsl:attribute name="title">
													    <xsl:value-of select="//lang_delete_request_statustext"/>
													  </xsl:attribute>
													</input>
												</xsl:when>
											</xsl:choose>
										</td>
									</tr>
								</xsl:for-each>
							</table>
						</td>
					</tr>
				</xsl:for-each>
				-->
			    </xsl:when>
			    <xsl:otherwise>
				<xsl:for-each select="value_origin">
				    <tr>
					<td valign="top">
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
			<tr>
			    <td valign="top">
				<xsl:value-of select="php:function('lang', 'project type')"/>
			    </td>
			    <td>
				<select name="values[project_type_id]">
				    <xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'project type')"/>
				    </xsl:attribute>
				    <option value="0">
					<xsl:value-of select="php:function('lang', 'select')"/>
				    </option>
				    <xsl:apply-templates select="project_types/options"/>
				</select>
			    </td>
			</tr>
			<tr>
			    <td valign="top">
				<xsl:value-of select="lang_name"/>
			    </td>
			    <td>
				<input type="hidden" name="values[origin]" value="{value_origin_type}"/>
				<input type="hidden" name="values[origin_id]" value="{value_origin_id}"/>
				<input type="text" name="values[name]" value="{value_name}">
				    <xsl:attribute name="title">
					<xsl:value-of select="lang_name_statustext"/>
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
			    <td>
				<xsl:value-of select="lang_coordinator"/>
			    </td>
			    <td>
				<xsl:call-template name="user_id_select"/>
			    </td>
			</tr>
			<xsl:call-template name="contact_form"/>
			<tr>
			    <td>
				<xsl:value-of select="lang_category"/>
			    </td>
			    <td>
				<xsl:call-template name="categories"/>
			    </td>
			</tr>
			<tr>
			    <td>
				<xsl:value-of select="lang_status"/>
			    </td>
			    <td>
				<xsl:call-template name="status_select"/>
			    </td>
			</tr>
			<xsl:choose>
			    <xsl:when test="value_project_id &gt; 0 and mode='edit'">
				<tr>
				    <td>
					<xsl:value-of select="lang_confirm_status"/>
				    </td>
				    <td>
					<input type="checkbox" name="values[confirm_status]" value="True">
					    <xsl:attribute name="title">
						<xsl:value-of select="lang_confirm_statustext"/>
					    </xsl:attribute>
					</input>
				    </td>
				</tr>
			    </xsl:when>
			</xsl:choose>
			<xsl:choose>
			    <xsl:when test="need_approval='1' and mode='edit'">
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_ask_approval"/>
				    </td>
				    <td>
					<table>
					    <xsl:for-each select="value_approval_mail_address">
						<tr>
						    <td>
							<input type="checkbox" name="values[approval][{id}]" value="True">
							    <xsl:attribute name="title">
								<xsl:value-of select="//lang_ask_approval_statustext"/>
							    </xsl:attribute>
							</input>
						    </td>
						    <td>
							<input type="text" name="values[mail_address][{id}]" value="{address}">
							    <xsl:attribute name="title">
								<xsl:value-of select="//lang_ask_approval_statustext"/>
							    </xsl:attribute>
							</input>
							<xsl:if test="default = '1'">
								<xsl:text>&lt;=</xsl:text>
							</xsl:if>
							</td>
						</tr>
					    </xsl:for-each>
					</table>
				    </td>
				</tr>
			    </xsl:when>
			</xsl:choose>
			<tr>
			    <td valign="top">
				<xsl:value-of select="lang_remark"/>
			    </td>
			    <td>
				<textarea cols="60" rows="6" name="values[remark]">
				    <xsl:attribute name="title">
					<xsl:value-of select="lang_remark_statustext"/>
				    </xsl:attribute>
				    <xsl:value-of select="value_remark"/>
				</textarea>
			    </td>
			</tr>
			<xsl:apply-templates select="custom_attributes/attributes"/>
		    </table>
		</div>
		<div id="location">
		    <table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
			    <xsl:when test="mode='edit'">

				<tr>
				    <td valign="top">
					<xsl:value-of select="php:function('lang', 'inherit location')"/>
				    </td>
				    <td>
					<input type="checkbox" name="values[inherit_location]" value="1">
					    <xsl:if test="inherit_location = 1">
						<xsl:attribute name="checked" value="checked"/>
					    </xsl:if>
					    <xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'inherit location')"/>
					    </xsl:attribute>
					</input>
				    </td>
				</tr>
				<xsl:call-template name="location_form"/>
			    </xsl:when>
			    <xsl:otherwise>
				<xsl:call-template name="location_view"/>
			    </xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
			    <xsl:when test="suppressmeter =''">
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_power_meter"/>
				    </td>
				    <td>
					<input type="text" name="values[power_meter]" value="{value_power_meter}" size="12" onMouseout="window.status='';return true;">
					    <xsl:attribute name="title">
						<xsl:value-of select="lang_power_meter_statustext"/>
					    </xsl:attribute>
					</input>
				    </td>
				</tr>
			    </xsl:when>
			</xsl:choose>
		    </table>
		</div>
		<div id="budget">
		    <table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
			    <td valign="top">
				<xsl:value-of select="lang_start_date"/>
			    </td>
			    <td>
				<input type="text" id="values_start_date" name="values[start_date]" size="10" value="{value_start_date}" readonly="readonly">
				    <xsl:attribute name="title">
					<xsl:value-of select="lang_start_date_statustext"/>
				    </xsl:attribute>
				</input>
			    </td>
			</tr>
			<tr>
			    <td valign="top">
				<xsl:value-of select="lang_end_date"/>
			    </td>
			    <td>
				<input type="text" id="values_end_date" name="values[end_date]" size="10" value="{value_end_date}" readonly="readonly">
				    <xsl:attribute name="title">
					<xsl:value-of select="lang_end_date_statustext"/>
				    </xsl:attribute>
				</input>
			    </td>
			</tr>
			<xsl:call-template name="project_group_form"/>
			<xsl:choose>
			    <xsl:when test="ecodimb_data!=''">
				<xsl:call-template name="ecodimb_form"/>
			    </xsl:when>
			</xsl:choose>
			<xsl:choose>
			    <xsl:when test="b_account_data!=''">
				<xsl:choose>
				    <xsl:when test="mode='edit'">
					<xsl:call-template name="b_account_form"/>
				    </xsl:when>
				    <xsl:otherwise>
					<xsl:call-template name="b_account_view"/>
				    </xsl:otherwise>
				</xsl:choose>
			    </xsl:when>
			</xsl:choose>
			<xsl:choose>
			    <xsl:when test="value_project_id!='' and mode='edit' and project_type_id !=3">
				<tr>
				    <td valign="top">
					<xsl:value-of select="php:function('lang', 'move')"/>
				    </td>
				    <td>
					<input type="text" name="values[new_project_id]" value="">
					    <xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'move budget and orders to another project')"/>
					    </xsl:attribute>
					</input>
				    </td>
				</tr>
			    </xsl:when>
			</xsl:choose>

			<tr>
			    <td valign="top">
				<xsl:value-of select="php:function('lang', 'budget')"/>
			    </td>
			    <td>
				<table>
				    <tr>
					<td>
					    <input type="text" name="values[budget]" value="">
						<xsl:attribute name="title">
						    <xsl:value-of select="php:function('lang', 'Enter the budget')"/>
						</xsl:attribute>
					    </input>
					    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</td>
					<td>
					    <select name="values[budget_year]">
						<xsl:attribute name="title">
						    <xsl:value-of select="php:function('lang', 'year')"/>
						</xsl:attribute>
						<option value="0">
						    <xsl:value-of select="php:function('lang', 'year')"/>
						</option>
						<xsl:apply-templates select="year_list/options"/>
					    </select>
					</td>

					<xsl:choose>
					    <xsl:when test="project_type_id ='3'">
						<td>
						    <input type="checkbox" name="values[budget_reset_buffer]" value="1">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'delete')"/>
							    <xsl:text> </xsl:text>
							    <xsl:value-of select="php:function('lang', 'buffer')"/>
							    <xsl:text> </xsl:text>
							    <xsl:value-of select="php:function('lang', 'budget')"/>
							</xsl:attribute>
						    </input>
						</td>
					    </xsl:when>
					</xsl:choose>
											
					<xsl:choose>
					    <xsl:when test="project_type_id !='3'">
						<td>
						    <select name="values[budget_periodization]">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'periodization')"/>
							</xsl:attribute>
							<option value="0">
							    <xsl:value-of select="php:function('lang', 'periodization')"/>
							</option>
							<xsl:apply-templates select="periodization_list/options"/>
						    </select>
						</td>
						<td>
						    <input type="checkbox" name="values[budget_periodization_all]" value="True">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'all')"/>
							    <xsl:text> </xsl:text>
							    <xsl:value-of select="php:function('lang', 'periods')"/>
							</xsl:attribute>
						    </input>
						</td>
						<td>
						    <input type="checkbox" name="values[budget_periodization_activate]" value="1">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'activate')"/>
							</xsl:attribute>
							<xsl:attribute name="checked" value="checked"/>
						    </input>
						</td>
					    </xsl:when>
					</xsl:choose>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td width="19%" align="left" valign="top">
				<xsl:value-of select="php:function('lang', 'budget')"/>
			    </td>
			    <td>
				<div id="paging_0"> </div>
				<div id="datatable-container_0"/>
			    </td>
			</tr>

			<xsl:choose>
			    <xsl:when test="value_project_id!='' and mode='edit'">
				<tr>
				    <td valign="top">
					<xsl:value-of select="php:function('lang', 'transfer')"/>
				    </td>
				    <td>
					<table>
					    <tr>
						<td valign="top">
						    <xsl:value-of select="php:function('lang', 'amount')"/>
						</td>
						<td valign="top">
						    <xsl:value-of select="php:function('lang', 'project')"/>
						</td>
						<td valign="top">
						    <xsl:value-of select="php:function('lang', 'remark')"/>
						</td>
					    </tr>
					    <tr>
						<td>
						    <input type="text" name="values[transfer_amount]" value="">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'amount to transfer')"/>
							</xsl:attribute>
						    </input>
						</td>
						<td>
						    <input type="text" name="values[transfer_target]" value="">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'target project')"/>
							</xsl:attribute>
						    </input>
						</td>
						<td>
						    <input type="text" name="values[transfer_remark]" value="">
							<xsl:attribute name="title">
							    <xsl:value-of select="php:function('lang', 'remark')"/>
							</xsl:attribute>
						    </input>
						</td>
					    </tr>
					</table>
				    </td>
				</tr>

			    </xsl:when>
			</xsl:choose>

			<xsl:choose>
			    <xsl:when test="project_type_id !='3'">
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_reserve"/>
				    </td>
				    <td>
					<input type="text" name="values[reserve]" value="{value_reserve}">
					    <xsl:attribute name="title">
						<xsl:value-of select="lang_reserve_statustext"/>
					    </xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				    </td>
				</tr>
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_sum"/>
				    </td>
				    <td>
					<xsl:value-of select="value_sum"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				    </td>
				</tr>
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_remainder"/>
				    </td>
				    <td>
					<xsl:value-of select="value_remainder"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				    </td>
				</tr>
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_reserve_remainder"/>
				    </td>
				    <td>
					<xsl:value-of select="value_reserve_remainder"/>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
					<xsl:text> % )</xsl:text>
				    </td>
				</tr>
			    </xsl:when>
			    <xsl:otherwise>
			    </xsl:otherwise>
			</xsl:choose>
			<tr>
			    <td class="th_text" valign="top">
				<xsl:value-of select="lang_workorder_id"/>
			    </td>
			    <xsl:choose>
				<xsl:when test="sum_workorder_budget=''">
				    <td class="th_text">
					<xsl:value-of select="lang_no_workorders"/>
				    </td>
				</xsl:when>
				<xsl:otherwise>
				    <td>
					<select id = "order_time_span" name="order_time_span">
					    <xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'select')"/>
					    </xsl:attribute>
					    <option value="0">
						<xsl:value-of select="php:function('lang', 'select')"/>
					    </option>
					    <xsl:apply-templates select="order_time_span/options"/>
					</select>
					<!-- DataTable -->
					<div id="paging_1"> </div>
					<div id="datatable-container_1"/>
				    </td>
				</xsl:otherwise>
			    </xsl:choose>
			</tr>
			<tr>
			    <td valign="top" class="th_text">
				<xsl:value-of select="php:function('lang', 'invoice')"/>
			    </td>
			    <td>
				<div id="paging_2"> </div>
				<div id="datatable-container_2"/>
			    </td>
			</tr>

		    </table>
		    <!--  DATATABLE DEFINITIONS-->
		    <script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_notify_url = <xsl:value-of select="base_java_notify_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();

			<xsl:for-each select="datatable">
			    datatable[<xsl:value-of select="name"/>] = [
			    {
			    values:<xsl:value-of select="values"/>,
			    total_records: <xsl:value-of select="total_records"/>,
			    edit_action:  <xsl:value-of select="edit_action"/>,
			    is_paginator:  <xsl:value-of select="is_paginator"/>,
			    <xsl:if test="rows_per_page">
				rows_per_page: "<xsl:value-of select="rows_per_page"/>",
			    </xsl:if>
			    <xsl:if test="initial_page">
				initial_page: "<xsl:value-of select="initial_page"/>",
			    </xsl:if>
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
		</div>
		<div id="coordination">
		    <table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:variable name="lang_contact_statustext">
			    <xsl:value-of select="php:function('lang', 'click this link to select')"/>
			</xsl:variable>
			<tr>
			    <td valign="top">
				<a href="javascript:notify_contact_lookup()" title="{$lang_contact_statustext}">
				    <xsl:value-of select="php:function('lang', 'contact')"/>
				</a>
			    </td>
			    <td>
				<table>
				    <tr>
					<td>
					    <input type="hidden" id="notify_contact" name="notify_contact" value="" title="{$lang_contact_statustext}">
					    </input>
					    <input type="hidden" name="notify_contact_name" value="" onClick="notify_contact_lookup();" readonly="readonly" title="{$lang_contact_statustext}"/>
					</td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td valign="top" class="th_text">
				<xsl:value-of select="php:function('lang', 'notify')"/>
			    </td>
			    <td>
				<div id="paging_3"> </div>
				<div id="datatable-container_3"/>
				<div id="datatable-buttons_3"/>
			    </td>
			</tr>
			<xsl:choose>
			    <xsl:when test="suppresscoordination =''">
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_branch"/>
				    </td>
				    <td>
					<xsl:variable name="lang_branch_statustext">
					    <xsl:value-of select="lang_branch_statustext"/>
					</xsl:variable>
					<select name="values[branch][]" class="forms" multiple="multiple" title="{$lang_branch_statustext}">
					    <xsl:apply-templates select="branch_list"/>
					</select>
				    </td>
				</tr>
				<tr>
				    <td valign="top">
					<xsl:value-of select="lang_other_branch"/>
				    </td>
				    <td>
					<input type="text" name="values[other_branch]" value="{value_other_branch}">
					    <xsl:attribute name="title">
						<xsl:value-of select="lang_other_branch_statustext"/>
					    </xsl:attribute>
					</input>
				    </td>
				</tr>
				<tr>
				    <td>
					<xsl:value-of select="lang_key_fetch"/>
				    </td>
				    <td>
					<xsl:variable name="lang_key_fetch_statustext">
					    <xsl:value-of select="lang_key_fetch_statustext"/>
					</xsl:variable>
					<select name="values[key_fetch]" class="forms" title="{$lang_key_fetch_statustext}">
					    <option value="">
						<xsl:value-of select="lang_no_key_fetch"/>
					    </option>
					    <xsl:apply-templates select="key_fetch_list"/>
					</select>
				    </td>
				</tr>
				<tr>
				    <td>
					<xsl:value-of select="lang_key_deliver"/>
				    </td>
				    <td>
					<xsl:variable name="lang_key_deliver_statustext">
					    <xsl:value-of select="lang_key_deliver_statustext"/>
					</xsl:variable>
					<select name="values[key_deliver]" class="forms" onMouseover="window.status='{$lang_key_deliver_statustext}'; return true;" onMouseout="window.status='';return true;">
					    <option value="">
						<xsl:value-of select="lang_no_key_deliver"/>
					    </option>
					    <xsl:apply-templates select="key_deliver_list"/>
					</select>
				    </td>
				</tr>
				<tr>
				    <td>
					<xsl:value-of select="lang_key_responsible"/>
				    </td>
				    <td>
					<xsl:variable name="lang_key_responsible_statustext">
					    <xsl:value-of select="lang_key_responsible_statustext"/>
					</xsl:variable>
					<select name="values[key_responsible]" class="forms" onMouseover="window.status='{$lang_key_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
					    <option value="">
						<xsl:value-of select="lang_no_key_responsible"/>
					    </option>
					    <xsl:apply-templates select="key_responsible_list"/>
					</select>
				    </td>
				</tr>
			    </xsl:when>
			</xsl:choose>
		    </table>
		</div>
		<xsl:choose>
		    <xsl:when test="value_project_id &gt; 0">
			<div id="documents">
			    <table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
				    <td align="left" valign="top">
					<xsl:value-of select="php:function('lang', 'files')"/>
				    </td>
				    <td>
					<div id="paging_5"> </div>
					<div id="datatable-container_5"/>
				    </td>
				</tr>
				<xsl:call-template name="file_upload"/>
			    </table>
			</div>
			<div id="history">
			    <div id="paging_4"> </div>
			    <div id="datatable-container_4"/>
			</div>
		    </xsl:when>
		</xsl:choose>

		<xsl:call-template name="attributes_values"/>
	    </div>
	</div>
	<xsl:choose>
	    <xsl:when test="mode='edit'">
		<table>
		    <tr height="50">
			<td>
			    <xsl:variable name="lang_save">
				<xsl:value-of select="lang_save"/>
			    </xsl:variable>
			    <input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
				    <xsl:value-of select="lang_save_statustext"/>
				</xsl:attribute>
			    </input>
			</td>
		    </tr>
		</table>
	    </xsl:when>
	</xsl:choose>
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
		    <input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
			    <xsl:value-of select="lang_done_statustext"/>
			</xsl:attribute>
		    </input>
		</form>
	    </td>
	    <xsl:choose>
		<xsl:when test="mode='view'">
		    <td>
			<xsl:variable name="edit_action">
			    <xsl:value-of select="edit_action"/>
			</xsl:variable>
			<xsl:variable name="lang_edit">
			    <xsl:value-of select="lang_edit"/>
			</xsl:variable>
			<form method="post" action="{$edit_action}">
			    <input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
				    <xsl:value-of select="lang_edit_statustext"/>
				</xsl:attribute>
			    </input>
			</form>
		    </td>
		</xsl:when>
	    </xsl:choose>
	</tr>
    </table>
    <!-- AQUI VA EL SCRIPT -->
    <xsl:choose>
	<xsl:when test="mode='edit'">
	    <xsl:variable name="add_sub_entry_action">
		<xsl:value-of select="add_sub_entry_action"/>
	    </xsl:variable>
	    <form method="post" name="add_sub_entry_form" action="{$add_sub_entry_action}">
	    </form>
	</xsl:when>
    </xsl:choose>
</xsl:template>


<!-- New template-->
<xsl:template match="branch_list">
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
<xsl:template match="key_responsible_list">
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
<xsl:template match="key_fetch_list">
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
<xsl:template match="key_deliver_list">
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
<xsl:template xmlns:php="http://php.net/xsl" match="bulk_update_status">
    <div align="left">
	<table cellpadding="2" cellspacing="2" width="80%" align="center">
	    <form name="form" method="post" action="{update_action}">
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'start date')"/>
		    </td>
		    <td>
			<input type="text" id="values_start_date" name="start_date" size="10" value="{start_date}" readonly="readonly">
			    <xsl:attribute name="title">
				<xsl:value-of select="lang_start_date_statustext"/>
			    </xsl:attribute>
			</input>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'end date')"/>
		    </td>
		    <td>
			<input type="text" id="values_end_date" name="end_date" size="10" value="{end_date}" readonly="readonly">
			    <xsl:attribute name="title">
				<xsl:value-of select="lang_end_date_statustext"/>
			    </xsl:attribute>
			</input>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'dimb')"/>
		    </td>
		    <td>
			<select name="ecodimb">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select')"/>
			    </xsl:attribute>
			    <option value="0">
				<xsl:value-of select="php:function('lang', 'select')"/>
			    </option>
			    <xsl:apply-templates select="ecodimb_list/options"/>
			</select>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'user')"/>
		    </td>
		    <td>
			<select name="user_id">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select user')"/>
			    </xsl:attribute>
			    <option value="0">
				<xsl:value-of select="php:function('lang', 'select')"/>
			    </option>
			    <xsl:apply-templates select="user_list/options"/>
			</select>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'type')"/>
		    </td>
		    <td>
			<select name="type" onChange="this.form.submit();">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select type')"/>
			    </xsl:attribute>
			    <xsl:apply-templates select="type_list/options"/>
			</select>
		    </td>
		</tr>
		<xsl:choose>
		    <xsl:when test="type='workorder'">
			<xsl:call-template name="b_account_form"/>
		    </xsl:when>
		</xsl:choose>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'status filter')"/>
		    </td>
		    <td>
			<select name="status_filter">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select status')"/>
			    </xsl:attribute>
			    <option value="0">
				<xsl:value-of select="php:function('lang', 'select status')"/>
			    </option>
			    <xsl:apply-templates select="status_list_filter/options"/>
			</select>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'status new')"/>
		    </td>
		    <td>
			<select name="status_new">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'select status')"/>
			    </xsl:attribute>
			    <option value="0">
				<xsl:value-of select="php:function('lang', 'select status')"/>
			    </option>
			    <xsl:apply-templates select="status_list_new/options"/>
			</select>
		    </td>
		</tr>
		<tr>
		    <td>
			<xsl:value-of select="php:function('lang', 'transfer budget')"/>
		    </td>
		    <td>
			<select name="transfer_budget">
			    <xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'transfer budget')"/>
			    </xsl:attribute>
			    <option value="0">
				<xsl:value-of select="php:function('lang', 'select year')"/>
			    </option>
			    <xsl:apply-templates select="year_list/options"/>
			</select>
		    </td>
		</tr>
		<xsl:choose>
		    <xsl:when test="check_paid = 1">
			<tr>
			    <td>
				<xsl:value-of select="php:function('lang', 'paid')"/>
			    </td>
			    <td>
				<input type="checkbox" name="paid" value="True">
				    <xsl:if test="paid = 1">
					<xsl:attribute name="checked" value="checked"/>
				    </xsl:if>
				    <xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'workorder')"/>
				    </xsl:attribute>
				</input>
			    </td>
			</tr>
		    </xsl:when>
		</xsl:choose>
		<xsl:choose>
		    <xsl:when test="check_closed_orders = 1">
			<tr>
			    <td>
				<xsl:value-of select="php:function('lang', 'closed')"/>
			    </td>
			    <td>
				<input type="checkbox" name="closed_orders" value="True">
				    <xsl:if test="closed_orders = 1">
					<xsl:attribute name="checked" value="checked"/>
				    </xsl:if>
				    <xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'projekt')"/>
				    </xsl:attribute>
				</input>
			    </td>
			</tr>
		    </xsl:when>
		</xsl:choose>
		<tr>
		    <td>
		    </td>
		    <td>
			<input type="submit" name="get_list">
			    <xsl:attribute name="value">
				<xsl:value-of select="php:function('lang', 'get list')"/>
			    </xsl:attribute>
			</input>
			<input type="submit" name="execute" onClick="onActionsClick()">
			    <xsl:attribute name="value">
				<xsl:value-of select="php:function('lang', 'save')"/>
			    </xsl:attribute>
			</input>
		    </td>
		</tr>
		<tr>
		    <td valign="top">
			<xsl:value-of select="total_records"/>
		    </td>
		    <td>
			<div id="paging_0"> </div>
			<div id="datatable-container_0"/>
			<input type="hidden" name="id_to_update" value=""/>
			<input type="hidden" name="new_budget" value=""/>
		    </td>
		</tr>
	    </form>
	</table>
    </div>
    <!--  DATATABLE DEFINITIONS-->
    <script type="text/javascript">
	var property_js = <xsl:value-of select="property_js"/>;
	//var base_java_url = <xsl:value-of select="base_java_url"/>;
	var datatable = new Array();
	var myColumnDefs = new Array();
	var td_count = <xsl:value-of select="td_count"/>;
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
