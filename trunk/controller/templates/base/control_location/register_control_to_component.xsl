<!-- $Id: dimb_role_user.xsl 9320 2012-05-08 18:07:51Z sigurdne $ -->

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
	<style type="text/css">
	#box { width: 200px; height: 5px; background: blue; }
	select { width: 200px; }
	.row_on,.th_bright
	{
		background-color: #CCEEFF;
	}

	.row_off
	{
		background-color: #DDF0FF;
	}

	</style>

	<xsl:call-template name="invoice" />
	<div id="popupBox"></div>	
	<div id="curtain"></div>
</xsl:template>

<xsl:template name="invoice" xmlns:php="http://php.net/xsl">
	<!-- loads translations into array for use with javascripts -->
	<!--
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'edit')"/>;
	</script>
	-->

	<div class="yui-content">
		<div id="invoice-layout">
				<div class="header">
					<h2><xsl:value-of select="php:function('lang', 'invoice')"/></h2>
				</div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
				<div class="body">
					<div id="voucher_details">
						<!--<xsl:call-template name="yui_phpgw_i18n"/>-->
						<table align = "center" width="95%">
							<xsl:apply-templates select="filter_form" />
						</table>
					  	<form action="{update_action}" name="acl_form" id="acl_form" method="post">
							<table align = "center" width="95%">
								<tr>
									<td colspan = '6'>
										<xsl:apply-templates select="paging"/>
										<xsl:apply-templates select="datatable"/>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<tr>
	<td colspan = '6'>
	<table>
	<tr>
		<td>
			<xsl:value-of select="php:function('lang', 'control area')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'control id')" />
		</td>
	</tr>
	  <tr id="filter1">
		<td>
		  <select id="control_area_id" name="control_area_id">
			<xsl:apply-templates select="control_area_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="control_id" name="control_id">
			<xsl:apply-templates select="control/options"/>
		  </select>
		</td>		
	  </tr>
	<tr>
		<td>
			<xsl:value-of select="php:function('lang', 'entity')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'category')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'district')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'part of town')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'property')" />
		</td>
		<td>
			<xsl:value-of select="php:function('lang', 'building')" />
		</td>
		<td >
			<xsl:value-of select="php:function('lang', 'search')" />
		</td>
	</tr>
	  <tr id="filter2">
		<td>
		  <select id="entity_id" name="entity_id">
			<xsl:apply-templates select="entity_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="cat_id" name="cat_id">
			<xsl:apply-templates select="category_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="district_id" name="district_id">
			<xsl:apply-templates select="district_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="part_of_town_id" name="part_of_town_id">
			<xsl:apply-templates select="part_of_town_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="loc1" name="loc1">
			<xsl:apply-templates select="loc1_list/options"/>
		  </select>
		</td>		
		<td>
		  <select id="loc2" name="loc2">
			<xsl:apply-templates select="loc2_list/options"/>
		  </select>
		</td>
		<td>
			<xsl:variable name="lang_search"><xsl:value-of select="php:function('lang', 'Search')" /></xsl:variable>
			<input type="button" id = "search" name="search" value="{$lang_search}" title = "{$lang_search}" />
		</td>	  		
	  </tr>
	  </table>
	  </td>
	  </tr>
</xsl:template>




<xsl:template match="datatable" xmlns:php="http://php.net/xsl">
	<div id="paging_0"/>
	<div id="datatable-container_0"/>

	<div id="data_paginator"/>
	<div id="datatable-container"/>
	
  	<xsl:call-template name="datasource-definition" />
	<div id="receipt"></div>
  	<xsl:variable name="label_submit"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
	<div class="row_on"><input type="submit" name="update_acl" id="frm_update_acl" value="{$label_submit}"/></div>
</xsl:template>

<xsl:template name="datasource-definition" xmlns:php="http://php.net/xsl">

		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="//property_js"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
			var td_count = <xsl:value-of select="//td_count"/>;

			<xsl:for-each select="//datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						edit_action:  <xsl:value-of select="edit_action"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>
			<xsl:for-each select="//myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="//myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
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

