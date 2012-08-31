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

<xsl:template name="add_component_to_control_group" xmlns:php="http://php.net/xsl">
	<!-- IMPORTANT!!! Loads YUI javascript -->
	<xsl:call-template name="common"/>

	<div class="yui-content">
		<div id="control_group_details">
			<xsl:call-template name="yui_phpgw_i18n"/>
			<xsl:apply-templates select="control_group_filters" />
			<xsl:apply-templates select="filter_form" />
			<xsl:apply-templates select="paging"/>
			<xsl:apply-templates select="datatable"/>
			<xsl:apply-templates select="form/list_actions"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="control_group_filters" name="control_group_filters" xmlns:php="http://php.net/xsl"> 
	
	<div id="select-wrp">
	  <div class="error_msg">Du må velge kontrollgruppe før du kan legge til bygningsdel</div>	
	  <h4 style="margin-bottom:5px;">Velg kontrollgruppe</h4>
		<!-- When control area is chosen, an ajax request is executed. The operation fetches control groups from db and populates the control group list.
			 The ajax opearation is handled in ajax.js --> 
		 <select id="control_group_area_list" name="control_group_area_list" style="float:left;">
			<xsl:for-each select="control_area_array">
				<xsl:variable name="control_area_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$control_area_id}">
					<xsl:value-of select="name"/>
				</option>			
			</xsl:for-each>
		</select>
		 
		 <form id="loc_form" action="" method="GET" style="margin-top:5px;">
			<select id="control_group_id" name="control_group_id">
				<xsl:choose>
					<xsl:when test="control_group_array/child::node()">
						<xsl:for-each select="control_group_array">
							<xsl:variable name="control_group_id"><xsl:value-of select="id"/></xsl:variable>
							<option value="{$control_group_id}">
								<xsl:value-of select="title"/>
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
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
	
	<h4 style="margin-left:20px;margin-bottom:5px;">Velg utstyr</h4>
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
		  <select id="ifc" name="ifc">
		  	<option value="">
				<xsl:value-of select="php:function('lang', 'Choose_component_category')"/>
			</option>
			<option value="0">
				<xsl:value-of select="php:function('lang', 'component_category_internal')"/>
			</option>
			<option value="1">
				<xsl:value-of select="php:function('lang', 'component_category_ifc')"/>
			</option>
		  </select>
		</li>
	  	<li>
		  <select id="bim_type_id" name="bim_type_id">
		  	<option value="">
				<xsl:value-of select="php:function('lang', 'Choose_component_type')"/>
			</option>
			<xsl:for-each select="bim_types">
				<xsl:variable name="bim_type_id"><xsl:value-of select="id"/></xsl:variable>
				<option value="{$bim_type_id}">
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
	<script type="text/javascript">
	<![CDATA[
	function checkAll(myclass)
  	{
		controls = YAHOO.util.Dom.getElementsByClassName(myclass);
		for(i=0;i<controls.length;i++)
		{
			//for class=mychecks, they have to be interchanged
			//checkbox is located within td->div->input. To get the input-object, use controls[i].children[0].children[0]
			if(myclass=='mychecks')
			{
				if(controls[i].children[0].children[0].checked)
				{
					controls[i].children[0].children[0].checked = false;
				}
				else
				{
					controls[i].children[0].children[0].checked = true;
				}
			}
			//for the rest, always id checked
			else
			{
				controls[i].children[0].children[0].checked = true;
			}
		}
	}
	
	function savecomponentToControl()
	{
		var divs = YAHOO.util.Dom.getElementsByClassName('component_submit');
		var mydiv = divs[divs.length-1];

		// styles for dont show
		mydiv.style.display = "none";

		valuesForPHP = YAHOO.util.Dom.getElementsByClassName('mychecks');
		var values_return = ""; //new Array(); 
		
		for(i=0;i<valuesForPHP.length;i++)
		{
			if(valuesForPHP[i].children[0].children[0].checked)
			{
				if(values_return != "")
					values_return +="|"+valuesForPHP[i].parentNode.firstChild.firstChild.firstChild.firstChild.nodeValue+';'+valuesForPHP[i].children[0].children[0].value;
				else
					values_return += valuesForPHP[i].parentNode.firstChild.firstChild.firstChild.firstChild.nodeValue+';'+valuesForPHP[i].children[0].children[0].value;
			}
		}
		
		//alert(document.getElementById('control_id').value);
		var control_group_id_value = document.getElementById('control_group_id').value;

		var returnfield = document.createElement('input');
		returnfield.setAttribute('name', 'values_assign');
		returnfield.setAttribute('type', 'text');
		returnfield.setAttribute('value', values_return);
		mydiv.appendChild(returnfield);
		
		var control_group_id_field = document.createElement('input');
		control_group_id_field.setAttribute('name', 'control_group_id');
		control_group_id_field.setAttribute('type', 'text');
		control_group_id_field.setAttribute('value', control_group_id_value);
		mydiv.appendChild(control_group_id_field); 
		
	}
	]]>
	</script>
	<div id="data_paginator"/>
	<div id="datatable-container"/>
  	<xsl:call-template name="datasource-definition" />
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
  	<xsl:variable name="label_checkAll"><xsl:value-of select="php:function('lang', 'invert_checkboxes')" /></xsl:variable>
  	<div><input type="button" id="select_all" value="{$label_checkAll}" onclick="checkAll('mychecks')"/></div>
  	<form action="#" name="component_form" id="component_form" method="post">
  		<div class="component_submit"><input type="submit" name="save_component" id="save_component" value="{$label_submit}" onclick="return savecomponentToControl()"/></div>
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
		var main_filters = ['bim_type_id'];
		var main_container = 'datatable-container';
		var main_table_id = 'datatable';
		var main_pag = 'data_paginator';
		var related_table = new Array('locations_table');
	
		setDataSource(main_source, main_columnDefs, main_form, main_filters, main_container, main_pag, main_table_id, related_table ); 
		
	</script>
	 
</xsl:template>
