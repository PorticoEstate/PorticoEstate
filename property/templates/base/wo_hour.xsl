
<!-- $Id$ -->
<xsl:template match="data">
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
		<xsl:when test="import_calculation">
			<xsl:apply-templates select="import_calculation"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list_hour"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="list_deviation">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
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

	<div class="proplist-col">
		<xsl:variable name="add_action">
			<xsl:value-of select="add_action"/>
		</xsl:variable>
		<a class="pure-button pure-button-primary" href="{$add_action}">
			<xsl:value-of select="lang_add"/>
			<!--
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
			-->
		</a>
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<a class="pure-button pure-button-primary" href="{$done_action}">
			<xsl:value-of select="lang_done"/>
			<!--
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_done_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
			-->
		</a>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_deviation">
	<tr class="th">
		<td class="th_text" width="1%" style="text-align:right;">
			<xsl:value-of select="lang_id"/>
		</td>
		<td class="th_text" width="6%" style="text-align:right;">
			<xsl:value-of select="lang_amount"/>
		</td>
		<td class="th_text" width="80%">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="80%">
			<xsl:value-of select="lang_date"/>
		</td>
		<td class="th_text" width="5%" style="text-align:left;">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" style="text-align:left;">
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
		<td class="small_text" style="text-align:right;">
			<xsl:value-of select="id"/>
		</td>
		<td class="small_text" style="text-align:right;">
			<xsl:value-of select="amount"/>
		</td>
		<td class="small_text" style="text-align:left;">
			<xsl:value-of select="descr"/>
		</td>
		<td class="small_text" style="text-align:left;">
			<xsl:value-of select="entry_date"/>
		</td>
		<td class="small_text" style="text-align:center;">
			<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td class="small_text" style="text-align:center;">
			<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="add_template">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<div>
		<xsl:variable name="add_action">
			<xsl:value-of select="add_action"/>
		</xsl:variable>
		<form id="form" name="form" method="post" action="{$add_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="generic">
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
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - enter a template name!')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
				</div>
			</div>

			<div class="proplist-col">
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<button type="submit" class="pure-button pure-button-primary" name="values[save]" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="$lang_add"/>
				</button>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<button type="button" class="pure-button pure-button-primary" name="done"  onClick="document.done.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_done_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="$lang_done"/>
				</button>
			</div>
		</form>
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<form name="done" id="done" action="{$done_action}" method="post"></form>
	</div>
	<xsl:apply-templates select="workorder_data"/>
	<xsl:for-each select="//datatable_def">
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
</xsl:template>

<!-- New template-->
<xsl:template match="list_hour">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td style="text-align:left;" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</table>
	<xsl:apply-templates select="workorder_data"/>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<tr>
			<td class="th_text" colspan="3" style="text-align:right;">
				<xsl:value-of select="lang_total_records"/>
				<xsl:text> : </xsl:text>
			</td>
			<td class="th_text" colspan="5" style="text-align:left;">
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
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<xsl:variable name="send_order_action">
		<xsl:value-of select="send_order_action"/>
	</xsl:variable>
	<form method="post" action="{$send_order_action}" class="pure-form pure-form-aligned" name="form" id="form">
		<xsl:choose>
			<xsl:when test="no_email =''">
				<div class="pure-control-group">
					<label>
						<a href="{print_action}">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'preview html')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'html')"/>
						</a>
					</label>
				</div>
				<div class="pure-control-group">
					<label>
						<a href="{pdf_action}" target="_blank">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'preview pdf')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'pdf')"/>
						</a>
					</label>
				</div>
				<xsl:choose>
					<xsl:when test="table_send !=''">
						<div class="pure-control-group">
							<button type="button" class="pure-button pure-button-primary" name="send_order" value="1" onClick="confirm_session('send_order');">
								<xsl:attribute name="title">
									<xsl:value-of select="table_send/lang_send_order_statustext"/>
								</xsl:attribute>
								<xsl:value-of select="table_send/lang_send_order"/>
							</button>
							<button type="submit" class="pure-button pure-button-primary" name="done" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="table_done/lang_done_statustext"/>
								</xsl:attribute>
								<xsl:value-of select="table_done/lang_done"/>
							</button>
						</div>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="mail_recipients !=''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_mail"/>
							</label>
							<xsl:value-of select="mail_recipients"/>
						</div>
					</xsl:when>
					<xsl:otherwise>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_mail"/>
							</label>
							<input type="text" name="to_email" value="{to_email}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_to_email_address_statustext"/>
								</xsl:attribute>
							</input>
						</div>
					</xsl:otherwise>
				</xsl:choose>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'request an email receipt')"/>
					</label>
					<input type="checkbox" name="email_receipt" value="true">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'request a confirmation email when your email is opened by the recipient')"/>
						</xsl:attribute>
						<xsl:if test="requst_email_receipt != 0">
							<xsl:attribute name="checked" value="checked"/>
						</xsl:if>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'send as pdf')"/>
					</label>
					<input type="checkbox" name="send_as_pdf" value="true">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Send pdf as attachment to email')"/>
						</xsl:attribute>
						<xsl:if test="send_as_pdf = 1">
							<xsl:attribute name="checked" value="checked"/>
						</xsl:if>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'notify client by sms')"/>
					</label>
					<input type="checkbox" name="notify_client_by_sms" value="true">
						<xsl:attribute name="title">
							<xsl:value-of select="value_sms_client_order_notice"/>
						</xsl:attribute>
					</input>
					<input type="text" name="to_sms_phone" value="{value_sms_phone}">
						<xsl:attribute name="title">
							<xsl:value-of select="value_sms_client_order_notice"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'show calculated cost')"/>
					</label>
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
				</div>
			</xsl:when>
		</xsl:choose>
		<div class="pure-control-group">
			<hr noshade="noshade" width="100%" style="text-align:center;" size="1"/>
		</div>
		<xsl:apply-templates select="email_data"/>
		<xsl:choose>
			<xsl:when test="files!=''">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'files')"/>
					</label>
					<div>
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
		<div class="pure-control-group">
			<xsl:choose>
				<xsl:when test="workorder_history=''">
					<label>
						<xsl:value-of select="lang_no_history"/>
					</label>
				</xsl:when>
				<xsl:otherwise>
					<div>
						<xsl:for-each select="//datatable_def">
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
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<xsl:choose>
			<xsl:when test="table_send !=''">
				<div class="pure-control-group">
					<button type="button" class="pure-button pure-button-primary" name="send_order" value="1" onClick="confirm_session('send_order');">
						<xsl:attribute name="title">
							<xsl:value-of select="table_send/lang_send_order_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="table_send/lang_send_order"/>
					</button>
					<button type="submit" class="pure-button pure-button-primary" name="done" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="table_done/lang_done_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="table_done/lang_done"/>
					</button>
				</div>
			</xsl:when>
		</xsl:choose>
	</form>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header_history">
	<tr class="th">
		<td class="th_text" width="20%" style="text-align:left;">
			<xsl:value-of select="lang_date"/>
		</td>
		<td class="th_text" width="10%" style="text-align:left;">
			<xsl:value-of select="lang_user"/>
		</td>
		<td class="th_text" width="30%" style="text-align:left;">
			<xsl:value-of select="lang_action"/>
		</td>
		<td class="th_text" width="10%" style="text-align:left;">
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
		<td style="text-align:left;">
			<xsl:value-of select="value_date"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="value_user"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="value_action"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="value_new_value"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="email_data"  xmlns:php="http://php.net/xsl">
	<table style="text-align:left;">
		<tr>
			<td width="100%">
				<table width="100%" cellpadding="2" cellspacing="2" style="text-align:left;">
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_date"/>
						</td>
						<td style="text-align:left;" colspan="2">
							<xsl:value-of select="date"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_workorder"/>
						</td>
						<td style="text-align:left;" colspan="2">
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
					<xsl:choose>
						<xsl:when test="contract_name !=''">
							<tr>
								<td class="th_text" style="text-align:left;">
									<xsl:value-of select="php:function('lang', 'contract')"/>
								</td>
								<td style="text-align:left;" colspan="2">
									<xsl:value-of select="contract_name"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>

					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_to"/>
						</td>
						<td style="text-align:left;" colspan="2">
							<xsl:value-of select="to_name"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" style="text-align:left;" valign="top">
							<xsl:value-of select="lang_from"/>
						</td>
						<td style="text-align:left;" colspan="2">
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
					<tr>
						<td class="th_text" style="text-align:left;" colspan = "2">
							<b>
								<xsl:value-of select="php:function('lang', 'delivery address')"/>
							</b>
						</td>
					</tr>
					<xsl:call-template name="location_view_local"/>
					<xsl:choose>
						<xsl:when test="formatted_gab_id !=''">
							<tr>
								<td class="th_text" style="text-align:left;">
									Gnr/Bnr/Feste/Seksjon
								</td>
								<td style="text-align:left;" colspan="2">
									<xsl:value-of select="formatted_gab_id"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="contact_phone !=''">
							<tr>
								<td class="th_text" style="text-align:left;">
									<xsl:value-of select="lang_contact_phone"/>
								</td>
								<td style="text-align:left;" colspan="2">
									<xsl:value-of select="contact_phone"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_title"/>
						</td>
						<td style="text-align:left;" colspan="2">
							<xsl:value-of select="title"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" style="text-align:left;" valign="top">
							<xsl:value-of select="lang_descr"/>
						</td>
						<td style="text-align:left;" colspan="2">
							<table border="1" width="100%" style="border-color:#000000;" cellspacing="0" cellpadding="0">
								<tr>
									<td width="100%">
										<xsl:value-of disable-output-escaping="yes" select="descr"/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_budget_account"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="budget_account"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="lang_cost_tax"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="sum_calculation!=''">
							<tr>
								<td class="th_text" style="text-align:left;">
									<xsl:value-of select="lang_sum_calculation"/>
								</td>
								<td style="text-align:left;">
									<xsl:value-of select="sum_calculation"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_start_date"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="start_date"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="lang_materials"/>
						</td>
					</tr>
					<tr>
						<td class="th_text" style="text-align:left;">
							<xsl:value-of select="lang_end_date"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="end_date"/>
						</td>
						<td style="text-align:left;">
							<xsl:value-of select="lang_work"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="branch_list/selected">
							<tr>
								<td class="th_text" style="text-align:left;" valign="top">
									<xsl:value-of select="lang_branch"/>
								</td>
								<td style="text-align:left;">
									<xsl:for-each select="branch_list[selected='selected' or selected = 1]">
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
								<td class="th_text" style="text-align:left;">
									<xsl:value-of select="lang_key_responsible"/>
								</td>
								<td style="text-align:left;">
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
							<td class="th_text" style="text-align:left;">
								<xsl:value-of select="lang_key_fetch"/>
							</td>
							<xsl:for-each select="key_fetch_list">
								<xsl:choose>
									<xsl:when test="selected">
										<td style="text-align:left;">
											<xsl:value-of select="name"/>
										</td>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</tr>
					</xsl:if>
					<xsl:if test="key_deliver_list/selected">
						<tr>
							<td class="th_text" style="text-align:left;">
								<xsl:value-of select="lang_key_deliver"/>
							</td>
							<xsl:for-each select="key_deliver_list">
								<xsl:choose>
									<xsl:when test="selected">
										<td style="text-align:left;">
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
					<td colspan="3">
						<xsl:choose>
							<xsl:when test="use_yui_table ='1'">
								<xsl:for-each select="//datatable_def">
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
							</xsl:when>
							<xsl:otherwise>
								<table class="details">
									<thead>
										<xsl:apply-templates select="table_header_view_order"/>
									</thead>
									<tbody>
										<xsl:apply-templates select="values_view_order"/>
									</tbody>
								</table>
							</xsl:otherwise>
						</xsl:choose>
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
			<xsl:when test="documentation_url !=''">
				<tr>
					<td>
						<a href="{documentation_url}">
							<xsl:value-of select="php:function('lang', 'import documents')"/>
						</a>
					</td>
				</tr>
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
	<tr>
		<th class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'post')"/>
		</th>
		<th class="th_text" width="15%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'Code')"/>
		</th>
		<th class="th_text" width="40%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'descr')"/>
		</th>
		<th class="th_text" width="4%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'Unit')"/>
		</th>
		<th class="th_text" width="2%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'Quantity')"/>
		</th>
		<th nowrap="true" class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'Bill per unit')"/>
		</th>
		<th class="th_text" width="15%" style="text-align:center;">
			<xsl:value-of select="php:function('lang', 'cost')"/>
		</th>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="values_view_order">
	<xsl:choose>
		<xsl:when test="new_grouping=1">
			<tr>
				<td style="text-align:left;" colspan="7" width="100%">
					<b>
						<xsl:value-of select="grouping_descr"/>
					</b>
				</td>
			</tr>
		</xsl:when>
	</xsl:choose>
	<tr>
		<td style="text-align:right;">
			<xsl:value-of select="post"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="code"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="hours_descr"/>
			<br/>
			<xsl:value-of select="remark"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="unit_name"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="quantity"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="billperae"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="cost"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="list_template">
	<xsl:apply-templates select="menu"/>
	<xsl:apply-templates select="workorder_data"/>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<tr>
			<td>
				<xsl:call-template name="chapter_filter"/>
			</td>
			<td style="text-align:right;">
				<xsl:call-template name="search_field"/>
			</td>
		</tr>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
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
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_template_id}">
				<xsl:value-of select="lang_template_id"/>
			</a>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_name}">
				<xsl:value-of select="lang_name"/>
			</a>
		</td>
		<td class="th_text" width="30%" style="text-align:left;">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="20%" style="text-align:left;">
			<xsl:value-of select="lang_chapter"/>
		</td>
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_owner"/>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<xsl:value-of select="lang_entry_date"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
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
		<td style="text-align:right;">
			<xsl:value-of select="template_id"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="name"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="descr"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="chapter"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="owner"/>
		</td>
		<td style="text-align:right;">
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
				<button type="submit" name="select" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="$lang_select"/>
				</button>
			</td>
		</form>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="list_template_hour">
	<xsl:apply-templates select="menu"/>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td style="text-align:left;" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<tr>
			<td>
				<xsl:apply-templates select="workorder_data"/>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
					<tr>
						<td style="text-align:right;">
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
					<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
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
									<td style="text-align:center;">
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
					<table style="text-align:left;">
						<tr height="50">
							<td>
								<xsl:variable name="lang_add">
									<xsl:value-of select="lang_add"/>
								</xsl:variable>
								<button type="submit" name="values[add]" value="{$lang_add}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_add_statustext"/>
									</xsl:attribute>
									<xsl:value-of select="$lang_add"/>
								</button>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" cellpadding="2" cellspacing="2" style="text-align:left;">
					<hr noshade="noshade" width="100%" style="text-align:center;" size="1"/>
					<tr>
						<td class="th_text" colspan="3" style="text-align:right;">
							<xsl:value-of select="lang_total_records"/>
							<xsl:text> : </xsl:text>
						</td>
						<td class="th_text" colspan="5" style="text-align:left;">
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
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_building_part}">
				<xsl:value-of select="lang_building_part"/>
			</a>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<xsl:value-of select="lang_code"/>
		</td>
		<td class="th_text" width="30%" style="text-align:left;">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_unit"/>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_billperae}">
				<xsl:value-of select="lang_billperae"/>
			</a>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_quantity"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_select"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_category"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
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
				<td class="th_text" style="text-align:center;" colspan="10" width="100%">
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
		<td style="text-align:right;">
			<xsl:value-of select="building_part"/>
		</td>
		<td style="text-align:left;">
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
		<td style="text-align:left;">
			<xsl:value-of select="hours_descr"/>
			<br/>
			<xsl:value-of select="remark"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="unit_name"/>
		</td>
		<td style="text-align:right;">
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
		<td style="text-align:center;">
			<input type="checkbox" name="values[select][{counter}]" value="{counter}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_select_statustext"/>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
		<td>
			<select name="values[wo_hour_cat][{counter}]" class="forms" title="{lang_wo_hour_cat_statustext}">
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
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td style="text-align:left;" colspan="3">
						<xsl:call-template name="msgbox"/>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
	</table>
	<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
		<tr>
			<td>
				<xsl:apply-templates select="workorder_data"/>
			</td>
		</tr>
		<xsl:choose>
			<xsl:when test="values_prizebook[activity_id]!=''">
				<tr>
					<td>
						<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
							<tr>
								<td style="text-align:right;">
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
							<table width="100%" cellpadding="2" cellspacing="2" style="text-align:center;">
								<xsl:apply-templates select="table_header_prizebook"/>
								<xsl:apply-templates select="values_prizebook"/>
							</table>
							<table style="text-align:left;">
								<tr height="50">
									<td>
										<xsl:variable name="lang_add">
											<xsl:value-of select="lang_add"/>
										</xsl:variable>
										<button type="submit" name="values[add]" value="1">
											<xsl:attribute name="title">
												<xsl:value-of select="lang_add_statustext"/>
											</xsl:attribute>
											<xsl:value-of select="$lang_add"/>
										</button>
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
				<hr noshade="noshade" width="100%" style="text-align:center;" size="1"/>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" cellpadding="2" cellspacing="2" style="text-align:left;">
					<tr>
						<td class="th_text" colspan="3" style="text-align:right;">
							<xsl:value-of select="lang_total_records"/>
							<xsl:text> : </xsl:text>
						</td>
						<td class="th_text" colspan="5" style="text-align:left;">
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
	<div>
		<xsl:variable name="link_project">
			<xsl:value-of select="link_project"/>
		</xsl:variable>
		<xsl:value-of select="lang_project_id"/>:<a href='{$link_project}'>
			<xsl:value-of select="project_id"/>
		</a>
	</div>
	<div>
		<xsl:variable name="link_workorder">
			<xsl:value-of select="link_workorder"/>
		</xsl:variable>
		<xsl:value-of select="lang_workorder_id"/>:<a href='{$link_workorder}'>
			<xsl:value-of select="workorder_id"/>
		</a>
	</div>
	<div>
		<xsl:value-of select="lang_workorder_title"/>:<xsl:value-of select="workorder_title"/>
	</div>
	<div>
		<xsl:value-of select="lang_vendor_name"/>:<xsl:value-of select="vendor_name"/>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_sum">
	<tr>
		<td>
		</td>
		<td class="th_text" style="text-align:left;">
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
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="value_sum_calculation"/>
		</td>
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="sum_deviation"/>
		</td>
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="sum_result"/>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td class="th_text" style="text-align:left;">
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
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="value_addition_rs"/>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td class="th_text" style="text-align:left;">
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
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="value_addition_percentage"/>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td class="th_text" style="text-align:left;">
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
		<td class="th_text" style="text-align:right;">
			<xsl:value-of select="value_sum_tax"/>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td class="th_text" style="text-align:left;">
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
		<td class="th_text" style="text-align:right;">
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
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_num}">
				<xsl:value-of select="lang_num"/>
			</a>
		</td>
		<td class="th_text" width="30%" style="text-align:left;">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="20%" style="text-align:left;">
			<xsl:value-of select="lang_base_descr"/>
		</td>
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_unit"/>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<xsl:value-of select="lang_w_cost"/>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<xsl:value-of select="lang_m_cost"/>
		</td>
		<td class="th_text" width="10%" style="text-align:right;">
			<a href="{$sort_total_cost}">
				<xsl:value-of select="lang_total_cost"/>
			</a>
		</td>
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_quantity"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_category"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
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
		<td style="text-align:left;">
			<xsl:value-of select="num"/>
			<input type="hidden" name="values[activity_id][{counter}]" value="{activity_id}"/>
			<input type="hidden" name="values[activity_num][{counter}]" value="{num}"/>
			<input type="hidden" name="values[unit][{counter}]" value="{unit}"/>
			<input type="hidden" name="values[dim_d][{counter}]" value="{dim_d}"/>
			<input type="hidden" name="values[ns3420_id][{counter}]" value="{ns3420_id}"/>
			<input type="hidden" name="values[descr][{counter}]" value="{descr}"/>
			<input type="hidden" name="values[total_cost][{counter}]" value="{total_cost}"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="descr"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="base_descr"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="unit_name"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="w_cost"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="m_cost"/>
		</td>
		<td style="text-align:right;">
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
			<select name="values[wo_hour_cat][{counter}]" class="forms" title="{lang_wo_hour_cat_statustext}">
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
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_post"/>
		</td>
		<td class="th_text" width="15%" style="text-align:left;">
			<xsl:value-of select="lang_code"/>
		</td>
		<td class="th_text" width="40%" style="text-align:left;">
			<xsl:value-of select="lang_descr"/>
		</td>
		<td class="th_text" width="4%" style="text-align:left;">
			<xsl:value-of select="lang_unit"/>
		</td>
		<td class="th_text" width="2%" style="text-align:right;">
			<xsl:value-of select="lang_quantity"/>
		</td>
		<td class="th_text" width="5%" style="text-align:right;">
			<xsl:value-of select="lang_billperae"/>
		</td>
		<td class="th_text" width="15%" style="text-align:right;">
			<xsl:value-of select="lang_cost"/>
		</td>
		<td class="th_text" width="15%" style="text-align:right;">
			<xsl:value-of select="lang_deviation"/>
		</td>
		<td class="th_text" width="15%" style="text-align:right;">
			<xsl:value-of select="lang_result"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_category"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_per_cent"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
			<xsl:value-of select="lang_edit"/>
		</td>
		<td class="th_text" width="5%" style="text-align:center;">
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
				<td class="th_text" style="text-align:center;" colspan="10" width="100%">
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
		<td style="text-align:right;">
			<xsl:value-of select="post"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="code"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="hours_descr"/>
			<br/>
			<xsl:value-of select="remark"/>
		</td>
		<td style="text-align:left;">
			<xsl:value-of select="unit_name"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="quantity"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="billperae"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="cost"/>
		</td>
		<td style="text-align:right;">
			<xsl:variable name="link_deviation">
				<xsl:value-of select="link_deviation"/>&amp;from=<xsl:value-of select="//function"/>
			</xsl:variable>
			<a href="{$link_deviation}">
				<xsl:value-of select="deviation"/>
			</a>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="result"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="wo_hour_category"/>
		</td>
		<td style="text-align:right;">
			<xsl:value-of select="cat_per_cent"/>
		</td>
		<td style="text-align:center;">
			<xsl:variable name="link_edit">
				<xsl:value-of select="link_edit"/>&amp;from=<xsl:value-of select="//function"/>&amp;template_id=<xsl:value-of select="//template_id"/>
			</xsl:variable>
			<a href="{$link_edit}" title="{$lang_edit_statustext}">
				<xsl:value-of select="text_edit"/>
			</a>
		</td>
		<td style="text-align:center;">
			<xsl:variable name="link_delete">
				<xsl:value-of select="//link_delete"/>&amp;hour_id=<xsl:value-of select="hour_id"/>&amp;template_id=<xsl:value-of select="//template_id"/>
			</xsl:variable>
			<a href="{$link_delete}" title="{$lang_delete_statustext}">
				<xsl:value-of select="text_delete"/>
			</a>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="table_add">
	<table width="80%">
		<tr>
			<td style="text-align:left;" height="50">
				<xsl:variable name="add_prizebook_action">
					<xsl:value-of select="add_prizebook_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add_prizebook">
					<xsl:value-of select="lang_add_prizebook"/>
				</xsl:variable>
				<form method="post" action="{$add_prizebook_action}">
					<button type="submit" name="add_prizebook" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_prizebook_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_add_prizebook"/>
					</button>
				</form>
			</td>
			<td style="text-align:left;" height="50">
				<xsl:variable name="add_template_action">
					<xsl:value-of select="add_template_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add_template">
					<xsl:value-of select="lang_add_template"/>
				</xsl:variable>
				<form method="post" action="{$add_template_action}">
					<button type="submit" name="add_template" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_template_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_add_template"/>
					</button>
				</form>
			</td>
			<td style="text-align:left;" height="50">
				<xsl:variable name="add_custom_action">
					<xsl:value-of select="add_custom_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add_custom">
					<xsl:value-of select="lang_add_custom"/>
				</xsl:variable>
				<form method="post" action="{$add_custom_action}">
					<button type="submit" name="add_custom" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_custom_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_add_custom"/>
					</button>
				</form>
			</td>
			<td style="text-align:left;" height="50">
				<xsl:variable name="save_template_action">
					<xsl:value-of select="save_template_action"/>
				</xsl:variable>
				<xsl:variable name="lang_save_template">
					<xsl:value-of select="lang_save_template"/>
				</xsl:variable>
				<form method="post" action="{$save_template_action}">
					<button type="submit" name="save_template" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_save_template_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_save_template"/>
					</button>
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
				<td style="text-align:left;" height="50">
					<button type="submit" name="print_view" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_print_view_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_print_view"/>
					</button>
				</td>
				<td>
					<xsl:value-of select="lang_show_details"/>
					<input type="checkbox" name="show_details" value="True" checked="checked">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_show_details_statustext"/>
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
				<td style="text-align:left;" height="50">
					<button type="submit" name="view_tender" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_view_tender_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_view_tender"/>
					</button>
				</td>
				<td>
					<xsl:value-of select="lang_show_cost"/>
					<input type="checkbox" name="show_cost" value="True">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_show_cost_statustext"/>
						</xsl:attribute>
					</input>
				</td>
				<td>
					<xsl:value-of select="lang_mark_draft"/>
					<input type="checkbox" name="mark_draft" value="True">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_mark_draft_statustext"/>
						</xsl:attribute>
					</input>
				</td>
			</form>
		</tr>
		<tr>
			<td style="text-align:left;" height="50">
				<form method="post" action="{done_action}">
					<button type="submit" name="save_done" value="1">
						<xsl:value-of select="lang_done"/>
					</button>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_done">
	<table width="100%" style="text-align:left;">
		<tr>
			<td height="50" style="text-align:left;">
				<xsl:variable name="done_action">
					<xsl:value-of select="done_action"/>
				</xsl:variable>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<form method="post" action="{$done_action}">
					<button type="submit" name="done" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_done_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_done"/>
					</button>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="table_send">
	<table width="80%">
		<tr>
			<td style="text-align:left;" height="50">
				<xsl:variable name="lang_send_order">
					<xsl:value-of select="lang_send_order"/>
				</xsl:variable>
				<button type="submit" name="send_order" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_send_order_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="$lang_send_order"/>
				</button>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- add / edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit_hour">
	<script type="text/javascript">
		function ns3420_lookup()
		{
		TINY.box.show({iframe:'<xsl:value-of select="ns3420_link"/>', boxid:"frameless",width:Math.round($(window).width()*0.9),height:Math.round($(window).height()*0.9),fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<div id="wo_hour_edit_tabview">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" id="form" name="form" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="generic">
					<xsl:choose>
						<xsl:when test="value_hour_id !=''">
							<xsl:choose>
								<xsl:when test="value_activity_num =''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="lang_copy_hour"/>
										</label>
										<input type="checkbox" name="values[copy_hour]" value="True" onMouseout="window.status='';return true;">
											<xsl:attribute name="onMouseover">
												<xsl:text>window.status='</xsl:text>
												<xsl:value-of select="lang_copy_hour_statustext"/>
												<xsl:text>'; return true;</xsl:text>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
							</xsl:choose>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_hour_id"/>
								</label>
								<xsl:value-of select="value_hour_id"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_activity_num"/>
								</label>
								<xsl:value-of select="value_activity_num"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_workorder"/>
						</label>
						<xsl:value-of select="value_workorder_title"/>
						<xsl:text> [ </xsl:text>
						<xsl:value-of select="value_workorder_id"/>
						<xsl:text> ]</xsl:text>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_vendor"/>
						</label>
						<xsl:value-of select="value_vendor_name"/>
						<xsl:text> [ </xsl:text>
						<xsl:value-of select="value_vendor_id"/>
						<xsl:text> ]</xsl:text>
					</div>
					<xsl:choose>
						<xsl:when test="value_activity_num=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_chapter"/>
								</label>
								<xsl:call-template name="chapter_select"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_grouping"/>
								</label>
								<xsl:call-template name="grouping_select"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_new_grouping"/>
								</label>
								<input type="text" name="values[new_grouping]" value="{value_new_grouping}" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_new_grouping_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'building part')"/>
						</label>
						<select name="values[building_part_id]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'select building part')"/>
							</xsl:attribute>
							<option value="0">
								<xsl:value-of select="php:function('lang', 'select building part')"/>
							</option>
							<xsl:apply-templates select="building_part_list/options"/>
						</select>
					</div>
					<xsl:choose>
						<xsl:when test="value_activity_num !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_ns3420"/>
								</label>
								<xsl:value-of select="value_ns3420_id"/>
								<input type="hidden" name="ns3420_id" value="{value_ns3420_id}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_descr"/>
								</label>
								<textarea cols="60" rows="4" name="values[descr]" onMouseout="window.status='';return true;">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_descr_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
									<xsl:value-of select="value_descr"/>
								</textarea>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<div class="pure-control-group">
								<label>
									<a href="javascript:ns3420_lookup()" onMouseover="window.status='{lang_ns3420_statustext}';return true;" onMouseout="window.status='';return true;">
										<xsl:value-of select="lang_ns3420"/>
									</a>
								</label>
								<input type="text" name="ns3420_id" value="{value_ns3420_id}" onClick="ns3420_lookup();" readonly="readonly">
									<xsl:attribute name="onMouseover">
										<xsl:text>window.status='</xsl:text>
										<xsl:value-of select="lang_ns3420_statustext"/>
										<xsl:text>'; return true;</xsl:text>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label></label>
								<textarea cols="40" rows="4" name="ns3420_descr" onMouseout="window.status='';return true;">
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
									<xsl:value-of select="lang_tolerance"/>
								</label>
								<xsl:call-template name="tolerance_select"/>
							</div>
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_unit"/>
						</label>
						<xsl:call-template name="unit_select"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_dim_d"/>
						</label>
						<xsl:call-template name="dim_d_select"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_wo_hour_category"/>
						</label>
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
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_quantity"/>
						</label>
						<input type="text" name="values[quantity]" value="{value_quantity}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_quantity_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_billperae"/>
						</label>
						<input type="text" name="values[billperae]" value="{value_billperae}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_billperae_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_total_cost"/>
						</label>
						<xsl:value-of select="value_total_cost"/>
						<xsl:text> </xsl:text> [ <xsl:value-of select="currency"/> ]
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_remark"/>
						</label>
						<textarea cols="60" rows="4" name="values[remark]" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_remark_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>
						</textarea>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="lang_save">
					<xsl:value-of select="lang_save"/>
				</xsl:variable>
				<button type="submit" class="pure-button pure-button-primary" name="values[save]" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="$lang_save"/>
				</button>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<button type="button" class="pure-button pure-button-primary" name="done" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_done_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="lang_done"/>
				</button>
			</div>
		</form>
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$done_action}" method="post"></form>
	</div>
</xsl:template>

<!-- add_deviation / edit_deviation  -->
<xsl:template match="edit_deviation">
	<div style="text-align:left;">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="generic">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_workorder"/>
						</label>
						<xsl:value-of select="value_workorder_id"/>
						<input type="hidden" name="workorder_id" value="{value_workorder_id}"/>
						<input type="hidden" name="hour_id" value="{value_hour_id}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_hour_id"/>
						</label>
						<xsl:value-of select="value_hour_id"/>
					</div>
					<xsl:choose>
						<xsl:when test="value_id != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_id"/>
								</label>
								<xsl:value-of select="value_id"/>
								<input type="hidden" name="values[id]" value="{value_id}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_date"/>
								</label>
								<xsl:value-of select="entry_date"/>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_amount"/>
						</label>
						<input type="text" name="values[amount]" value="{value_amount}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_amount_standardtext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_descr"/>
						</label>
						<textarea cols="60" rows="10" name="values[descr]" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_descr_standardtext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="value_descr"/>
						</textarea>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="lang_save">
					<xsl:value-of select="lang_save"/>
				</xsl:variable>
				<button type="submit" class="pure-button pure-button-primary" name="values[save]" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_standardtext"/>
					</xsl:attribute>
					<xsl:value-of select="lang_save"/>
				</button>
				<xsl:variable name="lang_done">
					<xsl:value-of select="lang_done"/>
				</xsl:variable>
				<button type="button" class="pure-button pure-button-primary" name="done" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_done_statustext"/>
					</xsl:attribute>
					<xsl:value-of select="lang_done"/>
				</button>
			</div>
		</form>
				
		<xsl:variable name="done_action">
			<xsl:value-of select="done_action"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$done_action}" method="post"></form>
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
	<tr>
		<td>
			<img src="{encoded_text}"/>
		</td>
	</tr>

</xsl:template>

<!-- New template-->
<!--xsl:template match="sms_data">
	<div class="pure-control-group">
		<label>
			<xsl:text> </xsl:text>
		</label>
		<div class="pure-custom">
			<div>
				<xsl:value-of select="heading"/>
			</div>
			<div>
				<xsl:value-of select="message"/>
			</div>
			<div>
				<xsl:value-of select="status_code_text"/>
				<xsl:text>: </xsl:text>
				<xsl:for-each select="status_code">
					<xsl:value-of select="name"/>
					<xsl:if test="position() != last()">, </xsl:if>
				</xsl:for-each>
			</div>
			<div>
				<xsl:value-of select="lang_example"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="example"/>
			</div>
			<div>
				<img src="{encoded_text}"/>
			</div>
		</div>
	</div>
</xsl:template-->

<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- Import calculation -->
	
<xsl:template match="import_calculation" xmlns:php="http://php.net/xsl">
	<h2>
		<xsl:value-of select="php:function('lang', 'import calculation')"/>
	</h2>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<form action="{form_action}" name="import_calculation_form" id="split_voucher_form" method="post" ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="upload_file">
				<input type="hidden" name="workorder_id" id="workorder_id" value="{workorder_id}"/>
				<div class="pure-control-group">
					<input type="file" id="file" name="file" size="40">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="lang_submit">
						<xsl:value-of select="php:function('lang', 'upload file')"/>
					</xsl:variable>
					<button type="submit" name="submit" value="1">
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_submit"/>
						</xsl:attribute>
						<xsl:value-of select="$lang_submit"/>
					</button>
				</div>
			</div>
		</div>
		<div class="proplist-col">
			<xsl:variable name="done_action">
				<xsl:value-of select="done_action"/>
			</xsl:variable>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<a class="pure-button pure-button-primary" href="{$done_action}">
				<xsl:value-of select="lang_done"/>
			</a>
		</div>
	</form>

</xsl:template>

<!-- $Id$ -->
<xsl:template name="location_view_local">
	<xsl:apply-templates select="address_element"/>
</xsl:template>


<xsl:template match="address_element">
	<tr>
		<td>
			<xsl:value-of select="text"/>
		</td>
		<td>
			<xsl:value-of disable-output-escaping="yes" select="value"/>
		</td>
	</tr>
</xsl:template>

