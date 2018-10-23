<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="booking-edit-page-content" class="margin-top-content">
    <div class="container wrapper">
		<div class="location">
			<span><a>
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Home')" />
			</a></span>
			<span><xsl:value-of select="php:function('lang', 'Booking')"/> #<xsl:value-of select="booking/id"/></span>										
		</div>

       	<div class="row">
			<form action="" method="POST" id="booking_form" class="col-md-8">
				<div class="col mb-4">
					<xsl:call-template name="msgbox"/>
				</div>

				<input type="hidden" name="season_id" value="{booking/season_id}"/>
				<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
				<input type="hidden" name="step" value="1"/>
				
				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Active')"/></label>
						<select id="field_active" class="form-control" name="active">
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
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
						<select name="activity_id" class="form-control" id="field_activity">
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

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Building')"/></label>
						<input id="field_building_id" class="form-control" name="building_id" type="hidden" value="{booking/building_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a building')" />
								</xsl:attribute>
						</input>
						<xsl:value-of select="booking/building_name"/>
					</div>
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Resources')"/></label>
						<input type="hidden" class="form-control" data-validation="application_resources">
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

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')"/></label>
						<input id="field_organization_id" class="form-control" name="organization_id" type="hidden" value="{booking/organization_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
								</xsl:attribute>
						</input>
						<xsl:value-of select="booking/organization_name"/>
					</div>
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Group')"/></label>
						<select name="group_id" class="form-control">
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
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'From')"/></label>
						<input class="form-control" id="field_from" type="text" name="from_">
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
					</div>
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'To')"/></label>
						<input class="form-control" id="field_to" type="text" name="to_">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:value-of select="booking/to_"/>
								</xsl:attribute>
						</input>
					</div>
				</div>				

				<div class="col-12 mb-5">
					<div class="form-group">
						<div>
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Recurring booking')" /></label>
							<input type="checkbox" class="mr-2" name="outseason" id="outseason">
								<xsl:if test="outseason='on'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:value-of select="php:function('lang', 'Out season')" />
						</div>
						<div>
							<input type="checkbox" class="mr-2" name="recurring" id="recurring">
								<xsl:if test="recurring='on'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:value-of select="php:function('lang', 'Repeat until')" />
						</div>
						<input class="form-control" id="field_repeat_until" name="repeat_until" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="repeat_until"/>
							</xsl:attribute>
						</input>
					</div>
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
						<input type="hidden" class="form-control" data-validation="target_audience">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
								</xsl:attribute>
						</input>
						<xsl:for-each select="audience">
									<div class="d-block">
										<input type="radio" class="mr-2" name="audience[]">
											<xsl:attribute name="value">
												<xsl:value-of select="id"/>
											</xsl:attribute>
											<xsl:if test="../booking/audience=id">
												<xsl:attribute name="checked">checked</xsl:attribute>
											</xsl:if>
										</input>
										<xsl:value-of select="name"/>
									</div>
						</xsl:for-each>
					</div>
				</div>

				<div class="col-12 mb-5">
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label>
						<input type="hidden" class="form-control" data-validation="number_participants">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Number of participants is required')" />
								</xsl:attribute>
							</input>
							<table id="agegroup" class="table">
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
												<input type="text" class="form-control" size="4">
													<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" class="form-control" size="4">
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
				</div>

				<div class="col mt-5">
					<input type="submit" class="btn btn-light mr-4">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Save')"/>
						</xsl:attribute>
					</input>
					<a class="cancel" href="" onclick="history.back(1); return false">
						<xsl:value-of select="php:function('lang', 'Go back')"/>
					</a>
				</div>


			</form>   
		</div>

	</div>
</div>

	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var initialAudience = <xsl:value-of select="booking/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang','Name', 'Resource Type')"/>;
	</script>
</xsl:template>
