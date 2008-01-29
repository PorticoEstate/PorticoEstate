
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
		<xsl:call-template name="menu"/>
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
				<td align="left">
					<xsl:call-template name="cat_filter"/>
				</td>
				<td align="center">
					<xsl:call-template name="status_filter"/>
				</td>
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
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_time_created" select="sort_time_created"/>
		<xsl:variable name="sort_claim_id" select="sort_claim_id"/>
		<xsl:variable name="sort_project" select="sort_project"/>
		<xsl:variable name="sort_name" select="sort_name"/>
		<xsl:variable name="sort_category" select="sort_category"/>
		
			<tr class="th">
				<td width="10%" align="right">
					<a href="{$sort_claim_id}" class="th_text"><xsl:value-of select="lang_claim_id"/></a>
				</td>
				<td width="10%" align="right">
					<a href="{$sort_project}" class="th_text"><xsl:value-of select="lang_project"/></a>
				</td>
				<td width="40%">
					<a href="{$sort_name}" class="th_text"><xsl:value-of select="lang_name"/></a>
				</td>
				<td width="20%" align="center">
					<a href="{$sort_time_created}" class="th_text"><xsl:value-of select="lang_time_created"/></a>
				</td>
				<td width="10%" align="center">
					<a href="{$sort_category}" class="th_text"><xsl:value-of select="lang_category"/></a>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_status"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_view"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
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
					<xsl:value-of select="claim_id"/>
				</td>
				<td align="right">
					<xsl:value-of select="project_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="center">
					<xsl:value-of select="entry_date"/>
				</td>
				<td align="left">
					<xsl:value-of select="category"/>
				</td>
				<td align="left">
					<xsl:value-of select="status"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
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
			</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">

		<script language="JavaScript">
			self.name="first_Window";
			function tenant_lookup()
			{
				Window1=window.open('<xsl:value-of select="tenant_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
		<form name="form" method="post" action="{$edit_url}">
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
					<tr >
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

			<xsl:for-each select="value_origin" >
				<xsl:variable name="link_origin_type"><xsl:value-of select="link"/></xsl:variable>
				<tr>
					<td valign ="top">
						<xsl:value-of select="descr"/>
					</td>
						<td class="th_text"  align="left" >
						<xsl:for-each select="data">
							<a href="{$link_origin_type}&amp;id={id}"  onMouseover="window.status='{//lang_origin_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="id"/></a>
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
				<xsl:for-each select="cat_list_project" >
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
						<td class="th_text"  align="left">
							<xsl:value-of select="lang_contact_phone"/>
						</td>
						<td  align="left">
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
							<td class="th_text"  align="right">
								<xsl:value-of select="sum_workorder_budget"/>
							</td>
							<td class="th_text"  align="right">
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
				<xsl:for-each select="user_list" >
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
					<xsl:for-each select="status_list" >
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

			<tr >
				<td align="left">
					<xsl:value-of select="lang_status"/>
				</td>
				<td align="left">
					<xsl:call-template name="status_select"/>
				</td>
			</tr>
			<tr>
				<td>
					<a href="javascript:tenant_lookup()" onMouseover="window.status='{lang_tenant_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_tenant"/></a>
				</td>
				<td>
					<input type="hidden" name="tenant_id" value="{value_tenant_id}"></input>
					<input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" onClick="tenant_lookup();" readonly="readonly">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_tenant_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input  size="{size_first_name}" type="text" name="first_name" value="{value_first_name}"  onClick="tenant_lookup();" readonly="readonly">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_tenant_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>


			<xsl:call-template name="b_account_form"/>

			<tr>
				<td valign="top">
					<xsl:value-of select="lang_amount"/>
				</td>
				<td>
					<input type="text" name="values[amount]" value="{value_amount}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_amount_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
				</td>
			</tr>

			<tr >
				<td align="left">
					<xsl:value-of select="lang_category"/>
				</td>
				<td align="left">
					<xsl:call-template name="cat_select"/>
				</td>
			</tr>
			<tr  align="left">
				<td valign="top" >
					<xsl:value-of select="lang_remark"/>
				</td>
				<td align="left">

					<textarea cols="60" rows="6" name="values[remark]" wrap="virtual" onMouseout="window.status='';return true;">
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
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
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
	</xsl:template>

<!-- view -->

	<xsl:template match="view">

		<div align="left">
		<table cellpadding="2" cellspacing="2" width="79%" align="left">
			<tr >
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
				<xsl:for-each select="cat_list_project" >
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
						<td class="th_text"  align="left">
							<xsl:value-of select="lang_contact_phone"/>
						</td>
						<td  align="left">
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
							<td class="th_text"  align="right">
								<xsl:value-of select="sum_workorder_budget"/>
							</td>
							<td class="th_text"  align="right">
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
				<xsl:for-each select="user_list" >
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
					<xsl:for-each select="status_list" >
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

			<tr >
				<td align="left">
					<xsl:value-of select="lang_status"/>
				</td>
				<xsl:for-each select="status_list" >
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
					<input  size="{size_first_name}" type="text" name="first_name" value="{value_first_name}"  readonly="readonly">
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

			<tr >
				<td align="left">
					<xsl:value-of select="lang_category"/>
				</td>
				<xsl:for-each select="cat_list" >
					<xsl:choose>
						<xsl:when test="selected='selected'">
							<td>
								<xsl:value-of select="name"/>
							</td>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>

			</tr>
			<tr  align="left">
				<td valign="top" >
					<xsl:value-of select="lang_remark"/>
				</td>
				<td align="left">

					<textarea cols="60" rows="6" name="values[remark]" wrap="virtual" onMouseout="window.status='';return true;">
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
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
					<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
					</form>
				</td>
			</tr>
		</table>
		</div>
	</xsl:template>


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

	<xsl:template match="workorder_budget">
		<xsl:variable name="workorder_link"><xsl:value-of select="//workorder_link"/>&amp;id=<xsl:value-of select="workorder_id"/></xsl:variable>
		<xsl:variable name="workorder_id"><xsl:value-of select="workorder_id"/></xsl:variable>
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
					<a href="{$workorder_link}" target="_blank"><xsl:value-of select="workorder_id"/></a>
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
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_select_workorder_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input type="checkbox" name="values[workorder][]" value="{$workorder_id}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="//lang_select_workorder_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</td>

			</tr>
	</xsl:template>
