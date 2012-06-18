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

<xsl:template match="data">
	
	<div id="addedProperties">
		<ul id="locations_for_control" name="locations_for_control">
			<xsl:for-each select="locations_for_control">
				<li>
					<div><xsl:value-of select="id"/></div>
					<div><xsl:value-of select="title"/></div>
					<div><xsl:value-of select="location_code"/></div>
				</li>			
			</xsl:for-each>
		</ul>
	</div>
	
	<iframe id="yui-history-iframe" src="phpgwapi/js/yahoo/history/assets/blank.html" ></iframe>
	<input id="yui-history-field" type="hidden"/>
	<xsl:call-template name="yui_booking_i18n"/>
	<xsl:apply-templates select="form" />
	<xsl:apply-templates select="paging"/>
	<div id="list_flash">
		<xsl:call-template name="msgbox"/>
	</div>
	<xsl:apply-templates select="datatable"/> 
	<xsl:apply-templates select="form/list_actions"/>
	
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
		<xsl:apply-templates select="toolbar"/>
	</form>

	<form id="update_table_dummy" method='POST' action='' >
	</form>

</xsl:template>


<xsl:template match="toolbar">
	<div id="toolbar">
		  <table class='yui-skin-sam'>
			<tr>
		<xsl:for-each select="item">
			<xsl:variable name="filter_key" select="concat('filter_', name)"/>
			<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
			<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
		
			<xsl:choose>
				<xsl:when test="type = 'date-picker'">
					<td>
					<div class="date-picker">
					<input id="filter_{name}" name="filter_{name}" type="text">
							<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key]"/></xsl:attribute>
						</input>
				</div>
					</td>
				</xsl:when>
				<xsl:when test="type = 'autocomplete'">
					<td>
						<div>
							<input id="filter_{name}_name" name="filter_{name}_name" type="text">
								<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key_name]"/></xsl:attribute>
							</input>
							<input id="filter_{name}_id" name="filter_{name}_id" type="hidden">
								<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key_id]"/></xsl:attribute>
							</input>
							<div id="filter_{name}_container"/>
						</div>
						<script type="text/javascript">	
						YAHOO.util.Event.onDOMReady(function() {
						   var name = "<xsl:value-of select="name"/>";
							var ui = "<xsl:value-of select="ui"/>";

							var itemSelectCallback = false;
							<xsl:if test="onItemSelect">
								itemSelectCallback = <xsl:value-of select="onItemSelect"/>;
							</xsl:if>

							var onClearSelectionCallback = false;
							<xsl:if test="onClearSelection">
								onClearSelectionCallback = <xsl:value-of select="onClearSelection"/>;
							</xsl:if>

							var requestGenerator = false;
							<xsl:if test="requestGenerator">
								requestGenerator = <xsl:value-of select="requestGenerator"/>;
							</xsl:if>

							<![CDATA[
							var oAC = YAHOO.portico.autocompleteHelper('index.php?menuaction=booking.ui'+ui+'.index&phpgw_return_as=json&', 
															 'filter_'+name+'_name', 'filter_'+name+'_id', 'filter_'+name+'_container');

							if (requestGenerator) {
								oAC.generateRequest = requestGenerator;
							}

							if (itemSelectCallback) {
								oAC.itemSelectEvent.subscribe(itemSelectCallback);
							}

							YAHOO.util.Event.addBlurListener('filter_'+name+'_name', function()
							{
								if (YAHOO.util.Dom.get('filter_'+name+'_name').value == "")
								{
									YAHOO.util.Dom.get('filter_'+name+'_id').value = "";
									if (onClearSelectionCallback) {
										onClearSelectionCallback();
									}
								}
							});

							YAHOO.portico.addPreSerializeQueryFormListener(function(form)
							{
								if (YAHOO.util.Dom.get('filter_'+name+'_name').value == "")
								{
									YAHOO.util.Dom.get('filter_'+name+'_id').value = "";
								} 
							});
							]]>
						});
						</script>
					</td>
				</xsl:when>
				<xsl:when test="type = 'filter'">
					<td>
					<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>
					
					<select id="{$name}" name="{$name}" onMouseout="window.status='';return true;">
						<xsl:for-each select="list">
							<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
							<xsl:if test="id = 'NEW'">
								<option value="{$id}" selected="selected">
									<xsl:value-of select="name"/>
								</option>
							</xsl:if>
							<xsl:if test="id != 'NEW'">
								<option value="{$id}">
									<xsl:value-of select="name"/>
								</option>
							</xsl:if>
						</xsl:for-each>
					</select>
					</td>
				</xsl:when>
				<xsl:otherwise>
					<td valign="top">
					<input id="innertoolbar">
						<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
						<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
						<xsl:attribute name="onclick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
						<xsl:attribute name="class"><xsl:value-of select="phpgw:conditional(not(class), '', class)"/></xsl:attribute>
					</input>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		  </tr>
			<xsl:if test="item/text and normalize-space(item/text)">
				<thead>
					<tr>
						<xsl:for-each select="item">
							<td>
								<xsl:if test="name">
									<label>
										<xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
										<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
									</label>
								</xsl:if>
							</td>
						</xsl:for-each>
					</tr>
				</thead>
			</xsl:if>
		</table>
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

		setDataSource('<xsl:value-of select="source"/>', YAHOO.controller.columnDefs, 'queryForm', ['building_types', 'cat_id', 'district_id', 'part-of_town_list', 'responsibility_roles_list'], 'datatable-container', '_form', '_paginator', null); 
		
	</script>
	 
</xsl:template>