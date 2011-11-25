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


<xsl:template match="data">
	<xsl:call-template name="yui_booking_i18n"/>
	<xsl:apply-templates select="lists"/>
	<xsl:apply-templates select="paging"/>
	<xsl:apply-templates select="datatable"/> 
</xsl:template>

<xsl:template match="lists">

<div style="background: none repeat scroll 0 0 #EDF5FF;border: 1px solid #243356;margin: 20px;padding: 20px;">
		<select id="control_area_list" name="control_area_list">
			<xsl:for-each select="control_area_list">
				<xsl:variable name="control_area_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$control_area_id}">
					<xsl:value-of select="title"/>
				</option>			
		    </xsl:for-each>
		</select>
		
		<select id="control_list" name="control_list">
			<xsl:for-each select="control_list">
				<xsl:variable name="control_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$control_id}">
					<xsl:value-of select="title"/>
				</option>
										
		    </xsl:for-each>
		</select>
	</div>
	
	<div style="background: none repeat scroll 0 0 #EDF5FF;border: 1px solid #243356;margin: 20px;padding: 20px;">
		<select id="building_types" name="building_types">
			<xsl:for-each select="building_types">
				<xsl:variable name="building_type_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$building_type_id}">
					<xsl:value-of select="name"/>
				</option>
										
		    </xsl:for-each>
		</select>
		
		<select id="category_types" name="category_types">
			<xsl:for-each select="category_types">
				<xsl:variable name="category_type_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$category_type_id}">
					<xsl:value-of select="name"/>
				</option>
										
		    </xsl:for-each>
		</select>
		
		<select id="district_list" name="district_list">
			<xsl:for-each select="district_list">
				<xsl:variable name="district_list_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$district_list_id}">
					<xsl:value-of select="name"/>
				</option>
										
		    </xsl:for-each>
		</select>
		
		<select id="part_of_town_list" name="part_of_town_list">
			<xsl:for-each select="part_of_town_list">
				<xsl:variable name="part_of_town_list_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$part_of_town_list_id}">
					<xsl:value-of select="name"/>
				</option>
										
		    </xsl:for-each>
		</select>
		<select id="responsibility_roles" name="responsibility_roles">
			<xsl:for-each select="responsibility_roles_list">
				<xsl:variable name="responsibility_roles_list_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$responsibility_roles_list_id}">
					<xsl:value-of select="name"/>
				</option>
										
		    </xsl:for-each>
		</select>
	</div>



	
</xsl:template>



	

<xsl:template match="datatable">
    <div id="paginator"/>
    <div id="datatable-container"/>
  	<xsl:call-template name="datasource-definition" />
</xsl:template>


<xsl:template name="datasource-definition">
	<script>
		YAHOO.namespace('controller');
	<!-- 
		YAHOO.controller.setupDatasource = function() {
			<xsl:if test="source">
	            YAHOO.controller.dataSourceUrl = '<xsl:value-of select="source"/>';
					YAHOO.controller.initialSortedBy = false;
					YAHOO.controller.initialFilters = false;
					<xsl:if test="sorted_by">
						YAHOO.controller.initialSortedBy = {key: '<xsl:value-of select="sorted_by/key"/>', dir: '<xsl:value-of select="sorted_by/dir"/>'};
					</xsl:if>
	        </xsl:if>
*/
 -->
 
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

		setDataSource('<xsl:value-of select="source"/>', YAHOO.controller.columnDefs, null, null, 'datatable-container', '_form', '_paginator', null); 
		
	</script>
	 
</xsl:template>