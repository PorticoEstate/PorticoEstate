<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		#agegroup td {padding: 0 0.3em;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
			<div id="booking_edit" class="booking-container">
				<fieldset>
					<h1>#<xsl:value-of select="booking/id"/></h1>
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Application')"/>
								</label>
								<xsl:if test="booking/application_id != ''">
									<a href="{booking/application_link}">#<xsl:value-of select="booking/application_id"/></a>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" name="active">
									<option value="1">
										<xsl:if test="booking/active=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="booking/active=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</div>
							<div class="pure-control-group">
								<label for="field_activity">
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
							<div class="heading">
								<legend>&nbsp;</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_building_name">
									<xsl:value-of select="php:function('lang', 'Building')"/>
								</label>
								<input id="field_building_id" name="building_id" type="hidden" value="{booking/building_id}"/>
								<input id="field_building_name" name="building_name" type="text" value="{booking/building_name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building')" />
									</xsl:attribute>
								</input>
								<div id="building_container"></div>
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
								<input type="hidden" data-validation="application_resources">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please choose at least 1 resource')" />
									</xsl:attribute>
								</input>
								<div id="resources_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>&nbsp;</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_org_name">
									<xsl:value-of select="php:function('lang', 'Organization')"/>
								</label>
								<input id="field_org_id" name="organization_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="booking/organization_id"/>
									</xsl:attribute>
								</input>
								<input id="field_org_name" name="organization_name" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="booking/organization_name"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a organization')" />
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
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
							<div class="pure-control-group">
								<label for="field_from">
									<xsl:value-of select="php:function('lang', 'From')"/>
								</label>
								<input class="datetime" id="field_from" name="from_" type="text" style="display:inline-block;">
									<xsl:attribute name="data-validation">
										<xsl:text>time_span_edit</xsl:text>
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
								<label for="field_to">
									<xsl:value-of select="php:function('lang', 'To')"/>
								</label>
								<input class="datetime" id="field_to" name="to_" type="text" style="display:inline-block;">
									<xsl:attribute name="data-validation">
										<xsl:text>time_span_edit</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an valid end date')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="booking/to_"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_cost">
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

							<div id="field_cost_comment" class="pure-control-group">
								<label for="field_cost_comment">
									<xsl:value-of select="php:function('lang', 'Cost comment')" />
								</label>
								<input id="field_cost_comment" name="cost_comment" type="text">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Cost comment')" />
									</xsl:attribute>
								</input>
								<input id="field_cost_orig" name="cost_orig" type="hidden" value= "{booking/cost}"/>
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


						</div>
						<div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
							<div class="heading">
								<legend></legend>
							</div>
							<div class="pure-g">
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label for="field_from">
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
									<div class="pure-control-group">
										<label for="field_from">
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
									</div>
									<div class="pure-control-group">
										<label for="sms_total">
											<xsl:value-of select="php:function('lang', 'SMS total')" />
										</label>
										<input type="text" name="sms_total" id="sms_total">
											<xsl:attribute name="value">
												<xsl:value-of select="booking/sms_total"/>
											</xsl:attribute>
										</input>
									</div>
									<div class="pure-control-group">
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
													<option value="2">
														<xsl:value-of select="php:function('lang', 'User has responded to the reminder')" />
													</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" />
													</option>
												</xsl:when>
												<xsl:when test="booking/reminder = 0">
													<option value="1">
														<xsl:value-of select="php:function('lang', 'Send reminder')" />
													</option>
													<option value="0" selected="selected">
														<xsl:value-of select="php:function('lang', 'Do not send reminder')" />
													</option>
													<option value="2">
														<xsl:value-of select="php:function('lang', 'User has responded to the reminder')" />
													</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" />
													</option>
												</xsl:when>
												<xsl:when test="booking/reminder = 2">
													<option value="1">
														<xsl:value-of select="php:function('lang', 'Send reminder')" />
													</option>
													<option value="0">
														<xsl:value-of select="php:function('lang', 'Do not send reminder')" />
													</option>
													<option value="2" selected="selected">
														<xsl:value-of select="php:function('lang', 'User has responded to the reminder')" />
													</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" />
													</option>
												</xsl:when>
												<xsl:when test="booking/reminder = 3">
													<option value="1">
														<xsl:value-of select="php:function('lang', 'Send reminder')" />
													</option>
													<option value="0">
														<xsl:value-of select="php:function('lang', 'Do not send reminder')" />
													</option>
													<option value="2">
														<xsl:value-of select="php:function('lang', 'User has responded to the reminder')" />
													</option>
													<option value="3" selected="selected">
														<xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" />
													</option>
												</xsl:when>
											</xsl:choose>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend></legend>
							</div>
							<div class="pure-control-group">
								<label for="field_mail">
									<xsl:value-of select="php:function('lang', 'Inform contact persons')" />
								</label>
								<xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" />
								<textarea id="field_mail" name="mail" class="full-width"></textarea>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
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
	<script>
		var season_id = '<xsl:value-of select="booking/season_id"/>';
		var group_id = '<xsl:value-of select="booking/group_id"/>';
		var initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var initialAudience = <xsl:value-of select="booking/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang','Name', 'Resource Type', 'Please select a season', 'Please select a group')"/>;
	</script>
</xsl:template>
