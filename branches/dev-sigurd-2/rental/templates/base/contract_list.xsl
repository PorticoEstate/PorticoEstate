<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.util.Event.onDOMReady(
			function()
			{
				initCalendar('from_date', 'calendarPeriodFrom', 'cal1', 'Velg dato');
				initCalendar('to_date', 'calendarPeriodTo', 'cal1', 'Velg dato');
			}
		);

		YAHOO.util.Event.addListener(
			'ctrl_reset_button', 
			'click', 
			function(e)
			{    	
	    		YAHOO.util.Event.stopEvent(e);
	        	window.location = 'index.php?menuaction=rental.uicontract.index';
    		}
    	);
	</script>
</xsl:template>

<xsl:template name="pageContent">
	<xsl:call-template name="datatable" />
</xsl:template>

<xsl:template name="datatable" xmlns:php="http://php.net/xsl">
	<div class="datatable">
		<xsl:call-template name="listForm"/>
		<div id="paginator" />
	    <div id="columnshowhide" />
		<div id="dt-dlg">
		    <div class="hd">Velg hvilke kolonner du ønsker å se:</div>
		    <div id="dt-dlg-picker" class="bd"></div>
		</div>
    	<div id="datatable-container"/>
  		<xsl:call-template name="datasource-definition">
  			<xsl:with-param name="number">1</xsl:with-param>
  			<xsl:with-param name="form">list_form</xsl:with-param>
  			<xsl:with-param name="filters">['ctrl_toggle_contract_status','ctrl_toggle_contract_type','from_date','to_date']</xsl:with-param>
  			<xsl:with-param name="container_name">datatable-container</xsl:with-param>
  			<xsl:with-param name="context_menu_labels">
				['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
				'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
			</xsl:with-param>
			<xsl:with-param name="context_menu_actions">
					['view',
					'edit']	
			</xsl:with-param>
			<xsl:with-param name="source">index.php?menuaction=rental.uicontract.query&amp;phpgw_return_as=json</xsl:with-param>
			<xsl:with-param name="columnDefinitions">
  				[{
					key: "id",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_number')"/>",
				    sortable: true
				},
				{
					key: "date_start",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_date_start')"/>",
				    sortable: true
				},
				{
					key: "date_end",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_date_end')"/>",
				    sortable: true
				},
				{
					key: "title",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_title')"/>",
				    sortable: true
				},
				{
					key: "composite",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_composite')"/>",
				    sortable: false
				},
				{
					key: "tentant",
					label: "<xsl:value-of select="php:function('lang', 'rental_common_tenant')"/>",
				    sortable: false
				},
				{
					key: "billing_type",
					label: "<xsl:value-of select="php:function('lang', 'rental_contract_billing_type')"/>",
				    sortable: false
				},
				{
					key: "actions",
					hidden: true
				}
				]
  			</xsl:with-param>
  		</xsl:call-template>
  	</div>
</xsl:template>  

<xsl:template name="listForm" xmlns:php="http://php.net/xsl">
	<form id="list_form" method="GET">
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>
					<td class="toolbarlabel">
						<xsl:value-of select="php:function('lang', 'rental_rc_search_options')"/>
					</td>
					<td class="toolbarcol" >
						<label class="toolbar_element_label" for="ctrl_search_query">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_for')"/>
						</label>
						<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctr_toggle_contract_type">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_where')"/> 
						</label>
						<select name="search_option" id="ctr_toggle_contract_type">
							<option value="all" selected="selected"><xsl:value-of select="php:function('lang', 'rental_rc_all')"/></option>
							<option value="id"><xsl:value-of select="php:function('lang', 'rental_contract_id')"/></option>
							<option value="tenant_name"><xsl:value-of select="php:function('lang', 'rental_contract_tenant_name')"/></option>
							<option value="composite"><xsl:value-of select="php:function('lang', 'rental_contract_composite')"/></option>
							<option value="gab"><xsl:value-of select="php:function('lang', 'gab')"/></option>
						</select>
						
					</td>
					<td class="toolbarcol" id="searchSubmitContainer">
						<input type="submit" id="ctrl_search_button" name="ctrl_search_button">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'rental_rc_search')"/>
							</xsl:attribute>
						</input>
					</td>
					<td class="toolbarcol" id="resetFormContainer">
						<input type="button" id="ctrl_reset_button">
							<xsl:attribute name="value">        
								<xsl:value-of select="php:function('lang', 'rental_reset')"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</div>
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>	
					<td class="toolbarlabel">
						<label><b>Filtre</b></label>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_toggle_contract_status"><xsl:value-of select="php:function('lang', 'rental_contract_status')"/></label>
						<select name="contract_status" id="ctrl_toggle_contract_status">
							<option value="under_planning"><xsl:value-of select="php:function('lang', 'rental_contract_under_planning')"/></option>
							<option value="running"><xsl:value-of select="php:function('lang', 'rental_contract_running')"/></option>
							<option value="under_dismissal"><xsl:value-of select="php:function('lang', 'rental_contract_under_dismissal')"/></option>
							<option value="fixed"><xsl:value-of select="php:function('lang', 'rental_contract_fixed')"/></option>
							<option value="ended"><xsl:value-of select="php:function('lang', 'rental_contract_ended')"/></option>
							<option value="all" selected="selected"><xsl:value-of select="php:function('lang', 'rental_contract_all')"/></option>
						</select>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_toggle_active_rental_composites"><xsl:value-of select="php:function('lang', 'rental_contract_type')"/></label>
						<select name="contract_type" id="ctrl_toggle_contract_type">
							<xsl:for-each select="//contractTypes/id">
								<xsl:element name="option">
									<xsl:attribute name="value"><xsl:value-of select="text()"/></xsl:attribute>
									<xsl:value-of select="../title/text()"/>
								</xsl:element>
							</xsl:for-each>
							<option value="all" selected="selected"><xsl:value-of select="php:function('lang', 'rental_contract_all')"/></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>	
					<td class="toolbarlabel">
						<label><b><xsl:value-of select="php:function('lang', 'rental_contract_period')"/></b></label>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="calendarPeriodFrom"><xsl:value-of select="php:function('lang', 'rental_contract_from')"/></label>
						<input type="text" name="from_date" id="from_date" size="10"/>
						<input type="hidden" name="from_date_hidden" id="from_date_hidden"/>
						<div id="calendarPeriodFrom">
						</div>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="calendarPeriodTo"><xsl:value-of select="php:function('lang', 'rental_contract_to')"/></label>
						<input type="text" name="to_date" id="to_date" size="10"/>
						<input type="hidden" name="to_date_hidden" id="to_date_hidden"/>
						<div id="calendarPeriodTo">
						</div>
					</td>
					<td class="toolbarcol">
						<input type="submit" id="updateForm">	
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_update')"/></xsl:attribute>
						</input>
					</td>
					<td class="toolbarcol">
						<input type="button" id="resetDates">	
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_update')"/></xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</div>
	</form>
</xsl:template>


