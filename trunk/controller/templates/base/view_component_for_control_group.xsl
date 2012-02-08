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

<xsl:template name="view_component_for_control_group">
	<!-- IMPORTANT!!! Loads YUI javascript -->
	<xsl:call-template name="common"/>

	<div class="yui-content">
		<div id="control_details">
			<div style="margin: 10px;padding: 10px; width: 25%;">
				
				<!-- When control area is chosen, an ajax request is executed. 
					 The operation fetches controls groups from db and populates the control group list.
					 The ajax operation is handled in ajax.js 
				 --> 
				 <select style="float:left;" id="control_group_area_list" name="control_group_area_list">
					<xsl:for-each select="control_area_array">
						<xsl:variable name="control_area_id"><xsl:value-of select="id"/></xsl:variable>
						<option value="{$control_area_id}">
							<xsl:value-of select="name"/>
						</option>			
					</xsl:for-each>
				 </select>
				 
				 <form id="loc_form" action="" method="GET">
			
					<select id="control_group_id" name="control_group_id" style="width: 250px;">
					<xsl:choose>
						<xsl:when test="control_group_array/child::node()">
							<xsl:for-each select="control_group_array">
								<xsl:variable name="control_group_id"><xsl:value-of select="id"/></xsl:variable>
								<option value="{$control_group_id}">
									<xsl:value-of select="group_name"/>
								</option>				
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<option>
								Ingen kontrollgrupper
							</option>
						</xsl:otherwise>
					</xsl:choose>
						
					</select>
				</form>
			</div>
			
			<div id="addedProperties">
				<ul id="locations_for_control_group" name="locations_for_control_group">
					<xsl:for-each select="locations_for_control">
						<li>
							<div><xsl:value-of select="id"/></div>
							<div><xsl:value-of select="title"/></div>
							<div><xsl:value-of select="location_code"/></div>
						</li>			
					</xsl:for-each>
				</ul>
			</div>
			
			<iframe id="yui-history-iframe" src="phpgwapi/js/yahoo/history/assets/blank.html" style="position:absolute;top:0; left:0;width:1px; height:1px;visibility:hidden;"></iframe>
			<input id="yui-history-field" type="hidden"/>
			
			<xsl:apply-templates select="locations_table"/>
			<xsl:call-template name="yui_booking_i18n"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="locations_table" xmlns:php="http://php.net/xsl">
	
	<div id="loc_paginator"/>
	<div style="margin:20px;" id="locations-container"/>
  	<xsl:call-template name="locations-definition" />
</xsl:template>

<xsl:template name="locations-definition">
	<script>
	 
 		YAHOO.controller.columnDefs = [
				<xsl:for-each select="//locations_table/field">
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
			
		var loc_source = '<xsl:value-of select="source"/>';
		var loc_columnDefs = YAHOO.controller.columnDefs;
		var loc_form = 'loc_form';
		var loc_filters = ['control_id'];
		var loc_container = 'locations-container';
		var loc_table_id = 'locations_table';
		var loc_data_table_pag = 'loc_paginator';
	
		setDataSource(loc_source, loc_columnDefs, loc_form, loc_filters, loc_container, loc_data_table_pag, loc_table_id, null, null, null); 
		
	</script>
	 
</xsl:template>
