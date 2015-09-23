<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        #agegroup td {padding: 0 0.3em;}        
    </style>
    <!--div id="content">

	<dl class="form">
		<dt class="heading"><xsl:value-of select="php:function('lang', 'New report')"/></dt>
	</dl-->
	<xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="report/tabs"/>
			<div id="report_new">
				<fieldset>
					<input type="hidden" name="application_id" value="{report/application_id}"/>
                        
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
									<h4>
										<xsl:value-of select="php:function('lang', 'Activity')" />
									</h4>
								</label>
								<select name="activity_id" id="field_activity" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<option value="">
										<xsl:value-of select="php:function('lang', '-- select an activity --')" />
									</option>
									<xsl:for-each select="activities">
										<option>
											<xsl:if test="../report/activity_id = id">
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
									<h4>
										<xsl:value-of select="php:function('lang', 'Description')" />
									</h4>
								</label>
								<textarea id="field_description" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="description">
									<xsl:value-of select="report/description"/>
								</textarea>
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
								<label>
									<h4>
										<xsl:value-of select="php:function('lang', 'Building')" />
									</h4>
								</label>
								<!--div class="autocomplete"-->
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="report/building_id"/>
									</xsl:attribute>
								</input>
								<input id="field_building_name" name="building_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:attribute name="value">
										<xsl:value-of select="report/building_name"/>
									</xsl:attribute>
								</input>
								<!--div id="building_container"/>
								</div-->
							</div>
							<div class="pure-control-group">
								<label>
									<h4>
										<xsl:value-of select="php:function('lang', 'Resources')" />
									</h4>
								</label>
								<div id="resources_container">
									<xsl:value-of select="php:function('lang', 'Select a building first')" />
								</div>
							</div>
						</div>
                            
						<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'When?')" />
									</h3>
								</legend>
							</div>
							<div id="dates-container"  class="pure-control-group">
								<xsl:for-each select="report/dates">
									<div class="date-container">
										<div class="pure-control-group">
											<label for="start_date">
												<xsl:value-of select="php:function('lang', 'From')" />
											</label>
											<input class="datetime pure-input-2-3" id="start_date" name="start_date" type="text">
											</input>
										</div>
										<div class="pure-control-group">
											<label for="end_date">
												<xsl:value-of select="php:function('lang', 'To')" />
											</label>
											<input class="datetime pure-input-2-3" id="end_date" name="end_date" type="text">
											</input>
										</div>
									</div>
								</xsl:for-each>
								<div class="pure-control-group">
									<label for="start_time">
										<xsl:value-of select="php:function('lang', 'start_time')" />
									</label>
									<span>
										<input maxlength="2" size="2" id="start_hour" name="start_hour" type="text">
										</input>
										<xsl:text>:</xsl:text>
										<input maxlength="2" size="2" id="start_minute" name="start_minute" type="text">
										</input>
									</span>
								</div>
								<div class="pure-control-group">
									<label for="end_time">
										<xsl:value-of select="php:function('lang', 'end_time')" />
									</label>
									<input maxlength="2" size="2" class="pure-input" id="end_hour" name="end_hour" type="text">
									</input>
									<xsl:text>:</xsl:text>
									<input maxlength="2" size="2" class="pure-input" id="end_minute" name="end_minute" type="text">
									</input>
								</div>

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
										<h4>
											<xsl:value-of select="php:function('lang', 'Target audience')" />
										</h4>
									</label>
									<ul style="list-style:none;">
										<xsl:for-each select="audience">
											<li>
												<label style="display:inline-block;">
													<input type="checkbox" name="audience[]">
														<xsl:attribute name="value">
															<xsl:value-of select="id"/>
														</xsl:attribute>
														<xsl:if test="../report/audience=id">
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
										<h4>
											<xsl:value-of select="php:function('lang', 'Number of participants')" />
										</h4>
									</label>
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
										<tbody>
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
																<xsl:value-of select="../report/agegroups/male[../agegroup_id = $id]"/>
															</xsl:attribute>
														</input>
													</td>
													<td>
														<input type="text" class="input50">
															<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
															<xsl:attribute name="value">
																<xsl:value-of select="../report/agegroups/female[../agegroup_id = $id]"/>
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
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Create')"/>
				</xsl:attribute>
			</input>
			<a class="cancel">
				<xsl:attribute name="href">
					<xsl:value-of select="report/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="report/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'From', 'To', 'Resource Type')"/>;
	</script>
</xsl:template>
