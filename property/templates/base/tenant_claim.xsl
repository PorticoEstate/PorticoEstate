  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<script type="text/javascript">
			self.name="first_Window";
			function tenant_lookup()
			{
				Window1=window.open('<xsl:value-of select="tenant_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<div class="yui-navset" id="edit_tabview">
			<div class="yui-content">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>
				<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$edit_url}">
					<table cellpadding="2" cellspacing="2" width="79%" align="left">
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
							<xsl:when test="value_claim_id!=''">
								<tr>
									<td width="25%" align="left">
										<xsl:value-of select="lang_claim_id"/>
									</td>
									<td width="75%" align="left">
										<xsl:value-of select="value_claim_id"/>
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
						<xsl:for-each select="value_origin">
							<tr>
								<td valign="top">
									<xsl:value-of select="descr"/>
								</td>
								<td class="th_text" align="left">
									<xsl:for-each select="data">
										<a href="{link}" title="{//lang_origin_statustext}" style="cursor:help">
											<xsl:value-of select="id"/>
										</a>
										<xsl:text> </xsl:text>
									</xsl:for-each>
								</td>
							</tr>
						</xsl:for-each>
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
								<xsl:value-of select="value_descr"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_category"/>
							</td>
							<xsl:for-each select="cat_list_project">
								<xsl:choose>
									<xsl:when test="selected='selected'">
										<td>
											<xsl:value-of select="name"/>
										</td>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</tr>
						<xsl:call-template name="location_view"/>
						<xsl:choose>
							<xsl:when test="contact_phone !=''">
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_contact_phone"/>
									</td>
									<td align="left">
										<xsl:value-of select="contact_phone"/>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_power_meter"/>
							</td>
							<td>
								<xsl:value-of select="value_power_meter"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_charge_tenant"/>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="charge_tenant='1'">
										<b>X</b>
									</xsl:when>
								</xsl:choose>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_budget"/>
							</td>
							<td><xsl:value-of select="value_budget"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_reserve"/>
							</td>
							<td>
								<xsl:value-of select="value_reserve"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_reserve_remainder"/>
							</td>
							<td>
								<xsl:value-of select="value_reserve_remainder"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								<xsl:text> </xsl:text> ( <xsl:value-of select="value_reserve_remainder_percent"/>
								<xsl:text> % )</xsl:text>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_actual_cost"/>
							</td>
							<td>
								<xsl:value-of select="sum_workorder_actual_cost"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div id="datatable-container_0"/>
							</td>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_coordinator"/>
							</td>
							<xsl:for-each select="user_list">
								<xsl:choose>
									<xsl:when test="selected">
										<td>
											<xsl:value-of select="name"/>
										</td>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</tr>
						<tr>
							<td>
								<xsl:value-of select="lang_status"/>
							</td>
							<xsl:for-each select="status_list">
								<xsl:choose>
									<xsl:when test="selected">
										<td>
											<xsl:value-of select="name"/>
										</td>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'entry date')" />
							</td>
							<td>
								<xsl:value-of select="value_entry_date"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_start_date"/>
							</td>
							<td>
								<xsl:value-of select="value_start_date"/>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="lang_end_date"/>
							</td>
							<td>
								<xsl:value-of select="value_end_date"/>
							</td>
						</tr>
						<tr>
							<td align="left">
								<xsl:value-of select="lang_status"/>
							</td>
							<td align="left">
								<xsl:call-template name="status_select"/>
							</td>
						</tr>
						<tr>
							<td>
								<a href="javascript:tenant_lookup()" onMouseover="window.status='{lang_tenant_statustext}';return true;" onMouseout="window.status='';return true;">
									<xsl:value-of select="lang_tenant"/>
								</a>
							</td>
							<td>
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
							</td>
						</tr>
						<!--
<tr>
<td valign="top">
<xsl:value-of select="php:function('lang', 'deposit claim')" />
</td>
<td>
<input type="text" name="values[deposit_claim]" value="{value_deposit_claim}">
<xsl:attribute name="title">
<xsl:value-of select="php:function('lang', 'deposit claim')" />
</xsl:attribute>
</input>
<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
</td>
</tr>

<tr  align="left">
<td valign="top" >
<xsl:value-of select="php:function('lang', 'remark')" />
</td>
<td align="left">

		    <textarea cols="60" rows="6" name="values[deposit_claim_text]">
		    <xsl:attribute name="title">
		    <xsl:value-of select="lang_remark_statustext"/>
		    </xsl:attribute>
		    <xsl:value-of select="value_deposit_claim_text"/>
		    </textarea>
		    </td>
		    </tr>
		    <tr>
		    <td valign="top">
		    <xsl:value-of select="php:function('lang', 'main claim')" />
		    </td>
		    </tr>
		    -->
		    <xsl:call-template name="b_account_form"/>
		    <tr>
			<td valign="top">
			    <xsl:value-of select="lang_amount"/>
			</td>
			<td>
			    <input type="text" name="values[amount]" value="{value_amount}" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
				    <xsl:value-of select="lang_amount_statustext"/>
				</xsl:attribute>
			    </input>
			    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
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
		    <tr align="left">
			<td valign="top">
			    <xsl:value-of select="lang_remark"/>
			</td>
			<td align="left">
			    <textarea cols="60" rows="6" name="values[remark]">
				<xsl:attribute name="title">
				    <xsl:value-of select="lang_remark_statustext"/>
				</xsl:attribute>
				<xsl:value-of select="value_remark"/>
			    </textarea>
			</td>
		    </tr>
		    <xsl:choose>
			<xsl:when test="value_claim_id!=''">
			    <tr>
				<td align="left" valign="top">
				    <xsl:value-of select="php:function('lang', 'files')"/>
				</td>
				<td>
				    <div id="paging_1"> </div>
				    <div id="datatable-container_1"/>
				</td>
			    </tr>
			    <xsl:call-template name="file_upload"/>
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
		    <tr>
			<td colspan="2">
			    <div id="datatable-container_2"/>
			</td>
		    </tr>

		</table>
	    </form>
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
	    </script>
	</div>
    </div>
</xsl:template>

<!-- New template-->
<!-- view -->
<xsl:template match="view" xmlns:php="http://php.net/xsl">
    <div align="left">
	<table cellpadding="2" cellspacing="2" width="79%" align="left">
	    <tr>
		<td width="25%" align="left">
		    <xsl:value-of select="lang_claim_id"/>
		</td>
		<td width="75%" align="left">
		    <xsl:value-of select="value_claim_id"/>
		</td>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_project_id"/>
		</td>
		<td>
		    <xsl:value-of select="value_project_id"/>
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
		    <xsl:value-of select="value_descr"/>
		</td>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_category"/>
		</td>
		<xsl:for-each select="cat_list_project">
		    <xsl:choose>
			<xsl:when test="selected='selected'">
			    <td>
				<xsl:value-of select="name"/>
			    </td>
			</xsl:when>
		    </xsl:choose>
		</xsl:for-each>
	    </tr>
	    <xsl:call-template name="location_view"/>
	    <xsl:choose>
		<xsl:when test="contact_phone !=''">
		    <tr>
			<td class="th_text" align="left">
			    <xsl:value-of select="lang_contact_phone"/>
			</td>
			<td align="left">
			    <xsl:value-of select="contact_phone"/>
			</td>
		    </tr>
		</xsl:when>
	    </xsl:choose>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_power_meter"/>
		</td>
		<td>
		    <xsl:value-of select="value_power_meter"/>
		</td>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_charge_tenant"/>
		</td>
		<td>
		    <xsl:choose>
			<xsl:when test="charge_tenant='1'">
			    <b>X</b>
			</xsl:when>
		    </xsl:choose>
		</td>
	    </tr>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_budget"/>
		</td>
		<td>
		    <xsl:value-of select="value_budget"/>
		    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
		</td>
	    </tr>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_reserve"/>
		</td>
		<td>
		    <xsl:value-of select="value_reserve"/>
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
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_actual_cost"/>
		</td>
		<td>
		    <xsl:value-of select="sum_workorder_actual_cost"/>
		    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
		</td>
	    </tr>
	    <tr>
		<xsl:choose>
		    <xsl:when test="sum_workorder_budget=''">
			<td class="th_text">
			    <xsl:value-of select="lang_no_workorders"/>
			</td>
		    </xsl:when>
		    <xsl:otherwise>
			<td colspan="2">
			    <table width="80%" cellpadding="2" cellspacing="2" align="left">
				<xsl:apply-templates select="table_header_workorder"/>
				<xsl:apply-templates select="workorder_budget"/>
				<tr class="th">
				    <td class="th_text" align="right">
					<xsl:value-of select="lang_sum"/>
				    </td>
				    <td class="th_text" align="right">
					<xsl:value-of select="sum_workorder_budget"/>
				    </td>
				    <td class="th_text" align="right">
					<xsl:value-of select="sum_workorder_calculation"/>
				    </td>
				    <td>
				    </td>
				    <td>
				    </td>
				    <td>
				    </td>
				</tr>
			    </table>
			</td>
		    </xsl:otherwise>
		</xsl:choose>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_coordinator"/>
		</td>
		<xsl:for-each select="user_list">
		    <xsl:choose>
			<xsl:when test="selected">
			    <td>
				<xsl:value-of select="name"/>
			    </td>
			</xsl:when>
		    </xsl:choose>
		</xsl:for-each>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_status"/>
		</td>
		<xsl:for-each select="status_list">
		    <xsl:choose>
			<xsl:when test="selected">
			    <td>
				<xsl:value-of select="name"/>
			    </td>
			</xsl:when>
		    </xsl:choose>
		</xsl:for-each>
	    </tr>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="php:function('lang', 'entry date')" />
		</td>
		<td>
		    <xsl:value-of select="value_entry_date"/>
		</td>
	    </tr>

	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_start_date"/>
		</td>
		<td>
		    <xsl:value-of select="value_start_date"/>
		</td>
	    </tr>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_end_date"/>
		</td>
		<td>
		    <xsl:value-of select="value_end_date"/>
		</td>
	    </tr>
	    <tr>
		<td align="left">
		    <xsl:value-of select="lang_status"/>
		</td>
		<xsl:for-each select="status_list">
		    <xsl:choose>
			<xsl:when test="selected='selected'">
			    <td>
				<xsl:value-of select="name"/>
			    </td>
			</xsl:when>
		    </xsl:choose>
		</xsl:for-each>
	    </tr>
	    <tr>
		<td>
		    <xsl:value-of select="lang_tenant"/>
		</td>
		<td>
		    <input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" readonly="readonly">
		    </input>
		    <input size="{size_first_name}" type="text" name="first_name" value="{value_first_name}" readonly="readonly">
		    </input>
		</td>
	    </tr>
	    <xsl:call-template name="b_account_view"/>
	    <tr>
		<td valign="top">
		    <xsl:value-of select="lang_amount"/>
		</td>
		<td>
		    <xsl:value-of select="value_amount"/>
		    <xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
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
	    <tr align="left">
		<td valign="top">
		    <xsl:value-of select="lang_remark"/>
		</td>
		<td align="left">
		    <textarea cols="60" rows="6" name="values[remark]" onMouseout="window.status='';return true;">
			<xsl:attribute name="onMouseover">
			    <xsl:text>window.status='</xsl:text>
			    <xsl:value-of select="lang_remark_statustext"/>
			    <xsl:text>'; return true;</xsl:text>
			</xsl:attribute>
			<xsl:value-of select="value_remark"/>
		    </textarea>
		</td>
	    </tr>
	    <tr height="50">
		<td>
		    <xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		    </xsl:variable>
		    <xsl:variable name="lang_done">
			<xsl:value-of select="lang_done"/>
		    </xsl:variable>
		    <form method="post" action="{$done_action}">
			<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
		    </form>
		</td>
	    </tr>
	</table>
    </div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_workorder">
    <tr class="th">
	<td class="th_text" width="4%" align="right">
	    <xsl:value-of select="lang_workorder_id"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_budget"/>
	</td>
	<td class="th_text" width="5%" align="right">
	    <xsl:value-of select="lang_calculation"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_vendor"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_charge_tenant"/>
	</td>
	<td class="th_text" width="10%" align="right">
	    <xsl:value-of select="lang_select"/>
	</td>
    </tr>
</xsl:template>

<!-- New template-->
<xsl:template match="workorder_budget">
    <xsl:variable name="workorder_link">
	<xsl:value-of select="//workorder_link"/>&amp;id=<xsl:value-of select="workorder_id"/>
    </xsl:variable>
    <xsl:variable name="workorder_id">
	<xsl:value-of select="workorder_id"/>
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
	<td align="right">
	    <a href="{$workorder_link}" target="_blank">
		<xsl:value-of select="workorder_id"/>
	    </a>
	</td>
	<td align="right">
	    <xsl:value-of select="budget"/>
	</td>
	<td align="right">
	    <xsl:value-of select="calculation"/>
	</td>
	<td align="left">
	    <xsl:value-of select="vendor_name"/>
	</td>
	<td align="center">
	    <xsl:choose>
		<xsl:when test="charge_tenant='1'">
		    <b>x</b>
		</xsl:when>
	    </xsl:choose>
	    <xsl:choose>
		<xsl:when test="claimed!=''">
		    <b>
			<xsl:text>[</xsl:text>
			<xsl:value-of select="claimed"/>
			<xsl:text>]</xsl:text>
		    </b>
		</xsl:when>
	    </xsl:choose>
	</td>
	<td align="center">
	    <xsl:choose>
		<xsl:when test="selected = 1">
		    <input type="checkbox" name="values[workorder][]" value="{$workorder_id}" checked="checked" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
			    <xsl:value-of select="//lang_select_workorder_statustext"/>
			</xsl:attribute>
		    </input>
		</xsl:when>
		<xsl:otherwise>
		    <input type="checkbox" name="values[workorder][]" value="{$workorder_id}" onMouseout="window.status='';return true;">
			<xsl:attribute name="title">
			    <xsl:value-of select="//lang_select_workorder_statustext"/>
			</xsl:attribute>
		    </input>
		</xsl:otherwise>
	    </xsl:choose>
	</td>
    </tr>
</xsl:template>
