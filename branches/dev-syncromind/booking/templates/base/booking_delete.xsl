<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<fieldset>
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
				<div id="booking_delete" class="booking-container">
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Delete Booking')"/>
							</h3>
						</legend>
					</div>
					<div class="pure-control-group">
						<h4>
							<xsl:value-of select="php:function('lang', 'Booking Delete Information')"/>
						</h4>
						<h4>
							<xsl:value-of select="php:function('lang', 'Booking Delete Information2')"/>
						</h4>
						<h4>
							<xsl:value-of select="php:function('lang', 'Booking Delete Information3')"/>
						</h4>
					</div>
					<div class="pure-control-group">
						<input type="hidden" name="application_id" value="{booking/application_id}"/>
						<input type="hidden" name="group_id" value="{booking/group_id}" />
						<input type="hidden" name="building_id" value="{booking/building_id}" />
						<input type="hidden" name="season_id" value="{booking/season_id}" />
						<input type="hidden" name="from_" value="{booking/from_}" />
						<input type="hidden" name="to_" value="{booking/to_}" />
					</div>
					<div class="pure-control-group">
						<label for="field_building">
							<xsl:value-of select="php:function('lang', 'Building')" />
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="booking/building_name"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_group">
							<xsl:value-of select="php:function('lang', 'Group')"/>
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="booking/group_name"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_season">
							<xsl:value-of select="php:function('lang', 'Season')"/>
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="booking/season_name"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_repeat_until">
							<xsl:value-of select="php:function('lang', 'Delete allocation also')" />
						</label>
						<div style="display:inline-block;">
							<label>
								<input type="checkbox" name="delete_allocation" id="delete_allocation">
									<xsl:if test="delete_allocation='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Delete allocations')" />
							</label>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_from">
							<xsl:value-of select="php:function('lang', 'From')" />
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="booking/from_"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_to">
							<xsl:value-of select="php:function('lang', 'To')"/>
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="booking/to_"/>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_repeat_until" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" />
						</label>
						<div style="display:inline-block;">
							<div style="display:inline-block;">
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
						<label for="field_interval">
							<xsl:value-of select="php:function('lang', 'Interval')" />
						</label>
						<div style="display: inline-block;">
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
			</div>
		</fieldset>
		<div class="form-buttons">
			<input class="pure-button pure-button-primary" type="submit">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Delete')"/>
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="booking/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')"/>
			</a>
		</div>
	</form>
	<script type="text/javascript">
		var season_id = '<xsl:value-of select="booking/season_id"/>';
		var group_id = '<xsl:value-of select="booking/group_id"/>';
		var initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
	</script>
</xsl:template>
