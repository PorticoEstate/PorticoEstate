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
				<div id="details">
					<input type="hidden" name="id" value="{activity_id}"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'title')"/>
						</label>
						<input type="text" name="title" id="title" value="{value_title}"></input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>
						<input type="text" name="title" id="title" value="{value_description}"></input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'state')"/>
						</label>
						<select id="state" name="state">
							<xsl:apply-templates select="list_state_options/options"/>
						</select>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<select id="category" name="category">
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
					<h2><xsl:value-of select="php:function('lang', 'where_when')"/></h2>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'arena')"/>
						</label>					
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'building')"/>
						</label>
						<select id="internal_arena_id" name="internal_arena_id">
							<xsl:apply-templates select="list_building_options/options"/>
						</select>											
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'external_arena')"/>
						</label>
						<select id="arena_id" name="arena_id" style="width: 300px;">
							<xsl:apply-templates select="list_arena_external_options/options"/>
						</select>											
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'time')"/>
						</label>
						<input type="text" name="time" id="time" value="{value_time}" />
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'office')"/>
						</label>
						<select id="office" name="office">
							<xsl:apply-templates select="list_office_options/options"/>
						</select>											
					</div>
					<h2><xsl:value-of select="php:function('lang', 'who')"/></h2>	
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'organization')"/>
						</label>
						<div class="pure-custom">
							<div>
								<select id="organization_id" name="organization_id">
									<xsl:apply-templates select="list_organization_options/options"/>
								</select>
							</div>
							<xsl:if test="organization_selected = 1">
								<div>
									<xsl:value-of select="php:function('lang', 'edit_contact_info')"/><xsl:text>: </xsl:text><a href="{organization_url}"><xsl:value-of select="php:function('lang', 'edit_contact_info_org')"/> </a>
								</div>
							</xsl:if>						
						</div>								
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
		<form id="form_upload" name="form_upload" method="post" action="" enctype="multipart/form-data"></form>
	</div>
</xsl:template>


<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">

	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
				</div>
			</div>
			<div class="proplist-col">
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
			<input id="{name}" type="checkbox" name="{name}" value="{value}" checked="checked"/>
		</xsl:when>
		<xsl:otherwise>
			<input id="{name}" type="checkbox" name="{name}" value="{value}"/>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:value-of select="label"/>
	<br></br>
</xsl:template>