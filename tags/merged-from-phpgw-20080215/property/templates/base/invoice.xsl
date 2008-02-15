<!-- $Id: invoice.xsl 18358 2007-11-27 04:43:37Z skwashd $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="import">
				<xsl:apply-templates select="import"/>
			</xsl:when>
			<xsl:when test="export">
				<xsl:apply-templates select="export"/>
			</xsl:when>
			<xsl:when test="rollback">
				<xsl:apply-templates select="rollback"/>
			</xsl:when>
			<xsl:when test="debug">
				<xsl:apply-templates select="debug"/>
			</xsl:when>
			<xsl:when test="edit_period">
				<xsl:apply-templates select="edit_period"/>
			</xsl:when>
			<xsl:when test="list_voucher">
				<xsl:apply-templates select="list_voucher"/>
			</xsl:when>
			<xsl:when test="list_voucher_paid">
				<xsl:apply-templates select="list_voucher_paid"/>
			</xsl:when>
			<xsl:when test="consume">
				<xsl:apply-templates select="consume"/>
			</xsl:when>
			<xsl:when test="remark">
				<xsl:apply-templates select="remark"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_invoice_sub"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="edit_period">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
	        <div align="center">
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
				<td>
				<xsl:variable name="select_name"><xsl:value-of select="select_name"/></xsl:variable>
				<select name="{$select_name}" class="forms" >
					<xsl:apply-templates select="period_list"/>
				</select>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="submit" value="{$lang_save}" >
					</input>
				</td>
			</tr>
		</table>
		</div>
		</form> 
	</xsl:template>

	<xsl:template match="period_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	<xsl:template match="remark">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td colspan="2" align="center">
					<xsl:value-of select="message"/>
				</td>
			</tr>
			<tr>
				<td align="center">
					<textarea cols="60" rows="15" name="remark" readonly="readonly" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_content_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="remark"/>		
					</textarea>
				</td>
			</tr>

		</table>
	</xsl:template>

	<xsl:template name="excel">
			<xsl:variable name="link_excel"><xsl:value-of select="link_excel"/></xsl:variable>
			<xsl:variable name="lang_excel_help"><xsl:value-of select="lang_excel_help"/></xsl:variable>
			<xsl:variable name="lang_excel"><xsl:value-of select="lang_excel"/></xsl:variable>
			<a href="javascript:var w=window.open('{$link_excel}','','')"
				onMouseOver="overlib('{$lang_excel_help}', CAPTION, '{$lang_excel}')"
				onMouseOut="nd()">
				<xsl:value-of select="lang_excel"/></a>
	</xsl:template>


<!-- list_voucher -->

	<xsl:template match="list_voucher">
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
				<td>
					<xsl:call-template name="cat_filter"/>
				</td>
				<td align="center">
					<xsl:call-template name="user_lid_filter"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
				<td colspan="3" width="100%" class="small_text" valign="top" align="left">
					<xsl:call-template name="excel"/>
				</td>
			</tr>
		</table>
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" name="form" action="{$form_action}">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_list_voucher"/>
			
			<xsl:choose>
				<xsl:when test="values_list_voucher[voucher_id]">
					<xsl:apply-templates select="values_list_voucher"/>

				</xsl:when>
			</xsl:choose>
			<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="small_text" align="right">
					<xsl:value-of select="sum"/>
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="center">
				<a href="javascript:check_all_radio('sign_none')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
			    	</td>
			    	<td align="center">
			    	 <a href="javascript:check_all_radio('sign_janitor')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
			    	</td>
			    	<td align="center">
			    	 <a href="javascript:check_all_radio('sign_supervisor')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
			    	</td>
			    	<td align="center">
			    	  <a href="javascript:check_all_radio('sign_budget_responsible')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
			    	</td>
			    	<td align="right">
			    	  <a href="javascript:check_all_checkbox('values[transfer]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
			    	</td>
			  </tr>
			
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

		</table>
		</form> 
		<xsl:choose>
			<xsl:when test="table_add_invoice !=''">
				<xsl:apply-templates select="table_add_invoice"/>
			</xsl:when>
		</xsl:choose>	
	</xsl:template>

	<xsl:template match="table_add_invoice">
		<table align = "left">
			<tr>
				<td height="50" align="left" valign="top">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
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
		</table>
	</xsl:template>
	
	
	<xsl:template match="table_header_list_voucher">
		<xsl:variable name="sort_voucher"><xsl:value-of select="sort_voucher"/></xsl:variable>
		<xsl:variable name="sort_voucher_date"><xsl:value-of select="sort_voucher_date"/></xsl:variable>
		<xsl:variable name="sort_vendor_id"><xsl:value-of select="sort_vendor_id"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="5%" align="right">
					<a href="{$sort_voucher}"><xsl:value-of select="lang_voucher"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<a href="{$sort_voucher_date}"><xsl:value-of select="lang_voucher_date"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_days"/>
				</td>
				<td class="th_text" width="8%" align="right">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td class="th_text" width="4%" align="right">
					<a href="{$sort_vendor_id}"><xsl:value-of select="lang_vendor_id"/></a>
				</td>
				<td class="th_text" width="3%" align="right">
					<xsl:value-of select="lang_num_sub_invoice"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_type"/>
				</td>
				<td class="th_text" width="3%" align="right">
					<xsl:value-of select="lang_period"/>
				</td>
				<td class="th_text" width="3%" align="right">
					<xsl:value-of select="lang_kredit"/>
				</td>
				<td class="th_text" width="3%" align="right">
					<xsl:value-of select="lang_none"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_janitor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_supervisor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_budget_responsible"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_transfer"/>
				</td>
				<xsl:choose>
					<xsl:when test="//acl_delete!=''">
						<td class="th_text" width="5%" align="center">
							<xsl:value-of select="lang_delete"/>
						</td>
					</xsl:when>
				</xsl:choose>
				<td class="th_text" width="1%" align="right">
					<xsl:value-of select="lang_front"/>
				</td>

			</tr>
	</xsl:template>

	<xsl:template match="values_list_voucher">

			<xsl:variable name="counter"><xsl:value-of select="counter"/></xsl:variable>
			<xsl:variable name="current_user"><xsl:value-of select="current_user"/></xsl:variable>
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

				<td class="small_text" align="right">
					<input type="hidden" name="values[counter][{$counter}]" value="{counter}">
					</input>
					<input type="hidden" name="values[voucher_id][{$counter}]" value="{voucher_id}">
					</input>
					<xsl:variable name="link_sub"><xsl:value-of select="link_sub"/>&amp;voucher_id=<xsl:value-of select="voucher_id"/></xsl:variable>
					<xsl:variable name="lang_sub_help"><xsl:value-of select="lang_sub_help"/></xsl:variable>
					<xsl:variable name="lang_sub"><xsl:value-of select="lang_sub"/></xsl:variable>
					<a href="{$link_sub}"
					onMouseOver="overlib('{$lang_sub_help}', CAPTION, '{$lang_sub}')"
					onMouseOut="nd()">
					<xsl:value-of select="voucher_id"/></a>					
				</td>
				<td class="small_text" align="right">
					<xsl:variable name="lang_payment_date"><xsl:value-of select="lang_payment_date"/></xsl:variable>
					<xsl:variable name="payment_date"><xsl:value-of select="payment_date"/></xsl:variable>
					<a href="javascript:void()"
					onMouseOver="overlib('{$payment_date}', CAPTION, '{$lang_payment_date}')"
					onMouseOut="nd()">
					<xsl:value-of select="voucher_date"/></a>								
				</td>
				<td class="small_text" align="right">
					<input type="hidden" name="values[sign_orig][{$counter}]" value="{sign_orig}">
					</input>
					<input type="hidden" name="values[num_days_orig][{$counter}]" value="{num_days}">
					</input>
					<input type="hidden" name="values[timestamp_voucher_date][{$counter}]" value="{timestamp_voucher_date}">
					</input>
					<input type="text" size="2" name="values[num_days][{$counter}]" value="{num_days}">
					</input>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="amount"/>
				</td>
				<td class="small_text" align="right">
					<xsl:variable name="vendor"><xsl:value-of select="vendor"/></xsl:variable>
					<xsl:variable name="vendor_id"><xsl:value-of select="vendor_id"/></xsl:variable>
					<a href="javascript:void()"
					onMouseOver="overlib('{$vendor}', CAPTION, '{$vendor_id}')"
					onMouseOut="nd()">
					<xsl:value-of select="vendor_id"/></a>

				</td>
				<td class="small_text" align="right">
					<input type="hidden" name="values[invoice_count][{$counter}]" value="{invoice_count}">
					</input>
					<xsl:value-of select="invoice_count"/>
				</td>
				<td class="small_text" align="center">
					<xsl:value-of select="type"/>
				</td>
				<td class="small_text" align="right">
					<xsl:variable name="link_period"><xsl:value-of select="link_period"/>&amp;voucher_id=<xsl:value-of select="voucher_id"/>&amp;period=<xsl:value-of select="period"/></xsl:variable>
					<xsl:variable name="lang_period_help"><xsl:value-of select="lang_period_help"/></xsl:variable>
					<xsl:variable name="lang_period"><xsl:value-of select="lang_period"/></xsl:variable>
					<a href="javascript:var w=window.open('{$link_period}','','width=150,height=150')"
					onMouseOver="overlib('{$lang_period_help}', CAPTION, '{$lang_period}')"
					onMouseOut="nd()">
					<xsl:value-of select="period"/></a>					
				</td>
				<td align="center">
					<xsl:choose>
						<xsl:when test="kreditnota='1'">
							<input type="checkbox" name="values[kreditnota][{$counter}]" value="true" checked="checked" onMouseout="window.status='';return true;">
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[kreditnota][{$counter}]" value="true" onMouseout="window.status='';return true;">
							</input>							
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="is_janitor">
							<input type="radio" name="values[sign][{$counter}]" value="sign_none" onMouseout="window.status='';return true;">
							</input>							
						</xsl:when>
						<xsl:when test="is_supervisor">
							<input type="radio" name="values[sign][{$counter}]" value="sign_none" onMouseout="window.status='';return true;">
							</input>							
						</xsl:when>
						<xsl:when test="is_budget_responsible">
							<input type="radio" name="values[sign][{$counter}]" value="sign_none" onMouseout="window.status='';return true;">
							</input>							
						</xsl:when>
						<xsl:otherwise>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="right">
	
					<xsl:choose>
						<xsl:when test="is_janitor='1'">
							<xsl:choose>
								<xsl:when test="jan_date=''">
									<input type="radio" name="values[sign][{$counter}]" value="sign_janitor" onMouseout="window.status='';return true;">
									</input>							
								</xsl:when>
								<xsl:otherwise>
									<xsl:choose>
										<xsl:when test="janitor = $current_user">
											<input type="radio" name="values[sign][{$counter}]" value="sign_janitor" checked="checked" onMouseout="window.status='';return true;">
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="jan_date=''">
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>

					<xsl:value-of select="janitor"/>
				</td>
				<td class="small_text" align="right">
				
					<xsl:choose>
						<xsl:when test="is_supervisor='1'"><xsl:value-of select="super_date"/>
							<xsl:choose>
								<xsl:when test="super_date=''">
									<input type="radio" name="values[sign][{$counter}]" value="sign_supervisor" onMouseout="window.status='';return true;">
									</input>							
								</xsl:when>
								<xsl:when test="super_date!=''">
									<xsl:choose>
										<xsl:when test="supervisor = $current_user">
											<input type="radio" name="values[sign][{$counter}]" value="sign_supervisor" checked="checked" onMouseout="window.status='';return true;">
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:when>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="super_date=''">
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>

					<xsl:value-of select="supervisor"/>
				</td>
				<td class="small_text" align="right">
					<xsl:choose>
						<xsl:when test="is_budget_responsible='1'">
							<xsl:choose>
								<xsl:when test="budget_date=''">
									<input type="radio" name="values[sign][{$counter}]" value="sign_budget_responsible" onMouseout="window.status='';return true;">
									</input>							
								</xsl:when>
								<xsl:otherwise>
									<xsl:choose>
										<xsl:when test="budget_responsible = $current_user">
											<input type="radio" name="values[sign][{$counter}]" value="sign_budget_responsible" checked="checked" onMouseout="window.status='';return true;">
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="budget_date=''">
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="budget_responsible"/>
				</td>
				<td class="small_text" align="right">
					<xsl:choose>
						<xsl:when test="is_transfer='1'">
							<xsl:choose>
								<xsl:when test="transfer_date=''">
									<input type="checkbox" name="values[transfer][{$counter}]" value="true" onMouseout="window.status='';return true;">
									</input>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="values[transfer][{$counter}]" value="true" checked="checked" onMouseout="window.status='';return true;">
									</input>							
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="transfer_id!=''">
									<input type="checkbox" name="" value="" checked="checked" disabled="disabled" ></input>
								</xsl:when>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="transfer_id"/>
				</td>
				<xsl:choose>
					<xsl:when test="//acl_delete!=''">
						<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
						<td class="small_text" align="center">
							<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
							<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
						</td>
					</xsl:when>
				</xsl:choose>

				<xsl:variable name="lang_front_statustext"><xsl:value-of select="lang_front_statustext"/></xsl:variable>
				<td class="small_text" align="center">
					<xsl:variable name="link_front"><xsl:value-of select="link_front"/></xsl:variable>
					<a href="{$link_front}" onMouseover="window.status='{$lang_front_statustext}';return true;" onMouseout="window.status='';return true;" target="_blank"><xsl:value-of select="text_front"/></a>
				</td>

			</tr>
	</xsl:template>



<!-- list_voucher_paid -->

	<xsl:template match="list_voucher_paid">
		<script language="JavaScript">
			self.name="first_Window";
			function abook()
			{
				Window1=window.open('<xsl:value-of select="addressbook_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
			function property_lookup()
			{
				Window1=window.open('<xsl:value-of select="property_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:apply-templates select="menu"/> 
		<xsl:variable name="form_action"><xsl:value-of select="link_url"/></xsl:variable>
		<div align="left">
		<form method="post" name="form" action="{$form_action}">
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
				<td>
					<input type="text" id="start_date" name="start_date" size="10" value="{start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
				<td>
					<input type="text" id="end_date" name="end_date" size="10" value="{end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
				<td>
					<input type="text" size="8" name="workorder_id" value="{workorder_id}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_workorder_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:void()"
					onMouseOver="overlib('{lang_workorder_statustext}', CAPTION, '{lang_workorder}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_workorder"/></a>					
				</td>

				<td align="left">
					<input type="text" name="vendor_id" value="{vendor_id}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:abook()"
					onMouseOver="overlib('{lang_select_vendor_statustext}', CAPTION, '{lang_vendor}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_vendor"/></a>					

					<input type="hidden" name="vendor_name"></input>
				</td>
			</tr>
			
			<tr>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
				<td align="left">
					<xsl:call-template name="user_lid_select"/>
				</td>

				<td align="left">
				<xsl:variable name="lang_account_class_statustext"><xsl:value-of select="lang_account_class_statustext"/></xsl:variable>
				<xsl:variable name="select_account_class_name"><xsl:value-of select="select_account_class_name"/></xsl:variable>
					<select name="{$select_account_class_name}" class="forms" onMouseover="window.status='{$lang_account_class_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_account_class"/></option>
							<xsl:apply-templates select="account_class_list"/>
					</select>

				</td>

				<td align="left">
					<input type="text" name="loc1" value="{loc1}" size="4" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_property_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:property_lookup()"
					onMouseOver="overlib('{lang_select_property_statustext}', CAPTION, '{lang_property}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_property"/></a>					

					<input type="hidden" name="loc1_name"></input>
				</td>

				<td align="left">
					<input type="text" name="voucher_id" value="{voucher_id}" size="8" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_voucher_id_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text>
					<xsl:value-of select="lang_voucher_id"/>
				</td>

				<td align="left">
					<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
					<input type="submit" name="submit_search" value="{$lang_search}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_search_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td colspan="11" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		</form>
		</div>

		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_list_voucher_paid"/>
			
			<xsl:choose>
				<xsl:when test="values_list_voucher_paid[voucher_id]">
					<xsl:apply-templates select="values_list_voucher_paid"/>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td class="th_text" align="right">
							<xsl:value-of select="sum"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
		</table>
	</xsl:template>
	
	
	<xsl:template match="table_header_list_voucher_paid">
		<xsl:variable name="sort_voucher"><xsl:value-of select="sort_voucher"/></xsl:variable>
		<xsl:variable name="sort_voucher_date"><xsl:value-of select="sort_voucher_date"/></xsl:variable>
		<xsl:variable name="sort_vendor_id"><xsl:value-of select="sort_vendor_id"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="5%" align="right">
					<a href="{$sort_voucher}"><xsl:value-of select="lang_voucher"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<a href="{$sort_voucher_date}"><xsl:value-of select="lang_voucher_date"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_days"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td class="th_text" width="4%" align="right">
					<a href="{$sort_vendor_id}"><xsl:value-of select="lang_vendor_id"/></a>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_num_sub_invoice"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_type"/>
				</td>
				<td class="th_text" width="3%" align="right">
					<xsl:value-of select="lang_period"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_janitor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_supervisor"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_budget_responsible"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_transfer"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_list_voucher_paid">

			<xsl:variable name="counter"><xsl:value-of select="counter"/></xsl:variable>
			<xsl:variable name="current_user"><xsl:value-of select="current_user"/></xsl:variable>
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
					<xsl:variable name="link_sub"><xsl:value-of select="link_sub"/>&amp;voucher_id=<xsl:value-of select="voucher_id"/>&amp;paid=true</xsl:variable>
					<xsl:variable name="lang_sub_help"><xsl:value-of select="lang_sub_help"/></xsl:variable>
					<xsl:variable name="lang_sub"><xsl:value-of select="lang_sub"/></xsl:variable>
					<a href="{$link_sub}"
					onMouseOver="overlib('{$lang_sub_help}', CAPTION, '{$lang_sub}')"
					onMouseOut="nd()">
					<xsl:value-of select="voucher_id"/></a>					
				</td>
				<td align="right">
					<xsl:variable name="lang_payment_date"><xsl:value-of select="lang_payment_date"/></xsl:variable>
					<xsl:variable name="payment_date"><xsl:value-of select="payment_date"/></xsl:variable>
					<a href="javascript:void()"
					onMouseOver="overlib('{$payment_date}', CAPTION, '{$lang_payment_date}')"
					onMouseOut="nd()">
					<xsl:value-of select="voucher_date"/></a>								
				</td>
				<td align="right">
					<xsl:value-of select="num_days"/>
				</td>
				<td align="right">
					<xsl:value-of select="amount"/>
				</td>
				<td align="right">
					<xsl:variable name="vendor"><xsl:value-of select="vendor"/></xsl:variable>
					<xsl:variable name="vendor_id"><xsl:value-of select="vendor_id"/></xsl:variable>
					<a href="javascript:void()"
					onMouseOver="overlib('{$vendor}', CAPTION, '{$vendor_id}')"
					onMouseOut="nd()">
					<xsl:value-of select="vendor_id"/></a>

				</td>
				<td align="right">
					<xsl:value-of select="invoice_count"/>
				</td>
				<td align="center">
					<xsl:value-of select="type"/>
				</td>
				<td align="center">
					<xsl:value-of select="period"/>
				</td>
				<td align="center">
					<xsl:value-of select="jan_date"/> - <xsl:value-of select="janitor"/>
				</td>
				<td align="center">
					<xsl:value-of select="super_date"/> - <xsl:value-of select="supervisor"/>
				</td>
				<td align="center">
					<xsl:value-of select="budget_date"/> - <xsl:value-of select="budget_responsible"/>
				</td>
				<td align="center">
					<xsl:value-of select="transfer_date"/> - <xsl:value-of select="transfer_id"/>
				</td>
			</tr>
	</xsl:template>


<!-- consume -->

	<xsl:template match="consume">
		<script language="JavaScript">
			self.name="first_Window";
			function abook()
			{
				Window1=window.open('<xsl:value-of select="addressbook_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
			function property_lookup()
			{
				Window1=window.open('<xsl:value-of select="property_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:apply-templates select="menu"/> 
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<div align="left">
		<form method="post" name="form" action="{$form_action}">
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
				<td>
					<input type="text" id="start_date" name="start_date" size="10" value="{start_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_start_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="start_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
				<td>
					<input type="text" id="end_date" name="end_date" size="10" value="{end_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_end_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="end_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
				<td>
					<input type="text" size="8" name="workorder_id" value="{workorder_id}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_workorder_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:void()"
					onMouseOver="overlib('{lang_workorder_statustext}', CAPTION, '{lang_workorder}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_workorder"/></a>					
				</td>

				<td align="left">
					<input type="text" name="vendor_id"  onClick="abook()" value="{vendor_id}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:abook()"
					onMouseOver="overlib('{lang_select_vendor_statustext}', CAPTION, '{lang_vendor}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_vendor"/></a>					

					<input type="hidden" name="vendor_name" value="{vendor_name}"></input>
				</td>
			</tr>
			
			<tr>
				<td>
					<xsl:call-template name="cat_select"/>
				</td>
				<td align="left">
					<xsl:call-template name="select_district"/>
				</td>
				<td align="left">
				<xsl:variable name="lang_account_class_statustext"><xsl:value-of select="lang_account_class_statustext"/></xsl:variable>
				<xsl:variable name="select_account_class_name"><xsl:value-of select="select_account_class_name"/></xsl:variable>
					<select name="{$select_account_class_name}" class="forms" onMouseover="window.status='{$lang_account_class_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_account_class"/></option>
							<xsl:apply-templates select="account_class_list"/>
					</select>

				</td>
				
				<td align="left">
					<input type="text" name="loc1" onClick="property_lookup()" value="{loc1}" size="4" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_property_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<a href="javascript:property_lookup()"
					onMouseOver="overlib('{lang_select_property_statustext}', CAPTION, '{lang_property}')"
					onMouseOut="nd()">
					<xsl:value-of select="lang_property"/></a>					

					<input type="hidden" name="loc1_name"></input>
				</td>
				<td align="left">
					<xsl:variable name="lang_search"><xsl:value-of select="lang_search"/></xsl:variable>
					<input type="submit" name="submit_search" value="{$lang_search}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_search_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		</div>

		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="vendor_name!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_vendor"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="vendor_name"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="loc1!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_property"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="loc1"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="workorder_id!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_workorder"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="workorder_id"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

		</table>

		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header_consume"/>
			
			<xsl:choose>
				<xsl:when test="values_consume[consume]">
					<xsl:apply-templates select="values_consume"/>

				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
				</td>
				<td>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td class="th_text" align="right">
					<xsl:value-of select="sum"/>
				</td>
			</tr>
		</table>
	</xsl:template>
	
	<xsl:template match="account_class_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="table_header_consume">
			<tr class="th">
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_district"/>
				</td>
				<td class="th_text" width="10%" align="right">
					<xsl:value-of select="lang_period"/>
				</td>
				<td class="th_text" width="25%" align="right">
					<xsl:value-of select="lang_budget_account"/>
				</td>
				<td class="th_text" width="55%" align="right">
					<xsl:value-of select="lang_consume"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_consume">
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
					<xsl:value-of select="district_id"/>
				</td>
				<td align="center">
					<xsl:value-of select="period"/>
				</td>
				<td align="center">
					<xsl:value-of select="account_class"/>
				</td>

				<td align = "right">
					<xsl:variable name="link_voucher"><xsl:value-of select="link_voucher"/></xsl:variable>
					<a href="{$link_voucher}" onMouseover="window.status='{consume}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="consume"/></a>
				</td>
			</tr>
	</xsl:template>

<!-- debug-->

	<xsl:template match="debug">
		<div align="left">
		<table width="50%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_type"/>
				</td>
				<td>
					<xsl:value-of select="artid"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_vendor"/>
				</td>
				<td>
					<xsl:value-of select="spvend_code"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="vendor_name"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_fakturadato"/>
				</td>
				<td>
					<xsl:value-of select="fakturadato"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_forfallsdato"/>
				</td>
				<td>
					<xsl:value-of select="forfallsdato"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_janitor"/>
				</td>
				<td>
					<xsl:value-of select="oppsynsmannid"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_supervisor"/>
				</td>
				<td>
					<xsl:value-of select="saksbehandlerid"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_budget_responsible"/>
				</td>
				<td>
					<xsl:value-of select="budsjettansvarligid"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_project_id"/>
				</td>
				<td>
					<xsl:value-of select="project_id"/>
				</td>
			</tr>
			<tr>
				<td class="th_text">
					<xsl:value-of select="lang_sum"/>
				</td>
				<td>
					<xsl:value-of select="sum"/>
				</td>
			</tr>
		</table>
		</div>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr class="th">
				<xsl:call-template name="table_header"/>
			</tr>
				<xsl:call-template name="values_debug"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template name="values_debug">
		<xsl:for-each select="values" >
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
			<xsl:for-each select="row" >
				<td align="{align}">
					<xsl:value-of select="value"/>					
				</td>
			</xsl:for-each>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
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
				<td height="50">
					<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"/></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add / edit -->
	<xsl:template match="add">

		<script language="JavaScript">
			self.name="first_Window";
			function abook()
			{
				Window1=window.open('<xsl:value-of select="addressbook_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:apply-templates select="menu"/>
	        <div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td colspan="2" align="center">
					<xsl:value-of select="message"/>
				</td>
			</tr>
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
				<xsl:when test="link_receipt != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:variable name="link_receipt"><xsl:value-of select="link_receipt"/></xsl:variable>
							<a href="{$link_receipt}" onMouseover="window.status='{lang_receipt}';return true;" onMouseout="window.status='';return true;" target="_blank"><xsl:value-of select="lang_receipt"/></a>
							
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">

			<tr>
				<td>
					<xsl:value-of select="lang_auto_tax"/>
				</td>
				<td>
					<input type="checkbox" name="auto_tax" value="True" checked="checked" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_auto_tax_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<xsl:call-template name="location_form"/>
			<xsl:call-template name="b_account_form"/>
			<tr>
				<td valign="top">
					<xsl:variable name="lang_vendor"><xsl:value-of select="lang_vendor"/></xsl:variable>
					<input type="button" name="convert" value="{$lang_vendor}" onClick="abook();" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="text" name="vendor_id" value="{value_vendor_id}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input type="text" name="vendor_name" value="{value_vendor_name}" size="20"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_janitor"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_janitor_statustext"><xsl:value-of select="lang_janitor_statustext"/></xsl:variable>
					<xsl:variable name="select_janitor"><xsl:value-of select="select_janitor"/></xsl:variable>
					<select name="{$select_janitor}" class="forms" onMouseover="window.status='{$lang_janitor_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_janitor"/></option>
						<xsl:apply-templates select="janitor_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_supervisor"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_supervisor_statustext"><xsl:value-of select="lang_supervisor_statustext"/></xsl:variable>
					<xsl:variable name="select_supervisor"><xsl:value-of select="select_supervisor"/></xsl:variable>
					<select name="{$select_supervisor}" class="forms" onMouseover="window.status='{$lang_supervisor_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_supervisor"/></option>
						<xsl:apply-templates select="supervisor_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget_responsible"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_budget_responsible_statustext"><xsl:value-of select="lang_budget_responsible_statustext"/></xsl:variable>
					<xsl:variable name="select_budget_responsible"><xsl:value-of select="select_budget_responsible"/></xsl:variable>
					<select name="{$select_budget_responsible}" class="forms" onMouseover="window.status='{$lang_budget_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_budget_responsible"/></option>
						<xsl:apply-templates select="budget_responsible_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_order"/>
				</td>
				<td>
					<input type="text" name="order_id" value="{value_order_id}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_order_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_art"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_art_statustext"><xsl:value-of select="lang_art_statustext"/></xsl:variable>
					<xsl:variable name="select_art"><xsl:value-of select="select_art"/></xsl:variable>
					<select name="{$select_art}" class="forms" onMouseover="window.status='{$lang_art_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_art"/></option>
						<xsl:apply-templates select="art_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_type"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_type_statustext"><xsl:value-of select="lang_type_statustext"/></xsl:variable>
					<xsl:variable name="select_type"><xsl:value-of select="select_type"/></xsl:variable>
					<select name="{$select_type}" class="forms" onMouseover="window.status='{$lang_type_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_type"/></option>
						<xsl:apply-templates select="type_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_dimb"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_dimb_statustext"><xsl:value-of select="lang_dimb_statustext"/></xsl:variable>
					<xsl:variable name="select_dimb"><xsl:value-of select="select_dimb"/></xsl:variable>
					<select name="{$select_dimb}" class="forms" onMouseover="window.status='{$lang_dimb_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_dimb"/></option>
						<xsl:apply-templates select="dimb_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_invoice_number"/>
				</td>
				<td>
					<input type="text" name="invoice_num" value="{value_invoice_num}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_invoice_num_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_kidnr"/>
				</td>
				<td>
					<input type="text" name="kid_nr" value="{value_kid_nr}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_kid_nr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_amount"/>
				</td>
				<td>
					<input type="text" name="amount" value="{value_amount}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_amount_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_invoice_date"/>
				</td>
				<td>
					<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_invoice_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="invoice_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_no_of_days"/>
				</td>
				<td>
					<input type="text" name="num_days" value="{value_num_days}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_num_days_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>

			<tr>

				<td valign="top">
					<xsl:value-of select="lang_payment_date"/>
				</td>
				<td>
					<input type="text" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_payment_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="payment_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_merknad"/>
				</td>
				<td>
					<textarea cols="60" rows="10" name="merknad" wrap="virtual" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_merknad_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
						<xsl:value-of select="value_merknad"/>		
					</textarea>

				</td>
			</tr>

			<tr height="50">
				<td>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<input type="submit" name="add_invoice" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"/></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
        </div>
	</xsl:template>

<!-- import -->

	<xsl:template match="import">

		<script language="JavaScript">
			self.name="first_Window";
			function abook()
			{
				Window1=window.open('<xsl:value-of select="addressbook_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:apply-templates select="menu"/>
	        <div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td colspan="2" align="center">
					<xsl:value-of select="message"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">

			<tr>
				<td>
					<xsl:value-of select="lang_auto_tax"/>
				</td>
				<td>
					<input type="checkbox" name="auto_tax" value="True" checked="checked" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_auto_tax_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_art"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_art_statustext"><xsl:value-of select="lang_art_statustext"/></xsl:variable>
					<xsl:variable name="select_art"><xsl:value-of select="select_art"/></xsl:variable>
					<select name="{$select_art}" class="forms" onMouseover="window.status='{$lang_art_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_art"/></option>
						<xsl:apply-templates select="art_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_type"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_type_statustext"><xsl:value-of select="lang_type_statustext"/></xsl:variable>
					<xsl:variable name="select_type"><xsl:value-of select="select_type"/></xsl:variable>
					<select name="{$select_type}" class="forms" onMouseover="window.status='{$lang_type_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_type"/></option>
						<xsl:apply-templates select="type_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_dimb"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_dimb_statustext"><xsl:value-of select="lang_dimb_statustext"/></xsl:variable>
					<xsl:variable name="select_dimb"><xsl:value-of select="select_dimb"/></xsl:variable>
					<select name="{$select_dimb}" class="forms" onMouseover="window.status='{$lang_dimb_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_dimb"/></option>
						<xsl:apply-templates select="dimb_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_invoice_number"/>
				</td>
				<td>
					<input type="text" name="invoice_num" value="{value_invoice_num}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_invoice_num_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_kidnr"/>
				</td>
				<td>
					<input type="text" name="kid_nr" value="{value_kid_nr}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_kid_nr_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:variable name="lang_vendor"><xsl:value-of select="lang_vendor"/></xsl:variable>
					<input type="button" name="convert" value="{$lang_vendor}" onClick="abook();" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="text" name="vendor_id" value="{value_vendor_id}" size="6"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input type="text" name="vendor_name" value="{value_vendor_name}" size="20"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_vendor_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_janitor"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_janitor_statustext"><xsl:value-of select="lang_janitor_statustext"/></xsl:variable>
					<xsl:variable name="select_janitor"><xsl:value-of select="select_janitor"/></xsl:variable>
					<select name="{$select_janitor}" class="forms" onMouseover="window.status='{$lang_janitor_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_janitor"/></option>
						<xsl:apply-templates select="janitor_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_supervisor"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_supervisor_statustext"><xsl:value-of select="lang_supervisor_statustext"/></xsl:variable>
					<xsl:variable name="select_supervisor"><xsl:value-of select="select_supervisor"/></xsl:variable>
					<select name="{$select_supervisor}" class="forms" onMouseover="window.status='{$lang_supervisor_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_supervisor"/></option>
						<xsl:apply-templates select="supervisor_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_budget_responsible"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_budget_responsible_statustext"><xsl:value-of select="lang_budget_responsible_statustext"/></xsl:variable>
					<xsl:variable name="select_budget_responsible"><xsl:value-of select="select_budget_responsible"/></xsl:variable>
					<select name="{$select_budget_responsible}" class="forms" onMouseover="window.status='{$lang_budget_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_budget_responsible"/></option>
						<xsl:apply-templates select="budget_responsible_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_invoice_date"/>
				</td>
				<td>
					<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_invoice_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="invoice_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_no_of_days"/>
				</td>
				<td>
					<input type="text" name="num_days" value="{value_num_days}" size="4"  onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_num_days_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>

			<tr>

				<td valign="top">
					<xsl:value-of select="lang_payment_date"/>
				</td>
				<td>
					<input type="text" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_payment_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="payment_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_file"/>
				</td>
				<td>
					<input type="file" name="tsvfile" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_file_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_conv"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"/></xsl:variable>
					<xsl:variable name="select_conv"><xsl:value-of select="select_conv"/></xsl:variable>
					<select name="{$select_conv}" class="forms" onMouseover="window.status='{$lang_conv_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_conversion"/></option>
						<xsl:apply-templates select="conv_list"/>
					</select>
				</td>
			</tr>

			<tr>
				<td>
					<xsl:value-of select="lang_debug"/>
				</td>
				<td>
					<input type="checkbox" name="download" value="True" checked="checked" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_debug_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_import"><xsl:value-of select="lang_import"/></xsl:variable>
					<input type="submit" name="convert" value="{$lang_import}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_import_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"/></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
        </div>
	</xsl:template>

<!-- art_list -->	

	<xsl:template match="art_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- type_list -->	

	<xsl:template match="type_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- dimb_list -->	

	<xsl:template match="dimb_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- janitor_list -->	

	<xsl:template match="janitor_list">
	<xsl:variable name="lid"><xsl:value-of select="lid"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"/> <xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"/><xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- supervisor_list -->	

	<xsl:template match="supervisor_list">
	<xsl:variable name="lid"><xsl:value-of select="lid"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"/> <xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"/> <xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- budget_responsible_list -->	

	<xsl:template match="budget_responsible_list">
	<xsl:variable name="lid"><xsl:value-of select="lid"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"/> <xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"/> <xsl:text> </xsl:text><xsl:value-of select="lastname"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- conv_list -->	

	<xsl:template match="conv_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- rollback_file_list -->	

	<xsl:template match="rollback_file_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- tax_code_list -->	

	<xsl:template match="tax_code_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- export -->

	<xsl:template match="export">
		<xsl:apply-templates select="menu"/> 
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
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">


			<tr>
				<td valign="top">
					<xsl:value-of select="lang_select_conv"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"/></xsl:variable>
					<xsl:variable name="select_conv"><xsl:value-of select="select_conv"/></xsl:variable>
					<select name="{$select_conv}" class="forms" onMouseover="window.status='{$lang_conv_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_conv"/></option>
						<xsl:apply-templates select="conv_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_force_period_year"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_force_period_year_statustext"><xsl:value-of select="lang_force_period_year_statustext"/></xsl:variable>
					<select name="values[force_period_year]" class="forms" onMouseover="window.status='{$lang_force_period_year_statustext}'; return true;" onMouseout="window.status='';return true;">
						<xsl:apply-templates select="force_period_year"/>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_export_to_file"/>
				</td>
				<td>
					<input type="checkbox" name="values[download]" value="on" checked="checked" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_debug_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

			<tr>
				<td>
				</td>
				<td>
					<xsl:variable name="link_rollback_file"><xsl:value-of select="link_rollback_file"/></xsl:variable>
					<a href="{$link_rollback_file}"><xsl:value-of select="lang_rollback_file"/></a>
				</td>
			</tr>

			<tr height="50">
				<td>
					<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
					<input type="submit" name="values[submit]" value="{$lang_submit}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_import_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"/></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	        </div>
	</xsl:template>


<!-- rollback -->

	<xsl:template match="rollback">
		<xsl:apply-templates select="menu"/> 
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
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">


			<tr>
				<td valign="top">
					<xsl:value-of select="lang_select_conv"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"/></xsl:variable>
					<xsl:variable name="select_conv"><xsl:value-of select="select_conv"/></xsl:variable>
					<select name="{$select_conv}" class="forms" onMouseover="window.status='{$lang_conv_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_select_conv"/></option>
						<xsl:apply-templates select="conv_list"/>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_select_file"/>
				</td>
				<td valign="top">
					<xsl:variable name="lang_file_statustext"><xsl:value-of select="lang_file_statustext"/></xsl:variable>
					<xsl:variable name="select_file"><xsl:value-of select="select_file"/></xsl:variable>
					<select name="{$select_file}" class="forms" onMouseover="window.status='{$lang_file_statustext}'; return true;" onMouseout="window.status='';return true;">
						<option value=""><xsl:value-of select="lang_no_file"/></option>
						<xsl:apply-templates select="rollback_file_list"/>
					</select>
				</td>
			</tr>


			<tr>

				<td valign="top">
					<xsl:value-of select="lang_date"/>
				</td>
				<td>
					<input type="text" id="date" name="date" size="10" value="{value_date}" readonly="readonly" onMouseout="window.status='';return true;" >
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_date_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<img id="date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"/></xsl:variable>
					<input type="submit" name="values[submit]" value="{$lang_submit}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_import_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			</form>
			<tr>
				<td>
					<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"/></xsl:variable>
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
		</div>
	</xsl:template>


<!--list_invoice_sub-->

	<xsl:template match="list_invoice_sub">
		<xsl:apply-templates select="menu"/> 
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
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
				<xsl:when test="vendor!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_vendor"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="vendor"/>
						</td>
						<td width="50%" class="small_text" valign="top" align="right">
							<xsl:call-template name="excel"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="voucher_id!=''">
					<tr>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="lang_voucher_id"/>
						</td>
						<td width="25%" class="th_text" align="left">
							<xsl:value-of select="voucher_id"/>
						</td>
						<td width="50%">
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" name="form" action="{$form_action}">
			<xsl:apply-templates select="table_header_list_invoice_sub"/>
			
			<xsl:choose>
				<xsl:when test="values_list_invoice_sub[id]">
					<xsl:apply-templates select="values_list_invoice_sub"/>
					<xsl:variable name="img_check"><xsl:value-of select="img_check"/></xsl:variable>
					<tr>
						<td></td>
						<td align="center">
			    	  			<a href="javascript:check_all_checkbox('values[close_order]')"><img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/></a>
					    	</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="small_text" align="right">
							<xsl:value-of select="sum"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>	
			<tr height="50">
				<td>
					<xsl:choose>
						<xsl:when test="paid=''">
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_save_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
					</xsl:choose>
				</td>
			</tr>
		</form> 
			<tr>
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>

		</table>
	</xsl:template>

	<xsl:template match="table_header_list_invoice_sub">
		<xsl:variable name="sort_workorder"><xsl:value-of select="sort_workorder"/></xsl:variable>
		<xsl:variable name="sort_budget_account"><xsl:value-of select="sort_budget_account"/></xsl:variable>
		<xsl:variable name="sort_sum"><xsl:value-of select="sort_sum"/></xsl:variable>
		<xsl:variable name="sort_dima"><xsl:value-of select="sort_dima"/></xsl:variable>
			<tr class="th">
				<td class="th_text" width="5%" align="right">
					<a href="{$sort_workorder}"><xsl:value-of select="lang_workorder"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_close_order"/>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_charge_tenant"/>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_invoice_id"/>
				</td>
				<td class="th_text" width="2%" align="right">
					<a href="{$sort_budget_account}"><xsl:value-of select="lang_budget_account"/></a>
				</td>
				<td class="th_text" width="5%" align="right">
					<a href="{$sort_sum}"><xsl:value-of select="lang_sum"/></a>
				</td>
				<td class="th_text" width="5%" align="right">
					<a href="{$sort_dima}"><xsl:value-of select="lang_dima"/></a>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_dimb"/>
				</td>
				<td class="th_text" width="5%" align="right">
					<xsl:value-of select="lang_dimd"/>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_tax_code"/>
				</td>
				<td class="th_text" width="2%" align="right">
					<xsl:value-of select="lang_remark"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values_list_invoice_sub">

			<xsl:variable name="counter"><xsl:value-of select="counter"/></xsl:variable>
			<xsl:variable name="current_user"><xsl:value-of select="current_user"/></xsl:variable>
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
					<input type="hidden" name="values[counter][{$counter}]" value="{counter}">
					</input>
					<input type="hidden" name="values[id][{$counter}]" value="{id}">
					</input>
					<input type="hidden" name="values[workorder_id][{$counter}]" value="{workorder_id}">
					</input>
					<xsl:variable name="link_order"><xsl:value-of select="link_order"/>&amp;order_id=<xsl:value-of select="workorder_id"/></xsl:variable>
					<a href="{$link_order}" target="_blank"><xsl:value-of select="workorder_id"/></a>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="workorder_id=''">
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="paid=''">
									<input type="hidden" name="values[close_order_orig][{$counter}]" value="{closed}">
									</input>
									<xsl:choose>
										<xsl:when test="closed='1'">
											<input type="checkbox" name="values[close_order][{$counter}]" value="true" checked="checked" onMouseout="window.status='';return true;">
											</input>
										</xsl:when>
										<xsl:otherwise>
											<input type="checkbox" name="values[close_order][{$counter}]" value="true" onMouseout="window.status='';return true;">
											</input>							
										</xsl:otherwise>
									</xsl:choose>
								</xsl:when>
								<xsl:otherwise>
									<xsl:choose>
										<xsl:when test="closed='1'">
											<b>x</b>
										</xsl:when>
									</xsl:choose>
								</xsl:otherwise>								
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="charge_tenant='1'">
							<xsl:choose>
								<xsl:when test="claim_issued=''">
									<xsl:variable name="link_claim"><xsl:value-of select="link_claim"/>&amp;project_id=<xsl:value-of select="project_id"/></xsl:variable>
									<a href="{$link_claim}" target="_blank"><xsl:value-of select="//lang_claim"/></a>
								</xsl:when>
							</xsl:choose>
							<b>x</b>
						</xsl:when>
					</xsl:choose>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="invoice_id"/>
				</td>
				<td class="small_text" align="right">
					<xsl:choose>
						<xsl:when test="paid='true'">
							<xsl:value-of select="budget_account"/>				
						</xsl:when>
						<xsl:otherwise>
							<input type="text" size="7" name="values[budget_account][{$counter}]" value="{budget_account}">
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="right">
					<xsl:value-of select="amount"/>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="paid='true'">
							<xsl:value-of select="dima"/>				
						</xsl:when>
						<xsl:otherwise>
							<input type="text" size="7" name="values[dima][{$counter}]" value="{dima}">
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="right">
					<xsl:choose>
						<xsl:when test="paid='true'">
							<xsl:value-of select="dimb"/>				
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_dimb_statustext"><xsl:value-of select="lang_dimb_statustext"/></xsl:variable>
							<select name="values[dimb][{$counter}]" class="forms" onMouseover="window.status='{$lang_dimb_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""></option>
								<xsl:apply-templates select="dimb_list"/>
							</select>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="paid='true'">
							<xsl:value-of select="dimd"/>				
						</xsl:when>
						<xsl:otherwise>
							<input type="text" size="4" name="values[dimd][{$counter}]" value="{dimd}">
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="center">
					<xsl:choose>
						<xsl:when test="paid='true'">
							<xsl:value-of select="tax_code"/>				
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_tax_code_statustext"><xsl:value-of select="lang_tax_code_statustext"/></xsl:variable>
							<select name="values[tax_code][{$counter}]" class="forms" onMouseover="window.status='{$lang_tax_code_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="tax_code_list"/>
							</select>
						</xsl:otherwise>
					</xsl:choose>
				</td>
				<td class="small_text" align="right">
					<xsl:choose>
						<xsl:when test="remark='1'">
							<xsl:variable name="link_remark"><xsl:value-of select="link_remark"/>&amp;id=<xsl:value-of select="id"/>&amp;paid=<xsl:value-of select="paid"/></xsl:variable>
							<xsl:variable name="lang_remark_help"><xsl:value-of select="lang_remark_help"/></xsl:variable>
							<xsl:variable name="lang_remark"><xsl:value-of select="lang_remark"/></xsl:variable>
							<a href="javascript:var w=window.open('{$link_remark}','','width=550,height=400,scrollbars')"
							onMouseOver="overlib('{$lang_remark_help}', CAPTION, '{$lang_remark}')"
							onMouseOut="nd()">
							<xsl:value-of select="lang_remark"/></a>					
						</xsl:when>
					</xsl:choose>
				</td>

			</tr>
	</xsl:template>

	<xsl:template match="force_period_year">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected='selected'">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
