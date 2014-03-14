  <!-- $Id$ -->
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit_hour">
				<xsl:apply-templates select="edit_hour"/>
			</xsl:when>
			<xsl:when test="list_template">
				<xsl:apply-templates select="list_template"/>
			</xsl:when>
			<xsl:when test="list_template_hour">
				<xsl:apply-templates select="list_template_hour"/>
			</xsl:when>
			<xsl:when test="add_template">
				<xsl:apply-templates select="add_template"/>
			</xsl:when>
			<xsl:when test="email_data">
				<xsl:apply-templates select="email_data"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="prizebook">
				<xsl:apply-templates select="prizebook"/>
			</xsl:when>
			<xsl:when test="list_deviation">
				<xsl:apply-templates select="list_deviation"/>
			</xsl:when>
			<xsl:when test="edit_deviation">
				<xsl:apply-templates select="edit_deviation"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list_hour"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_deviation">
		<!-- DataTable 0 DESVIATION-->
		<div id="datatable-container_0"/>
		<div id="contextmenu_0"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<!--  <xsl:apply-templates select="table_header_deviation"/><xsl:apply-templates select="values_deviation"/> -->
			<!--  <tr><td></td><td class="small_text" align="right"><xsl:value-of select="sum_deviation"/></td></tr>  -->
			<tr>
				<td height="50" width="4%">
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
								<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td height="50" width="96%">
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
								<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
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
						permission:<xsl:value-of select="permission"/>,
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
	<xsl:template match="table_header_deviation">
		<tr class="th">
			<td class="th_text" width="1%" align="right">
				<xsl:value-of select="lang_id"/>
			</td>
			<td class="th_text" width="6%" align="right">
				<xsl:value-of select="lang_amount"/>
			</td>
			<td class="th_text" width="80%">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="80%">
				<xsl:value-of select="lang_date"/>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_deviation">
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="link_edit">
			<xsl:value-of select="link_edit"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<xsl:variable name="link_delete">
			<xsl:value-of select="link_delete"/>
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
			<td class="small_text" align="right">
				<xsl:value-of select="id"/>
			</td>
			<td class="small_text" align="right">
				<xsl:value-of select="amount"/>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="descr"/>
			</td>
			<td class="small_text" align="left">
				<xsl:value-of select="entry_date"/>
			</td>
			<td class="small_text" align="center">
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td class="small_text" align="center">
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="add_template">
		<div align="left">
			<xsl:apply-templates select="menu"/>
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
			</table>
			<table width="50%" cellpadding="2" cellspacing="2" align="center">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<form method="post" action="{$add_action}">
					<tr height="50">
						<td>
							<xsl:value-of select="lang_name"/>
						</td>
						<td>
							<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td>
							<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"/>
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td>
						</td>
						<td>
							<input type="submit" class="forms" name="values[save]" value="{$lang_add}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr height="50">
					<td>
					</td>
					<td>
						<xsl:variable name="done_action">
							<xsl:value-of select="done_action"/>
						</xsl:variable>
						<xsl:variable name="lang_done">
							<xsl:value-of select="lang_done"/>
						</xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
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
			<xsl:apply-templates select="workorder_data"/>
			<hr noshade="noshade" width="100%" align="center" size="1"/>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td class="th_text" align="center">
						<xsl:value-of select="lang_total_records"/>
						<xsl:text> : </xsl:text>
						<xsl:value-of select="total_hours_records"/>
					</td>
				</tr>
				<tr>
					<td colspan="1">
						<!-- DataTable 0 -->
						<div id="paging_0"> </div>
						<div id="datatable-container_0"/>
					</td>
				</tr>
				<!-- <xsl:apply-templates select="table_header_hour"/><xsl:apply-templates select="values_hour"/>  -->
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

	<!-- New template-->
	<xsl:template match="list_hour">
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
		</table>
		<xsl:apply-templates select="workorder_data"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="th_text" colspan="3" align="right">
					<xsl:value-of select="lang_total_records"/>
					<xsl:text> : </xsl:text>
				</td>
				<td class="th_text" colspan="5" align="left">
					<xsl:value-of select="total_hours_records"/>
				</td>
			</tr>
			<xsl:apply-templates select="table_header_hour"/>
			<xsl:apply-templates select="values_hour"/>
			<xsl:apply-templates select="table_sum"/>
		</table>
		<xsl:apply-templates select="table_add"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="email_list">
		<xsl:variable name="email">
			<xsl:value-of select="email"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected != 0">
				<option value="{$email}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="email"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$email}">
					<xsl:value-of disable-output-escaping="yes" select="email"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="view">
		<xsl:variable name="send_order_action">
			<xsl:value-of select="send_order_action"/>
		</xsl:variable>
		<table align="left" width="100%">
			<form method="post" action="{$send_order_action}">
				<xsl:choose>
					<xsl:when test="no_email =''">
						<tr>
							<td>
								<table width="100%" cellpadding="2" cellspacing="2" align="left">
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
											<a href="{print_action}">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'preview html')"/>
												</xsl:attribute>
												<xsl:value-of select="php:function('lang', 'html')"/>
											</a>
										</td>
										<td>
										</td>
									</tr>
									<tr>
										<td class="th_text" align="left">
											<a href="{pdf_action}">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'preview pdf')"/>
												</xsl:attribute>
												<xsl:value-of select="php:function('lang', 'pdf')"/>
											</a>
										</td>
										<td>
										</td>
									</tr>
									<xsl:choose>
										<xsl:when test="table_send !=''">
											<tr>
												<td colspan="2">
													<input type="submit" name="send_order" value="{table_send/lang_send_order}">
														<xsl:attribute name="title">
															<xsl:value-of select="table_send/lang_send_order_statustext"/>
														</xsl:attribute>
													</input>
													<input type="submit" name="done" value="{table_done/lang_done}">
														<xsl:attribute name="title">
															<xsl:value-of select="table_done/lang_done_statustext"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
										</xsl:when>
									</xsl:choose>
									<xsl:choose>
										<xsl:when test="mail_recipients !=''">
											<tr>
												<td class="th_text" align="left">
													<xsl:value-of select="lang_mail"/>
												</td>
												<td align="left">
													<xsl:value-of select="mail_recipients"/>
												</td>
											</tr>
										</xsl:when>
										<xsl:otherwise>
											<tr>
												<td class="th_text" align="left">
													<xsl:value-of select="lang_mail"/>
												</td>
												<td align="left">
													<input type="text" name="to_email" value="{to_email}">
														<xsl:attribute name="title">
															<xsl:value-of select="lang_to_email_address_statustext"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
										</xsl:otherwise>
									</xsl:choose>
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="php:function('lang', 'request an email receipt')"/>
										</td>
										<td align="left">
											<input type="checkbox" name="email_receipt" value="true">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'request a confirmation email when your email is opened by the recipient')"/>
												</xsl:attribute>
												<xsl:if test="requst_email_receipt != 0">
													<xsl:attribute name="checked" value="checked"/>
												</xsl:if>
											</input>
										</td>
									</tr>
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="php:function('lang', 'send as pdf')"/>
										</td>
										<td align="left">
											<input type="checkbox" name="send_as_pdf" value="true">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Send pdf as attachment to email')"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="php:function('lang', 'notify client by sms')"/>
										</td>
										<td align="left">
											<table>
												<tr>
													<td>
														<input type="checkbox" name="notify_client_by_sms" value="true">
															<xsl:attribute name="title">
																<xsl:value-of select="value_sms_client_order_notice"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="text" name="to_sms_phone" value="{value_sms_phone}">
															<xsl:attribute name="title">
																<xsl:value-of select="value_sms_client_order_notice"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="php:function('lang', 'show calculated cost')"/>
										</td>
										<td align="left">
											<input type="checkbox" name="show_cost" value="true">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'warning: show cost estimate')"/>
												</xsl:attribute>
												<xsl:if test="value_show_cost = '1'">
													<xsl:attribute name="checked">
														<xsl:text>checked</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td align="left">
						<hr noshade="noshade" width="100%" align="center" size="1"/>
						<xsl:apply-templates select="email_data"/>
					</td>
				</tr>
				<tr>
					<td>
						<hr noshade="noshade" width="100%" align="center" size="1"/>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="files!=''">
						<tr>
							<td>
								<table>
									<tr>
										<td>
											<xsl:call-template name="file_list"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<tr>
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<xsl:choose>
								<xsl:when test="workorder_history=''">
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="lang_no_history"/>
										</td>
									</tr>
								</xsl:when>
								<xsl:otherwise>
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="lang_history"/>
										</td>
									</tr>
									<tr>
										<td>
											<div id="paging_1"> </div>
											<div id="datatable-container_1"/>
										</td>
									</tr>
								</xsl:otherwise>
							</xsl:choose>
						</table>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="table_send !=''">
						<tr>
							<td colspan="2">
								<input type="submit" name="send_order" value="{table_send/lang_send_order}">
									<xsl:attribute name="title">
										<xsl:value-of select="table_send/lang_send_order_statustext"/>
									</xsl:attribute>
								</input>
								<input type="submit" name="done" value="{table_done/lang_done}">
									<xsl:attribute name="title">
										<xsl:value-of select="table_done/lang_done_statustext"/>
									</xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</form>
		</table>
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
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

	<!-- New template-->
	<xsl:template match="workorder_history">
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

	<!-- New template-->
	<xsl:template match="email_data">
		<font size="-1">
			<table align="left">
				<tr>
					<td width="100%">
						<table width="100%" cellpadding="2" cellspacing="2" align="left">
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_date"/>
								</td>
								<td align="left" colspan="2">
									<xsl:value-of select="date"/>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_workorder"/>
								</td>
								<td align="left" colspan="2">
									<xsl:value-of select="workorder_id"/>
									<xsl:choose>
										<xsl:when test="lang_reminder !=''">
											<xsl:text> - </xsl:text>
											<b>
												<xsl:value-of select="lang_reminder"/>
											</b>
										</xsl:when>
									</xsl:choose>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_to"/>
								</td>
								<td align="left" colspan="2">
									<xsl:value-of select="to_name"/>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left" valign="top">
									<xsl:value-of select="lang_from"/>
								</td>
								<td align="left" colspan="2">
									<xsl:value-of select="from_name"/>
									<br/>
									<xsl:choose>
										<xsl:when test="ressursnr !=''">
											<xsl:text>RessursNr: </xsl:text>
											<xsl:value-of select="ressursnr"/>
											<br/>
										</xsl:when>
									</xsl:choose>
									<xsl:value-of select="org_name"/>
									<xsl:value-of select="lang_district"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="district"/>
									<br/>
									<xsl:text> [ </xsl:text>
									<xsl:value-of select="from_phone"/>
									<xsl:text> ] </xsl:text>
									<xsl:value-of select="from_email"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="contact_data/value_contact_name">
									<xsl:call-template name="contact_form"/>
								</xsl:when>
							</xsl:choose>
							<xsl:call-template name="location_view"/>
							<xsl:choose>
								<xsl:when test="formatted_gab_id !=''">
									<tr>
										<td class="th_text" align="left">
											Gnr/Bnr/Feste/Seksjon
										</td>
										<td align="left" colspan="2">
											<xsl:value-of select="formatted_gab_id"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="contact_phone !=''">
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="lang_contact_phone"/>
										</td>
										<td align="left" colspan="2">
											<xsl:value-of select="contact_phone"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_title"/>
								</td>
								<td align="left" colspan="2">
									<xsl:value-of select="title"/>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left" valign="top">
									<xsl:value-of select="lang_descr"/>
								</td>
								<td align="left" colspan="2">
									<table border="1" width="100%" bordercolor="#000000" cellspacing="0" cellpadding="0">
										<tr>
											<td width="100%">
												<xsl:value-of select="descr"/>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_budget_account"/>
								</td>
								<td align="left">
									<xsl:value-of select="budget_account"/>
								</td>
								<td align="left">
									<xsl:value-of select="lang_cost_tax"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="sum_calculation!=''">
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="lang_sum_calculation"/>
										</td>
										<td align="left">
											<xsl:value-of select="sum_calculation"/>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_start_date"/>
								</td>
								<td align="left">
									<xsl:value-of select="start_date"/>
								</td>
								<td align="left">
									<xsl:value-of select="lang_materials"/>
								</td>
							</tr>
							<tr>
								<td class="th_text" align="left">
									<xsl:value-of select="lang_end_date"/>
								</td>
								<td align="left">
									<xsl:value-of select="end_date"/>
								</td>
								<td align="left">
									<xsl:value-of select="lang_work"/>
								</td>
							</tr>
							<xsl:choose>
								<xsl:when test="branch_list/selected">
									<tr>
										<td class="th_text" align="left" valign="top">
											<xsl:value-of select="lang_branch"/>
										</td>
										<td align="left">
											<xsl:for-each select="branch_list[selected='selected']">
												<xsl:value-of select="name"/>
												<xsl:if test="position() != last()">, </xsl:if>
											</xsl:for-each>
											<xsl:choose>
												<xsl:when test="other_branch!=''">
													<xsl:text>, </xsl:text>
													<xsl:value-of select="other_branch"/>
												</xsl:when>
											</xsl:choose>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="key_responsible_list/selected">
									<tr>
										<td class="th_text" align="left">
											<xsl:value-of select="lang_key_responsible"/>
										</td>
										<td align="left">
											<xsl:for-each select="key_responsible_list">
												<xsl:choose>
													<xsl:when test="selected">
														<xsl:value-of select="name"/>
													</xsl:when>
												</xsl:choose>
											</xsl:for-each>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<xsl:if test="key_fetch_list/selected">
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_key_fetch"/>
									</td>
									<xsl:for-each select="key_fetch_list">
										<xsl:choose>
											<xsl:when test="selected">
												<td align="left">
													<xsl:value-of select="name"/>
												</td>
											</xsl:when>
										</xsl:choose>
									</xsl:for-each>
								</tr>
							</xsl:if>
							<xsl:if test="key_deliver_list/selected">
								<tr>
									<td class="th_text" align="left">
										<xsl:value-of select="lang_key_deliver"/>
									</td>
									<xsl:for-each select="key_deliver_list">
										<xsl:choose>
											<xsl:when test="selected">
												<td align="left">
													<xsl:value-of select="name"/>
												</td>
											</xsl:when>
										</xsl:choose>
									</xsl:for-each>
								</tr>
							</xsl:if>
						</table>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="values_view_order!=''">
						<tr>
							<td colspand="3">
								<table width="100%" cellpadding="2" cellspacing="2" align="center">
									<xsl:choose>
										<xsl:when test="use_yui_table='1'">
											<td>
												<div id="paging_0"> </div>
												<div id="datatable-container_0"/>
											</td>
										</xsl:when>
										<xsl:otherwise>
											<xsl:apply-templates select="table_header_view_order"/>
											<xsl:apply-templates select="values_view_order"/>
										</xsl:otherwise>
									</xsl:choose>
								</table>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="sms_data!=''">
						<xsl:apply-templates select="sms_data"/>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="order_footer_header!=''">
						<tr>
							<td>
								<br/>
								<h4>
									<xsl:value-of select="order_footer_header"/>
								</h4>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="order_footer!=''">
						<tr>
							<td>
								<xsl:value-of select="order_footer"/>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
			</table>
		</font>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_view_order">
		<xsl:variable name="sort_activity_num">
			<xsl:value-of select="sort_activity_num"/>
		</xsl:variable>
		<xsl:variable name="sort_descr">
			<xsl:value-of select="sort_descr"/>
		</xsl:variable>
		<xsl:variable name="sort_quantity">
			<xsl:value-of select="sort_quantity"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_post"/>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_code"/>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="4%" align="left">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="2%" align="right">
				<xsl:value-of select="lang_quantity"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_billperae"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_cost"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_view_order">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
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
				<xsl:value-of select="post"/>
			</td>
			<td align="left">
				<xsl:value-of select="code"/>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"/>
				<br/>
				<xsl:value-of select="remark"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit_name"/>
			</td>
			<td align="right">
				<xsl:value-of select="quantity"/>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"/>
			</td>
			<td align="right">
				<xsl:value-of select="cost"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_template">
		<xsl:apply-templates select="menu"/>
		<xsl:apply-templates select="workorder_data"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:call-template name="chapter_filter"/>
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
			<xsl:apply-templates select="table_header_template"/>
			<xsl:choose>
				<xsl:when test="values_template[template_id]!=''">
					<xsl:apply-templates select="values_template"/>
				</xsl:when>
			</xsl:choose>
		</table>
		<xsl:apply-templates select="table_done"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_template">
		<xsl:variable name="sort_name">
			<xsl:value-of select="sort_name"/>
		</xsl:variable>
		<xsl:variable name="sort_template_id">
			<xsl:value-of select="sort_template_id"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_template_id}">
					<xsl:value-of select="lang_template_id"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_name}">
					<xsl:value-of select="lang_name"/>
				</a>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_chapter"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_owner"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_entry_date"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_template">
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_prizing_statustext">
			<xsl:value-of select="lang_prizing_statustext"/>
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
				<xsl:value-of select="template_id"/>
			</td>
			<td align="left">
				<xsl:value-of select="name"/>
			</td>
			<td align="left">
				<xsl:value-of select="descr"/>
			</td>
			<td align="left">
				<xsl:value-of select="chapter"/>
			</td>
			<td align="left">
				<xsl:value-of select="owner"/>
			</td>
			<td align="right">
				<xsl:value-of select="entry_date"/>
			</td>
			<xsl:variable name="form_action_select">
				<xsl:value-of select="form_action_select"/>
			</xsl:variable>
			<form method="post" action="{$form_action_select}">
				<td valign="top">
					<input type="hidden" name="template_id" value="{template_id}"/>
					<input type="hidden" name="workorder_id" value="{workorder_id}"/>
					<xsl:variable name="lang_select">
						<xsl:value-of select="lang_select"/>
					</xsl:variable>
					<input type="submit" name="select" value="{$lang_select}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_select_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</form>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="list_template_hour">
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
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:apply-templates select="workorder_data"/>
				</td>
			</tr>
			<tr>
				<td>
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
				</td>
			</tr>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>
			<tr>
				<td>
					<form method="post" name="form" action="{$form_action}">
						<table width="100%" cellpadding="2" cellspacing="2" align="center">
							<xsl:apply-templates select="table_header_template_hour"/>
							<xsl:choose>
								<xsl:when test="values_template_hour[counter]!=''">
									<xsl:apply-templates select="values_template_hour"/>
									<tr>
										<td/>
										<td/>
										<td/>
										<td/>
										<td/>
										<td/>
										<td align="center">
											<xsl:variable name="img_check">
												<xsl:value-of select="img_check"/>
											</xsl:variable>
											<a href="javascript:check_all_checkbox('values[select]')">
												<img src="{$img_check}" border="0" height="16" width="21" alt="{lang_select_all}"/>
											</a>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
						</table>
						<table align="left">
							<tr height="50">
								<td>
									<xsl:variable name="lang_add">
										<xsl:value-of select="lang_add"/>
									</xsl:variable>
									<input type="submit" name="values[add]" value="{$lang_add}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_add_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="left">
						<hr noshade="noshade" width="100%" align="center" size="1"/>
						<tr>
							<td class="th_text" colspan="3" align="right">
								<xsl:value-of select="lang_total_records"/>
								<xsl:text> : </xsl:text>
							</td>
							<td class="th_text" colspan="5" align="left">
								<xsl:value-of select="total_hours_records"/>
							</td>
						</tr>
						<xsl:apply-templates select="table_header_hour"/>
						<xsl:apply-templates select="values_hour"/>
						<xsl:apply-templates select="table_sum"/>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:apply-templates select="table_done"/>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_template_hour">
		<xsl:variable name="sort_billperae">
			<xsl:value-of select="sort_billperae"/>
		</xsl:variable>
		<xsl:variable name="sort_building_part">
			<xsl:value-of select="sort_building_part"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_building_part}">
					<xsl:value-of select="lang_building_part"/>
				</a>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_code"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_billperae}">
					<xsl:value-of select="lang_billperae"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_quantity"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_category"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_per_cent"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_template_hour">
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_prizing_statustext">
			<xsl:value-of select="lang_prizing_statustext"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
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
				<xsl:value-of select="building_part"/>
			</td>
			<td align="left">
				<xsl:value-of select="code"/>
				<input type="hidden" name="values[chapter_id][{counter}]" value="{chapter_id}"/>
				<input type="hidden" name="values[grouping_descr][{counter}]" value="{grouping_descr}"/>
				<input type="hidden" name="values[activity_id][{counter}]" value="{activity_id}"/>
				<input type="hidden" name="values[activity_num][{counter}]" value="{activity_num}"/>
				<input type="hidden" name="values[unit][{counter}]" value="{unit}"/>
				<input type="hidden" name="values[dim_d][{counter}]" value="{dim_d}"/>
				<input type="hidden" name="values[ns3420_id][{counter}]" value="{ns3420_id}"/>
				<input type="hidden" name="values[tolerance][{counter}]" value="{tolerance}"/>
				<input type="hidden" name="values[building_part][{counter}]" value="{building_part}"/>
				<input type="hidden" name="values[hours_descr][{counter}]" value="{hours_descr}"/>
				<input type="hidden" name="values[remark][{counter}]" value="{remark}"/>
				<input type="hidden" name="values[billperae][{counter}]" value="{billperae}"/>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"/>
				<br/>
				<xsl:value-of select="remark"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit_name"/>
			</td>
			<td align="right">
				<xsl:choose>
					<xsl:when test="billperae!=0">
						<xsl:value-of select="billperae"/>
					</xsl:when>
					<xsl:otherwise>
						<input type="text" size="6" name="values[billperae][{counter}]"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td>
				<input type="text" size="6" name="values[quantity][{counter}]"/>
			</td>
			<td align="center">
				<input type="checkbox" name="values[select][{counter}]" value="{counter}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_select_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
			<td>
				<select name="values[wo_hour_cat][{counter}]" class="forms" title="{lang_wo_hour_cat_statustext}" style="cursor:help">
					<option value="">
						<xsl:value-of select="//lang_no_wo_hour_cat"/>
					</option>
					<xsl:for-each select="//wo_hour_cat_list">
						<xsl:variable name="id">
							<xsl:value-of select="id"/>
						</xsl:variable>
						<option value="{$id}">
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</td>
			<td>
				<input type="text" size="3" maxlength="3" name="values[cat_per_cent][{counter}]"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="prizebook">
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
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td>
					<xsl:apply-templates select="workorder_data"/>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="values_prizebook[activity_id]!=''">
					<tr>
						<td>
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
						</td>
					</tr>
					<tr>
						<td>
							<xsl:variable name="form_action">
								<xsl:value-of select="form_action"/>
							</xsl:variable>
							<form method="post" name="form" action="{$form_action}">
								<table width="100%" cellpadding="2" cellspacing="2" align="center">
									<xsl:apply-templates select="table_header_prizebook"/>
									<xsl:apply-templates select="values_prizebook"/>
								</table>
								<table align="left">
									<tr height="50">
										<td>
											<xsl:variable name="lang_add">
												<xsl:value-of select="lang_add"/>
											</xsl:variable>
											<input type="submit" name="values[add]" value="{$lang_add}" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_add_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</table>
							</form>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
					<hr noshade="noshade" width="100%" align="center" size="1"/>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" cellpadding="2" cellspacing="2" align="left">
						<tr>
							<td class="th_text" colspan="3" align="right">
								<xsl:value-of select="lang_total_records"/>
								<xsl:text> : </xsl:text>
							</td>
							<td class="th_text" colspan="5" align="left">
								<xsl:value-of select="total_hours_records"/>
							</td>
						</tr>
						<xsl:apply-templates select="table_header_hour"/>
						<xsl:apply-templates select="values_hour"/>
						<xsl:apply-templates select="table_sum"/>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:apply-templates select="table_done"/>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="workorder_data">
		<xsl:variable name="link_workorder">
			<xsl:value-of select="link_workorder"/>
		</xsl:variable>
		<xsl:variable name="link_project">
			<xsl:value-of select="link_project"/>
		</xsl:variable>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_project_id"/>
					<xsl:text> :</xsl:text>
				</td>
				<td class="th_text" align="left">
					<a href="{$link_project}">
						<xsl:value-of select="project_id"/>
					</a>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_workorder_id"/>
					<xsl:text> :</xsl:text>
				</td>
				<td class="th_text" align="left">
					<a href="{$link_workorder}">
						<xsl:value-of select="workorder_id"/>
					</a>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_workorder_title"/>
					<xsl:text> :</xsl:text>
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="workorder_title"/>
				</td>
			</tr>
			<tr>
				<td class="th_text" align="left">
					<xsl:value-of select="lang_vendor_name"/>
					<xsl:text> :</xsl:text>
				</td>
				<td class="th_text" align="left">
					<xsl:value-of select="vendor_name"/>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_sum">
		<tr>
			<td>
			</td>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_sum_calculation"/>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="value_sum_calculation"/>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="sum_deviation"/>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="sum_result"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_addition_rs"/>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="value_addition_rs"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_addition_percentage"/>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="value_addition_percentage"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_sum_tax"/>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="value_sum_tax"/>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="th_text" align="left">
				<xsl:value-of select="lang_total_sum"/>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td class="th_text" align="right">
				<xsl:value-of select="value_total_sum"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_prizebook">
		<xsl:variable name="sort_num">
			<xsl:value-of select="sort_num"/>
		</xsl:variable>
		<xsl:variable name="sort_total_cost">
			<xsl:value-of select="sort_total_cost"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_num}">
					<xsl:value-of select="lang_num"/>
				</a>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_base_descr"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_w_cost"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<xsl:value-of select="lang_m_cost"/>
			</td>
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_total_cost}">
					<xsl:value-of select="lang_total_cost"/>
				</a>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_quantity"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_category"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_per_cent"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="values_prizebook">
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_prizing_statustext">
			<xsl:value-of select="lang_prizing_statustext"/>
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
				<xsl:value-of select="num"/>
				<input type="hidden" name="values[activity_id][{counter}]" value="{activity_id}"/>
				<input type="hidden" name="values[activity_num][{counter}]" value="{num}"/>
				<input type="hidden" name="values[unit][{counter}]" value="{unit}"/>
				<input type="hidden" name="values[dim_d][{counter}]" value="{dim_d}"/>
				<input type="hidden" name="values[ns3420_id][{counter}]" value="{ns3420_id}"/>
				<input type="hidden" name="values[descr][{counter}]" value="{descr}"/>
				<input type="hidden" name="values[total_cost][{counter}]" value="{total_cost}"/>
			</td>
			<td align="left">
				<xsl:value-of select="descr"/>
			</td>
			<td align="left">
				<xsl:value-of select="base_descr"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit_name"/>
			</td>
			<td align="right">
				<xsl:value-of select="w_cost"/>
			</td>
			<td align="right">
				<xsl:value-of select="m_cost"/>
			</td>
			<td align="right">
				<xsl:choose>
					<xsl:when test="total_cost!=0">
						<xsl:value-of select="total_cost"/>
					</xsl:when>
					<xsl:otherwise>
						<input type="text" size="6" name="values[total_cost][{counter}]"/>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td>
				<input type="text" size="6" name="values[quantity][{counter}]"/>
			</td>
			<td>
				<select name="values[wo_hour_cat][{counter}]" class="forms" title="{lang_wo_hour_cat_statustext}" style="cursor:help">
					<option value="">
						<xsl:value-of select="//lang_no_wo_hour_cat"/>
					</option>
					<xsl:for-each select="//wo_hour_cat_list">
						<xsl:variable name="id">
							<xsl:value-of select="id"/>
						</xsl:variable>
						<option value="{$id}">
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</td>
			<td>
				<input type="text" size="3" maxlength="3" name="values[cat_per_cent][{counter}]"/>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_header_hour">
		<xsl:variable name="sort_activity_num">
			<xsl:value-of select="sort_activity_num"/>
		</xsl:variable>
		<xsl:variable name="sort_descr">
			<xsl:value-of select="sort_descr"/>
		</xsl:variable>
		<xsl:variable name="sort_quantity">
			<xsl:value-of select="sort_quantity"/>
		</xsl:variable>
		<tr class="th">
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_post"/>
			</td>
			<td class="th_text" width="15%" align="left">
				<xsl:value-of select="lang_code"/>
			</td>
			<td class="th_text" width="40%" align="left">
				<xsl:value-of select="lang_descr"/>
			</td>
			<td class="th_text" width="4%" align="left">
				<xsl:value-of select="lang_unit"/>
			</td>
			<td class="th_text" width="2%" align="right">
				<xsl:value-of select="lang_quantity"/>
			</td>
			<td class="th_text" width="5%" align="right">
				<xsl:value-of select="lang_billperae"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_cost"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_deviation"/>
			</td>
			<td class="th_text" width="15%" align="right">
				<xsl:value-of select="lang_result"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_category"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_per_cent"/>
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
	<xsl:template match="values_hour">
		<xsl:variable name="lang_view_statustext">
			<xsl:value-of select="lang_view_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_edit_statustext">
			<xsl:value-of select="lang_edit_statustext"/>
		</xsl:variable>
		<xsl:variable name="lang_delete_statustext">
			<xsl:value-of select="lang_delete_statustext"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="new_grouping=1">
				<tr>
					<td class="th_text" align="center" colspan="10" width="100%">
						<xsl:value-of select="grouping_descr"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
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
				<xsl:value-of select="post"/>
			</td>
			<td align="left">
				<xsl:value-of select="code"/>
			</td>
			<td align="left">
				<xsl:value-of select="hours_descr"/>
				<br/>
				<xsl:value-of select="remark"/>
			</td>
			<td align="left">
				<xsl:value-of select="unit_name"/>
			</td>
			<td align="right">
				<xsl:value-of select="quantity"/>
			</td>
			<td align="right">
				<xsl:value-of select="billperae"/>
			</td>
			<td align="right">
				<xsl:value-of select="cost"/>
			</td>
			<td align="right">
				<xsl:variable name="link_deviation"><xsl:value-of select="link_deviation"/>&amp;from=<xsl:value-of select="//function"/></xsl:variable>
				<a href="{$link_deviation}" onMouseover="window.status='';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="deviation"/>
				</a>
			</td>
			<td align="right">
				<xsl:value-of select="result"/>
			</td>
			<td align="right">
				<xsl:value-of select="wo_hour_category"/>
			</td>
			<td align="right">
				<xsl:value-of select="cat_per_cent"/>
			</td>
			<td align="center">
				<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/>&amp;from=<xsl:value-of select="//function"/>&amp;template_id=<xsl:value-of select="//template_id"/></xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_edit"/>
				</a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete"><xsl:value-of select="//link_delete"/>&amp;hour_id=<xsl:value-of select="hour_id"/>&amp;template_id=<xsl:value-of select="//template_id"/></xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="text_delete"/>
				</a>
			</td>
		</tr>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_add">
		<table width="80%">
			<tr>
				<td align="left" height="50">
					<xsl:variable name="add_prizebook_action">
						<xsl:value-of select="add_prizebook_action"/>
					</xsl:variable>
					<xsl:variable name="lang_add_prizebook">
						<xsl:value-of select="lang_add_prizebook"/>
					</xsl:variable>
					<form method="post" action="{$add_prizebook_action}">
						<input type="submit" name="add_prizebook" value="{$lang_add_prizebook}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_prizebook_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td align="left" height="50">
					<xsl:variable name="add_template_action">
						<xsl:value-of select="add_template_action"/>
					</xsl:variable>
					<xsl:variable name="lang_add_template">
						<xsl:value-of select="lang_add_template"/>
					</xsl:variable>
					<form method="post" action="{$add_template_action}">
						<input type="submit" name="add_template" value="{$lang_add_template}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_template_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td align="left" height="50">
					<xsl:variable name="add_custom_action">
						<xsl:value-of select="add_custom_action"/>
					</xsl:variable>
					<xsl:variable name="lang_add_custom">
						<xsl:value-of select="lang_add_custom"/>
					</xsl:variable>
					<form method="post" action="{$add_custom_action}">
						<input type="submit" name="add_custom" value="{$lang_add_custom}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_custom_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
				<td align="left" height="50">
					<xsl:variable name="save_template_action">
						<xsl:value-of select="save_template_action"/>
					</xsl:variable>
					<xsl:variable name="lang_save_template">
						<xsl:value-of select="lang_save_template"/>
					</xsl:variable>
					<form method="post" action="{$save_template_action}">
						<input type="submit" name="save_template" value="{$lang_save_template}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_template_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
			<tr>
				<xsl:variable name="print_view_action">
					<xsl:value-of select="print_view_action"/>
				</xsl:variable>
				<xsl:variable name="lang_print_view">
					<xsl:value-of select="lang_print_view"/>
				</xsl:variable>
				<form method="post" action="{$print_view_action}">
					<td align="left" height="50">
						<input type="submit" name="print_view" value="{$lang_print_view}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_print_view_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td>
						<xsl:value-of select="lang_show_details"/>
						<input type="checkbox" name="show_details" value="True" checked="checked" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_show_details_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td>
						<xsl:value-of select="lang_show_cost"/>
						<input type="checkbox" name="show_cost" value="True" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_show_cost_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</form>
			</tr>
			<tr>
				<xsl:variable name="view_tender_action">
					<xsl:value-of select="view_tender_action"/>
				</xsl:variable>
				<xsl:variable name="lang_view_tender">
					<xsl:value-of select="lang_view_tender"/>
				</xsl:variable>
				<form method="post" action="{$view_tender_action}" target="_new">
					<td align="left" height="50">
						<input type="submit" name="view_tender" value="{$lang_view_tender}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_view_tender_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td>
						<xsl:value-of select="lang_show_cost"/>
						<input type="checkbox" name="show_cost" value="True" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_show_cost_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
					<td>
						<xsl:value-of select="lang_mark_draft"/>
						<input type="checkbox" name="mark_draft" value="True" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_mark_draft_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</form>
			</tr>
			<tr>
				<td align="left" height="50">
					<form method="post" action="{done_action}">
						<input type="submit" name="save_done" value="{lang_done}">
						</input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_done">
		<table width="100%" align="left">
			<tr>
				<td height="50" align="left">
					<xsl:variable name="done_action">
						<xsl:value-of select="done_action"/>
					</xsl:variable>
					<xsl:variable name="lang_done">
						<xsl:value-of select="lang_done"/>
					</xsl:variable>
					<form method="post" action="{$done_action}">
						<input type="submit" name="done" value="{$lang_done}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_done_statustext"/>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="table_send">
		<table width="80%">
			<tr>
				<td align="left" height="50">
					<xsl:variable name="lang_send_order">
						<xsl:value-of select="lang_send_order"/>
					</xsl:variable>
					<input type="submit" name="send_order" value="{$lang_send_order}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_send_order_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</xsl:template>

	<!-- add / edit -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit_hour">
		<script type="text/javascript">
			self.name="first_Window";
			function ns3420_lookup()
			{
				Window1=window.open('<xsl:value-of select="ns3420_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
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
				<form method="post" name="form" action="{$form_action}">
					<xsl:choose>
						<xsl:when test="value_hour_id !=''">
							<xsl:choose>
								<xsl:when test="value_activity_num =''">
									<tr>
										<td>
											<xsl:value-of select="lang_copy_hour"/>
										</td>
										<td>
											<input type="checkbox" name="values[copy_hour]" value="True" onMouseout="window.status='';return true;">
												<xsl:attribute name="onMouseover">
													<xsl:text>window.status='</xsl:text>
													<xsl:value-of select="lang_copy_hour_statustext"/>
													<xsl:text>'; return true;</xsl:text>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</xsl:when>
							</xsl:choose>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_hour_id"/>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_hour_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_activity_num"/>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_activity_num"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_workorder"/>
						</td>
						<td class="th_text">
							<xsl:value-of select="value_workorder_title"/>
							<xsl:text> [ </xsl:text>
							<xsl:value-of select="value_workorder_id"/>
							<xsl:text> ]</xsl:text>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_vendor"/>
						</td>
						<td class="th_text">
							<xsl:value-of select="value_vendor_name"/>
							<xsl:text> [ </xsl:text>
							<xsl:value-of select="value_vendor_id"/>
							<xsl:text> ]</xsl:text>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_activity_num=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_chapter"/>
								</td>
								<td class="th_text">
									<xsl:call-template name="chapter_select"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_grouping"/>
								</td>
								<td class="th_text">
									<xsl:call-template name="grouping_select"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_new_grouping"/>
								</td>
								<td>
									<input type="text" name="values[new_grouping]" value="{value_new_grouping}" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_new_grouping_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="php:function('lang', 'building part')"/>
						</td>
						<td>
							<select name="values[building_part_id]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'select building part')"/>
								</xsl:attribute>
								<option value="0">
									<xsl:value-of select="php:function('lang', 'select building part')"/>
								</option>
								<xsl:apply-templates select="building_part_list/options"/>
							</select>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_ns3420"/>
								</td>
								<td class="th_text">
									<xsl:value-of select="value_ns3420_id"/>
									<input type="hidden" name="ns3420_id" value="{value_ns3420_id}"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_descr"/>
								</td>
								<td>
									<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_descr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_descr"/>
									</textarea>
								</td>
							</tr>
						</xsl:when>
						<xsl:otherwise>
							<tr>
								<td valign="top">
									<a href="javascript:ns3420_lookup()" onMouseover="window.status='{lang_ns3420_statustext}';return true;" onMouseout="window.status='';return true;">
										<xsl:value-of select="lang_ns3420"/>
									</a>
								</td>
								<td valign="top">
									<input type="text" name="ns3420_id" value="{value_ns3420_id}" onClick="ns3420_lookup();" readonly="readonly">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_ns3420_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
									</input>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td>
									<textarea cols="40" rows="4" name="ns3420_descr" onMouseout="window.status='';return true;">
										<xsl:attribute name="onMouseover">
											<xsl:text>window.status='</xsl:text>
											<xsl:value-of select="lang_descr_statustext"/>
											<xsl:text>'; return true;</xsl:text>
										</xsl:attribute>
										<xsl:value-of select="value_descr"/>
									</textarea>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:value-of select="lang_tolerance"/>
								</td>
								<td>
									<xsl:call-template name="tolerance_select"/>
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="lang_unit"/>
						</td>
						<td>
							<xsl:call-template name="unit_select"/>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_dim_d"/>
						</td>
						<td>
							<xsl:call-template name="dim_d_select"/>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_wo_hour_category"/>
						</td>
						<td>
							<xsl:variable name="lang_wo_hour_cat_statustext">
								<xsl:value-of select="lang_to_email_address_statustext"/>
							</xsl:variable>
							<select name="values[wo_hour_cat]" class="forms" onMouseover="window.status='{$lang_wo_hour_cat_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value="">
									<xsl:value-of select="lang_select_wo_hour_category"/>
								</option>
								<xsl:apply-templates select="wo_hour_cat_list"/>
							</select>
							<xsl:value-of select="lang_per_cent"/>
							<input type="text" size="3" maxlength="3" name="values[cat_per_cent]" value="{value_cat_per_cent}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cat_per_cent_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_quantity"/>
						</td>
						<td>
							<input type="text" name="values[quantity]" value="{value_quantity}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_quantity_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_billperae"/>
						</td>
						<td><input type="text" name="values[billperae]" value="{value_billperae}" onMouseout="window.status='';return true;"><xsl:attribute name="onMouseover"><xsl:text>window.status='</xsl:text><xsl:value-of select="lang_billperae_statustext"/><xsl:text>'; return true;</xsl:text></xsl:attribute></input><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_total_cost"/>
						</td>
						<td><xsl:value-of select="value_total_cost"/><xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_remark"/>
						</td>
						<td>
							<textarea cols="60" rows="4" name="values[remark]" onMouseout="window.status='';return true;">
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
									<xsl:value-of select="lang_done_statustext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<!-- add_deviation / edit_deviation  -->
	<xsl:template match="edit_deviation">
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
						<td class="th_text" align="left">
							<xsl:value-of select="lang_workorder"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="value_workorder_id"/>
							<input type="hidden" name="workorder_id" value="{value_workorder_id}"/>
							<input type="hidden" name="hour_id" value="{value_hour_id}"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" align="left">
							<xsl:value-of select="lang_hour_id"/>
						</td>
						<td class="th_text" align="left">
							<xsl:value-of select="value_hour_id"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_id"/>
								</td>
								<td>
									<xsl:value-of select="value_id"/>
									<input type="hidden" name="values[id]" value="{value_id}"/>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_date"/>
								</td>
								<td>
									<xsl:value-of select="entry_date"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_amount"/>
						</td>
						<td>
							<input type="text" name="values[amount]" value="{value_amount}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_amount_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"/>
							</textarea>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save">
								<xsl:value-of select="lang_save"/>
							</xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_standardtext"/>
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
									<xsl:value-of select="lang_done_standardtext"/>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="wo_hour_cat_list">
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
	<xsl:template match="sms_data">
		<tr>
			<td>
				<br/>
				<xsl:value-of select="heading"/>
			</td>
		</tr>
		<tr>
			<td class="th_text">
				<xsl:value-of select="message"/>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="status_code_text"/>
				<xsl:text>: </xsl:text>
				<xsl:for-each select="status_code">
					<xsl:value-of select="name"/>
					<xsl:if test="position() != last()">, </xsl:if>
				</xsl:for-each>
			</td>
		</tr>
		<tr>
			<td class="th_text">
				<xsl:value-of select="lang_example"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="example"/>
			</td>
		</tr>
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
