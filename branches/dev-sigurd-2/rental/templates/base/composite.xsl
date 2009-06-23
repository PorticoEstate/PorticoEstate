<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.util.Event.onDOMReady(
			function()
			{
				initCalendar('available_date', 'calendarPeriodFrom', 'cal1', 'Velg dato');
			}
		);
	</script>
</xsl:template>

<xsl:template name="pageContent">
	<xsl:apply-templates select="data"/>
</xsl:template>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h3><xsl:value-of select="php:function('lang', 'rental_rc_rental_composite')" />: <xsl:value-of select="composite/name"/></h3>
	<div id="composite_edit_tabview" class="yui-navset">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<xsl:apply-templates select="composite"/>
			<div id="elements">
				<xsl:call-template name="datatable_included_areas" />
				<xsl:if test="access = 1">
	    			<xsl:call-template name="datatable_available_areas" />
	    		</xsl:if>
			</div>
			<div id="contracts">
			    <xsl:call-template name="datatable_contracts" />
			</div>
		</div>
	</div>		
</xsl:template>

<xsl:template match="composite" xmlns:php="http://php.net/xsl">
	<div id="details">
		<form action="#" method="post">
			<dl class="proplist-col">
				<dt>
					<label for="name"><xsl:value-of select="php:function('lang', 'rental_rc_name')" /></label>
				</dt>
				<dd>
					<input type="text" name="name" id="name">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="name"/></xsl:attribute>
					</input>
				</dd>
				
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_address')" /></dt>
				<dd>
					<xsl:value-of select="adresse1"/>
					<xsl:if test="adresse2 != ''">
						<br /><xsl:value-of select="adresse2"/>
					</xsl:if>
					<br />
					<xsl:if test="postnummer != '0'">
						<br /><xsl:value-of select="postnummer"/>&#160;<xsl:value-of select="poststed"/>
					</xsl:if>
				</dd>
				
				<dt>
					<label for="address_1"><xsl:value-of select="php:function('lang', 'rental_rc_overridden_address')" /></label>
					/ <label for="house_number"><xsl:value-of select="php:function('lang', 'rental_rc_house_number')" /></label>
				</dt>
				<dd>
					<input type="text" name="address_1" id="address_1">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="address_1"/></xsl:attribute>
					</input>
					<input type="text" name="house_number" id="house_number">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="house_number"/></xsl:attribute>
					</input>
				</dd>				
				<dt>
					<label for="postcode"><xsl:value-of select="php:function('lang', 'rental_rc_post_code')" /></label> / <label for="place"><xsl:value-of select="php:function('lang', 'rental_rc_post_place')" /></label>
				</dt>
				<dd>
					<input type="text" name="postcode" id="postcode" class="postcode">
						<xsl:if test="//access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="postcode"/></xsl:attribute>
					</input>
					<input type="text" name="place" id="place">
						<xsl:if test="//access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:attribute name="value"><xsl:value-of select="place"/></xsl:attribute>
					</input>
				</dd>
			</dl>
			
			<dl class="proplist-col">
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_serial')" /></dt>
				<dd><xsl:value-of select="id"/></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_gros')" /></dt>
				<dd><xsl:value-of select="area_gros"/> m<sup>2</sup></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_area_net')" /></dt>
				<dd><xsl:value-of select="area_net"/> m<sup>2</sup></dd>
				<dt><xsl:value-of select="php:function('lang', 'rental_rc_propertyident')" /></dt>
				<dd><xsl:value-of select="gab_id"/></dd>
				
				<dt>
					<label for="is_active"><xsl:value-of select="php:function('lang', 'rental_rc_available?')" /></label>
				</dt>
				<dd>
					<input type="checkbox" name="is_active" id="is_active">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:if test="is_active = 1">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</dd>
			</dl>
			
			<dl class="rental-description-edit">
				<dt>
					<label for="description"><xsl:value-of select="php:function('lang', 'rental_rc_description')" /></label>
				</dt>
				<dd>
					<textarea name="description" id="description" rows="10" cols="50">
						<xsl:if test="../access = 0">
							<xsl:attribute name="disabled" value="true"/>
						</xsl:if>
						<xsl:value-of select="description"/>
					</textarea>
				</dd>
			</dl>
			<div class="form-buttons">
				<xsl:choose>
					<xsl:when test="../access = 1">
						<input type="submit" name="save_composite">	
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_save')"/></xsl:attribute>
						</input>
						<a class="cancel">
						<xsl:attribute name="href"><xsl:value-of select="../cancel_link"></xsl:value-of></xsl:attribute>
	       					<xsl:value-of select="php:function('lang', 'rental_rc_cancel')"/>
	       				 </a>
	       			</xsl:when>
	       			<xsl:otherwise>
	       				<a class="cancel">
						<xsl:attribute name="href"><xsl:value-of select="../cancel_link"></xsl:value-of></xsl:attribute>
	       					<xsl:value-of select="php:function('lang', 'rental_rc_back')"/>
	       				 </a>
	       			</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template name="datatable_included_areas" xmlns:php="http://php.net/xsl">
	<h3><xsl:value-of select="php:function('lang', 'rental_rc_added_areas')" /></h3>
	<div class="datatable">
		<div id="datatable-container-included-areas">
			<xsl:call-template name="datasource-definition" >
				<xsl:with-param name="number">1</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container-included-areas</xsl:with-param>
				<xsl:with-param name="source">index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=included_areas&amp;id=<xsl:value-of select="composite_id"/></xsl:with-param>
				<xsl:with-param name="context_menu_labels">
					<xsl:choose>
						<xsl:when test="../access = 1">
							['<xsl:value-of select="php:function('lang', 'rental_cm_remove')"/>']
						</xsl:when>
						<xsl:otherwise>
							[]
						</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>
				<xsl:with-param name="context_menu_actions">
					<xsl:choose>
						<xsl:when test="../access = 1">
							['remove_unit']
						</xsl:when>
						<xsl:otherwise>
							[]
						</xsl:otherwise>
					</xsl:choose>
				</xsl:with-param>
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
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_building')"/>"
					},
					{
						key: "loc3_name",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_section')"/>"
					},
					{
						key: "address",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_address')"/>"
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
	</div>
</xsl:template>

<xsl:template name="datatable_available_areas" xmlns:php="http://php.net/xsl">
	<h3><xsl:value-of select="php:function('lang', 'rental_rc_add_area')" /></h3>
	<form id="available_areas_form" method="GET">
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>
					<td class="toolbarlabel">
						<xsl:value-of select="php:function('lang', 'rental_rc_toolbar_filters')"/>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_toggle_level"><xsl:value-of select="php:function('lang', 'rental_rc_level')"/></label>
						<select name="level" id="ctrl_toggle_level">
							<option value="1"><xsl:value-of select="php:function('lang', 'rental_rc_property')"/></option>
							<option value="2" default=""><xsl:value-of select="php:function('lang', 'rental_rc_building')"/></option>
							<option value="3"><xsl:value-of select="php:function('lang', 'rental_rc_floor')"/></option>
							<option value="4"><xsl:value-of select="php:function('lang', 'rental_rc_section')"/></option>
							<option value="5"><xsl:value-of select="php:function('lang', 'rental_rc_room')"/></option>
						</select>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="calendarPeriodFrom"><xsl:value-of select="php:function('lang', 'rental_rc_available_at')"/></label>
						<input type="text" name="available_date" id="available_date" size="10"/>
						<input type="hidden" name="available_date_hidden" id="available_date_hidden"/>
						<div id="calendarPeriodFrom">
						</div>
					</td>
					<td class="toolbarcol">
						<input type="submit">	
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_update')"/></xsl:attribute>
						</input>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<div class="datatable">
		<div id="datatable-container-available-areas">
			<xsl:call-template name="datasource-definition">
				<xsl:with-param name="number">2</xsl:with-param>
				<xsl:with-param name="form">available_areas_form</xsl:with-param>
				<xsl:with-param name="filters">['ctrl_toggle_level']</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container-available-areas</xsl:with-param>
				<xsl:with-param name="source">index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=available_areas&amp;id=<xsl:value-of select="composite_id"/></xsl:with-param>
				<xsl:with-param name="context_menu_labels">
					['<xsl:value-of select="php:function('lang', 'rental_cm_add')"/>']
				</xsl:with-param>
				<xsl:with-param name="context_menu_actions">
					['add_unit']
				</xsl:with-param>
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
						key: "occupied",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_availibility')"/>"
					},
					{
						key: "actions",
						hidden: 1
					}
				]
				</xsl:with-param>
			</xsl:call-template>
		</div>
	</div>
</xsl:template>

<xsl:template name="datatable_contracts" xmlns:php="http://php.net/xsl">
	<h3><xsl:value-of select="php:function('lang', 'rental_rc_contracts_containing_this_composite')" /></h3>
	<form id="contracts_form" method="GET">
		<div id="datatableToolbar">
			<table class="datatableToolbar">
				<tr>
					<td class="toolbarlabel">
						<xsl:value-of select="php:function('lang', 'rental_rc_toolbar_filters')"/>
					</td>
					<td class="toolbarcol">
						<label class="toolbar_element_label" for="ctrl_toggle_contract_status"><xsl:value-of select="php:function('lang', 'rental_rc_contract_status')"/></label>
						<select name="contract_status" id="ctrl_toggle_contract_status">
							<option value="active" default=""><xsl:value-of select="php:function('lang', 'rental_rc_active')"/></option>
							<option value="not_started"><xsl:value-of select="php:function('lang', 'rental_rc_not_started')"/></option>
							<option value="both"><xsl:value-of select="php:function('lang', 'rental_rc_ended')"/></option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<div class="datatable">
		<div id="datatable-container-contracts">
			<xsl:call-template name="datasource-definition">
				<xsl:with-param name="number">3</xsl:with-param>
				<xsl:with-param name="form">contracts_form</xsl:with-param>
				<xsl:with-param name="filters">['ctrl_toggle_contract_status']</xsl:with-param>
				<xsl:with-param name="container_name">datatable-container-contracts</xsl:with-param>
				<xsl:with-param name="source">index.php?menuaction=rental.uicomposite.query&amp;phpgw_return_as=json&amp;type=contracts&amp;id=<xsl:value-of select="composite_id"/></xsl:with-param>
				<xsl:with-param name="context_menu_labels">
					['<xsl:value-of select="php:function('lang', 'rental_cm_show')"/>',
					'<xsl:value-of select="php:function('lang', 'rental_cm_edit')"/>']
				</xsl:with-param>
				<xsl:with-param name="context_menu_actions">
						['view_contract',
						'edit_contract']	
				</xsl:with-param>
				<xsl:with-param name="columnDefinitions">
					[{
						key: "id",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_id')"/>",
					    sortable: true
					},
					{
						key: "date_start",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_date_start')"/>",
					    sortable: true
					},
					{
						key: "date_end",
						label: "<xsl:value-of select="php:function('lang', 'rental_rc_date_end')"/>",
					    sortable: true
					},
					{
						key: "tentant",
						label: "<xsl:value-of select="php:function('lang', 'rental_common_tenant')"/>",
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
	</div>
</xsl:template>


