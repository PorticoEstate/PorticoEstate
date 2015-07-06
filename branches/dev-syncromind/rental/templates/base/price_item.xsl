  <!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:apply-templates select="edit" />
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<xsl:value-of select="validator"/>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">
					<fieldset>
						<input type="hidden" name="id" value="{price_item_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_title"/>
							</label>
							<input type="text" name="title" id="title" value="{value_title}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_field_of_responsibility"/>
							</label>
							<xsl:choose>
								<xsl:when test="price_item_id = 0 or price_item_id = ''">
									<input type="hidden" name="responsibility_id" id="responsibility_id" value="{responsibility_id}"/>
								</xsl:when>
							</xsl:choose>							
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_agresso_id"/>
							</label>
							<input type="text" name="agresso_id" id="agresso_id" value="{value_agresso_id}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_is_area"/>
							</label>
							<div class="pure-custom">
								<div>
									<input type="radio" name="is_area">
										<xsl:if test="is_area = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="lang_calculate_price_per_area"/>
								</div>
								<div>
									<input type="radio" name="is_area">
										<xsl:if test="is_area = 0">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="lang_calculate_price_apiece"/>
								</div>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_type"/>
							</label>
							<select id="price_type_id" name="price_type_id">
								<xsl:apply-templates select="list_type/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_price"/>
							</label>
							<input type="text" name="price" id="price" value="{value_price}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_is_inactive"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive">
								<xsl:if test="is_inactive = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
								<xsl:if test="has_active_contract = 1">
									<xsl:attribute name="disabled" value="disabled"/>
								</xsl:if>
							</input>
							<xsl:if test="has_active_contract = 1">
								<xsl:value-of select="lang_price_element_in_use"/>
							</xsl:if>									
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_is_adjustable"/>
							</label>
							<input type="checkbox" name="is_adjustable" id="is_adjustable">
								<xsl:if test="is_adjustable = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>			
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_is_standard"/>
							</label>
							<input type="checkbox" name="is_standard" id="is_standard">
								<xsl:if test="is_standard = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>			
						</div>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();"/>
			</div>
		</form>
		<xsl:variable name="cancel_url">
			<xsl:value-of select="cancel_url"/>
		</xsl:variable>
		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post"></form>
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