<!-- $Id$ -->

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
	<style type="text/css">
	#box { width: 200px; height: 5px; background: blue; }
	//select { width: 200px; }
	#voucher_id_filter { width: 800px; }
	#dim_b { width: 200px; }
	#dim_e { width: 200px; }
	#period { width: 80px; }
	#periodization { width: 200px; }
	#periodization_start { width: 80px; }
	#process_code { width: 200px; }
	#tax_code { width: 200px; }
	#approve_as { width: 200px; }
	#_oppsynsmannid { width: 200px; }
	#_saksbehandlerid { width: 200px; }
	#_budsjettansvarligid { width: 200px; }
	.row_on,.th_bright
	{
		background-color: #CCEEFF;
	}

	.row_off
	{
		background-color: #DDF0FF;
	}

	</style>

	<xsl:call-template name="invoice" />
	<div id="popupBox"></div>	
	<div id="curtain"></div>
</xsl:template>

<xsl:template name="invoice" xmlns:php="http://php.net/xsl">
	<!-- loads translations into array for use with javascripts -->
	<!--
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'edit')"/>;
	</script>
	-->
		<script type="text/javascript">
			var invoice_layout_config = <xsl:value-of select="invoice_layout_config"/>;
		</script>

	<!-- IMPORTANT!!! Loads YUI javascript -->
<!--	<xsl:call-template name="common"/> -->

	<div class="yui-content">
		<div id="invoice-layout">
				<div class="layout-north">
					<div class="body">
						<h2 class="icon"><xsl:value-of select="site_title"/></h2>
						<div class="button-bar">
							<a href="{home_url}" class="icon icon-home">
								<xsl:value-of select="home_text"/>
							</a>
							<a href="{about_url}" class="icon icon-about">
								<xsl:value-of select="about_text"/>
							</a>
							<a href="{preferences_url}" class="icon icon-preferences">
								<xsl:value-of select="preferences_text"/>
							</a>
							<a href="{logout_url}" class="icon icon-logout">
								<xsl:value-of select="logout_text"/>
							</a>
						</div>
					</div>
				</div>

			<div class="layout-center">
				<div class="header">
					<h2><xsl:value-of select="php:function('lang', 'invoice')"/></h2>
				</div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
				<div class="body">
					<div id="voucher_details">
						<!--<xsl:call-template name="yui_phpgw_i18n"/>-->
						<table align = "center" width="95%" border="0">
							<xsl:apply-templates select="filter_form" />
							<xsl:apply-templates select="filter_invoice" />
						</table>
					  	<form action="{update_action}" name="voucher_form" id="voucher_form" method="post">
						<table align = "center" width="95%">
								<tr>
									<td colspan = '6'>
										<xsl:apply-templates select="paging"/>
										<xsl:apply-templates select="datatable"/>
									</td>
								</tr>
								<xsl:call-template name="voucher_fields" />
								<xsl:call-template name="approve"/>
							</table>
						</form>
					</div>
				</div>
			</div>
			<div class="layout-east">
				<div class="header">
					<h2>Bilde</h2>
				</div>
				<div class="body">
				  	<div id="image">
						<xsl:choose>
							<xsl:when test="voucher_info/voucher/image_url  != ''">
								<iframe id="image_content" width="100%" height="1000" src = "{voucher_info/voucher/image_url}" ><p>Your browser does not support iframes.</p></iframe>
							</xsl:when>
							<xsl:otherwise>
								<iframe id="image_content" width="100%" height="1000" ><p>Your browser does not support iframes.</p></iframe>
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</div>
			</div>
		</div>

		<table>
			<tr>
				<td>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<tr>
		<td>
			<table border="0">
				<tr>
					<td>
						<label><xsl:value-of select="php:function('lang', 'janitor')" /></label>
					</td>
					<td>
						<label><xsl:value-of select="php:function('lang', 'supervisor')" /></label>
					</td>
					<td>
						<label><xsl:value-of select="php:function('lang', 'budget responsible')" /></label>
					</td>
					<td>
						<label><xsl:value-of select="php:function('lang', 'search criteria')" /></label>
					</td>
					<td >
						<label><xsl:value-of select="php:function('lang', 'search')" /></label>
					</td>
				</tr>
			  	<!--<tr id="filters">-->
			  	<tr>
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
					  <select id="criteria" name="criteria">
						<xsl:apply-templates select="criteria_list/options"/>
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
		<td >
			<form id="invoice_queryForm" name="invoice_queryForm">
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
			<td colspan = "2">
				<table>
					<tr class ='row_on'>
						<td>
							<xsl:value-of select="php:function('lang', 'period')" />
						</td>
						<td>
							<xsl:value-of select="php:function('lang', 'periodization')" />
						</td>
						<td>
							<xsl:value-of select="php:function('lang', 'periodization start')" />
						</td>
					</tr>
					<tr class ='row_on'>
						<td>
							<select id="period" name="values[period]">
								<xsl:apply-templates select="voucher_info/generic/period_list/options"/>
					  		</select>
						</td>
						<td>
							<select id="periodization" name="values[periodization]">
								<xsl:apply-templates select="voucher_info/generic/periodization_list/options"/>
					  		</select>
						</td>
						<td>
							<select id="periodization_start" name="values[periodization_start]">
								<xsl:apply-templates select="voucher_info/generic/periodization_start_list/options"/>
					  		</select>
						</td>
					</tr>
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
			  	<input type="text" name="values[order_id]" id="order_id" value="{voucher_info/voucher/order_id}"/>
			  	<input type="hidden" name="values[order_id_orig]" id="order_id_orig" value="{voucher_info/voucher/order_id}"/>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'dime')" />
			</td>
			<td>
				<select id="dim_e" name="values[dim_e]">
					<xsl:apply-templates select="voucher_info/generic/dime_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'budget account')" />
			</td>
			<td>
			  	<input type="text" name="values[b_account_id]" id="b_account_id" value="{voucher_info/voucher/b_account_id}"/>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'dim b')" />
			</td>
			<td>
				<select id="dim_b" name="values[dim_b]">
					<xsl:apply-templates select="voucher_info/generic/dimb_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'invoice line text')" />
			</td>
			<td>
			  	<input type="text" name="values[line_text]" id="line_text" value="{voucher_info/voucher/line_text}"/>
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
			  	<input type="text" name="values[dim_a]" id="dim_a" value="{voucher_info/voucher/dim_a}"/>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'tax code')" />
			</td>
			<td>
				<select id="tax_code" name="values[tax_code]">
					<xsl:apply-templates select="voucher_info/generic/tax_code_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'project group')" />
			</td>
			<td>
			  	<input type="text" name="values[project_group]" id="project_group" value="{voucher_info/voucher/project_group}"/>
			</td>
		</tr>
</xsl:template>


	<!-- approve voucher  -->
	<xsl:template xmlns:php="http://php.net/xsl" name="approve">
		<xsl:apply-templates select="voucher_info/generic/approved_list"/>
		<tr class ='row_off'>
			<input id="sign_orig" type="hidden" name="values[sign_orig]" value="{voucher_info/generic/sign_orig}"/>
			<input id="my_initials" type="hidden" name="values[my_initials]" value="{voucher_info/generic/my_initials}"/>
			<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
				<xsl:value-of select="php:function('lang', 'approve as')"/>
			</td>
			<td class="th_text" valign="top" align="left">
				<div id = "approve_as2"> </div>
				<!--
				<select id = "approve_as" name="values[approve]" with="40">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'approve as')"/>
					</xsl:attribute>
					<xsl:apply-templates select="voucher_info/generic/approve_list/options"/>
				</select>
				-->
			</td>
		</tr>
		<tr class ='row_off'>
			<td>
				<xsl:value-of select="php:function('lang', 'voucher process code')" />
			</td>
			<td>
				<select id="process_code" name="values[process_code]">
					<xsl:apply-templates select="voucher_info/generic/process_code_list/options"/>
		  		</select>
			</td>
		</tr>
		<tr class ='row_off'>
			<td class="th_text" align="left" valign="top" style="white-space: nowrap;">
				<xsl:value-of select="php:function('lang', 'voucher process log')"/>
			</td>
			<td align="left">
				<textarea id="process_log" cols="60" rows="10" name="values[process_log]" wrap="virtual">
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
								<select id ="_{role_sign}" name="values[forward][{role_sign}]">
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


<xsl:template match="datatable" xmlns:php="http://php.net/xsl">
	<div id="paging_0"/>
	<div id="datatable-container_0"/>

	<div id="data_paginator"/>
	<div id="datatable-container"/>
	
  	<xsl:call-template name="datasource-definition" />
	<div id="receipt"></div>
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
	<div class="row_on"><input type="submit" name="values[update_voucher]" id="frm_update_voucher" value="{$label_submit}"/></div>
</xsl:template>

<xsl:template name="datasource-definition" xmlns:php="http://php.net/xsl">

		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="//property_js"/>;
			var base_java_url = <xsl:value-of select="//base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
			var td_count = <xsl:value-of select="//td_count"/>;

			<xsl:for-each select="//datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						edit_action:  <xsl:value-of select="edit_action"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>
			<xsl:for-each select="//myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="//myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
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

