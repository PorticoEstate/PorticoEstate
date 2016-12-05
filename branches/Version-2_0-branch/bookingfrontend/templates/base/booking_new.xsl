<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'New Booking')"/>
			</dt>
		</dl>
		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id="booking_form">
			<input type="hidden" name="season_id" value="{booking/season_id}"/>
			<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
        
			<div class="pure-g">
				<div class="pure-u-1">
					<dl clas="form-col">
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
						</dd>
					</dl>
				</div>
			</div>
        
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')"/>
							</label>
						</dt>
						<dd>
							<input id="field_building_id" name="building_id" type="hidden" value="{booking/building_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
								</xsl:attribute>
							</input>
							<xsl:value-of select="booking/building_name" />
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_resources">
								<xsl:value-of select="php:function('lang', 'Resources')"/>
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
						<dt>
							<label for="field_org">
								<xsl:value-of select="php:function('lang', 'Organization')"/>
							</label>
						</dt>
						<dd>
							<xsl:value-of select="booking/organization_name" />
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_group">
								<xsl:value-of select="php:function('lang', 'Group')"/>
							</label>
						</dt>
						<dd>
							<div id="group_container">
								<select name="group_id">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select a group')" />
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="php:function('lang', 'Select a group')"/>
									</option>
									<xsl:for-each select="groups">
										<option value="{id}">
											<xsl:if test="../booking/group_id = id">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'From')"/>
							</label>
						</dt>
						<dd>
							<!--div class="time-picker">
				<xsl:value-of select="date_from"/>
				<input id="field_from" name="from_" type="text">
					<xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
				</input>
							</div-->
							<input class="datetime" id="field_from" type="text" name="from_">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a from date')" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:value-of select="booking/from_" />
								</xsl:attribute>
							</input>
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_to">
								<xsl:value-of select="php:function('lang', 'To')"/>
							</label>
						</dt>
						<dd>
							<!--div class="time-picker">
				<xsl:value-of select="date_to"/>
				<input id="field_to" name="to_" type="text">
					<xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
				</input>
							</div-->
							<input class="datetime" id="field_to" type="text" name="to_">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:value-of select="booking/to_" />
								</xsl:attribute>
							</input>
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_repeat_until">
								<xsl:value-of select="php:function('lang', 'Recurring booking')" />
							</label>
						</dt>
						<dd>
							<label>
								<input type="checkbox" name="outseason" id="outseason">
									<xsl:if test="outseason='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Out season')" />
							</label>
						</dd>
						<dd>
							<label>
								<input type="checkbox" name="recurring" id="recurring">
									<xsl:if test="recurring='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Repeat until')" />
							</label>
						</dd>
						<dd>
							<input class="datetime" id="field_repeat_until" name="repeat_until" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="repeat_until" />
								</xsl:attribute>
							</input>
						</dd>
						<!--dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
						</dd-->
					</dl>
					<dl class="form-col">
						<dt>
							<xsl:value-of select="php:function('lang', 'Interval')" />
						</dt>
						<dd>
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
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt>
							<label for="field_from">
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
											<xsl:if test="../booking/audience=id">
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
					</dl>
					<dl class="form-col">
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
														<xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" size="4">
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
						</dd>
					</dl>
				</div>
			</div>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Create')"/>
					</xsl:attribute>
				</input>
				<a class="cancel" href="" onclick="history.back(1); return false">
					<xsl:value-of select="php:function('lang', 'Go back')"/>
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="booking/resources_json" />;
		var initialAudience = <xsl:value-of select="booking/audience_json" />;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
	</script>
</xsl:template>
