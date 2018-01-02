<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		#agegroup td {padding: 0 0.3em;}
	</style>
	<xsl:call-template name="msgbox"/>

	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
			<div id="booking_new" class="booking-container">
				<fieldset>
					<input type="hidden" name="application_id" value="{booking/application_id}"/>
					<div class="pure-g pure-form pure-form-aligned">
						<div class="pure-u-1">
							<div class="pure-control-group">
								<label style="width:auto;" for="field_activity">
									<xsl:value-of select="php:function('lang', 'Activity')" />
								</label>
								<select name="activity_id" id="field_activity">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select an activity')" />
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', '-- select an activity --')" />
									</option>
									<xsl:for-each select="activities">
										<option>
											<xsl:if test="../booking/activity_id = id">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:attribute name="value">
												<xsl:value-of select="id"/>
											</xsl:attribute>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<legend></legend>
							<div class="pure-control-group">
								<label for="field_building_name">
									<xsl:value-of select="php:function('lang', 'Building')"/>
								</label>
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="booking/building_id"/>
									</xsl:attribute>
								</input>
								<input id="field_building_name" name="building_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/building_name"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Season')"/>
								</label>
								<div id="season_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Resources')"/>
								</label>
								<div id="resources_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<legend></legend>
							<div class="pure-control-group">
								<label for="field_org_name">
									<xsl:value-of select="php:function('lang', 'Organization')"/>
								</label>
								<input id="field_org_id" name="organization_id" type="hidden">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a organization')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/organization_id"/>
									</xsl:attribute>
								</input>
								<input id="field_org_name" name="organization_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a organization')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/organization_name"/>
									</xsl:attribute>
								</input>
								<div id="org_container"></div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Group')"/>
								</label>
								<div id="group_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select an organization first')" />
									</span>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'From')"/>
								</label>
								<input class="datetime pure-input-2-3" id="start_date" name="from_" type="text" style="display:inline-block;">
									<xsl:attribute name="data-validation">
										<xsl:text>time_span</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a valid from date')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/from_"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'To')"/>
								</label>
								<input class="datetime pure-input-2-3" id="end_date" name="to_" type="text" style="display:inline-block;">
									<xsl:attribute name="data-validation">
										<xsl:text>time_span</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a valid end date')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/to_"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Cost')" />
								</label>
								<input id="field_cost" name="cost" type="text" value="{booking/cost}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a cost')" />
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_repeat_until">
									<xsl:value-of select="php:function('lang', 'Recurring booking')" />
								</label>
							</div>
							<div class="pure-control-group">
								<label>
									<input type="checkbox" name="outseason" id="outseason">
										<xsl:if test="outseason='on'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'Out season')" />
								</label>
							</div>
							<div class="pure-control-group">
								<label>
									<input type="checkbox" name="recurring" id="recurring">
										<xsl:if test="recurring='on'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
									<xsl:value-of select="php:function('lang', 'Repeat until')" />
								</label>
							</div>
							<div class="pure-control-group">
								<!--input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
								</input-->
								<input class="datetime pure-input-2-3" id="field_repeat_until" name="repeat_until" type="text" style="display:inline-block;" />
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
						<div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
							<legend></legend>
							<div class="pure-g">
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label>
										<xsl:value-of select="php:function('lang', 'Target audience')" />
									</label>
									<input type="hidden" data-validation="target_audience">
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
										</xsl:attribute>
									</input>
									<ul style="list-style:none;padding-left:10px;" id="audience">
										<xsl:for-each select="audience">
											<li>
												<label>
													<input type="radio" name="audience[]">
														<xsl:attribute name="value">
															<xsl:value-of select="id"/>
														</xsl:attribute>
														<xsl:if test="../booking/audience=id">
															<xsl:attribute name="checked">checked</xsl:attribute>
														</xsl:if>
													</input>
													<xsl:value-of select="name"/>
												</label>
											</li>
										</xsl:for-each>
									</ul>
								</div>
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label>
										<xsl:value-of select="php:function('lang', 'Number of participants')" />
									</label>
									<input type="hidden" data-validation="number_participants">
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Number of participants is required')" />
										</xsl:attribute>
									</input>
									<table id="agegroup" class="pure-table pure-table-bordered">
										<thead>
											<tr>
												<th></th>
												<th>
													<xsl:value-of select="php:function('lang', 'Male')" />
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'Female')" />
												</th>
											</tr>
										</thead>
										<tbody id="agegroup_tbody">
											<xsl:for-each select="agegroups">
												<xsl:variable name="id">
													<xsl:value-of select="id"/>
												</xsl:variable>
												<tr>
													<th>
														<xsl:value-of select="name"/>
													</th>
													<td>
														<input type="text" class="input50">
															<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="text" class="input50">
															<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../booking/agegroups/female[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</xsl:for-each>
										</tbody>
									</table>
									<label>
										<xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" />
									</label>
									<select name="reminder" id="field_reminder">
										<xsl:choose>
											<xsl:when test="booking/reminder = 1">
												<option value="1" selected="selected">
													<xsl:value-of select="php:function('lang', 'Send reminder')" />
												</option>
												<option value="0">
													<xsl:value-of select="php:function('lang', 'Do not send reminder')" />
												</option>
											</xsl:when>
											<xsl:otherwise test="booking/reminder = 0">
												<option value="1">
													<xsl:value-of select="php:function('lang', 'Send reminder')" />
												</option>
												<option value="0" selected="selected">
													<xsl:value-of select="php:function('lang', 'Do not send reminder')" />
												</option>
											</xsl:otherwise>
										</xsl:choose>
									</select>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Create')"/>
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
		season_id = '<xsl:value-of select="booking/season_id"/>';
		group_id = '<xsl:value-of select="booking/group_id"/>';
		initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var initialAudience = <xsl:value-of select="booking/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type', 'Please select a season', 'Please select a group')"/>;
	</script>
</xsl:template>
