<!-- $Id: pending_vouchers.xsl 8854 2012-02-14 07:54:40Z vator $ -->

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

<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="pending_for_approval_tabview">
	<div class="identifier-header">
		<h1><xsl:value-of select="php:function('lang', 'invoice')"/></h1>
	</div>
	<xsl:call-template name="invoice" />
</div>
	
</xsl:template>

<xsl:template name="invoice" xmlns:php="http://php.net/xsl">
	<!-- loads translations into array for use with javascripts -->
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'edit')"/>;
	</script>

	<!-- IMPORTANT!!! Loads YUI javascript -->
	<xsl:call-template name="common"/>

	<div class="yui-content">
		<div id="voucher_details">
			<xsl:call-template name="yui_phpgw_i18n"/>
			<table>
				<xsl:apply-templates select="filter_form" />
				<xsl:apply-templates select="filter_invoice" />
				<xsl:call-template name="voucher_fields" />
				<tr>
					<td colspan = '6'>
						<xsl:apply-templates select="paging"/>
						<xsl:apply-templates select="datatable"/>
					</td>
				</tr>
				<xsl:apply-templates select="form/list_actions"/>
			</table>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<tr>
	<td colspan = '6'>
	<table>
	<tr>
		<td>
			<xsl:value-of select="php:function('lang', 'janitor')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'supervisor')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'budget responsible')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'voucher id')" />
		</td>
	</tr>
	  <tr id="filters">
		<td>
		  <select id="janitor_lid" name="janitor_lid">
			<xsl:apply-templates select="janitor_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="supervisor_lid" name="supervisor_lid">
			<xsl:apply-templates select="supervisor_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="budget_responsible_lid" name="budget_responsible_lid">
			<xsl:apply-templates select="budget_responsible_list/options"/>
		  </select>
		</td>		
		<td>
			<input type="text" name="query" id="query"/>
		</td>
		<td>
			<xsl:variable name="lang_search"><xsl:value-of select="php:function('lang', 'Search')" /></xsl:variable>
			<input type="button" id = "search" name="search" value="{$lang_search}" title = "{$lang_search}" />
		</td>	  		
	  </tr>
	  </table>
	  </td>
	  </tr>
</xsl:template>

<xsl:template match="filter_invoice" xmlns:php="http://php.net/xsl">
	<tr>
		<td colspan='4'>
			<form id="invoice_queryForm">
				<xsl:attribute name="method">
					<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
				</xsl:attribute>

				<xsl:attribute name="action">
					<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
				</xsl:attribute>
					<!-- When janitor area is chosen, an ajax request is executed. The operation fetches vouchers from db and populates the voucher list.
					 The ajax opearation is handled in ajax_invoice.js --> 
				<select id="voucher_id_filter" name="voucher_id_filter">
					<xsl:apply-templates select="voucher_list/options"/>
				</select>
			</form>
		
			<form id="update_table_dummy" method='POST' action='' ></form>
		</td>
	</tr>

</xsl:template>

<xsl:template name="voucher_fields" xmlns:php="http://php.net/xsl">
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'voucher')" />
			</td>
			<td>
			  	<input type="hidden" name="voucher_id" id="voucher_id" value="{voucher_info/voucher/voucher_id}	"/>
			  	<div id= 'voucher_id_text'>
				  <xsl:value-of select="voucher_info/voucher/voucher_id"/>		  	
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'vendor')" />
			</td>
			<td>
			  	<div id="vendor">
			  		<xsl:value-of select="voucher_info/voucher/vendor"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
			  	<div id="invoice_id_text">
					<xsl:value-of select="php:function('lang', 'invoice number')" />
				</div>
			</td>
			<td>
			  	<div id="invoice_id">
			  		<xsl:value-of select="voucher_info/voucher/invoice_id"/>
			  	</div>

			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'kid nr')" />
			</td>
			<td>
			  	<div id="kid_nr">
			  		<xsl:value-of select="voucher_info/voucher/kid_nr"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'amount')" />
			</td>
			<td>
			  	<div id="amount">
			  		<xsl:value-of select="voucher_info/voucher/amount"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'approved amount')" />
			</td>
			<td>
			  	<div id="approved_amount">
			  		<xsl:value-of select="voucher_info/voucher/approved_amount"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'currency')" />
			</td>
			<td>
			  	<div id="currency">
			  		<xsl:value-of select="voucher_info/voucher/currency"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'invoice date')" />
			</td>
			<td>
			  	<div id="invoice_date">
			  		<xsl:value-of select="voucher_info/voucher/invoice_date"/>
			  	</div>

			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'payment date')" />
			</td>
			<td>
			  	<div id="payment_date">
			  		<xsl:value-of select="voucher_info/voucher/payment_date"/>
			  	</div>

			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'janitor')" />
			</td>
			<td>
			  	<div id="janitor">
			  		<xsl:value-of select="voucher_info/voucher/janitor"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'supervisor')" />
			</td>
			<td>
			  	<div id="supervisor">
			  		<xsl:value-of select="voucher_info/voucher/supervisor"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'budget responsible')" />
			</td>
			<td>
			  	<div id="budget_responsible">
			  		<xsl:value-of select="voucher_info/voucher/budget_responsible"/>
			  	</div>

			</td>
		</tr>
		<tr>
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
			  	<input type="text" name="order_id" id="order_id" value="{voucher_info/voucher/order_id}"/>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'close order')" />
			</td>
			<td>
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
		<tr>
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
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'dim b')" />
			</td>
			<td>
				<select id="dim_b" name="dim_b">
					<xsl:apply-templates select="voucher_info/generic/dimb_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr>
			<td>
					<xsl:value-of select="php:function('lang', 'dim a')" />
			</td>
			<td>
			  	<input type="text" name="dim_a" id="dim_a" value="{voucher_info/voucher/dim_a}"/>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'tax code')" />
			</td>
			<td>
				<select id="tax_code" name="tax_code">
					<xsl:apply-templates select="voucher_info/generic/tax_code_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'project group')" />
			</td>
			<td>
			  	<input type="text" name="project_group" id="project_group" value="{voucher_info/voucher/project_group}"/>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'budget account')" />
			</td>
			<td>
			  	<input type="text" name="b_account_id" id="b_account_id" value="{voucher_info/voucher/b_account_id}"/>
			</td>
		</tr>

		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'period')" />
			</td>
			<td>
				<select id="period" name="period">
					<xsl:apply-templates select="voucher_info/generic/period_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'periodization')" />
			</td>
			<td>
				<select id="periodization" name="periodization">
					<xsl:apply-templates select="voucher_info/generic/periodization_list/options"/>
		  		</select>
			</td>
		</tr>

		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'periodization start')" />
			</td>
			<td>
				<select id="periodization_start" name="periodization_start">
					<xsl:apply-templates select="voucher_info/generic/periodization_start_list/options"/>
		  		</select>
			</td>
		</tr>

		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'oppsynsigndato')" />
			</td>
			<td>
			  	<div id="oppsynsigndato">
			  		<xsl:value-of select="voucher_info/voucher/oppsynsigndato"/>
			  	</div>

			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'saksigndato')" />
			</td>
			<td>
			  	<div id="saksigndato">
			  		<xsl:value-of select="voucher_info/voucher/saksigndato"/>
			  	</div>

			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'budsjettsigndato')" />
			</td>
			<td>
			  	<div id="budsjettsigndato">
			  		<xsl:value-of select="voucher_info/voucher/budsjettsigndato"/>
			  	</div>
			</td>
		</tr>
		<tr>
			<td>
				<xsl:value-of select="php:function('lang', 'remark')" />
			</td>
			<td>
			  	<div id="remark">
			  		<xsl:value-of select="voucher_info/voucher/remark_link"/>
			  	</div>
			</td>
		</tr>

<!--
            merknad
                    [art] => 1
                    [type] => 1
                    [dim_a] => 
                    [dim_b] => 0
                    [dim_d] => 441
                    [tax] => 

                    [project_id] => 
                    [merknad] => 
                    [b_account_id] => 12304262
                    [kostra_id] => 

                    [process_code] => 
                    [process_log] => 
 
-->
</xsl:template>

<xsl:template match="datatable" xmlns:php="http://php.net/xsl">
	<div id="data_paginator"/>
	<div class="error_msg" style="margin-left:20px;">Du må velge bruker for godkjenning</div>
	<div id="datatable-container"/>
	
  	<xsl:call-template name="datasource-definition" />
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
  	<xsl:variable name="label_checkAll"><xsl:value-of select="php:function('lang', 'invert_checkboxes')" /></xsl:variable>
  	<div><input type="button" id="select_all" value="{$label_checkAll}" onclick="checkAll('mychecks')"/></div>
  	
  	<form action="#" name="voucher_form" id="voucher_form" method="post">
  		<div class="voucher_submit"><input type="submit" name="values[save_voucher]" id="save_voucher" value="{$label_submit}" onclick="return onSave()"/></div>
  	</form>
</xsl:template>


<xsl:template name="datasource-definition" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.namespace('portico');
	 
 		YAHOO.portico.columnDefs = [
				<xsl:for-each select="//datatable/field">
					{
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
						</xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: true,
						</xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
						</xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];

		var main_source = '<xsl:value-of select="source"/>';
		var main_columnDefs = YAHOO.portico.columnDefs;
		var main_form = 'invoice_queryForm';
		var main_filters = ['voucher_id_filter', 'responsibility_roles_list'];
		var main_container = 'datatable-container';
		var main_table_id = 'datatable';
		var main_pag = 'data_paginator';
		var related_table = new Array('vouchers_table');
	
		setDataSource(main_source, main_columnDefs, main_form, main_filters, main_container, main_pag, main_table_id, related_table ); 
		
	</script>
	 
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

