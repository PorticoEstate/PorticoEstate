<!-- $Id: tts.xsl 6705 2010-12-26 23:10:55Z sigurdne $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>



<!-- add -->

	<xsl:template match="add" xmlns:php="http://php.net/xsl">
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
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<div class="yui-navset" id="ticket_tabview">
				<div class="yui-content">

					<table cellpadding="2" cellspacing="2" width="80%" align="center">

						<xsl:for-each select="value_origin" >
							<tr>
								<td valign ="top">
									<xsl:value-of select="descr"/>
								</td>
								<td>
									<table>							
										<xsl:for-each select="data">
											<tr>
												<td align="left" >
													<xsl:variable name="link_request"><xsl:value-of select="//link_request"/>&amp;id=<xsl:value-of select="id"/></xsl:variable>
													<a href="{link}"  title="{//lang_origin_statustext}"><xsl:value-of select="id"/></a>
													<xsl:text> </xsl:text>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</td>
							</tr>
						</xsl:for-each>
						<input type="hidden" name="values[origin]" value="{value_origin_type}"></input>
						<input type="hidden" name="values[origin_id]" value="{value_origin_id}"></input>

<!--
						<xsl:call-template name="location_form"/>
-->
						<tr>
							<td>
								<xsl:value-of select="lang_category"/>
							</td>
							<td>
								<xsl:call-template name="categories"/>
							</td>
						</tr>

						<xsl:choose>
							<xsl:when test="simple !='1'">
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Group')" />							
									</td>
									<td>
										<xsl:call-template name="group_select"/>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Assign to')" />							
									</td>
									<td>
										<xsl:call-template name="user_id_select"/>
									</td>
								</tr>
								<xsl:call-template name="contact_form"/>	
								<tr>
									<td>
										<xsl:value-of select="php:function('lang', 'Send e-mail')" />							
									</td>
									<td>
										<input type="checkbox" name="values[send_mail]" value="1" >
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')" />
											</xsl:attribute>
											<xsl:if test="pref_send_mail = '1'">
												<xsl:attribute name="checked">
													<xsl:text>checked</xsl:text>
												</xsl:attribute>
											</xsl:if>
										</input>
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'Priority')" />							
									</td>
									<td>
										<xsl:variable name="lang_priority_statustext"><xsl:value-of select="lang_priority_statustext"/></xsl:variable>
										<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
										<select name="{$select_priority_name}" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
											<xsl:apply-templates select="priority_list"/>
										</select>			
									</td>
								</tr>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'status')" />
									</td>
									<td>
										<select name="values[status]" >
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'Set the status of the ticket')" />
											</xsl:attribute>
											<xsl:apply-templates select="status_list"/>
										</select>			
									</td>
								</tr>
								<xsl:choose>
									<xsl:when test="show_finnish_date ='1'">
										<tr>
											<td>
												<xsl:value-of select="php:function('lang', 'finnish date')" />							
											</td>
											<td>
												<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;" >
													<xsl:attribute name="title">
														<xsl:value-of select="lang_finnish_date_statustext"/>
													</xsl:attribute>
												</input>
												<img id="values_finnish_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
											</td>
										</tr>
									</xsl:when>
								</xsl:choose>
							</xsl:when>
						</xsl:choose>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'subject')" />
							</td>
							<td>
								<input type="text" name="values[subject]" value="{value_subject}" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter the subject of this ticket')" />
									</xsl:attribute>
								</input>

							</td>
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'Details')" />
							</td>
							<td>
								<textarea cols="60" rows="10" name="values[details]" onMouseout="window.status='';return true;">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Enter the details of this ticket')" />
									</xsl:attribute>
									<xsl:value-of select="value_details"/>		
								</textarea>
							</td>
						</tr>

						<xsl:choose>
							<xsl:when test="fileupload = 1">
								<tr>
									<td valign="top">
										<xsl:value-of select="lang_upload_file"/>
									</td>
									<td>
										<input type="file" name="file" size="40" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_file_statustext"/>
											</xsl:attribute>
										</input>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
					</table>
				</div>
				<table cellpadding="2" cellspacing="2" width="50%" align="center">
					<tr height="50">
						<td>
							<input type="hidden" id="save" name="values[save]" value=""></input>
							<input type="hidden" id="apply" name="values[apply]" value=""></input>
							<input type="hidden" id="cancel" name="values[cancel]" value=""></input>
							<input type="button" name="save" value="{lang_send}"  onClick="confirm_session('save');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"/>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="button" name="apply" value="{lang_save}"  onClick="confirm_session('apply');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"/>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="button" name="cancel" value="{lang_cancel}"  onClick="confirm_session('cancel');">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_send_statustext"/>
								</xsl:attribute>
							</input>
						</td>
<!--
					<input type="submit" name="values[save]" value="{lang_send}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_send_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[apply]" value="{lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<input type="submit" name="values[cancel]" value="{lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_cancel_statustext"/>
						</xsl:attribute>
					</input>
				</td>
-->
			</tr>
		</table>
	</div>
</form>
	</xsl:template>


	<xsl:template match="priority_list">
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

<!-- view -->
	<xsl:template match="view" xmlns:php="http://php.net/xsl">
		<script type="text/javascript">
			self.name="first_Window";
			function generate_order()
			{
			Window1=window.open('<xsl:value-of select="order_link"/>');
			}		
			function generate_request()
			{
			Window1=window.open('<xsl:value-of select="request_link"/>');
			}		

			function template_lookup()
			{
			var oArgs = {menuaction:'property.uilookup.order_template',type:'order_template'};

			var strURL = phpGWLink('index.php', oArgs);

			Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
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
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
			<div class="yui-navset" id="ticket_tabview">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div class="yui-content">
					<div id="general">
						<table cellpadding="2" cellspacing="2" width="95%" align="center">
							<tr class="th">
								<td class="th_text" valign="top">
									<xsl:value-of select="php:function('lang', 'Ticket')" />
								</td>
								<td class="th_text" valign="top">
									<xsl:value-of select="value_id"/>
									<input type="text" name="values[subject]" value="{value_subject}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'update subject')" />
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<xsl:for-each select="value_origin">
								<tr>
									<td valign ="top">
										<xsl:value-of select="descr"/>
									</td>
									<td>
										<table>							
											<xsl:for-each select="data">
												<tr>
													<td class="th_text"  align="left" >
														<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
														<xsl:text> </xsl:text>
													</td>
												</tr>
											</xsl:for-each>
										</table>
									</td>
								</tr>
							</xsl:for-each>
<!--
							<xsl:choose>
								<xsl:when test="lookup_type ='view'">
									<xsl:call-template name="location_view"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:call-template name="location_form"/>
								</xsl:otherwise>
							</xsl:choose>
-->
							<xsl:choose>
								<xsl:when test="contact_phone !=''">
									<tr>
										<td class="th_text"  align="left">
											<xsl:value-of select="php:function('lang', 'Contact phone')" />
										</td>
										<td  align="left">
											<xsl:value-of select="contact_phone"/>					
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>

<!--
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Open Date')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_opendate"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Assigned from')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedfrom"/>
				</td>
			</tr>

			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'Assigned to')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_assignedto_name"/>
				</td>
			</tr>
-->
			<xsl:call-template name="contact_form"/>

			<xsl:for-each select="value_target" >
				<tr>
					<td class="th_text" valign ="top">
						<xsl:value-of select="descr"/>
					</td>
					<td class="th_text"  align="left" >
						<xsl:for-each select="data">
							<a href="{link}"  title="{statustext}"><xsl:value-of select="id"/></a>
							<xsl:text> </xsl:text>
						</xsl:for-each>
					</td>
				</tr>
			</xsl:for-each>

<!--
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'details')" />
				</td>
				<td valign="top">
					<xsl:value-of select="value_details"/>
				</td>
			</tr>

-->
			<tr>
				<td class="th_text" valign="top">
					<xsl:value-of select="php:function('lang', 'details')" />
				</td>
				<xsl:choose>
					<xsl:when test="additional_notes=''">
						<td class="th_text">
							<xsl:value-of select="php:function('lang', 'no additional notes')" />
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td>
							<table width="100%" cellpadding="2" cellspacing="2" align="center">
								<!--  DATATABLE 0-->
								<!--  <xsl:apply-templates select="table_header_additional_notes"/><xsl:apply-templates select="additional_notes"/>-->
								<td><div id="paging_0"></div><div id="datatable-container_0"></div> </td>
							</table>
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</tr>
			<xsl:choose>
				<xsl:when test="simple !='1'">
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'group')" />
						</td>
						<td>
							<xsl:call-template name="group_select"/>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'assigned to')" />
						</td>
						<td>
							<xsl:call-template name="user_id_select"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="lang_takeover != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_takeover"/>
								</td>
								<td>
									<input type="checkbox" name="values[takeover]" value="1">
										<xsl:attribute name="title">				
											<xsl:value-of select="php:function('lang', 'Take over the assignment for this ticket')" />
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'Send e-mail')" />
						</td>
						<td>
							<input type="checkbox" name="values[send_mail]" value="1" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Choose to send mailnotification')" />
								</xsl:attribute>
								<xsl:if test="pref_send_mail = '1'">
									<xsl:attribute name="checked">
										<xsl:text>checked</xsl:text>
									</xsl:attribute>
								</xsl:if>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'Priority')" />
						</td>
						<td>
							<xsl:variable name="lang_priority_statustext"><xsl:value-of select="php:function('lang', 'Select the priority the selection belongs to')" /></xsl:variable>
							<xsl:variable name="select_priority_name"><xsl:value-of select="select_priority_name"/></xsl:variable>
							<select name="{$select_priority_name}" class="forms" title = "{$lang_priority_statustext}" onMouseover="window.status='{$lang_priority_statustext}'; return true;" onMouseout="window.status='';return true;">
								<xsl:apply-templates select="priority_list"/>
							</select>			
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_order_id=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'status')" />
								</td>
								<td>
									<xsl:variable name="status_name"><xsl:value-of select="status_name"/></xsl:variable>
									<select name="{$status_name}" class="forms">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Set the status of the ticket')" />
										</xsl:attribute>
										<xsl:apply-templates select="status_list"/>
									</select>			
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'category')" />
						</td>
						<td>
							<xsl:call-template name="categories"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="show_finnish_date ='1'">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'finnish date')" />
								</td>
								<td>
									<input type="text" id="values_finnish_date" name="values[finnish_date]" size="10" value="{value_finnish_date}" readonly="readonly" onMouseout="window.status='';return true;" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'select the estimated date for closing the task')" />
										</xsl:attribute>
									</input>
									<img id="values_finnish_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;" />
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="show_billable_hours ='1'">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'billable hours')" />
								</td>
								<td>
									<input type="text" id="values_billable_hour" name="values[billable_hours]" size="10" value="{value_billable_hours}" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'enter the billable hour for the task')" />
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

				</xsl:when>
				<xsl:otherwise>
					<input type="hidden" name="values[status]" value="{value_status}"></input>
					<input type="hidden" name="values[assignedto]" value="{value_assignedto_id}"></input>
					<input type="hidden" name="values[group_id]" value="{value_group_id}"></input>
					<input type="hidden" name="values[priority]" value="{value_priority}"></input>
					<input type="hidden" name="values[cat_id]" value="{value_cat_id}"></input>
					<input type="hidden" name="values[finnish_date]" value="{value_finnish_date}"></input>
					<input type="hidden" name="values[billable_hour]" value="{value_billable_hours}"></input>
				</xsl:otherwise>
			</xsl:choose>

			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'new note')" />
				</td>
				<td>
					<textarea cols="{textareacols}" rows="{textarearows}" name="values[note]">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'add new comments')" />
						</xsl:attribute>
					</textarea>

				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="files!=''">
					<!-- <xsl:call-template name="file_list"/> -->						
					<tr>
						<td width="19%" align="left" valign="top">
							<xsl:value-of select="php:function('lang', 'files')" />
						</td>
						<td>
							<div id="datatable-container_2"></div>
						</td>
					</tr>	
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="fileupload = 1">
					<xsl:call-template name="file_upload"/>
				</xsl:when>
			</xsl:choose>

			<xsl:choose>
				<xsl:when test="access_order = 1">
					<xsl:choose>
						<xsl:when test="value_order_id=''">
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'make order')" />
								</td>
								<td>
									<input type="checkbox" name="values[make_order]" value="True">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'make order')" />
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="value_order_id!=''">
							<tr class="th">
								<td class="th_text">
									<xsl:value-of select="php:function('lang', 'order id')" />
								</td>
								<td>
									<xsl:value-of select="value_order_id"/>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'on behalf of assigned')" />
								</td>
								<td>
									<input type="checkbox" name="values[on_behalf_of_assigned]" value="True">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'on behalf of assigned - vacation mode')" />
										</xsl:attribute>
									</input>
								</td>
							</tr>

							<xsl:call-template name="vendor_form"/>
							<xsl:call-template name="ecodimb_form"/>
							<xsl:call-template name="b_account_form"/>

							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'building part')" />
								</td>
								<td>
									<select name="values[building_part]" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'select building part')" />
										</xsl:attribute>
										<option value="0">
											<xsl:value-of select="php:function('lang', 'select building part')" />
										</option>
										<xsl:apply-templates select="building_part_list/status_list"/>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="php:function('lang', 'order_dim1')" />
								</td>
								<td>
									<select name="values[order_dim1]" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'order_dim1')" />
										</xsl:attribute>
										<option value="0">
											<xsl:value-of select="php:function('lang', 'order_dim1')" />
										</option>
										<xsl:apply-templates select="order_dim1_list/status_list"/>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'cost estimate')" />
								</td>
								<td>
									<input type="text" name="values[budget]" value="{value_budget}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Enter the budget')" />
										</xsl:attribute>
									</input>
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="php:function('lang', 'actual cost')" />
								</td>
								<td>
									<input type="text" name="values[actual_cost]" value="{value_actual_cost}">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'Enter actual cost')" />
										</xsl:attribute>
									</input>
									<!--<xsl:value-of select="value_actual_cost"/> -->
									<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
								</td>
							</tr>
							<tr>
								<td valign="top">
									<a href="javascript:template_lookup()" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'lookup template')" />
										</xsl:attribute>
										<xsl:value-of select="php:function('lang', 'description')" />
									</a>

								</td>
								<td>
									<textarea cols="{textareacols}" rows="{textarearows}" id='order_descr' name="values[order_descr]" wrap="virtual">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'description order')" />
										</xsl:attribute>
										<xsl:value-of select="value_order_descr"/>
									</textarea>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="need_approval='1'">
									<tr>
										<td valign="top">
											<xsl:value-of select="php:function('lang', 'ask for approval')" />
										</td>
										<td>
											<table>
												<xsl:for-each select="value_approval_mail_address" >
													<tr>
														<td>
															<input type="checkbox" name="values[approval][{id}]" value="{address}">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'ask for approval')" />
																</xsl:attribute>
															</input>
														</td>
														<td valign='top'>
															<xsl:value-of select="address"/>
														</td>
													</tr>
												</xsl:for-each>
											</table>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign='top'>
									<xsl:value-of select="php:function('lang', 'send order')" />
								</td>
								<td><div id="paging_3"></div><div id="datatable-container_3"></div> </td>
								<tr>
									<td valign="top">
										<xsl:value-of select="php:function('lang', 'extra mail address')" />
									</td>
									<td>
										<input type="text" name="values[vendor_email][]" value="">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'The order will also be sent to this one')" />
											</xsl:attribute>
										</input>
									</td>
								</tr>

								<!--	<td>
								<table>
									<xsl:apply-templates select="vendor_email"/>
								</table>
							</td>
						-->
						</tr>
						<tr>
							<td valign="top">
								<xsl:value-of select="php:function('lang', 'status')" />
							</td>
							<td>
								<xsl:variable name="status_name"><xsl:value-of select="status_name"/></xsl:variable>
								<select name="{$status_name}" class="forms">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'Set the status of the ticket')" />
									</xsl:attribute>
									<xsl:apply-templates select="status_list"/>
								</select>			
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</xsl:when>
		</xsl:choose>
	</table>
</div>
<div id="history">
	<div id="paging_1"></div>
	<div id="datatable-container_1"></div>	
</div>
<table cellpadding="2" cellspacing="2" width="80%" align="center">
	<tr height="50">
		<td>
			<input type="hidden" id="save" name="values[save]" value=""></input>
			<input type="button" name="save" onClick="confirm_session('save');">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'save')" />
				</xsl:attribute>
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'save the ticket')" />
				</xsl:attribute>
			</input>
		</td>
<!--
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save the ticket')" />
						</xsl:attribute>
					</input>
				</td>
-->
			</tr>
		</table>
	</div>
</div>
			</form>
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="php:function('lang', 'done')" /></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'Back to the ticket list')" />
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
			<hr noshade="noshade" width="100%" align="center" size="1"/>
			<table width="80%" cellpadding="2" cellspacing="2" align="center">
				<tr>
					<xsl:choose>
						<xsl:when test="request_link != ''">
							<td valign="top">
								<xsl:variable name="request_link"><xsl:value-of select="request_link"/></xsl:variable>
								<form method="post" action="{$request_link}">
									<xsl:variable name="lang_generate_request"><xsl:value-of select="php:function('lang', 'Generate Request')" /></xsl:variable>
									<input type="submit" name="location" value="{$lang_generate_request}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to generate a request with this information')" />
										</xsl:attribute>
									</input>
								</form>
							</td>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="order_link != ''">

							<td valign="top">
								<xsl:variable name="order_link"><xsl:value-of select="order_link"/></xsl:variable>
								<form method="post" action="{$order_link}">
									<xsl:variable name="lang_generate_project"><xsl:value-of select="php:function('lang', 'generate new project')" /></xsl:variable>
									<input type="submit" name="location" value="{$lang_generate_project}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to generate a project with this information')" />
										</xsl:attribute>
									</input>
								</form>
							</td>
							<td valign="top">
								<xsl:variable name="add_to_project_link"><xsl:value-of select="add_to_project_link"/></xsl:variable>
								<form method="post" action="{$add_to_project_link}">
									<xsl:variable name="lang_add_to_project"><xsl:value-of select="php:function('lang', 'add to project')" /></xsl:variable>
									<input type="submit" name="location" value="{$lang_add_to_project}" onMouseout="window.status='';return true;">
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'click this to add an order to an existing project')" />
										</xsl:attribute>
									</input>
								</form>
							</td>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="link_entity!=''">
							<xsl:for-each select="link_entity" >
								<td valign="top">
									<xsl:variable name="link"><xsl:value-of select="link"/></xsl:variable>
									<form method="post" action="{$link}">
										<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>
										<input type="submit" name="location" value="{$name}" onMouseout="window.status='';return true;">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_start_statustext"/>
											</xsl:attribute>
										</input>
									</form>
								</td>
							</xsl:for-each>
						</xsl:when>
					</xsl:choose>	
				</tr>
			</table>

			<hr noshade="noshade" width="100%" align="center" size="1"/>

			<!--  DATATABLE DEFINITIONS-->
			<script type="text/javascript">
				var property_js = <xsl:value-of select="property_js" />;
				var base_java_url = <xsl:value-of select="base_java_url" />;
				var datatable = new Array();
				var myColumnDefs = new Array();
				var myButtons = new Array();
				var td_count = <xsl:value-of select="td_count" />;

				<xsl:for-each select="datatable">
					datatable[<xsl:value-of select="name"/>] = [
					{
					values			:	<xsl:value-of select="values"/>,
					total_records	: 	<xsl:value-of select="total_records"/>,
					is_paginator	:  	<xsl:value-of select="is_paginator"/>,
					<!--		permission		:	<xsl:value-of select="permission"/>, -->
					footer			:	<xsl:value-of select="footer"/>
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


	<xsl:template match="table_header_additional_notes">
		<tr class="th">
			<td class="th_text" width="4%" align="right">
				<xsl:value-of select="lang_count"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_note"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="additional_notes">
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
				<xsl:value-of select="value_count"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_date"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_note"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_action"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_new_value"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
				<xsl:value-of select="value_date"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_action"/>
			</td>
			<td align="left">
				<xsl:value-of select="value_new_value"/>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="priority_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<xsl:template match="status_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

	<xsl:template match="vendor_email" xmlns:php="http://php.net/xsl">
		<tr>
			<td>
				<input type="checkbox" name="values[vendor_email][]" value="{email}" >
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'The address to which this order will be sendt')" />
					</xsl:attribute>
				</input>
			</td>
			<td>
				<xsl:value-of select="email"/>
			</td>
		</tr>
	</xsl:template>
