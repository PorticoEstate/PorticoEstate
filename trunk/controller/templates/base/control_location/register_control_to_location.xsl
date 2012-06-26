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

<xsl:template name="register_control_to_location" xmlns:php="http://php.net/xsl">
	<!-- IMPORTANT!!! Loads YUI javascript -->
	<xsl:call-template name="common"/>

	<div class="yui-content">
		<div>
			<xsl:call-template name="yui_phpgw_i18n"/>
			<xsl:call-template name="control_filters" />
			<xsl:apply-templates select="filter_form" />
			<xsl:apply-templates select="paging"/>
			<xsl:apply-templates select="datatable"/>
			<xsl:apply-templates select="form/list_actions"/>
		</div>
	</div>
</xsl:template>

<xsl:template name="control_filters" xmlns:php="http://php.net/xsl">
	
	<div id="choose_control">
		<!-- 
			When control area is chosen, an ajax request is executed. 
			The operation fetches controls from db and populates the control list.
			The ajax opearation is handled in ajax.js 
		-->
		 <div class="error_msg">Du må velge kontroll før du kan legge til bygg</div>
		 <label>Velg kontroll</label> 
		 <select id="control_area_list" name="control_area_list">
			<option value="">Velg kontrollområde</option>
			<xsl:for-each select="control_areas_array2">
				<option value="{id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:for-each>
		  </select>
		  
		 <form id="loc_form" action="" method="GET">
			<select id="control_id" name="control_id">
				<xsl:choose>
					<xsl:when test="control_array/child::node()">
						<xsl:for-each select="control_array">
							<xsl:variable name="control_id"><xsl:value-of select="id"/></xsl:variable>
							<option value="{$control_id}">
								<xsl:value-of select="title"/>
							</option>				
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<option>
							Ingen kontroller
						</option>
					</xsl:otherwise>
				</xsl:choose>
			</select>
		</form>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">

	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
		<xsl:call-template name="filter_list"/>
	</form>
	
	<form id="update_table_dummy" method='POST' action='' ></form>

</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<div id="choose-location">
		<label>Velg bygg/eiendom</label>
	  <ul id="filters">
	  	<li>
			<input type="hidden" id="hidden_control_id" name="control_id">
				<xsl:attribute name="value">
					<xsl:value-of select="//control_id"/>
				</xsl:attribute>
			</input>

			<input type="hidden" id="hidden_control_area_id" name="control_area_id">
			</input>
	  	
		  <select id="type_id" name="type_id">
			<xsl:for-each select="building_types">
				<xsl:variable name="building_type_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$building_type_id}">
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		  </select>
		</li>
		<li>
		  <select id="cat_id" name="cat_id">
			<xsl:for-each select="category_types">
				<xsl:variable name="category_type_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$category_type_id}">
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		  </select>
		</li>
		<li>
		  <select id="district_id" name="district_id">
			<xsl:for-each select="district_list">
				<xsl:variable name="district_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$district_id}">
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		  </select>
		</li>
		<li>
		  <select id="part_of_town_id" name="part_of_town_id">
			<xsl:for-each select="part_of_town_list">
				<xsl:variable name="part_of_town_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$part_of_town_id}">
					<xsl:value-of select="name"/>
				</option>
			</xsl:for-each>
		  </select>
		</li>		
	  </ul>
	  <ul id="search_list">
		  <li>
		  	<input type="text" name="query" />
		  </li>
		  <li>
		  	<xsl:variable name="lang_search"><xsl:value-of select="php:function('lang', 'Search')" /></xsl:variable>
		  	<input type="submit" name="search" value="{$lang_search}" title = "{$lang_search}" />
		  </li>	  		
	  </ul>
	</div>
</xsl:template>

<xsl:template match="datatable" xmlns:php="http://php.net/xsl">
	<div id="data_paginator"/>
	<div class="error_msg">Du må velge bygg før du kan legge til en kontroll</div>
	<div id="datatable-container"/>
	
  	<xsl:call-template name="datasource-definition" />
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
  	<xsl:variable name="label_checkAll"><xsl:value-of select="php:function('lang', 'invert_checkboxes')" /></xsl:variable>
  	<div><input type="button" id="select_all" value="{$label_checkAll}" onclick="checkAll('mychecks')"/></div>
  	<form action="#" name="location_form" id="location_form" method="post">
  		<div class="location_submit"><input type="submit" name="save_location" id="save_location" value="{$label_submit}" onclick="return saveLocationToControl()"/></div>
  	</form>
</xsl:template>


<xsl:template name="datasource-definition" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.namespace('controller');
	 
 		YAHOO.controller.columnDefs = [
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
		var main_columnDefs = YAHOO.controller.columnDefs;
		var main_form = 'queryForm';
		var main_filters = ['type_id', 'cat_id', 'district_id', 'part_of_town_id', 'responsibility_roles_list', 'control_area_list', 'control_id'];
		var main_container = 'datatable-container';
		var main_table_id = 'datatable';
		var main_pag = 'data_paginator';
		var related_table = new Array('locations_table');
	
		setDataSource(main_source, main_columnDefs, main_form, main_filters, main_container, main_pag, main_table_id, related_table ); 
		
	</script>
	 
</xsl:template>
