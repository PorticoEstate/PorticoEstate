  <!-- $Id: party.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
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
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_field_of_responsibility"/>
							</label>
							<xsl:choose>
								<xsl:when test="contract_id > 0">
									<input type="hidden" name="location_id" id="location_id" value="{location_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_contract_type"/>
							</label>
							<select id="contract_type" name="contract_type">
								<xsl:apply-templates select="list_contract_type/options"/>
							</select>
						</div>	
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_executive_officer"/>
							</label>
							<select id="executive_officer" name="executive_officer">
								<xsl:apply-templates select="list_executive_officer/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_date_start"/>
							</label>
							<input type="text" id="date_start" name="date_start" size="10" value="{value_date_start}" readonly="readonly"/>
						</div>	
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_date_end"/>
							</label>
							<input type="text" id="date_end" name="date_end" size="10" value="{value_date_end}" readonly="readonly"/>
						</div>	
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_contract" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<input type="button" class="pure-button pure-button-primary" name="contract_back" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="document.cancel_form.submit();"/>
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