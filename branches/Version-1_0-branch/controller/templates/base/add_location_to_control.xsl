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

<xsl:template name="add_location_to_control">
	<!-- IMPORTANT!!! Loads YUI javascript -->
	<xsl:call-template name="common"/>

	<div class="yui-content">
		<div id="control_details">
			<xsl:call-template name="yui_booking_i18n"/>
			<xsl:apply-templates select="control_filters" />
			<xsl:apply-templates select="filter_form" />
			<xsl:apply-templates select="paging"/>
			<xsl:apply-templates select="datatable"/>
			<xsl:apply-templates select="form/list_actions"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="control_filters" name="control_filters">
	<div style="margin: 10px;padding: 10px; width: 25%;">
		
		<!-- When control area is chosen, an ajax request is executed. The operation fetches controls from db and populates the control list.
			 The ajax opearation is handled in ajax.js --> 
		 <select style="float:left;" id="control_area_list" name="control_area_list">
			<xsl:for-each select="control_area_array">
				<xsl:variable name="control_area_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$control_area_id}">
					<xsl:value-of select="title"/>
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

<xsl:template match="filter_form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
        <xsl:call-template name="filter_list"/>
	</form>
	
	<form id="update_table_dummy" method='POST' action='' >
	</form>

</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
    <div>
	  <ul id="filters">
	  	<li>
		  <select id="type_id" name="type_id">
		  	<option value="">
				<xsl:value-of select="php:function('lang', 'Choose_building_type')"/>
			</option>
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
		  	<option value="">
				<xsl:value-of select="php:function('lang', 'Choose_building_category')"/>
			</option>
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
		  	<option value="">
					<xsl:value-of select="php:function('lang', 'Choose_district')"/>
			</option>
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
		  	<option value="">
					<xsl:value-of select="php:function('lang', 'Choose_part_of_town')"/>
			</option>
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

<xsl:template match="datatable">
    <div id="data_paginator"/>
    <div id="datatable-container"/>
  	<xsl:call-template name="datasource-definition" />
</xsl:template>


<xsl:template name="datasource-definition">
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
		var main_filters = ['type_id', 'cat_id', 'district_id', 'part-of_town_list', 'responsibility_roles_list'];
		var main_container = 'datatable-container';
		var main_table_id = 'datatable';
		var main_pag = 'data_paginator';
		var related_table = new Array('locations_table');
	
		setDataSource(main_source, main_columnDefs, main_form, main_filters, main_container, main_pag, main_table_id, related_table ); 
		
	</script>
	 
</xsl:template>