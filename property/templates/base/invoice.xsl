<!-- $Id$ -->
<xsl:template match="data">
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
		<xsl:when test="remark">
			<xsl:apply-templates select="remark"/>
		</xsl:when>
		<xsl:when test="forward">
			<xsl:apply-templates select="forward"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="split_voucher" xmlns:php="http://php.net/xsl">
	<style scoped="">
		.button-success,
		.button-error,
		.button-warning,
		.button-secondary {
		color: white;
		border-radius: 4px;
		text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
		}

		.button-success {
		background: rgb(28, 184, 65);
		/* this is a green */
		}

		.button-error {
		background: rgb(202, 60, 60);
		/* this is a maroon */
		}

		.button-warning {
		background: rgb(223, 117, 20);
		/* this is an orange */
		}

		.button-secondary {
		background: rgb(66, 184, 221);
		/* this is a light blue */
		}
	</style>

	<h2>
		<xsl:value-of select="php:function('lang', 'upload file')"/>
	</h2>

	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>


	<form action="{form_action}" name="split_voucher_form" id="split_voucher_form" method="post" ENCTYPE="multipart/form-data" class="pure-form pure-form-aligned">
		<input type="hidden" name="voucher_id" id="voucher_id" value="{voucher_id}"/>

		<fieldset>

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'template')"/>
				</label>
				<xsl:variable name="lang_template">
					<xsl:value-of select="php:function('lang', 'template')" />
				</xsl:variable>
				<input type="button" id = "get_template" name="get_template" value="{$lang_template}" title = "{$lang_template}" class="pure-button button-warning"/>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'select file to upload')"/>
				</label>
				<input type="file" id="file" name="file" size="40">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
					</xsl:attribute>
				</input>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'upload file')"/>
				</label>
				<xsl:variable name="lang_submit">
					<xsl:value-of select="php:function('lang', 'upload file')"/>
				</xsl:variable>
				<input type="submit" name="submit" value="{$lang_submit}" class="pure-button pure-button-primary">
					<xsl:attribute name="title">
						<xsl:value-of select="$lang_submit"/>
					</xsl:attribute>
				</input>
			</div>

		</fieldset>


	</form>

</xsl:template>

<!-- New template-->
<xsl:template match="remark">
	<dl>
		<dt>
			<xsl:value-of select="message"/>
		</dt>
	</dl>
	<div class="pure-control-group">
		<xsl:choose>
			<xsl:when test="html = ''">
				<!--
						<textarea cols="60" rows="15" name="remark" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_content_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="remark"/>
				</textarea> -->
				<xsl:variable name="title">
					<xsl:value-of select="lang_content_statustext"/>
				</xsl:variable>
				<div title="{$title}">
					<xsl:value-of select="remark"/>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div>
					<xsl:value-of disable-output-escaping="yes" select="remark"/>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template name="download">
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
</xsl:template>

<!-- New template-->
<xsl:template match="table_add_invoice">
	<table align="left">
		<tr>
			<td height="50" align="left" valign="top">
				<xsl:variable name="add_action">
					<xsl:value-of select="add_action"/>
				</xsl:variable>
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="" value="{$lang_add}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_add_statustext"/>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</table>
</xsl:template>

<!-- New template-->
<xsl:template match="account_class_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$id}">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<!-- debug-->
<xsl:template match="debug">
	<div class="content">
		<form name="form" id="form" method="" action="" class= "pure-form pure-form-aligned">
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="confirm">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_type"/>
						</label>
						<xsl:value-of select="artid"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_vendor"/>
						</label>
						<xsl:value-of select="spvend_code"/>
						<xsl:text> </xsl:text>
						<xsl:value-of select="vendor_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_fakturadato"/>
						</label>
						<xsl:value-of select="fakturadato"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_forfallsdato"/>
						</label>
						<xsl:value-of select="forfallsdato"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_janitor"/>
						</label>
						<xsl:value-of select="oppsynsmannid"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_supervisor"/>
						</label>
						<xsl:value-of select="saksbehandlerid"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_budget_responsible"/>
						</label>
						<xsl:value-of select="budsjettansvarligid"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_project_id"/>
						</label>
						<xsl:value-of select="project_id"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_sum"/>
						</label>
						<xsl:value-of select="sum"/>
					</div>
					<table class="display cell-border compact responsive no-wrap dataTable dtr-inline">
						<thead>
							<tr role="row">
								<xsl:for-each select="table_header">
									<th>
										<xsl:value-of select="header"/>
									</th>
								</xsl:for-each>
							</tr>
						</thead>
						<tbody>
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
										<td align="{align}">
											<xsl:value-of select="value"/>
										</td>
									</xsl:for-each>
								</tr>
							</xsl:for-each>
						</tbody>
						<tfoot>
							<tr>
								<xsl:for-each select="table_header">
									<th></th>
								</xsl:for-each>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</form>
		<xsl:apply-templates select="table_add"/>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="table_add">
	<div class="proplist-col">
		<xsl:variable name="lang_add">
			<xsl:value-of select="lang_add"/>
		</xsl:variable>
		<input type="button" class="pure-button pure-button-primary" name="add" value="{$lang_add}" onClick="document.add_form.submit();">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_add_statustext"/>
			</xsl:attribute>
		</input>
		<xsl:variable name="lang_cancel">
			<xsl:value-of select="lang_cancel"/>
		</xsl:variable>
		<input type="button" class="pure-button pure-button-primary" name="cancel" value="{$lang_cancel}" onClick="document.cancel_form.submit();">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_cancel_statustext"/>
			</xsl:attribute>
		</input>
	</div>
	<xsl:variable name="add_action">
		<xsl:value-of select="add_action"/>
	</xsl:variable>
	<form method="post" name="add_form" id="add_form" action="{$add_action}"></form>

	<xsl:variable name="cancel_action">
		<xsl:value-of select="cancel_action"/>
	</xsl:variable>
	<form method="post" name="cancel_form" id="cancel_form" action="{$cancel_action}"></form>
</xsl:template>

<!-- add / edit -->
<xsl:template match="add">
	<script type="text/javascript">
		self.name="first_Window";
		function abook()
		{
		TINY.box.show({iframe:'<xsl:value-of select="addressbook_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<xsl:apply-templates select="menu"/>
	<div>
		<xsl:value-of select="message"/>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="link_receipt != ''">
				<dl>
					<dt>
						<xsl:variable name="link_receipt">
							<xsl:value-of select="link_receipt"/>
						</xsl:variable>
						<a href="{$link_receipt}" title="{lang_receipt}" target="_blank">
							<xsl:value-of select="lang_receipt"/>
						</a>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" id="form" name="form" action="{$form_action}" class= "pure-form pure-form-aligned">
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="generic">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_auto_tax"/>
						</label>
						<input type="checkbox" name="auto_tax" value="True" checked="checked">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_auto_tax_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<xsl:call-template name="location_form"/>
					<xsl:call-template name="b_account_form"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_dimb"/>
						</label>
						<xsl:variable name="lang_dimb_statustext">
							<xsl:value-of select="lang_dimb_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_dimb">
							<xsl:value-of select="select_dimb"/>
						</xsl:variable>
						<select name="{$select_dimb}" class="forms" title="{$lang_dimb_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_dimb"/>
							</option>
							<xsl:apply-templates select="dimb_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<a href="javascript:abook()">
								<xsl:value-of select="lang_vendor"/>
							</a>
						</label>
						<input type="text" name="vendor_id" value="{value_vendor_id}" size="5">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_vendor_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - select Vendor!')"/>
							</xsl:attribute>
						</input>
						<input type="text" name="vendor_name" value="{value_vendor_name}" size="40">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_vendor_name_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_janitor"/>
						</label>
						<xsl:variable name="lang_janitor_statustext">
							<xsl:value-of select="lang_janitor_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_janitor">
							<xsl:value-of select="select_janitor"/>
						</xsl:variable>
						<select name="{$select_janitor}" class="forms" title="{$lang_janitor_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_janitor"/>
							</option>
							<xsl:apply-templates select="janitor_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_supervisor"/>
						</label>
						<xsl:variable name="lang_supervisor_statustext">
							<xsl:value-of select="lang_supervisor_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_supervisor">
							<xsl:value-of select="select_supervisor"/>
						</xsl:variable>
						<select name="{$select_supervisor}" class="forms" title="{$lang_supervisor_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_supervisor"/>
							</option>
							<xsl:apply-templates select="supervisor_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_budget_responsible"/>
						</label>
						<xsl:variable name="lang_budget_responsible_statustext">
							<xsl:value-of select="lang_budget_responsible_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_budget_responsible">
							<xsl:value-of select="select_budget_responsible"/>
						</xsl:variable>
						<select name="{$select_budget_responsible}" class="forms" title="{$lang_budget_responsible_statustext}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - select budget responsible!')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="lang_select_budget_responsible"/>
							</option>
							<xsl:apply-templates select="budget_responsible_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_order"/>
						</label>
						<input type="text" data-validation="number" name="order_id" value="{value_order_id}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_order_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - enter an integer for order!')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_art"/>
						</label>
						<xsl:variable name="lang_art_statustext">
							<xsl:value-of select="lang_art_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_art">
							<xsl:value-of select="select_art"/>
						</xsl:variable>
						<select name="{$select_art}" class="forms" title="{$lang_art_statustext}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - select type invoice!')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="lang_select_art"/>
							</option>
							<xsl:apply-templates select="art_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_type"/>
						</label>
						<xsl:variable name="lang_type_statustext">
							<xsl:value-of select="lang_type_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_type">
							<xsl:value-of select="select_type"/>
						</xsl:variable>
						<select name="{$select_type}" class="forms" title="{$lang_type_statustext}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - select type order!')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="lang_no_type"/>
							</option>
							<xsl:apply-templates select="type_list"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_invoice_number"/>
						</label>
						<input type="text" name="invoice_num" value="{value_invoice_num}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_invoice_num_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - enter a invoice num!')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_kidnr"/>
						</label>
						<input type="text" name="kid_nr" value="{value_kid_nr}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_kid_nr_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_amount"/>
						</label>
						<input type="text" name="amount" value="{value_amount}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_amount_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - enter an amount!')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_invoice_date"/>
						</label>
						<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_invoice_date_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_no_of_days"/>
						</label>
						<input type="text" name="num_days" id="num_days" value="{value_num_days}" size="4">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_num_days_statustext"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_payment_date"/>
						</label>
						<input type="text" data-validation="days_payment_date" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_payment_date_statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please - select either payment date or number of days from invoice date !')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_merknad"/>
						</label>
						<textarea cols="60" rows="10" name="merknad">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_merknad_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="value_merknad"/>
						</textarea>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="lang_add">
					<xsl:value-of select="lang_add"/>
				</xsl:variable>
				<input type="submit" class="pure-button pure-button-primary" name="add_invoice" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="lang_cancel"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel_invoice" value="{$lang_cancel}" onClick="document.cancel_form.submit();">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_cancel_statustext"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
		<xsl:variable name="cancel_action">
			<xsl:value-of select="cancel_action"/>
		</xsl:variable>
		<form method="post" name="cancel_form" id="cancel_form" action="{$cancel_action}"></form>
	</div>
	<script type="text/javascript">
		$.formUtils.addValidator({
		name : 'days_payment_date',
		validatorFunction : function(value, $el, config, language, $form) {
		var payment_date = (value == '') ? 0 : 1;
		var nun_days = ($('#num_days').val() == parseInt($('#num_days').val(), 10)) ? 1 : 0;
		var result = (nun_days + payment_date == 0) ? false : true;
		return result;
		},
		errorMessage : '',
		errorMessageKey: ''
		});
	</script>

</xsl:template>

<!-- import -->
<xsl:template match="import">
	<script type="text/javascript">
		self.name="first_Window";
		function abook()
		{
		Window1=window.open('<xsl:value-of select="addressbook_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
		}
	</script>
	<xsl:apply-templates select="menu"/>
	<div align="left">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">
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
				<tr>
					<td>
						<xsl:value-of select="lang_auto_tax"/>
					</td>
					<td>
						<input type="checkbox" name="auto_tax" value="True" checked="checked">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_auto_tax_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_art"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_art_statustext">
							<xsl:value-of select="lang_art_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_art">
							<xsl:value-of select="select_art"/>
						</xsl:variable>
						<select name="{$select_art}" class="forms" title="{$lang_art_statustext}">
							<option value="">
								<xsl:value-of select="lang_select_art"/>
							</option>
							<xsl:apply-templates select="art_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_type"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_type_statustext">
							<xsl:value-of select="lang_type_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_type">
							<xsl:value-of select="select_type"/>
						</xsl:variable>
						<select name="{$select_type}" class="forms" title="{$lang_type_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_type"/>
							</option>
							<xsl:apply-templates select="type_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_dimb"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_dimb_statustext">
							<xsl:value-of select="lang_dimb_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_dimb">
							<xsl:value-of select="select_dimb"/>
						</xsl:variable>
						<select name="{$select_dimb}" class="forms" title="{$lang_dimb_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_dimb"/>
							</option>
							<xsl:apply-templates select="dimb_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_invoice_number"/>
					</td>
					<td>
						<input type="text" name="invoice_num" value="{value_invoice_num}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_invoice_num_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_kidnr"/>
					</td>
					<td>
						<input type="text" name="kid_nr" value="{value_kid_nr}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_kid_nr_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:variable name="lang_vendor">
							<xsl:value-of select="lang_vendor"/>
						</xsl:variable>
						<input type="button" name="convert" value="{$lang_vendor}" onClick="abook();">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_select_vendor_statustext"/>
							</xsl:attribute>
						</input>
					</td>
					<td>
						<input type="text" name="vendor_id" value="{value_vendor_id}" size="6">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_vendor_statustext"/>
							</xsl:attribute>
						</input>
						<input type="text" name="vendor_name" value="{value_vendor_name}" size="20">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_vendor_name_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_janitor"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_janitor_statustext">
							<xsl:value-of select="lang_janitor_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_janitor">
							<xsl:value-of select="select_janitor"/>
						</xsl:variable>
						<select name="{$select_janitor}" class="forms" title="{$lang_janitor_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_janitor"/>
							</option>
							<xsl:apply-templates select="janitor_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_supervisor"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_supervisor_statustext">
							<xsl:value-of select="lang_supervisor_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_supervisor">
							<xsl:value-of select="select_supervisor"/>
						</xsl:variable>
						<select name="{$select_supervisor}" class="forms" title="{$lang_supervisor_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_supervisor"/>
							</option>
							<xsl:apply-templates select="supervisor_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_budget_responsible"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_budget_responsible_statustext">
							<xsl:value-of select="lang_budget_responsible_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_budget_responsible">
							<xsl:value-of select="select_budget_responsible"/>
						</xsl:variable>
						<select name="{$select_budget_responsible}" class="forms" title="{$lang_budget_responsible_statustext}">
							<option value="">
								<xsl:value-of select="lang_select_budget_responsible"/>
							</option>
							<xsl:apply-templates select="budget_responsible_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_invoice_date"/>
					</td>
					<td>
						<input type="text" id="invoice_date" name="invoice_date" size="10" value="{value_invoice_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_invoice_date_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_no_of_days"/>
					</td>
					<td>
						<input type="text" name="num_days" value="{value_num_days}" size="4">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_num_days_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_payment_date"/>
					</td>
					<td>
						<input type="text" id="payment_date" name="payment_date" size="10" value="{value_payment_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_payment_date_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_file"/>
					</td>
					<td>
						<input type="file" name="tsvfile">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_file_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_conv"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_conv_statustext">
							<xsl:value-of select="lang_conv_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_conv">
							<xsl:value-of select="select_conv"/>
						</xsl:variable>
						<select name="{$select_conv}" class="forms" title="{$lang_conv_statustext}">
							<option value="">
								<xsl:value-of select="lang_select_conversion"/>
							</option>
							<xsl:apply-templates select="conv_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_debug"/>
					</td>
					<td>
						<input type="checkbox" name="download" value="True" checked="checked">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_debug_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr height="50">
					<td>
						<xsl:variable name="lang_import">
							<xsl:value-of select="lang_import"/>
						</xsl:variable>
						<input type="submit" name="convert" value="{$lang_import}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_import_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td>
					<xsl:variable name="cancel_action">
						<xsl:value-of select="cancel_action"/>
					</xsl:variable>
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_cancel_statustext"/>
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

<!-- type_list -->
<xsl:template match="type_list">
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

<!-- dimb_list -->
<xsl:template match="dimb_list">
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

<!-- janitor_list -->
<xsl:template match="janitor_list">
	<xsl:variable name="lid">
		<xsl:value-of select="lid"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$lid}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$lid}">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- supervisor_list -->
<xsl:template match="supervisor_list">
	<xsl:variable name="lid">
		<xsl:value-of select="lid"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$lid}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$lid}">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- budget_responsible_list -->
<xsl:template match="budget_responsible_list">
	<xsl:variable name="lid">
		<xsl:value-of select="lid"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$lid}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$lid}">
				<xsl:value-of disable-output-escaping="yes" select="firstname"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="lastname"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- conv_list -->
<xsl:template match="conv_list">
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

<!-- rollback_file_list -->
<xsl:template match="rollback_file_list">
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

<!-- tax_code_list -->
<xsl:template match="tax_code_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="selected">
			<option value="{$id}" selected="selected">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:when>
		<xsl:otherwise>
			<option value="{$id}">
				<xsl:value-of disable-output-escaping="yes" select="id"/>
			</option>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- export -->
<xsl:template match="export">
	<xsl:apply-templates select="menu"/>
	<div align="left">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" name="form" action="{$form_action}">
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
					<td valign="top">
						<xsl:value-of select="lang_select_conv"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_conv_statustext">
							<xsl:value-of select="lang_conv_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_conv">
							<xsl:value-of select="select_conv"/>
						</xsl:variable>
						<select name="{$select_conv}" class="forms" title="{$lang_conv_statustext}">
							<option value="">
								<xsl:value-of select="lang_select_conv"/>
							</option>
							<xsl:apply-templates select="conv_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_export_to_file"/>
					</td>
					<td>
						<input type="checkbox" name="values[download]" value="on" checked="checked">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_debug_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
						<xsl:variable name="link_rollback_file">
							<xsl:value-of select="link_rollback_file"/>
						</xsl:variable>
						<a href="{$link_rollback_file}">
							<xsl:value-of select="lang_rollback_file"/>
						</a>
					</td>
				</tr>
				<tr height="50">
					<td>
						<xsl:variable name="lang_submit">
							<xsl:value-of select="lang_submit"/>
						</xsl:variable>
						<input type="submit" name="values[submit]" value="{$lang_submit}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_export_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td>
					<form method="post" action="{cancel_action}">
						<input type="submit" name="done" value="{lang_cancel}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_cancel_statustext"/>
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
	<xsl:apply-templates select="menu"/>
	<div align="left">
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" name="form" action="{$form_action}">
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
					<td valign="top">
						<xsl:value-of select="lang_select_conv"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_conv_statustext">
							<xsl:value-of select="lang_conv_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_conv">
							<xsl:value-of select="select_conv"/>
						</xsl:variable>
						<select name="{$select_conv}" title='{$lang_conv_statustext}'>
							<option value="">
								<xsl:value-of select="lang_select_conv"/>
							</option>
							<xsl:apply-templates select="conv_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_select_file"/>
					</td>
					<td valign="top">
						<xsl:variable name="lang_file_statustext">
							<xsl:value-of select="lang_file_statustext"/>
						</xsl:variable>
						<xsl:variable name="select_file">
							<xsl:value-of select="select_file"/>
						</xsl:variable>
						<select name="{$select_file}" class="forms" title="{$lang_file_statustext}">
							<option value="">
								<xsl:value-of select="lang_no_file"/>
							</option>
							<xsl:apply-templates select="rollback_file_list"/>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'voucher')"/>
					</td>
					<td valign="top">
						<input type="text" id="voucher_id" name="values[voucher_id]" value="">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'voucher')"/>
								<xsl:text> extern</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'voucher')"/>
						<xsl:text> intern</xsl:text>
					</td>
					<td valign="top">
						<input type="text" id="voucher_id_intern" name="values[voucher_id_intern]" value="">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'voucher')"/>
								<xsl:text> intern</xsl:text>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_date"/>
					</td>
					<td>
						<input type="text" id="date" name="date" size="10" value="{value_date}" readonly="readonly">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_date_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
				<tr height="50">
					<td>
						<xsl:variable name="lang_submit">
							<xsl:value-of select="lang_submit"/>
						</xsl:variable>
						<input type="submit" name="values[submit]" value="{$lang_submit}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_import_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</form>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr>
				<td>
					<xsl:variable name="cancel_action">
						<xsl:value-of select="cancel_action"/>
					</xsl:variable>
					<xsl:variable name="lang_cancel">
						<xsl:value-of select="lang_cancel"/>
					</xsl:variable>
					<form method="post" action="{$cancel_action}">
						<input type="submit" name="done" value="{$lang_cancel}">
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

<!-- edit single voucher line  -->
<xsl:template  match="edit">
	<xsl:choose>
		<xsl:when test="normalize-space(redirect) != ''">
			<script>
				window.parent.location = '<xsl:value-of select="redirect"/>';
				window.close();
			</script>
		</xsl:when>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<form name="form" id="form" method="post" action="{form_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<input type="hidden" name="paid" value="{paid}"/>
				<xsl:for-each select="approved_list">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="role"/>
						</label>
						<xsl:if test="initials != ''">
							<xsl:value-of select="initials"/>
							<xsl:text>: </xsl:text>
							<xsl:value-of select="date"/>
						</xsl:if>
					</div>
				</xsl:for-each>
				<xsl:choose>
					<xsl:when test="paid != 1">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'order id')"/>
							</label>
							<input type="text" name="values[order_id]" value="{order_id}">
								<xsl:attribute name="size">
									<xsl:text>20</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'order id')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<input type="hidden" name="values[sign_orig]" value="{sign_orig}"/>
							<input type="hidden" name="values[my_initials]" value="{my_initials}"/>
							<label>
								<xsl:value-of select="php:function('lang', 'approve')"/>
							</label>
							<select name="values[approve]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'grant')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="approve_list"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'voucher process code')"/>
							</label>
							<select name="values[process_code]">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'voucher process code')"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="php:function('lang', 'voucher process code')"/>
								</option>
								<xsl:apply-templates select="process_code_list"/>
							</select>
						</div>
						<xsl:call-template name="external_project_form"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'voucher process log')"/>
							</label>
							<textarea cols="60" rows="10" name="values[process_log]" wrap="virtual">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'voucher process log')"/>
								</xsl:attribute>
								<xsl:value-of select="value_process_log"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'approved amount')"/>
							</label>
							<input type="text" name="values[approved_amount]" value="{value_approved_amount}">
								<xsl:attribute name="size">
									<xsl:text>20</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'approved amount')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'split line')"/>
							</label>
							<input type="checkbox" name="values[split_line]" value="1">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'split line')"/>
								</xsl:attribute>
							</input>
							<xsl:text> [ </xsl:text>
							<xsl:value-of select="value_amount"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="value_currency"/>
							<xsl:text> ]</xsl:text>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'amount')"/>
							</label>
							<input type="text" name="values[split_amount]">
								<xsl:attribute name="size">
									<xsl:text>20</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'amount')"/>
								</xsl:attribute>
							</input>
						</div>
					</xsl:when>
					<xsl:otherwise>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'voucher process log')"/>
							</label>
							<textarea cols="60" rows="10" wrap="virtual">
								<xsl:attribute name="readonly">
									<xsl:text>readonly</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_process_log"/>
							</textarea>
						</div>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>
		<div class="proplist-col">
			<xsl:choose>
				<xsl:when test="paid != 1">
					<xsl:variable name="lang_send">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_send}" title="{$lang_send}"></input>
				</xsl:when>
			</xsl:choose>
		</div>
	</form>
</xsl:template>

<!-- New template-->
<xsl:template match="process_code_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of select="name"/>
	</option>
</xsl:template>

<!-- New template-->
<xsl:template match="approve_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of select="name"/>
	</option>
</xsl:template>

<!-- New template-->
<xsl:template match="approved_list" xmlns:php="http://php.net/xsl">
	<div class="pure-g">
		<div class="pure-u-1-4">
			<xsl:value-of select="role"/>
		</div>
		<div class="pure-u-1-4">
			<xsl:if test="initials != ''">
				<xsl:value-of select="initials"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="date"/>
			</xsl:if>
		</div>
		<div class="pure-u-1-4">
			<xsl:if test="date = ''">
				<select name="values[forward][{role_sign}]">
					<xsl:attribute name="title">
						<xsl:value-of select="role"/>
					</xsl:attribute>
					<option value="">
						<xsl:value-of select="php:function('lang', 'select')"/>
					</option>
					<xsl:apply-templates select="user_list/options_user"/>
				</select>
			</xsl:if>
		</div>
	</div>
</xsl:template>


<!-- forward voucher  -->
<xsl:template xmlns:php="http://php.net/xsl" match="forward">
	<xsl:choose>
		<xsl:when test="normalize-space(redirect) != ''">
			<script>
				window.parent.location = '<xsl:value-of select="redirect"/>';
				window.close();
			</script>
		</xsl:when>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<form id="form" name="form" method="post" action="{form_action}" class="pure-form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="record_detail">
				<xsl:apply-templates select="approved_list"/>
				<div class="pure-g">
					<div class="pure-u-1-4">
						<input type="hidden" name="values[sign_orig]" value="{sign_orig}"/>
						<input type="hidden" name="values[my_initials]" value="{my_initials}"/>
						<xsl:value-of select="php:function('lang', 'approve')"/>
					</div>
					<div class="pure-u-1-4">
						<select name="values[approve]">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'grant')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="approve_list"/>
						</select>
					</div>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4">
						<xsl:variable name="lang_send">
							<xsl:value-of select="php:function('lang', 'save')"/>
						</xsl:variable>
						<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_send}" title="{$lang_send}"></input>
					</div>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4">
						<xsl:value-of select="php:function('lang', 'order id')"/>
					</div>
					<div class="pure-u-1-4">
						<xsl:for-each select="orders">
							<label class="pure-checkbox">
								<xsl:value-of select="id"/>
								<input type="checkbox" name="orders[]" value="{id}" checked="checked"/>
							</label>
						</xsl:for-each>
					</div>
				</div>
			</div>
		</div>
	</form>
</xsl:template>

<!-- New template-->
<xsl:template match="options_user">
	<option value="{lid}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="lastname"/>
		<xsl:text>, </xsl:text>
		<xsl:value-of disable-output-escaping="yes" select="firstname"/>
	</option>
</xsl:template>

