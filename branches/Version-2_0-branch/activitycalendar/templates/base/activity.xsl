
<!-- $Id: activity.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="activity">
					<input type="hidden" name="id" value="{activity_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'title')"/>
						</label>
						<input type="text" name="title" id="title" value="{value_title}" class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Tittel må fylles ut!')"/>
							</xsl:attribute>							
						</input>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="value_description"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'state')"/>
						</label>
						<select id="state" name="state" class="pure-input-1-2" >
							<xsl:apply-templates select="list_state_options/options"/>
						</select>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<select id="category" name="category" class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Kategori må fylles ut!')"/>
							</xsl:attribute>							
							<xsl:apply-templates select="list_category_options/options"/>
						</select>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'target')"/>
						</label>
						<div class="pure-custom">
							<xsl:apply-templates select="list_target_checks/choice"/>
						</div>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'district')"/>
						</label>
						<div class="pure-custom">
							<xsl:apply-templates select="list_district_checks/choice"/>
						</div>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'special_adaptation')"/>
						</label>
						<div class="pure-custom">
							<input type="checkbox" name="special_adaptation" id="special_adaptation">
								<xsl:if test="special_adaptation_checked = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>								
							</input>
						</div>						
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'where_when')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'arena')"/>
						</label>					
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'building')"/>
						</label>
						<select id="internal_arena_id" name="internal_arena_id" onchange="javascript: check_internal();" class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>arena</xsl:text>
							</xsl:attribute>
							<xsl:apply-templates select="list_building_options/options"/>
						</select>											
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external_arena')"/>
						</label>
						<select id="arena_id" name="arena_id" onchange="javascript: check_external();" class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>arena</xsl:text>
							</xsl:attribute>
							<xsl:apply-templates select="list_arena_external_options/options"/>
						</select>											
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'time')"/>
						</label>
						<input type="text" name="time" id="time" value="{value_time}"  class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Tid må fylles ut!')"/>
							</xsl:attribute>							
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'office')"/>
						</label>
						<select id="office" name="office" class="pure-input-1-2" >
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Hovedansvarlig kulturkontor må fylles ut!')"/>
							</xsl:attribute>							
							<xsl:apply-templates select="list_office_options/options"/>
						</select>											
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'who')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'organization')"/>
						</label>
						<div class="pure-custom">
							<div>
								<select id="organization_id" name="organization_id" onchange="javascript:get_available_groups();" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Organisasjon må fylles ut!')"/>
									</xsl:attribute>									
									<xsl:apply-templates select="list_organization_options/options"/>
								</select>
							</div>
							<xsl:if test="organization_selected = 1">
								<div>
									<xsl:value-of select="php:function('lang', 'edit_contact_info')"/>
									<xsl:text>: </xsl:text>
									<a href="{organization_url}">
										<xsl:value-of select="php:function('lang', 'edit_contact_info_org')"/>
									</a>
								</div>
							</xsl:if>						
						</div>								
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'group')"/>
						</label>
						<div class="pure-custom">
							<xsl:if test="new_group = 1">
								<input type="hidden" name="group_id" id="group_id" value="{local_group_id}" />
								<xsl:value-of select="local_group_name"/>
							</xsl:if>		
							<xsl:if test="new_group = 0">
								<div id="div_group_id">
									<select name="group_id" id="group_id" class="pure-input-1-2" >
										<option value="">Ingen gruppe valgt</option>
									</select>
								</div>
								<xsl:if test="group_selected = 1">
									<div>										
										<xsl:value-of select="php:function('lang', 'edit_contact_info')"/>
										<xsl:text>: </xsl:text>
										<a href="{group_url}">
											<xsl:value-of select="php:function('lang', 'edit_contact_info_group')"/>
										</a>
									</div>
								</xsl:if>								
							</xsl:if>
							<input type="hidden" name="group_selected_id" id="group_selected_id" value="{group_selected_id}" />				
						</div>								
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'contact_info')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_1')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="contact_person_1" disable-output-escaping="yes"/>
						</div>
					</div>	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_2')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="contact_person_2" disable-output-escaping="yes"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_2_address')"/>
						</label>
						<input type="text" name="contact_person_2_address" id="contact_person_2_address" value="{contact_person_2_address}" class="pure-input-1-2" >
						</input>
						<div id="contact_person_2_address_container"></div>									
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_2_zip')"/>
						</label>
						<input type="text" name="contact_person_2_zip" id="contact_person_2_zip" value="{contact_person_2_zip}" class="pure-input-1-2" >
						</input>
					</div>				
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_contract" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>	
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'select arena: internal or external')"/>;

		$("[name='target[]']:eq(0)")
		.valAttr('','validate_checkbox_group')
		.valAttr('qty','min1')
		.valAttr('error-msg','Målgruppe må fylles ut!');

		$("[name='district[]']:eq(0)")
		.valAttr('','validate_checkbox_group')
		.valAttr('qty','min1')
		.valAttr('error-msg','Bydel må fylles ut!');
		
	</script>
</xsl:template>


<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="activity">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'title')"/>
						</label>
						<xsl:value-of select="value_title"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>
						<xsl:value-of select="value_description"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'state')"/>
						</label>
						<xsl:value-of select="state_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:value-of select="category_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'target')"/>
						</label>
						<div class="pure-custom">
							<xsl:for-each select="list_target_names">
								<div>
									<xsl:value-of select="name"/>
								</div>
							</xsl:for-each>
						</div>	
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'district')"/>
						</label>
						<div class="pure-custom">
							<xsl:for-each select="list_district_names">
								<div>
									<xsl:value-of select="name"/>
								</div>
							</xsl:for-each>
						</div>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'special_adaptation')"/>
						</label>
						<div class="pure-custom">
							<input type="checkbox" name="special_adaptation" id="special_adaptation" disabled="disabled">
								<xsl:if test="special_adaptation_checked = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>								
							</input>
						</div>						
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'where_when')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'arena')"/>
						</label>					
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'building')"/>
						</label>
						<xsl:value-of select="building_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external_arena')"/>
						</label>
						<xsl:value-of select="arena_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'time')"/>
						</label>
						<xsl:value-of select="value_time"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'office')"/>
						</label>
						<xsl:value-of select="office_name"/>
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'who')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'organization')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="organization_name"/>
						</div>								
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'group')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="group_name"/>
						</div>								
					</div>
					<h2>
						<xsl:value-of select="php:function('lang', 'contact_info')"/>
					</h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_1')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="contact_person_1" disable-output-escaping="yes"/>
						</div>						
					</div>	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'contact_person_2')"/>
						</label>
						<div class="pure-custom">
							<xsl:value-of select="contact_person_2" disable-output-escaping="yes"/>
						</div>						
					</div>
					<xsl:if test="contact_person_2_address != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_person_2_address')"/>
							</label>
							<xsl:value-of select="contact_person_2_address"/>								
						</div>
					</xsl:if>
					<xsl:if test="contact_person_2_zip != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_person_2_zip')"/>
							</label>
							<xsl:value-of select="contact_person_2_zip"/>
						</div>
					</xsl:if>					
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="edit_url">
					<xsl:value-of select="edit_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="edit" value="{lang_edit}" onMouseout="window.status='';return true;" onClick="window.location = '{edit_url}';"/>				
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>


<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of select="name"/>
	</option>
</xsl:template>

<xsl:template match="option_group">
	<optgroup label="{label}">
		<xsl:apply-templates select="options"/>
	</optgroup>
</xsl:template>

<xsl:template match="choice">
	<xsl:choose>
		<xsl:when test="checked='checked'">
			<input id="{name}" data-validation="validate_checkbox_group" type="checkbox" name="{name}" value="{value}" checked="checked"/>
		</xsl:when>
		<xsl:otherwise>
			<input id="{name}" data-validation="validate_checkbox_group" type="checkbox" name="{name}" value="{value}"/>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:value-of select="label"/>
	<br></br>
</xsl:template>