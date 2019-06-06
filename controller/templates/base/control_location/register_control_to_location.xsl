<!-- $Id$ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
	<div class="yui-navset yui-navset-top" id="control_location_tabview">
		<div class="identifier-header">
			<h1>
				<xsl:value-of select="php:function('lang', 'locations for control')"/>
			</h1>
		</div>
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<xsl:call-template name="register_control_to_component" />
	</div>
</xsl:template>

<xsl:template name="register_control_to_component" xmlns:php="http://php.net/xsl">
	<div class="content-wrp">
		<div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
			
				<div id="choose_control" class="pure-form">

					<fieldset>
						<legend>Velg kontroll</legend>
						<select id="control_area_id" name="control_area_id">
							<xsl:apply-templates select="control_area_list/options"/>
						</select>
						<select id="control_id" name="control_id">
							<xsl:apply-templates select="control/options"/>
						</select>
					</fieldset>
				</div>
				
				<div id="choose-location">
					<xsl:apply-templates select="filter_form" />
					
					<form action="{update_action}" name="acl_form" id="acl_form" method="post" class="pure-form pure-form-stacked">
						<xsl:call-template name="datatable"/>
					</form>
				</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<div class="pure-form pure-form-stacked">
		<fieldset id="comp-filters">
			<legend>Velg lokasjoner som du vil knytte til kontrollen</legend>
			<input id= "control_id_hidden" type="hidden" name="control_id"/>
			<div>
				<label>
					<xsl:value-of select="php:function('lang', 'registered')" />
				</label>
				<input id= "control_registered" type="checkbox" name="control_registered" value="1"/>
			</div>

			<div class="filter">
				<label>
					<xsl:value-of select="php:function('lang', 'location type')" />
				</label>
				<select id="location_type" name="location_type" class="pure-input-1" >
					<xsl:apply-templates select="location_type_list/options"/>
				</select>
			</div>
			<div class="filter">
				<label>
					<xsl:value-of select="php:function('lang', 'location category')" />
				</label>
				<select id="location_type_category" name="location_type_category" class="pure-input-1" >

				</select>
			</div>
	  
			<div class="filter">
				<label>
					<xsl:value-of select="php:function('lang', 'district')" />
				</label>
				<select id="district_id" name="district_id" class="pure-input-1" >
					<xsl:apply-templates select="district_list/options"/>
				</select>
			</div>
			<div class="filter">
				<label>
					<xsl:value-of select="php:function('lang', 'part of town')" />
				</label>
				<select id="part_of_town_id" name="part_of_town_id" class="pure-input-1" >
					<xsl:apply-templates select="part_of_town_list/options"/>
				</select>
			</div>
		</fieldset>
	</div>
</xsl:template>


<xsl:template name="datatable" xmlns:php="http://php.net/xsl">
	<div id="table_def" class="pure-table pure-table-bordered pure-custom" width="80%"></div>

	<div id="receipt"></div>
	<div class="pure-controls">
		<xsl:variable name="label_submit">
			<xsl:value-of select="php:function('lang', 'save')" />
		</xsl:variable>
		<input type="submit" name="update_acl" id="frm_update_acl" class="btn" value="{$label_submit}"/>

		<xsl:variable name="label_select_add">
			<xsl:value-of select="php:function('lang', 'select add')" />
		</xsl:variable>
		<input type="button" name="select_add" id="frm_update_add" class="btn" value="{$label_select_add}" onclick="JqueryPortico.checkAll('mychecks_add')"/>
	
		<xsl:variable name="label_select_delete">
			<xsl:value-of select="php:function('lang', 'select delete')" />
		</xsl:variable>
		<input type="button" name="select_add" id="frm_update_delete" class="btn" value="{$label_select_delete}" onclick="JqueryPortico.checkAll('mychecks_delete')"/>
	</div>
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
