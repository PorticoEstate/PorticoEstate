  <!-- $Id: composite.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:apply-templates select="edit" />
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<xsl:value-of select="validator"/>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<dl>
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<dt>
							<xsl:call-template name="msgbox"/>
						</dt>
					</xsl:when>
				</xsl:choose>
			</dl>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<input type="hidden" name="id" value="{composite_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_name"/>
							</label>
							<input type="text" name="name" id="name" value="{value_name}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_address"/>
							</label>
						</div>
						<xsl:if test="count(//list_composite_standard/options) > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_composite_standard"/>
								</label>
								<select id="composite_standard_id" name="composite_standard_id">
									<xsl:apply-templates select="list_composite_standard/options"/>
								</select>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_furnish_type"/>
							</label>
							<select id="furnish_type_id" name="furnish_type_id">
								<xsl:apply-templates select="list_furnish_type/options"/>
							</select>
						</div>	
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_has_custom_address"/>
							</label>
							<input type="checkbox" name="has_custom_address" id="has_custom_address">
								<xsl:if test="has_custom_address = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_overridden_address"/> / <xsl:value-of select="lang_house_number"/>
							</label>
							<input type="text" name="address_1" id="address_1" value="{value_custom_address_1}"/>
							<input type="text" name="house_number" id="house_number" value="{value_custom_house_number}"/>
							<input type="text" name="address_2" id="address_2" value="{value_custom_address_2}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_post_code"/> / <xsl:value-of select="lang_post_place"/>
							</label>
							<input type="text" name="postcode" id="postcode" value="{value_custom_postcode}"/>
							<input type="text" name="place" id="place" value="{value_custom_place}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_description"/>
							</label>
							<textarea name="description" id="description" rows="10" cols="50"><xsl:value-of select="value_description"/></textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_area_gros"/>
							</label>
							<xsl:value-of select="value_area_gros"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_area_net"/>
							</label>
							<xsl:value-of select="value_area_net"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_available"/>
							</label>
							<input type="checkbox" name="is_active" id="is_active">
								<xsl:if test="is_active = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" value="{lang_save}" onMouseout="window.status='';return true;"/>
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
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>