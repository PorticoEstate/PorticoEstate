<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.util.Event.addListener(
			'ctrl_add_rental_composite', 
			'click', 
			function(e)
			{    	
	    		YAHOO.util.Event.stopEvent(e);
	    		newName = document.getElementById('ctrl_add_rental_composite_name').value;
	        	window.location = 'index.php?menuaction=rental.uicomposite.add&amp;rental_composite_name=' + newName;
    		}
    	);
	   </script>
	<div id="toolbar">
		<table class="pageToolbar">
			<tr>
				<td class="toolbarlabel">
					<label><xsl:value-of select="php:function('lang', 'rental_rc_toolbar_new')"/></label>
				</td>
				<td class="pageToolbarInput">
					<label for="ctrl_add_rental_composite_name"><xsl:value-of select="php:function('lang', 'rental_rc_name')"/></label>
					<input type="text" id="ctrl_add_rental_composite_name"/>
				</td>
				<td id="pageToolbarSubmit">
					<input type="submit" name="ctrl_add_rental_composite" id="ctrl_add_rental_composite">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'rental_rc_toolbar_functions_new_rc')"/>	
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</div> 
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
  			<xsl:with-param name="filters">['ctrl_toggle_active_rental_composites']</xsl:with-param>
  			<xsl:with-param name="container_name">datatable-container</xsl:with-param>
  			<xsl:with-param name="context_menu_labels">
				['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
				'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
			</xsl:with-param>
			<xsl:with-param name="context_menu_actions">
					['view',
					'edit']	
			</xsl:with-param>
			<xsl:with-param name="source">index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json</xsl:with-param>
			<xsl:with-param name="columnDefinitions">
  				[{
					key: "id",
					label: "<xsl:value-of select="php:function('lang', 'rental_rc_serial')"/>	",
					sortable: true
				},
				{
					key: "name",
					label: "<xsl:value-of select="php:function('lang', 'rental_rc_name')"/>	",
				    sortable: true
				},
				{
					key: "adresse1",
					label: "<xsl:value-of select="php:function('lang', 'rental_rc_address')"/>	",
				    sortable: false
				},
				{
					key: "gab_id",
					label: "<xsl:value-of select="php:function('lang', 'rental_rc_propertyident')"/>	",
				    sortable: true
				},
				{
					key: "actions",
					label: "unselectable",
				    sortable: true
				}]
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
						<xsl:value-of select="php:function('lang', 'rental_rc_toolbar_functions')"/>
					</td>
					<td class="toolbarcol" id="functionsContainer">
						<input type="button" id="dt-options-link" name="dt-options-link">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'rental_rc_toolbar_functions_select_columns')"/>
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
						<xsl:value-of select="php:function('lang', 'rental_rc_search_options')"/>
					</td>
					<td class="toolbarcol" >
						<label class="toolbar_element_label" for="ctrl_search_query">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_for')"/>
						</label>
						<input id="ctrl_search_query" type="text" name="query" autocomplete="off" />
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_search_option">
							<xsl:value-of select="php:function('lang', 'rental_rc_search_where')"/>
							<select name="search_option" id="ctrl_search_option">
								<option value="all"><xsl:value-of select="php:function('lang', 'rental_rc_all')"/></option>
								<option value="id"><xsl:value-of select="php:function('lang', 'rental_rc_serial')"/></option>
								<option value="name"><xsl:value-of select="php:function('lang', 'rental_rc_name')"/></option>
								<option value="address"><xsl:value-of select="php:function('lang', 'rental_rc_address')"/></option>
								<option value="gab"><xsl:value-of select="php:function('lang', 'gab')"/></option>
								<option value="ident"><xsl:value-of select="php:function('lang', 'rental_rc_gab')"/></option>
								<option value="property_id"><xsl:value-of select="php:function('lang', 'rental_rc_property_id')"/></option>
							</select>
						</label>
					</td>
					<td class="toolbarcol" id="searchSubmitContainer">
						<input type="submit" id="ctrl_search_button" name="ctrl_search_button">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'rental_rc_search')"/>
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
					<td class="toolbarcol" id="filterContainer">
						<label class="toolbar_element_label" for="ctrl_toggle_active_rental_composites">Tilgjengelighet</label>
						<select name="is_active" id="ctrl_toggle_active_rental_composites">
							<option value="active"><xsl:value-of select="php:function('lang', 'rental_rc_available')"/></option>
							<option value="non_active"><xsl:value-of select="php:function('lang', 'rental_rc_not_available')"/></option>
							<option value="both"><xsl:value-of select="php:function('lang', 'rental_rc_all')"/></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</form>
</xsl:template>


