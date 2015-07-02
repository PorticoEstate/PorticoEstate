  <!-- $Id: adjustment.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
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
				<div id="regulation">
					<fieldset>
						<input type="hidden" name="id" value="{adjustment_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_field_of_responsibility"/>
							</label>
							<xsl:choose>
								<xsl:when test="adjustment_id = 0 or adjustment_id = ''">
									<input type="hidden" name="responsibility_id" id="responsibility_id" value="{responsibility_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_adjustment_type"/>
							</label>
							<select id="adjustment_type" name="adjustment_type">
								<xsl:apply-templates select="list_adjustment_type/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_percent"/>
							</label>
							<input type="text" id="percent" name="percent" size="10" value="{value_percent}"/> %
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_interval"/>
							</label>
							<select id="interval" name="interval">
								<xsl:apply-templates select="list_interval/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_year"/>
							</label>
							<select id="adjustment_year" name="adjustment_year">
								<xsl:apply-templates select="list_years/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_adjustment_date"/>
							</label>
							<input type="text" id="adjustment_date" name="adjustment_date" size="10" value="{value_adjustment_date}" readonly="readonly"/>
						</div>
						<xsl:choose>
							<xsl:when test="is_extra_adjustment">				
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="lang_extra_adjustment"/>
									</label>
									<input type="checkbox" name="extra_adjustment" id="extra_adjustment">
										<xsl:if test="is_extra_adjustment = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label></label>
							<xsl:value-of select="msg_executed"/>
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