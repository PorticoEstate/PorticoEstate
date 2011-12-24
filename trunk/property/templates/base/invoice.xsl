<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="add">
				<xsl:apply-templates select="add"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="import">
				<xsl:apply-templates select="import"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="export">
				<xsl:apply-templates select="export"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="rollback">
				<xsl:apply-templates select="rollback"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="debug">
				<xsl:apply-templates select="debug"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="remark">
				<xsl:apply-templates select="remark"></xsl:apply-templates>
			</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="remark">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td colspan="2" align="center">
					<xsl:value-of select="message"></xsl:value-of>
				</td>
			</tr>
			<tr>
				<td align="left">
					<xsl:choose>
						<xsl:when test="html = ''">
							<textarea cols="60" rows="15" name="remark" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_content_statustext"></xsl:value-of>
								</xsl:attribute>
								<xsl:value-of select="remark"></xsl:value-of>
							</textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of disable-output-escaping="yes" select="remark"></xsl:value-of>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>

		</table>
	</xsl:template>

	<xsl:template name="download">
		<xsl:variable name="link_download"><xsl:value-of select="link_download"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_download_help"><xsl:value-of select="lang_download_help"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_download"><xsl:value-of select="lang_download"></xsl:value-of></xsl:variable>
		<a href="javascript:var w=window.open('{$link_download}','','left=50,top=100')" onMouseOver="overlib('{$lang_download_help}', CAPTION, '{$lang_download}')" onMouseOut="nd()">
			<xsl:value-of select="lang_download"></xsl:value-of></a>
	</xsl:template>



	<xsl:template match="table_add_invoice">
		<table align="left">
			<tr>
				<td height="50" align="left" valign="top">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_add_statustext"></xsl:value-of>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>


	<xsl:template match="account_class_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- debug-->

	<xsl:template match="debug">
		<div align="left">
			<table width="50%" cellpadding="2" cellspacing="2" align="center">
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_type"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="artid"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_vendor"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="spvend_code"></xsl:value-of>
						<xsl:text> </xsl:text>
						<xsl:value-of select="vendor_name"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_fakturadato"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="fakturadato"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_forfallsdato"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="forfallsdato"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_janitor"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="oppsynsmannid"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_supervisor"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="saksbehandlerid"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_budget_responsible"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="budsjettansvarligid"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_project_id"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="project_id"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td class="th_text">
						<xsl:value-of select="lang_sum"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="sum"></xsl:value-of>
					</td>
				</tr>
			</table>
		</div>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr class="th">
				<xsl:call-template name="table_header"></xsl:call-template>
			</tr>
			<xsl:call-template name="values_debug"></xsl:call-template>
			<xsl:apply-templates select="table_add"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template name="values_debug">
		<xsl:for-each select="values">
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"></xsl:value-of>
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
					<td align="{align}">
						<xsl:value-of select="value"></xsl:value-of>
					</td>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<td height="50">
				<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
				<form method="post" action="{$cancel_action}">
					<input type="submit" name="cancel" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

<!-- add / edit -->
	<xsl:template match="add">

		<script type="text/javascript">
			self.name="first_Window";
			function abook()
			{
			Window1=window.open('<xsl:value-of select="addressbook_link"></xsl:value-of>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}			</script>

		<xsl:apply-templates select="menu"></xsl:apply-templates>
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td colspan="2" align="center">
						<xsl:value-of select="message"></xsl:value-of>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="link_receipt != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:variable name="link_receipt"><xsl:value-of select="link_receipt"></xsl:value-of></xsl:variable>
								<a href="{$link_receipt}" onMouseover="window.status='{lang_receipt}';return true;" onMouseout="window.status='';return true;" target="_blank"><xsl:value-of select="lang_receipt"></xsl:value-of>
								</a>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">

					<tr>
						<td>
							<xsl:value-of select="lang_auto_tax"></xsl:value-of>
						</td>
						<td>
							<input type="checkbox" name="auto_tax" value="True" checked="checked" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_auto_tax_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<xsl:call-template name="location_form"></xsl:call-template>
					<xsl:call-template name="b_account_form"></xsl:call-template>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_dimb"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_dimb_statustext"><xsl:value-of select="lang_dimb_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_dimb"><xsl:value-of select="select_dimb"></xsl:value-of></xsl:variable>
							<select name="{$select_dimb}" class="forms" onMouseover="window.status='{$lang_dimb_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_dimb"></xsl:value-of></option>
								<xsl:apply-templates select="dimb_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:variable name="lang_vendor"><xsl:value-of select="lang_vendor"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_vendor}" onClick="abook();" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_vendor_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="text" name="vendor_id" value="{value_vendor_id}" size="4" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_vendor_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<input type="text" name="vendor_name" value="{value_vendor_name}" size="20" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_vendor_name_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_janitor"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_janitor_statustext"><xsl:value-of select="lang_janitor_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_janitor"><xsl:value-of select="select_janitor"></xsl:value-of></xsl:variable>
							<select name="{$select_janitor}" class="forms" onMouseover="window.status='{$lang_janitor_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_janitor"></xsl:value-of></option>
								<xsl:apply-templates select="janitor_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_supervisor"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_supervisor_statustext"><xsl:value-of select="lang_supervisor_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_supervisor"><xsl:value-of select="select_supervisor"></xsl:value-of></xsl:variable>
							<select name="{$select_supervisor}" class="forms" onMouseover="window.status='{$lang_supervisor_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_supervisor"></xsl:value-of></option>
								<xsl:apply-templates select="supervisor_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_budget_responsible"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_budget_responsible_statustext"><xsl:value-of select="lang_budget_responsible_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_budget_responsible"><xsl:value-of select="select_budget_responsible"></xsl:value-of></xsl:variable>
							<select name="{$select_budget_responsible}" class="forms" onMouseover="window.status='{$lang_budget_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_budget_responsible"></xsl:value-of></option>
								<xsl:apply-templates select="budget_responsible_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_order"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="order_id" value="{value_order_id}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_order_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_art"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_art_statustext"><xsl:value-of select="lang_art_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_art"><xsl:value-of select="select_art"></xsl:value-of></xsl:variable>
							<select name="{$select_art}" class="forms" onMouseover="window.status='{$lang_art_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_art"></xsl:value-of></option>
								<xsl:apply-templates select="art_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_type"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_type_statustext"><xsl:value-of select="lang_type_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_type"><xsl:value-of select="select_type"></xsl:value-of></xsl:variable>
							<select name="{$select_type}" class="forms" onMouseover="window.status='{$lang_type_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_type"></xsl:value-of></option>
								<xsl:apply-templates select="type_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_invoice_number"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="invoice_num" value="{value_invoice_num}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_invoice_num_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_kidnr"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="kid_nr" value="{value_kid_nr}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_kid_nr_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_amount"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="amount" value="{value_amount}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_amount_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_invoice_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_invoice_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="invoice_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_no_of_days"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="num_days" value="{value_num_days}" size="4" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_num_days_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>

					<tr>

						<td valign="top">
							<xsl:value-of select="lang_payment_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_payment_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="payment_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_merknad"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="10" name="merknad" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_merknad_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_merknad"></xsl:value-of>
							</textarea>
						</td>
					</tr>

					<tr height="50">
						<td>
							<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
							<input type="submit" name="add_invoice" value="{$lang_add}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
						<form method="post" action="{$cancel_action}">
							<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
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

		<script type="text/javascript">
			self.name="first_Window";
			function abook()
			{
			Window1=window.open('<xsl:value-of select="addressbook_link"></xsl:value-of>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}			</script>

		<xsl:apply-templates select="menu"></xsl:apply-templates>
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td colspan="2" align="center">
						<xsl:value-of select="message"></xsl:value-of>
					</td>
				</tr>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">

					<tr>
						<td>
							<xsl:value-of select="lang_auto_tax"></xsl:value-of>
						</td>
						<td>
							<input type="checkbox" name="auto_tax" value="True" checked="checked" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_auto_tax_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_art"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_art_statustext"><xsl:value-of select="lang_art_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_art"><xsl:value-of select="select_art"></xsl:value-of></xsl:variable>
							<select name="{$select_art}" class="forms" onMouseover="window.status='{$lang_art_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_art"></xsl:value-of></option>
								<xsl:apply-templates select="art_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_type"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_type_statustext"><xsl:value-of select="lang_type_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_type"><xsl:value-of select="select_type"></xsl:value-of></xsl:variable>
							<select name="{$select_type}" class="forms" onMouseover="window.status='{$lang_type_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_type"></xsl:value-of></option>
								<xsl:apply-templates select="type_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_dimb"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_dimb_statustext"><xsl:value-of select="lang_dimb_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_dimb"><xsl:value-of select="select_dimb"></xsl:value-of></xsl:variable>
							<select name="{$select_dimb}" class="forms" onMouseover="window.status='{$lang_dimb_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_dimb"></xsl:value-of></option>
								<xsl:apply-templates select="dimb_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_invoice_number"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="invoice_num" value="{value_invoice_num}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_invoice_num_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_kidnr"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="kid_nr" value="{value_kid_nr}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_kid_nr_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:variable name="lang_vendor"><xsl:value-of select="lang_vendor"></xsl:value-of></xsl:variable>
							<input type="button" name="convert" value="{$lang_vendor}" onClick="abook();" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_select_vendor_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
						<td>
							<input type="text" name="vendor_id" value="{value_vendor_id}" size="6" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_vendor_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<input type="text" name="vendor_name" value="{value_vendor_name}" size="20" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_vendor_name_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_janitor"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_janitor_statustext"><xsl:value-of select="lang_janitor_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_janitor"><xsl:value-of select="select_janitor"></xsl:value-of></xsl:variable>
							<select name="{$select_janitor}" class="forms" onMouseover="window.status='{$lang_janitor_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_janitor"></xsl:value-of></option>
								<xsl:apply-templates select="janitor_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_supervisor"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_supervisor_statustext"><xsl:value-of select="lang_supervisor_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_supervisor"><xsl:value-of select="select_supervisor"></xsl:value-of></xsl:variable>
							<select name="{$select_supervisor}" class="forms" onMouseover="window.status='{$lang_supervisor_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_supervisor"></xsl:value-of></option>
								<xsl:apply-templates select="supervisor_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_budget_responsible"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_budget_responsible_statustext"><xsl:value-of select="lang_budget_responsible_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_budget_responsible"><xsl:value-of select="select_budget_responsible"></xsl:value-of></xsl:variable>
							<select name="{$select_budget_responsible}" class="forms" onMouseover="window.status='{$lang_budget_responsible_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_budget_responsible"></xsl:value-of></option>
								<xsl:apply-templates select="budget_responsible_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_invoice_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_invoice_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="invoice_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_no_of_days"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="num_days" value="{value_num_days}" size="4" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_num_days_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>

					<tr>

						<td valign="top">
							<xsl:value-of select="lang_payment_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_payment_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="payment_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>

					<tr>
						<td valign="top">
							<xsl:value-of select="lang_file"></xsl:value-of>
						</td>
						<td>
							<input type="file" name="tsvfile" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_file_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_conv"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_conv"><xsl:value-of select="select_conv"></xsl:value-of></xsl:variable>
							<select name="{$select_conv}" class="forms" onMouseover="window.status='{$lang_conv_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_conversion"></xsl:value-of></option>
								<xsl:apply-templates select="conv_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>

					<tr>
						<td>
							<xsl:value-of select="lang_debug"></xsl:value-of>
						</td>
						<td>
							<input type="checkbox" name="download" value="True" checked="checked" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_debug_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_import"><xsl:value-of select="lang_import"></xsl:value-of></xsl:variable>
							<input type="submit" name="convert" value="{$lang_import}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_import_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
						<form method="post" action="{$cancel_action}">
							<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
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
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- type_list -->
	<xsl:template match="type_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- dimb_list -->
	<xsl:template match="dimb_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- janitor_list -->
	<xsl:template match="janitor_list">
		<xsl:variable name="lid"><xsl:value-of select="lid"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of> <xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of><xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- supervisor_list -->
	<xsl:template match="supervisor_list">
		<xsl:variable name="lid"><xsl:value-of select="lid"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of> <xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of> <xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- budget_responsible_list -->
	<xsl:template match="budget_responsible_list">
		<xsl:variable name="lid"><xsl:value-of select="lid"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$lid}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of> <xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$lid}"><xsl:value-of disable-output-escaping="yes" select="firstname"></xsl:value-of> <xsl:text> </xsl:text><xsl:value-of select="lastname"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


<!-- conv_list -->
	<xsl:template match="conv_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- rollback_file_list -->
	<xsl:template match="rollback_file_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- tax_code_list -->
	<xsl:template match="tax_code_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="id"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

<!-- export -->

	<xsl:template match="export">
		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">


					<tr>
						<td valign="top">
							<xsl:value-of select="lang_select_conv"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_conv"><xsl:value-of select="select_conv"></xsl:value-of></xsl:variable>
							<select name="{$select_conv}" class="forms" title="{$lang_conv_statustext}">
								<option value=""><xsl:value-of select="lang_select_conv"></xsl:value-of></option>
								<xsl:apply-templates select="conv_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_export_to_file"></xsl:value-of>
						</td>
						<td>
							<input type="checkbox" name="values[download]" value="on" checked="checked">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_debug_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
					</tr>

					<tr>
						<td>
						</td>
						<td>
							<xsl:variable name="link_rollback_file"><xsl:value-of select="link_rollback_file"></xsl:value-of></xsl:variable>
							<a href="{$link_rollback_file}"><xsl:value-of select="lang_rollback_file"></xsl:value-of></a>
						</td>
					</tr>

					<tr height="50">
						<td>
							<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[submit]" value="{$lang_submit}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_export_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
						<form method="post" action="{$cancel_action}">
							<input type="submit" name="done" value="{$lang_cancel}">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>


<!-- rollback -->

	<xsl:template xmlns:php="http://php.net/xsl" match="rollback">
		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" name="form" action="{$form_action}">


					<tr>
						<td valign="top">
							<xsl:value-of select="lang_select_conv"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_conv_statustext"><xsl:value-of select="lang_conv_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_conv"><xsl:value-of select="select_conv"></xsl:value-of></xsl:variable>
							<select name="{$select_conv}" class="forms" onMouseover="window.status='{$lang_conv_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_select_conv"></xsl:value-of></option>
								<xsl:apply-templates select="conv_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_select_file"></xsl:value-of>
						</td>
						<td valign="top">
							<xsl:variable name="lang_file_statustext"><xsl:value-of select="lang_file_statustext"></xsl:value-of></xsl:variable>
							<xsl:variable name="select_file"><xsl:value-of select="select_file"></xsl:value-of></xsl:variable>
							<select name="{$select_file}" class="forms" title="{$lang_file_statustext}">
								<option value=""><xsl:value-of select="lang_no_file"></xsl:value-of></option>
								<xsl:apply-templates select="rollback_file_list"></xsl:apply-templates>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="php:function('lang', 'voucher')"></xsl:value-of>
						</td>
						<td valign="top">
							<input type="text" id="voucher_id" name="values[voucher_id]" value="">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'voucher')"></xsl:value-of>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>

						<td valign="top">
							<xsl:value-of select="lang_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="date" name="date" size="10" value="{value_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[submit]" value="{$lang_submit}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_import_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="cancel_action"><xsl:value-of select="cancel_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"></xsl:value-of></xsl:variable>
						<form method="post" action="{$cancel_action}">
							<input type="submit" name="done" value="{$lang_cancel}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_cancel_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>

<!-- edit single voucher line  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="edit">
		<xsl:choose>
			<xsl:when test="normalize-space(redirect) != ''">
				<script>
					window.parent.location = '<xsl:value-of select="redirect"></xsl:value-of>';
					window.close();
				</script>
			</xsl:when>
		</xsl:choose>
		<form name="form" method="post" action="{form_action}">
			<table cellpadding="0" cellspacing="0" width="100%">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="2">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<!--<xsl:variable name="lang_process_code"><xsl:value-of select="php:function('lang', 'voucher process code')" /></xsl:variable>-->
				<xsl:apply-templates select="approved_list"></xsl:apply-templates>
				<tr>
					<input type="hidden" name="values[sign_orig]" value="{sign_orig}"></input>
					<input type="hidden" name="values[my_initials]" value="{my_initials}"></input>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'approve')"></xsl:value-of>
					</td>
                    <td class="th_text" valign="top" align="left">
						<select name="values[approve]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'grant')"></xsl:value-of>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"></xsl:value-of>
							</option>
							<xsl:apply-templates select="approve_list"></xsl:apply-templates>
						</select>
					</td>
				</tr>

				<tr>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'voucher process code')"></xsl:value-of>
					</td>
                    <td align="left" class="th_text" valign="top">
						<select name="values[process_code]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'voucher process code')"></xsl:value-of>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'voucher process code')"></xsl:value-of>
							</option>
							<xsl:apply-templates select="process_code_list"></xsl:apply-templates>
						</select>							</td>
				</tr>
				<xsl:call-template name="project_group_form"></xsl:call-template>
				<tr>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'order id')"></xsl:value-of>
					</td>
					<td align="left" class="th_text" valign="top">
						<input type="text" name="values[order_id]" value="{order_id}">
							<xsl:attribute name="size">
								<xsl:text>20</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'order id')"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'voucher process log')"></xsl:value-of>
					</td>
					<td align="left">
						<textarea cols="60" rows="10" name="values[process_log]" wrap="virtual">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'voucher process log')"></xsl:value-of>
							</xsl:attribute>
							<xsl:value-of select="value_process_log"></xsl:value-of>
							</textarea>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'split line')"></xsl:value-of>
					</td>
					<td align="left" valign="top">
						<input type="checkbox" name="values[split_line]" value="1">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'split line')"></xsl:value-of>
							</xsl:attribute>
						</input>
						<xsl:text> [ </xsl:text>
						<xsl:value-of select="value_amount"></xsl:value-of>
						<xsl:text> </xsl:text>
						<xsl:value-of select="value_currency"></xsl:value-of>
						<xsl:text> ]</xsl:text>
					</td>
				</tr>
				<tr>
					<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
						<xsl:value-of select="php:function('lang', 'amount')"></xsl:value-of>
					</td>
					<td align="left" class="th_text" valign="top">
						<input type="text" name="values[split_amount]">
							<xsl:attribute name="size">
								<xsl:text>20</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'amount')"></xsl:value-of>
							</xsl:attribute>
						</input>
					</td>
				</tr>

				<tr height="50">
					<td>
						<xsl:variable name="lang_send"><xsl:value-of select="php:function('lang', 'save')"></xsl:value-of></xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_send}" title="{$lang_send}">
						</input>
					</td>
				</tr>

			</table>
		</form>
	</xsl:template>

	<xsl:template match="process_code_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select="name"></xsl:value-of>
		</option>
	</xsl:template>

	<xsl:template match="approve_list">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select="name"></xsl:value-of>
		</option>
	</xsl:template>
	<xsl:template match="approved_list">
		<tr>
			<td align="left" style="white-space: nowrap;">
				<xsl:value-of select="role"></xsl:value-of>					</td>
			<td align="left" style="white-space: nowrap;">
				<xsl:if test="initials != ''">
					<xsl:value-of select="initials"></xsl:value-of>
					<xsl:text>: </xsl:text>
					<xsl:value-of select="date"></xsl:value-of>
				</xsl:if>
			</td>
		</tr>
	</xsl:template>
