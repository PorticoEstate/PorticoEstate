<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style  type="text/css">
		#agegroup td {padding: 0 0.3em;}
		input.datetime,
		input.time {display: inline-block !important;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="event/tabs"/>
			<div id="event_edit" class="booking-container">
				<fieldset>
					<div class="pure-g pure-form pure-form-aligned">
						<div class="pure-u-1">
							<h1>#<xsl:value-of select="event/id"/></h1>
							<div class="pure-control-group">
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" name="active">
									<option value="1">
										<xsl:if test="event/active=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="event/active=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Application')"/>
								</label>
								<xsl:if test="event/application_id != ''">
									<a href="{event/application_link}">#<xsl:value-of select="event/application_id"/></a>
								</xsl:if>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'History and comments (%1)', count(comments/author))" />
									</h3>
								</legend>
							</div>
							<xsl:for-each select="comments[author]">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
									</label>
									<span>
										<xsl:value-of select="comment" disable-output-escaping="yes"/>
									</span>
								</div>
							</xsl:for-each>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Why')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_activity">
									<xsl:value-of select="php:function('lang', 'Activity')" />
								</label>
								<select name="activity_id" id="field_activity" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
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
											<xsl:if test="../event/activity_id = id">
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
							<div class="pure-control-group">
								<label for="field_description">
									<xsl:value-of select="php:function('lang', 'Description')" />
								</label>
								<textarea id="field_description" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="description">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a description')" />
									</xsl:attribute>
									<xsl:value-of select="event/description"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label for="field_public">
									<xsl:value-of select="php:function('lang', 'Event type')"/>
								</label>
								<select id="field_public" name="is_public" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<option value="1">
										<xsl:if test="event/is_public=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Public event')"/>
									</option>
									<option value="0">
										<xsl:if test="event/is_public=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Private event')"/>
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Where')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_building_name">
									<xsl:value-of select="php:function('lang', 'Building')" />
								</label>
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/building_id"/>
									</xsl:attribute>
								</input>
								<input id="field_building_name" name="building_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/building_name"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Resources')" />
								</label>
								<div id="resources_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'When')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="from_">
									<xsl:value-of select="php:function('lang','Start date')" />
								</label>
								<input class="datetime pure-input-2-3" id="from_" name="from_" type="text">
									<xsl:if test="event/from_ != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="event/from_"/>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a start date')" />
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="to_">
									<xsl:value-of select="php:function('lang', 'End date')" />
								</label>
								<input class="datetime pure-input-2-3" id="to_" name="to_" type="text">
									<xsl:if test="event/to_ != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="event/to_"/>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
									</xsl:attribute>
								</input>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Who')" />
									</h3>
								</legend>
							</div>
							<div class="pure-g">
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label>
										<xsl:value-of select="php:function('lang', 'Target audience')" />
									</label>
									<input type="hidden" data-validation="">
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
										</xsl:attribute>
									</input>
									<ul id="audience" style="list-style:none;padding: 0 0 0 10px;">
										<xsl:for-each select="audience">
											<li>
												<label style="display:inline-block">
													<input type="radio" name="audience[]">
														<xsl:attribute name="value">
															<xsl:value-of select="id"/>
														</xsl:attribute>
														<xsl:if test="../event/audience=id">
															<xsl:attribute name="checked">checked</xsl:attribute>
														</xsl:if>
													</input>
													<span>
														<xsl:value-of select="name"/>
													</span>
												</label>
											</li>
										</xsl:for-each>
									</ul>
								</div>
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
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
												<th>&nbsp;</th>
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
														<input class="input50" type="text">
															<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../event/agegroups/male[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input class="input50" type="text">
															<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../event/agegroups/female[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</xsl:for-each>
										</tbody>
									</table>
									<label for="sms_total">
										<h4>
											<xsl:value-of select="php:function('lang', 'SMS total')" />
										</h4>
									</label>
									<input type="text" id="sms_total" name="sms_total">
										<xsl:attribute name="value">
											<xsl:value-of select="event/sms_total"/>
										</xsl:attribute>
									</input>
								</div>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Contact information')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_contact_name">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
								<input id="field_contact_name" name="contact_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a contact name')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/contact_name"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_contact_mail">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<input id="field_contact_mail" name="contact_email" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="value">
										<xsl:value-of select="event/contact_email"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_contact_phone">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<input id="field_contact_phone" name="contact_phone" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="value">
										<xsl:value-of select="event/contact_phone"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_cost">
									<xsl:value-of select="php:function('lang', 'Cost')" />
								</label>
								<input id="field_cost" name="cost" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a cost')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/cost"/>
									</xsl:attribute>
								</input>
							</div>
							<div id="field_cost_comment" class="pure-control-group">
								<label for="field_cost_comment">
									<xsl:value-of select="php:function('lang', 'Cost comment')" />
								</label>
								<input id="field_cost_comment" name="cost_comment" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Cost comment')" />
									</xsl:attribute>
								</input>
								<input id="field_cost_orig" name="cost_orig" type="hidden" value= "{event/cost}"/>
							</div>
							<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
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
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Invoice information')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<xsl:copy-of select="phpgw:booking_customer_identifier(event, '')"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Internal Customer')"/>
								</label>
								<xsl:copy-of select="phpgw:option_checkbox(event/customer_internal, 'customer_internal')"/>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label style="display:none;">!</label>
								<select name="reminder" id="field_reminder" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:choose>
										<xsl:when test="event/reminder = 1">
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
										<xsl:when test="event/reminder = 0">
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
										<xsl:when test="event/reminder = 2">
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
										<xsl:when test="event/reminder = 3">
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
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<label for="field_mail">
										<h3>
											<xsl:value-of select="php:function('lang', 'Inform contact persons')" />
										</h3>
									</label>
								</legend>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" />
								</label>
								<textarea rows="4" id="field_mail" name="mail" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3"></textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<input type="checkbox" value="1" name="sendtocontact" />
									<xsl:value-of select="php:function('lang', 'Send to contact')" />
								</label>
							</div>
							<div class="pure-control-group">
								<label>
									<input type="checkbox" value="1" name="sendtocollision" />
									<xsl:value-of select="php:function('lang', 'Send to contact for overlaping allocations/bookings')" />
								</label>
							</div>
							<div class="pure-control-group">
								<label>
									<input type="checkbox" value="1" name="sendtorbuilding" />
									<xsl:value-of select="php:function('lang', 'Send warning to building responsible')" />
								</label>
							</div>
							<div class="pure-control-group">
								<label for="sendtorbuilding_email1">
									<xsl:value-of select="php:function('lang', 'Optional e-mail adress')" />
								</label>
								<input type="text" id="sendtorbuilding_email1" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="sendtorbuilding_email1" />
							</div>
							<div class="pure-control-group">
								<label for="sendtorbuilding_email2">
									<xsl:value-of select="php:function('lang', 'Optional e-mail adress')" />
								</label>
								<input type="text" id="sendtorbuilding_email2" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="sendtorbuilding_email2" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Send')"/>
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="event/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		$('#field_customer_identifier_type,#field_customer_ssn,#field_customer_organization_number').removeClass('pure-input-1').addClass('pure-u-1 pure-u-sm-1-2 pure-u-md-1');
		var initialSelection = <xsl:value-of select="event/resources_json"/>;
		var initialAudience = <xsl:value-of select="event/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
		$('#field_customer_identifier_type').attr("data-validation","customer_identifier").attr("data-validation-error-msg","<xsl:value-of select="php:function('lang', 'There is set a cost, but no invoice data is filled inn')" />");
	</script>
</xsl:template>
