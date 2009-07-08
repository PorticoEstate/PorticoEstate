<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">

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
  			<xsl:with-param name="filters">['ctrl_toggle_active_rental_composites', 'ctrl_toggle_occupancy_of_rental_composites']</xsl:with-param>
  			<xsl:with-param name="container_name">datatable-container</xsl:with-param>
  			<xsl:with-param name="context_menu_labels">
				['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
				'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
			</xsl:with-param>
			<xsl:with-param name="context_menu_actions">
					['view',
					'edit']	
			</xsl:with-param>
			<xsl:with-param name="source">index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=orphan_units</xsl:with-param>
			<xsl:with-param name="columnDefinitions">
					[{
						key: "location_code",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_id')"/>",
					    sortable: true
					},
					{
						key: "loc1_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_property')"/>",
					    sortable: true
					},
					{
						key: "loc2_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_building')"/>",
					    sortable: true
					},
					{
						key: "loc3_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_floor')"/>",
					    sortable: true
					},
					{
						key: "loc4_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_section')"/>",
					    sortable: true
					},
					{
						key: "loc5_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_room')"/>",
					    sortable: true
					},
					{
						key: "address",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_address')"/>",
					    sortable: true
					},
					{
						key: "area_gros",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_area_gros')"/>"
					},
					{
						key: "area_net",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_area_net')"/>"
					},
					{
						key: "actions",
						hidden: 1
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
								<option value="property_id"><xsl:value-of select="php:function('lang', 'rental_rc_property_id')"/></option>
								<option value="property"><xsl:value-of select="php:function('lang', 'rental_rc_name')"/></option>
								<option value="building"><xsl:value-of select="php:function('lang', 'rental_rc_address')"/></option>
								<option value="floor"><xsl:value-of select="php:function('lang', 'gab')"/></option>
								<option value="section"><xsl:value-of select="php:function('lang', 'rental_rc_gab')"/></option>
								<option value="room"><xsl:value-of select="php:function('lang', 'rental_rc_room')"/></option>
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
		
	</form>
</xsl:template>