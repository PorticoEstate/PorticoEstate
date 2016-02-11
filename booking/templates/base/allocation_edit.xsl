<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
			<div id="allocations_edit" class="booking-container">
				<fieldset>
					<h1>#<xsl:value-of select="allocation/id"/></h1>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Application')"/>
						</label>
						<xsl:if test="allocation/application_id!=''">
							<a href="{allocation/application_link}">#<xsl:value-of select="allocation/application_id"/></a>
						</xsl:if>
					</div>
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
						<div id="building_container"></div>
					</div>
					<div class="pure-control-group">
						<label for="field_active">
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</label>
						<select id="field_active" name="active">
							<option value="1">
								<xsl:if test="allocation/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</option>
							<option value="0">
								<xsl:if test="allocation/active=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Inactive')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Season')" />
						</label>
						<div id="season_container" style="display:inline-block;">
							<span class="select_first_text">
								<span class="select_first_text">
									<xsl:value-of select="php:function('lang', 'Select a building first')" />
								</span>
							</span>
						</div>
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
						<div id="org_container"></div>
					</div>
					<div class="pure-control-group">
						<label for="field_from">
							<xsl:value-of select="php:function('lang', 'From')" />
						</label>
						<input class="datetime" id="field_from" name="from_" type="text">
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
						<input class="datetime" id="field_to" name="to_" type="text">
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

					<div id="field_cost_comment" class="pure-control-group">
						<label for="field_cost_comment">
							<xsl:value-of select="php:function('lang', 'Cost comment')" />
						</label>
						<input id="field_cost_comment" name="cost_comment" type="text">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'Cost comment')" />
							</xsl:attribute>
						</input>
						<input id="field_cost_orig" name="cost_orig" type="hidden" value= "{allocation/cost}"/>
					</div>
					<div>
						<div class="heading">
							<legend>
								<h3>
									<xsl:value-of select="php:function('lang', 'History of Cost (%1)', count(cost_history/author))" />
								</h3>
							</legend>
						</div>
						<xsl:for-each select="cost_history[author]">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
								</label>
								<span>
									<xsl:value-of select="comment"/>
									<xsl:text> :: </xsl:text>
									<xsl:value-of select="cost"/>
								</span>
							</div>
						</xsl:for-each>
					</div>

					<div class="pure-control-group">
						<label for="field_mail">
							<xsl:value-of select="php:function('lang', 'Inform contact persons')" />
						</label>
						<p style="display: inline-block;max-width:80%;">
							<span>
								<xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" />
							</span>
							<textarea id="field_mail" name="mail" class="full-width" style="display: block;"></textarea>
						</p>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="pure-control-group form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
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
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
	</script>
</xsl:template>
