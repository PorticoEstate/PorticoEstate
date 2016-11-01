<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">
		<ul class="pathway">
			<li>
				<xsl:value-of select="php:function('lang', 'Edit Events')" />
			</li>
			<li>#<xsl:value-of select="event/id"/></li>
		</ul>
		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id="event_form" name="event_form">
			<div class="pure-g">
				<div class="pure-u-1">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'Why')" />
						</dt>
						<dt>
							<label for="field_activity">
								<xsl:value-of select="php:function('lang', 'Activity')" />
							</label>
						</dt>
						<dd>
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
						</dd>
						<div class="clr"/>
						<dt>
							<label for="field_public">
								<xsl:value-of select="php:function('lang', 'Event type')"/>
							</label>
						</dt>
						<dd>
							<select id="field_public" name="is_public">
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
						</dd>

						<div class="clr"/>

						<dt>
							<label for="field_description">
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
						</dt>
						<dd>
							<textarea id="field_description" class="full-width" name="description">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a description')" />
								</xsl:attribute>
								<xsl:value-of select="event/description"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'Where')" />
						</dt>
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/building_id"/>
									</xsl:attribute>
								</input>
								<input id="field_building_name" name="building_name" type="text">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building')" />
									</xsl:attribute>
									<xsl:attribute name="value">
										<xsl:value-of select="event/building_name"/>
									</xsl:attribute>
								</input>
								<div id="building_container"/>
							</div>
						</dd>
						<dt>
							<label for="field_resources">
								<xsl:value-of select="php:function('lang', 'Resources')" />
							</label>
						</dt>
						<dd>
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
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'When')" />
						</dt>
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'From')" />
							</label>
						</dt>
						<dd>
							<xsl:value-of select="event/from_"/>
							<br />
							<input name="org_from" type="hidden">
								<xsl:attribute name="value">
									<xsl:value-of select="event/from_"/>
								</xsl:attribute>
							</input>
							<!--div class="time-picker">
								<input id="field_from" name="from_" type="text">
									<xsl:attribute name="value"><xsl:value-of select="event/from_"/></xsl:attribute>
								</input>
							</div-->
							<input class="datetime pure-input-2-3" id="from_" name="from_" type="text">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a from date')" />
								</xsl:attribute>
								<xsl:if test="event/from_ != ''">
									<xsl:attribute name="value">
										<xsl:value-of select="event/from_2" />
									</xsl:attribute>
								</xsl:if>
							</input>
						</dd>
						<dt>
							<label for="field_to">
								<xsl:value-of select="php:function('lang', 'To')" />
							</label>
						</dt>
						<dd>
							<xsl:value-of select="event/to_"/>
							<br />
							<input name="org_to" type="hidden">
								<xsl:attribute name="value">
									<xsl:value-of select="event/to_"/>
								</xsl:attribute>
							</input>
							<!--div class="time-picker">
								<input id="field_to" name="to_" type="text">
									<xsl:attribute name="value"><xsl:value-of select="event/to_"/></xsl:attribute>
								</input>
							</div-->
							<input class="datetime pure-input-2-3" id="to_" name="to_" type="text">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
								</xsl:attribute>
								<xsl:if test="event/to_ != ''">
									<xsl:attribute name="value">
										<xsl:value-of select="event/to_2" />
									</xsl:attribute>
								</xsl:if>
							</input>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'Who')" />
						</dt>
						<dt>
							<label>
								<xsl:value-of select="php:function('lang', 'Target audience')" />
							</label>
						</dt>
						<dd>
							<input type="hidden" data-validation="target_audience">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
								</xsl:attribute>
							</input>
							<ul id="audience">
								<xsl:for-each select="audience">
									<li>
										<input type="radio" name="audience[]">
											<xsl:attribute name="value">
												<xsl:value-of select="id"/>
											</xsl:attribute>
											<xsl:if test="../event/audience=id">
												<xsl:attribute name="checked">checked</xsl:attribute>
											</xsl:if>
										</input>
										<label>
											<xsl:value-of select="name"/>
										</label>
									</li>
								</xsl:for-each>
							</ul>
						</dd>
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'Number of participants')" />
							</label>
						</dt>
						<dd>
							<input type="hidden" data-validation="number_participants">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Number of participants is required')" />
								</xsl:attribute>
							</input>
							<table id="agegroup" class="pure-table pure-table-bordered">
								<thead>
									<tr>
										<th/>
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
												<input type="text" size="4">
													<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../event/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" size="4">
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
						</dd>
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'Contact information')" />
						</dt>
						<dt>
							<label for="field_contact_name">
								<xsl:value-of select="php:function('lang', 'Name')" />
							</label>
						</dt>
						<dd>
							<input id="field_contact_name" name="contact_name" type="text">
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
						</dd>
						<dt>
							<label for="field_contact_email">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
						</dt>
						<dd>
							<input id="field_contact_mail" name="contact_email" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="event/contact_email"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_contact_phone">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
						</dt>
						<dd>
							<input id="field_contact_phone" name="contact_phone" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="event/contact_phone"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_cost">
								<xsl:value-of select="php:function('lang', 'Cost')" />
							</label>
						</dt>
						<dd>
							<input id="field_cost" name="cost" type="text" readonly="readonly">
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
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt class="heading">
							<xsl:value-of select="php:function('lang', 'Invoice information')" />
						</dt>
						<xsl:copy-of select="phpgw:booking_customer_identifier(event, '')"/>
					</dl>
				</div>
			</div>

			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Save')"/>
					</xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href">
						<xsl:value-of select="event/cancel_link"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="event/resources_json" />;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resources Type')" />;
	</script>
</xsl:template>
