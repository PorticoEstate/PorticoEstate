<!-- $Id: pending_users.xsl 8854 2012-02-14 07:54:40Z vator $ -->

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

<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="pending_for_approval_tabview">
	<div class="identifier-header">
		<h1><xsl:value-of select="php:function('lang', 'Pending for approval')"/></h1>
	</div>
	<xsl:call-template name="pending_users" />
</div>
	
</xsl:template>

<xsl:template name="pending_users" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'edit')"/>;
	</script>

	<div class="yui-content">
		<div id="control_details">
			<xsl:call-template name="yui_phpgw_i18n"/>
			<xsl:apply-templates select="filter_form" />
			<xsl:apply-templates select="paging"/>
			<xsl:apply-templates select="datatable"/>
			<xsl:apply-templates select="form/list_actions"/>
		</div>
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
	  <ul id="filters">
		<li>
		  <select id="status_id" name="status_id">
			<xsl:apply-templates select="status_list/options"/>
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
</xsl:template>

<xsl:template match="datatable" xmlns:php="http://php.net/xsl">
	<div id="data_paginator"/>
	<div class="error_msg" style="margin-left:20px;">Du må velge bruker for godkjenning</div>
	<div id="datatable-container"/>
	
  	<xsl:call-template name="datasource-definition" />
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
  	<xsl:variable name="label_process"><xsl:value-of select="php:function('lang', 'process')" /></xsl:variable>
  	<xsl:variable name="label_checkAll"><xsl:value-of select="php:function('lang', 'invert_checkboxes')" /></xsl:variable>
  	<div><input type="button" id="select_all" value="{$label_checkAll}" onclick="checkAll('mychecks')"/></div>
  	
  	<form action="#" name="user_form" id="user_form" method="post">
  		<div class="user_submit">
  			<input type="submit" name="values[save_user]" id="save_user" value="{$label_submit}" onclick="return onSave()"/>
  			<input type="submit" name="values[process_user]" id="process_user" value="{$label_process}" onclick="return onSave()"/>
  		</div>
  	</form>
</xsl:template>


<xsl:template name="datasource-definition" xmlns:php="http://php.net/xsl">
	<script>
		YAHOO.namespace('portico');
	 
 		YAHOO.portico.columnDefs = [
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
//		var main_columnDefs = YAHOO.portico.columnDefs;
//		var main_form = 'queryForm';
//		var main_filters = ['status_id', 'responsibility_roles_list'];
//		var main_container = 'datatable-container';
//		var main_table_id = 'datatable';
//		var main_pag = 'data_paginator';
//		var related_table = new Array('users_table');
	
//		setDataSource(main_source, main_columnDefs, main_form, main_filters, main_container, main_pag, main_table_id, related_table ); 
		
	</script>
	 
</xsl:template>

<!-- options for use with select-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

