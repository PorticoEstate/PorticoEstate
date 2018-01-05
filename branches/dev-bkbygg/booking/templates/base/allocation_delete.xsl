<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<fieldset>
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
				<div id="allocation_delete" class="booking-container">
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Delete allocation')"/>
							</h3>
						</legend>
					</div>
					<div class="pure-control-group">
						<h4>
							<xsl:value-of select="php:function('lang', 'Delete Information')"/>
						</h4>
						<h4>
							<xsl:value-of select="php:function('lang', 'Delete Information2')"/>
						</h4>
					</div>
					<div class="pure-control-group">
						<input type="hidden" name="application_id" value="{allocation/application_id}"/>
						<input id="field_org_id" name="organization_id" type="hidden" value="{allocation/organization_id}" />
						<input id="field_building_id" name="building_id" type="hidden" value="{allocation/building_id}" />
						<input id="field_from" name="from_" type="hidden" value="{allocation/from_}" />
						<input id="field_to" name="to_" type="hidden" value="{allocation/to_}" />
					</div>
					<div class="pure-control-group">
						<label for="field_building" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Building')" />
						</label>
						<div class="autocomplete" style="display:inline-block;">
							<xsl:value-of select="allocation/building_name"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_org" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Organization')" />
						</label>
						<div class="autocomplete" style="display:inline-block;">
							<xsl:value-of select="allocation/organization_name"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_from" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'From')" />
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="allocation/from_"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_to" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'To')" />
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="allocation/to_"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_repeat_until" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" />
						</label>
						<div style="display:inline-block;">
							<div>
								<label style="display: block !important;text-align: left !important;">
									<input type="checkbox" name="outseason" id="outseason">
										<xsl:if test="outseason='on'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'Out season')" />
								</label>
								<label style="display: block !important;text-align: left !important;">
									<input type="checkbox" name="recurring" id="recurring">
										<xsl:if test="recurring='on'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'Delete until')" />
								</label>
							</div>
							<div>
								<input class="datetime" id="field_repeat_until" name="repeat_until" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="repeat_until"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Interval')" />
						</label>
						<xsl:value-of select="../field_interval" />
						<select id="field_interval" name="field_interval">
							<option value="1">
								<xsl:if test="interval=1">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '1 week')" />
							</option>
							<option value="2">
								<xsl:if test="interval=2">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '2 weeks')" />
							</option>
							<option value="3">
								<xsl:if test="interval=3">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '3 weeks')" />
							</option>
							<option value="4">
								<xsl:if test="interval=4">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', '4 weeks')" />
							</option>
						</select>
					</div>
				</div>
			</div>
		</fieldset>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Delete')"/>
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="allocation/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		var season_id = '<xsl:value-of select="allocation/season_id"/>';
		var initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
	</script>
</xsl:template>
