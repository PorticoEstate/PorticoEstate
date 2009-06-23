<xsl:include href="rental/templates/base/common.xsl"/>

<xsl:template name="pageForm" xmlns:php="http://php.net/xsl">
</xsl:template>

<xsl:template name="pageContent">
	<xsl:apply-templates select="data"/>
</xsl:template>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h3><xsl:value-of select="php:function('lang', 'rental_common_tenant')" />: <xsl:value-of select="tenant/name"/></h3>
	<div id="composite_edit_tabview" class="yui-navset">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
			<xsl:apply-templates select="tenant"/>
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

<xsl:template match="tenant" xmlns:php="http://php.net/xsl">
	<div id="details">
		<form action="#" method="post">
			<dl class="proplist-col">
				<dt>
					<label for="personal_identification_number"><xsl:value-of select="php:function('lang', 'rental_tenant_ssn')" /> / <xsl:value-of select="php:function('lang', 'rental_tenant_organisation_number')" /></label>
				</dt>
				<dd>
					<input type="text" name="personal_identification_number" id="personal_identification_number"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="personal_identification_number"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="firstname"><xsl:value-of select="php:function('lang', 'rental_common_firstname')" /></label>
				</dt>
				<dd>
					<input type="text" name="firstname" id="firstname"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="firstname"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="lastname"><xsl:value-of select="php:function('lang', 'rental_common_lastname')" /></label>
				</dt>
				<dd>
					<input type="text" name="lastname" id="lastname"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="lastname"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="title"><xsl:value-of select="php:function('lang', 'rental_common_title')" /></label>
				</dt>
				<dd>
					<input type="text" name="title" id="title"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="title"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="company_name"><xsl:value-of select="php:function('lang', 'rental_common_company')" /></label>
				</dt>
				<dd>
					<input type="text" name="company_name" id="company_name"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="company_name"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="department"><xsl:value-of select="php:function('lang', 'rental_common_department')" /></label>
				</dt>
				<dd>
					<input type="text" name="department" id="department"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="department"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="address1"><xsl:value-of select="php:function('lang', 'rental_rc_address')" /></label>
				</dt>
				<dd>
					<input type="text" name="address1" id="address1"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="address1"/></xsl:attribute></input>
					<br/>
					<input type="text" name="address2" id="address2"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="address2"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="postal_code"><xsl:value-of select="php:function('lang', 'rental_common_postal_code_place')" /></label>
				</dt>
				<dd>
					<input type="text" name="postal_code" id="postal_code" size="4"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="postal_code"/></xsl:attribute></input>
					<input type="text" name="place" id="place"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="place"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="phone"><xsl:value-of select="php:function('lang', 'rental_common_phone')" /></label>
				</dt>
				<dd>
					<input type="text" name="phone" id="phone"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="phone"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="fax"><xsl:value-of select="php:function('lang', 'rental_common_fax')" /></label>
				</dt>
				<dd>
					<input type="text" name="fax" id="fax"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="fax"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="email"><xsl:value-of select="php:function('lang', 'rental_common_email')" /></label>
				</dt>
				<dd>
					<input type="text" name="email" id="email"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="email"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="url"><xsl:value-of select="php:function('lang', 'rental_common_url')" /></label>
				</dt>
				<dd>
					<input type="text" name="url" id="url"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="url"/></xsl:attribute></input>
				</dd>
			</dl>
			<dl class="proplist-col">
				<dt>
					<label for="type_id"><xsl:value-of select="php:function('lang', 'rental_common_tenant_type')" /></label>
				</dt>
				<dd>
					<input type="text" name="type_id" id="type_id"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="type_id"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="post_bank_account_number"><xsl:value-of select="php:function('lang', 'rental_common_post_bank_account_number')" /></label>
				</dt>
				<dd>
					<input type="text" name="post_bank_account_number" id="post_bank_account_number"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="post_bank_account_number"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="account_number"><xsl:value-of select="php:function('lang', 'rental_common_account_number')" /></label>
				</dt>
				<dd>
					<input type="text" name="account_number" id="account_number"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="account_number"/></xsl:attribute></input>
				</dd>
				<dt>
					<label for="reskontro"><xsl:value-of select="php:function('lang', 'rental_common_reskontro')" /></label>
				</dt>
				<dd>
					<input type="text" name="reskontro" id="reskontro"><xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if><xsl:attribute name="value"><xsl:value-of select="reskontro"/></xsl:attribute></input>
				</dd>
			</dl>
			<div class="form-buttons">
				<xsl:if test="../access = 1">
					<input type="submit">	
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'rental_rc_save')"/></xsl:attribute>
					</input>
				</xsl:if>
				<a class="cancel">
					<xsl:attribute name="href"><xsl:value-of select="../cancel_link"></xsl:value-of></xsl:attribute>
      				<xsl:value-of select="php:function('lang', 'rental_rc_cancel')"/>
				</a>
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


