<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		#agegroup td {padding: 0 0.3em;}
	</style>
	<h3></h3>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='application_form' class= "pure-form pure-form-stacked" name="application_form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="application/tabs"/>
			<div id="application_edit" class="booking-container">
				<fieldset>
					<h1>
						<xsl:value-of select="php:function('lang', 'Application')"/> (<xsl:value-of select="application/id"/>)</h1>
					<div class="pure-g pure-form pure-form-aligned">
						<div class="pure-u-1">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" name="active">
									<option value="1">
										<xsl:if test="application/active=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="application/active=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>1. <xsl:value-of select="php:function('lang', 'Why?')" /></h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_activity">
									<xsl:value-of select="php:function('lang', 'Activity')" />
								</label>
								<xsl:if test="config/application_activities">
									<p>
										<xsl:value-of select="config/application_activities"/>
									</p>
								</xsl:if>
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
											<xsl:if test="../application/activity_id = id">
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
								<label for="field_name">
									<xsl:value-of select="php:function('lang', 'Event name')" />
								</label>
								<input id="field_name" name="name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<xsl:attribute name="value">
										<xsl:value-of select="application/name"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_organizer">
									<xsl:value-of select="php:function('lang', 'Organizer')" />
								</label>
								<input id="field_organizer" name="organizer" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<xsl:attribute name="value">
										<xsl:value-of select="application/organizer"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_homepage">
									<xsl:value-of select="php:function('lang', 'Homepage for the event')" />
								</label>
								<input id="field_homepage" name="homepage" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<xsl:attribute name="value">
										<xsl:value-of select="application/homepage"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_description">
									<xsl:value-of select="php:function('lang', 'description')" />
								</label>
								<xsl:if test="config/application_description">
									<p>
										<xsl:value-of select="config/application_description"/>
									</p>
								</xsl:if>
								<textarea id="field_description" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="description">
									<xsl:value-of select="application/description"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label for="field_equipment">
									<xsl:value-of select="php:function('lang', 'Extra info')" />
								</label>
								<xsl:if test="config/application_equipment">
									<p>
										<xsl:value-of select="config/application_equipment"/>
									</p>
								</xsl:if>
								<textarea id="field_equipment" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="equipment">
									<xsl:value-of select="application/equipment"/>
								</textarea>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>2. <xsl:value-of select="php:function('lang', 'Where?')" /></h3>
								</legend>
							</div>
							<xsl:if test="config/application_where">
								<p>
									<xsl:value-of select="config/application_where"/>
								</p>
							</xsl:if>
							<div class="pure-control-group">
								<label for="field_building_name">
									<xsl:value-of select="php:function('lang', 'Building')" />
								</label>
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="application/building_id"/>
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
										<xsl:value-of select="application/building_name"/>
									</xsl:attribute>
								</input>
								<div id="building_container"></div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Resources')" />
								</label>
								<input type="hidden" data-validation="application_resources">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please choose at least 1 resource')" />
									</xsl:attribute>
								</input>
								<div id="resources_container" style="display:inline-block;">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>3. <xsl:value-of select="php:function('lang', 'When?')" /></h3>
								</legend>
							</div>
							<xsl:if test="config/application_when">
								<p>
									<xsl:value-of select="config/application_when"/>
								</p>
							</xsl:if>
							<div id="dates-container">
								<input type="hidden" data-validation="application_dates">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Invalid date')" />
									</xsl:attribute>
								</input>
								<input type="hidden" id="date_format" />
								<xsl:for-each select="application/dates">
									<xsl:variable name="index" select="position()-2"/>
									<xsl:choose>
										<xsl:when test="position() > 1">
											<div class="date-container">
												<a href="javascript:void(0);" class="close-btn btnclose">-</a>
												<div class="pure-control-group">
													<label for="start_date_{$index}">
														<xsl:value-of select="php:function('lang', 'From')" />
													</label>
													<input class="newaddedpicker datetime pure-input-2-3" id="start_date_{$index}" type="text" name="from_[]">
														<!--input id="field_{position()}_from" name="from_[]" type="text"-->
														<xsl:attribute name="value">
															<xsl:value-of select="from_"/>
														</xsl:attribute>
														<xsl:attribute name="readonly">
															<xsl:text>readonly</xsl:text>
														</xsl:attribute>

													</input>
												</div>
												<div class="pure-control-group">
													<label for="end_date">
														<xsl:value-of select="php:function('lang', 'To')" />
													</label>
													<input class="newaddedpicker datetime pure-input-2-3" id="end_date_{$index}" type="text" name="to_[]">
														<xsl:attribute name="value">
															<xsl:value-of select="to_"/>
														</xsl:attribute>
														<xsl:attribute name="readonly">
															<xsl:text>readonly</xsl:text>
														</xsl:attribute>

													</input>
												</div>
											</div>
										</xsl:when>
										<xsl:otherwise>
											<div class="date-container">
												<a href="javascript:void(0);" class="close-btn btnclose">-</a>
												<div class="pure-control-group">
													<label for="start_date">
														<xsl:value-of select="php:function('lang', 'From')" />
													</label>
													<input class="datetime pure-input-2-3" id="start_date" type="text" name="from_[]">
														<xsl:attribute name="value">
															<xsl:value-of select="from_"/>
														</xsl:attribute>
														<xsl:attribute name="readonly">
															<xsl:text>readonly</xsl:text>
														</xsl:attribute>

													</input>
												</div>
												<div class="pure-control-group">
													<label for="end_date">
														<xsl:value-of select="php:function('lang', 'To')" />
													</label>
													<input class="datetime pure-input-2-3" id="end_date" type="text" name="to_[]">
														<xsl:attribute name="value">
															<xsl:value-of select="to_"/>
														</xsl:attribute>
														<xsl:attribute name="readonly">
															<xsl:text>readonly</xsl:text>
														</xsl:attribute>

													</input>
												</div>
											</div>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:for-each>
							</div>
							<div>
								<a href="javascript:void(0)" id="add-date-link">
									<xsl:value-of select="php:function('lang', 'Add another date')" />
								</a>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>4. <xsl:value-of select="php:function('lang', 'Who?')" /></h3>
								</legend>
							</div>
							<xsl:if test="config/application_who">
								<p>
									<xsl:value-of select="config/application_who"/>
								</p>
							</xsl:if>
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
									<ul id="audience" style="list-style:none;padding-left:10px;">
										<xsl:for-each select="audience">
											<li>
												<label>
													<input type="radio" name="audience[]">
														<xsl:attribute name="value">
															<xsl:value-of select="id"/>
														</xsl:attribute>
														<xsl:if test="../application/audience=id">
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
									<label for="field_from">
										<xsl:value-of select="php:function('lang', 'Number of participants')" />
									</label>
									<xsl:if test="config/application_howmany">
										<p>
											<xsl:value-of select="config/application_howmany"/>
										</p>
									</xsl:if>
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
																<xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="text" class="input50">
															<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
												</tr>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Contact information')" />
									</h3>
								</legend>
							</div>
							<xsl:if test="config/application_contact_information">
								<p>
									<xsl:value-of select="config/application_contact_information"/>
								</p>
							</xsl:if>
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
										<xsl:value-of select="application/contact_name"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_contact_email">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<input id="field_contact_email" name="contact_email" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a contact email')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="application/contact_email"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_contact_phone">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<input id="field_contact_phone" name="contact_phone" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="value">
										<xsl:value-of select="application/contact_phone"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
							</div>
							<div class="pure-control-group">
								<label for="field_street">
									<xsl:value-of select="php:function('lang', 'Street')"/>
								</label>
								<input id="field_responsible_street" name="responsible_street" type="text" value="{application/responsible_street}" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Street')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_zip_code">
									<xsl:value-of select="php:function('lang', 'Zip code')"/>
								</label>
								<input type="text" name="responsible_zip_code" id="field_responsible_zip_code" value="{application/responsible_zip_code}" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Zip code')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_responsible_city">
									<xsl:value-of select="php:function('lang', 'Postal City')"/>
								</label>
								<input type="text" name="responsible_city" id="field_responsible_city" value="{application/responsible_city}" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Postal City')"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
						<div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
							<div class="pure-g">
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<div class="heading">
										<legend>
											<h3>
												<xsl:value-of select="php:function('lang', 'Terms and conditions')" />
											</h3>
										</legend>
									</div>
									<xsl:if test="config/application_terms">
										<p>
											<xsl:value-of select="config/application_terms"/>
										</p>
									</xsl:if>

									<input type="hidden" data-validation="regulations_documents">
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'You must accept to follow all terms and conditions of lease first')" />
										</xsl:attribute>
									</input>
									<div id='regulation_documents'></div>
									<xsl:if test="config/application_terms2">
										<p>
											<xsl:value-of select="config/application_terms2"/>
										</p>
									</xsl:if>

								</div>
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1"></div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="pure-control-group">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
				</xsl:attribute>
			</input>
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="application/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		$('#field_customer_identifier_type,#field_customer_ssn,#field_customer_organization_number').removeClass('pure-input-1').addClass('pure-u-1 pure-u-sm-1-2 pure-u-md-1');
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
		var initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		var initialAcceptAllTerms = true;
		var initialSelection = <xsl:value-of select="application/resources_json"/>;
		var initialAudience = <xsl:value-of select="application/audience_json"/>;
		$('#field_customer_identifier_type').attr("data-validation","customer_identifier").attr("data-validation-error-msg", "<xsl:value-of select="php:function('lang', 'Customer identifier type is required')" />");
	</script>
</xsl:template>
