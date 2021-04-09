<!-- $Id$ -->

<!-- separate tabs and  inline tables-->


<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		.row_on,.th_bright
		{
			background-color: #CCEEFF;
		}

		.row_off
		{
			background-color: #DDF0FF;
		}

		.modal-dialog
		{
			height:600px !important;
		}

		.modal-content
		{
			height: 100%;
		}

		.sticky-top
		{
			top: 200px;
			position: fixed;
			position: sticky
		}

		.sticky_bottom
		{
			position: fixed;
		    bottom: 50px;
			right: 10px;
			display: none;
			z-index:1000;
		}

		#voucher_details
		{
			line-height: 0.8;
			font-size: 11px;
		}

	</style>

	<xsl:call-template name="invoice" />
</xsl:template>

<xsl:template name="invoice" xmlns:php="http://php.net/xsl">

	<script type="text/javascript">

		var lang = <xsl:value-of select="php:function('js_lang', 'edit')"/>;
		var email_base_url = <xsl:value-of select="//email_base_url"/>;


	$(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.sticky_bottom').fadeIn();
        } else {
            $('.sticky_bottom').fadeOut();
        }
    });


	</script>
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>

	<div id="voucher_details">
		<table class="pure-table">
			<xsl:apply-templates select="filter_form" />
			<xsl:apply-templates select="filter_invoice" />
		</table>

<!--		<div class='row'>
			<div class='col-sm-9'>

			</div>
				<div class='col-sm-3'>
				<a href="#" id="show_image" class="pure-button pure-button-primary" data-toggle="modal" data-target="#mapModal" style="display:none; margin-bottom: 5px">
					<p><i class="fas fa-image"></i>  Trykk for å se faktura</p>
				</a>
			</div>
		</div>-->


		<form action="{update_action}" name="voucher_form" id="voucher_form" method="post" class="pure-form">
				<xsl:variable name="label_submit">
					<xsl:value-of select="php:function('lang', 'save')" />
				</xsl:variable>
					<div class='sticky_bottom'>
						<div>
							<button type="submit" class="pure-button pure-button-primary " name="values[update_voucher]">
									<p>
										<i class="fas fa-save"></i>
										<xsl:text> </xsl:text>
										<xsl:value-of select="$label_submit"/>
									</p>
							</button>
						</div>
					</div>

				<div class='row'>
						<div class='col-sm-12'>
					<div class=" col pure-table">
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_1'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
						</div>
				</div>
				<div class='row'>
					<div class='col'>
						<div id="receipt"></div>
					</div>
				</div>

				<div class='row'>
					<div class='col-sm-9'>
			
						<table class="pure-table">
							<tr>
								<td valign="top" height="30">
									<div id = 'split_text'>
									</div>
								</td>
							</tr>
							<tr class ='row_on'>
								<td colspan = '6'>
									<div class="row_on">
										<button type="submit" class="pure-button pure-button-primary " name="values[update_voucher]">
											<p>
												<i class="fas fa-save"></i>
												<xsl:text> </xsl:text>
												<xsl:value-of select="$label_submit"/>
											</p>
										</button>
									</div>
								</td>
							</tr>
							<xsl:call-template name="voucher_fields" />
							<xsl:call-template name="approve"/>
						</table>
					</div>
<!--					<div class='col-sm-3 align-self-start float-left sticky_bottom'>-->
<!--					<div class='col-sm-3  sticky_bottom'>
						<div>
							<button type="submit" class="pure-button pure-button-primary " name="values[update_voucher]">
									<p>
										<i class="fas fa-save"></i>
										<xsl:text> </xsl:text>
										<xsl:value-of select="$label_submit"/>
									</p>
							</button>
						</div>
						<div id="receipt"></div>
					</div>-->

				</div>
		</form>
	</div>
	<!-- invoice Modal -->
	<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header text-center">
					<h2 class="modal-title w-100">
						Fakturaimage
					</h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							<i class="fas fa-times"></i>
						</span>
					</button>
				</div>
				<div class="modal-body">
					<div style="width: 100%; height:100%">
						<iframe id ="image_content" src="" frameborder="0" marginheight="0" marginwidth="0" style="width: 100%; height:100%"></iframe>
					</div>
					<br />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary w-100" data-dismiss="modal">Lukk vindu</button>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<table class="pure-table pure-form">
				 <thead>
					<tr>
						<th>
							<label>
								<xsl:value-of select="php:function('lang', 'janitor')" />
							</label>
						</th>
						<th>
							<label>
								<xsl:value-of select="php:function('lang', 'supervisor')" />
							</label>
						</th>
						<th>
							<label>
								<xsl:value-of select="php:function('lang', 'budget responsible')" />
							</label>
						</th>
						<th>
							<label>
								<xsl:value-of select="php:function('lang', 'search criteria')" />
							</label>
						</th>
						<th >
							<label>
								<xsl:value-of select="php:function('lang', 'search')" />
							</label>
						</th>
					</tr>
				 </thead>
				<tr>
					<td>
						<select id="janitor_lid" name="janitor_lid" class="pure-u-md-1">
							<xsl:apply-templates select="janitor_list/options"/>
						</select>
					</td>		
					<td>
						<select id="supervisor_lid" name="supervisor_lid" class="pure-u-md-1">
							<xsl:apply-templates select="supervisor_list/options"/>
						</select>
					</td>		
					<td>
						<select id="budget_responsible_lid" name="budget_responsible_lid" class="pure-u-md-1">
							<xsl:apply-templates select="budget_responsible_list/options"/>
						</select>
					</td>		
					<td>
						<select id="criteria" name="criteria" class="pure-u-md-1">
							<xsl:apply-templates select="criteria_list/options"/>
						</select>
					</td>		
					<td>
						<input type="text" name="query" id="query" class="pure-u-md-1"/>
					</td>
					<td>
						<xsl:variable name="lang_search">
							<xsl:value-of select="php:function('lang', 'Search')" />
						</xsl:variable>
						<button class="pure-button pure-button-primary" id="search" name="search" value="{$lang_search}" title = "{$lang_search}" >
							<p>
								<i class="fas fa-search"></i>
								<xsl:text> </xsl:text>
								<xsl:value-of select="$lang_search" />
							</p>


						</button>
					</td>	  		
				</tr>
			</table>
		</td>
	</tr>
</xsl:template>

<xsl:template match="filter_invoice" xmlns:php="http://php.net/xsl">
	<tr>
		<td >
			<form id="invoice_queryForm" name="invoice_queryForm" class="pure-form">
				<xsl:attribute name="method">
					<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
				</xsl:attribute>
				<xsl:attribute name="action">
					<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
				</xsl:attribute>
				<!-- When janitor area is chosen, an ajax request is executed. The operation fetches vouchers from db and populates the voucher list.
				The ajax opearation is handled in ajax_invoice.js -->
				<select id="voucher_id_filter" name="voucher_id_filter" class="pure-u-md-1">
					<xsl:apply-templates select="voucher_list/options"/>
				</select>
			</form>
			<form id="update_table_dummy" method='POST' action='' ></form>
		</td>
	</tr>

</xsl:template>

<xsl:template name="voucher_fields" xmlns:php="http://php.net/xsl">
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'voucher')" />
		</td>
		<td>
			<input type="hidden" name="voucher_id" id="voucher_id" value="{voucher_info/voucher/voucher_id}"/>
			<input type="hidden" name="line_id" id="line_id" value="{voucher_info/voucher/id}"/>
			<div id= 'voucher_id_text'>
				<xsl:value-of select="voucher_info/voucher/voucher_id"/>
			</div>
		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'vendor')" />
		</td>
		<td>
			<div id="vendor">
				<xsl:value-of select="voucher_info/voucher/vendor"/>
			</div>
		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<div id="invoice_id_text">
				<xsl:choose>
					<xsl:when test="voucher_info/voucher/external_ref  != ''">
						<xsl:value-of disable-output-escaping="yes" select="voucher_info/voucher/external_ref"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('lang', 'invoice number')" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</td>
		<td>
			<div id="invoice_id">
				<xsl:value-of select="voucher_info/voucher/invoice_id"/>
			</div>

		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'kid nr')" />
		</td>
		<td>
			<div id="kid_nr">
				<xsl:value-of select="voucher_info/voucher/kid_nr"/>
			</div>
		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'currency')" />
		</td>
		<td>
			<div id="currency">
				<xsl:value-of select="voucher_info/voucher/currency"/>
			</div>
		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'invoice date')" />
		</td>
		<td>
			<div id="invoice_date">
				<xsl:value-of select="voucher_info/voucher/invoice_date"/>
			</div>

		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'payment date')" />
		</td>
		<td>
			<div id="payment_date">
				<xsl:value-of select="voucher_info/voucher/payment_date"/>
			</div>

		</td>
	</tr>
	<tr class ='row_on'>
		<td>
			<xsl:value-of select="php:function('lang', 'park invoice')" />
		</td>
		<td>
			<div id="park_order">
				<input type="checkbox" name="values[park_invoice]" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'park invoice')"/>
					</xsl:attribute>
					<xsl:if test="voucher_info/voucher/parked = '1'">
						<xsl:attribute name="checked">
							<xsl:text>checked</xsl:text>
						</xsl:attribute>
					</xsl:if>
				</input>
				<xsl:if test="voucher_info/voucher/parked = '1'">
					<xsl:text> X</xsl:text>
				</xsl:if>
			</div>
		</td>
	</tr>
	<tr class ='row_on'>
		<td>
		</td>
		<td>
			<table class="pure-table">
				<thead>
					<tr>
						<th>
							<xsl:value-of select="php:function('lang', 'period')" />
						</th>
						<th>
							<xsl:value-of select="php:function('lang', 'periodization')" />
						</th>
						<th>
							<xsl:value-of select="php:function('lang', 'periodization start')" />
						</th>
					</tr>
				</thead>
				 <tbody>
					<tr class ='row_on'>
						<td>
							<select id="period" name="values[period]" class="pure-u-md-1">
								<xsl:apply-templates select="voucher_info/generic/period_list/options"/>
							</select>
						</td>
						<td>
							<select id="periodization" name="values[periodization]" class="pure-u-md-1">
								<xsl:apply-templates select="voucher_info/generic/periodization_list/options"/>
							</select>
						</td>
						<td>
							<select id="periodization_start" name="values[periodization_start]" class="pure-u-md-1">
								<xsl:apply-templates select="voucher_info/generic/periodization_start_list/options"/>
							</select>
						</td>
					</tr>
				 </tbody>
			</table>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<div id = 'order_text'>
				<xsl:choose>
					<xsl:when test="voucher_info/voucher/order_link  != ''">
						<a href="{voucher_info/voucher/order_link}" target="_blank" title="{voucher_info/voucher/status}">
							<xsl:value-of select="php:function('lang', 'order')" />
						</a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('lang', 'order')" />
					</xsl:otherwise>
				</xsl:choose>

			</div>
		</td>
		<td>
			<input type="text" name="values[order_id]" id="order_id" value="{voucher_info/voucher/order_id}" class="pure-u-md-1"/>
			<input type="hidden" name="values[order_id_orig]" id="order_id_orig" value="{voucher_info/voucher/order_id}"/>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'dime')" />
		</td>
		<td>
			<select id="dim_e" name="values[dim_e]" class="pure-u-md-1">
				<xsl:apply-templates select="voucher_info/generic/dime_list/options"/>
			</select>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'budget account')" />
		</td>
		<td>
			<input type="text" name="values[b_account_id]" id="b_account_id" value="{voucher_info/voucher/b_account_id}" class="pure-u-md-1"/>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'dim b')" />
		</td>
		<td>
			<select id="dim_b" name="values[dim_b]" class="pure-u-md-1">
				<xsl:apply-templates select="voucher_info/generic/dimb_list/options"/>
			</select>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'invoice line text')" />
		</td>
		<td>
			<input type="text" name="values[line_text]" id="line_text" value="{voucher_info/voucher/line_text}" class="pure-u-md-1"/>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'close order')" />
		</td>
		<td>
			<input type="hidden" id ="close_order_orig" name="values[close_order_orig]" value="{voucher_info/voucher/closed}"/>
				
			<div id="close_order">
				<input type="checkbox" name="values[close_order]" value="1">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'close order')"/>
					</xsl:attribute>
					<xsl:if test="voucher_info/voucher/pref_close_order = '1' or voucher_info/voucher/closed = '1'">
						<xsl:attribute name="checked">
							<xsl:text>checked</xsl:text>
						</xsl:attribute>
					</xsl:if>
				</input>
				<xsl:text> </xsl:text>
				<xsl:value-of select="voucher_info/voucher/status"/>
			</div>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'dim a')" />
		</td>
		<td>
			<input type="text" name="values[dim_a]" id="dim_a" value="{voucher_info/voucher/dim_a}" class="pure-u-md-1"/>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'tax code')" />
		</td>
		<td>
			<select id="tax_code" name="values[tax_code]" class="pure-u-md-1">
				<xsl:apply-templates select="voucher_info/generic/tax_code_list/options"/>
			</select>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'project group')" />
		</td>
		<td>
			<input type="text" name="values[external_project_id]" id="external_project_id" value="{voucher_info/voucher/external_project_id}" class="pure-u-md-1"/>
		</td>
	</tr>
</xsl:template>


<!-- approve voucher  -->
<xsl:template xmlns:php="http://php.net/xsl" name="approve">
	<xsl:apply-templates select="voucher_info/generic/approved_list"/>
	<tr class ='row_off'>
		<td class="th_text" align="left" valign="top" style="white-space: nowrap;" height="40">
			<input id="sign_orig" type="hidden" name="values[sign_orig]" value="{voucher_info/generic/sign_orig}"/>
			<input id="my_initials" type="hidden" name="values[my_initials]" value="{voucher_info/generic/my_initials}"/>
			<xsl:value-of select="php:function('lang', 'approve as')"/>
		</td>
		<td class="th_text" valign="top" align="left" height="40">
			<div id = "approve_as2"> </div>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
		</td>
		<td height="50">
			<div id = 'email_link'></div>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'voucher process code')" />
		</td>
		<td>
			<select id="process_code" name="values[process_code]" class="pure-u-md-1">
				<xsl:apply-templates select="voucher_info/generic/process_code_list/options"/>
			</select>
		</td>
	</tr>
	<tr class ='row_off'>
		<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
			<xsl:value-of select="php:function('lang', 'voucher process log')"/>
		</td>
		<td align="left">
			<textarea id="process_log" cols="60" rows="10" name="values[process_log]" wrap="virtual" class="pure-u-md-1">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'voucher process log')"/>
				</xsl:attribute>
				<!--xsl:value-of select="voucher_info/generic/process_log"/-->
			</textarea>
		</td>
	</tr>

</xsl:template>


<!-- New template-->
<xsl:template match="approved_list" xmlns:php="http://php.net/xsl">
	<tr class ='row_off'>
		<td align="left" style="white-space: nowrap;">
			<xsl:value-of select="role"/>
		</td>
		<td align="left" style="white-space: nowrap;">
			<div id = "{role_sign}">
				<xsl:choose>
					<xsl:when test="date != ''">
						<xsl:value-of select="initials"/>
						<xsl:text>: </xsl:text>
						<xsl:value-of select="date"/>
					</xsl:when>
					<xsl:otherwise>
						<select id ="_{role_sign}" name="values[forward][{role_sign}]" class="pure-u-md-1">
							<xsl:attribute name="title">
								<xsl:value-of select="role"/>
							</xsl:attribute>
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</td>
	</tr>
</xsl:template>

<!-- options for use with select-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

