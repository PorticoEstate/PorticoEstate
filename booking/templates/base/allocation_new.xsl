<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		.pure-control-group h4 {margin: 0px;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
			<div id="allocation_new" class="booking-container">
				<input type="hidden" name="application_id" value="{allocation/application_id}"/>
				<div class="pure-control-group">
					<label for="field_building_name">
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<input id="field_building_id" name="building_id" type="hidden">
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/building_id"/>
						</xsl:attribute>
					</input>
					<input id="field_building_name" name="building_name" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/building_name"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_org_name">
						<xsl:value-of select="php:function('lang', 'Organization')" />
					</label>
					<input id="field_org_id" name="organization_id" type="hidden">
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/organization_id"/>
						</xsl:attribute>
					</input>
					<input id="field_org_name" name="organization_name" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter an organization name')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/organization_name"/>
						</xsl:attribute>
					</input>
				</div>
				<!--div class="pure-control-group">
					<label for="field_weekday">
						<xsl:value-of select="php:function('lang', 'Weekday')" />
					</label>
					<select name="weekday" id="field_weekday">
						<option value="monday">
							<xsl:if test="../allocation/weekday = 'monday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Monday')" />
						</option>
						<option value="tuesday">
							<xsl:if test="weekday = 'tuesday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Tuesday')" />
						</option>
						<option value="wednesday">
							<xsl:if test="weekday = 'wednesday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Wednesday')" />
						</option>
						<option value="thursday">
							<xsl:if test="weekday = 'thursday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Thursday')" />
						</option>
						<option value="friday">
							<xsl:if test="weekday = 'friday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Friday')" />
						</option>
						<option value="saturday">
							<xsl:if test="weekday = 'saturday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Saturday')" />
						</option>
						<option value="sunday">
							<xsl:if test="weekday = 'sunday'">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Sunday')" />
						</option>
					</select>
				</div-->
				<div class="pure-control-group">
					<label for="field_from">
						<xsl:value-of select="php:function('lang', 'From')" />
					</label>
					<input id="field_from" name="from_" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a from date')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/from_"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_to">
						<xsl:value-of select="php:function('lang', 'To')" />
					</label>
					<input id="field_to" name="to_" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/to_"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Recurring allocation')" />
					</label>
					<input type="checkbox" name="outseason" id="outseason">
						<xsl:if test="outseason='on'">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
					<label style="text-align:left;margin-left:5px;font-weight: normal;" for="outseason">
						<xsl:value-of select="php:function('lang', 'Out season')" />
					</label>
				</div>
				<div class="pure-control-group">
					<label for="field_interval">
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
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'Season')" />
					</label>
					<div id="season_container" style="display:inline-block;">
						<span class="select_first_text">
							<xsl:value-of select="php:function('lang', 'Select a building first')" />
						</span>
					</div>
				</div>
				<div class="pure-control-group">
					<label for="field_cost">
						<xsl:value-of select="php:function('lang', 'Cost')" />
					</label>
					<input id="field_cost" name="cost" type="text">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a cost')" />
						</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="allocation/cost"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'Resources')" />
					</label>
					<div id="resources_container" style="display:inline-block;">
						<span class="select_first_text">
							<xsl:value-of select="php:function('lang', 'Select a building first')" />
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Create')"/>
				</xsl:attribute>
			</input>
			<a class="cancel button pure-button pure-button-primary">
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
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
	</script>
</xsl:template>
