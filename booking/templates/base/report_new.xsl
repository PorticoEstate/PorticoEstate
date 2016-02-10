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

	<form action="" method="POST" id='report_form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="report/tabs"/>
			<div id="report_new">
				<fieldset>
					<input type="hidden" name="report_id" value="{report/report_id}"/>
                        
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'what')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<label for="field_activity">
									<h4>
										<xsl:value-of select="php:function('lang', 'type')" />
									</h4>
								</label>
								<select name="report_type" id="report_type" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<xsl:for-each select="report_types">
										<option>
											<xsl:if test="selected = 1">
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
								<label for="field_activity">
									<h4>
										<xsl:value-of select="php:function('lang', 'Activity')" />
									</h4>
								</label>
								<select name="activity_id" id="field_activity" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-allowing">
										<xsl:text>positive</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select an activity')" />
									</xsl:attribute>
									<option value="-1">
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
						<div class="pure-u-1">
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
								<ul id= "variable_vertical" style="display:inline-block;list-style:none;padding:0px;margin:0px;">
									<li>
										<label>
											<input id="check_all_buildings" type="checkbox" value="1" name="all_buildings" >
											</input>
											<xsl:value-of select="php:function('lang', 'All')" />
										</label>
									</li>
								</ul>
								<div id="building_container">
									<input id="field_building_id" name="building_id" type="hidden">
										<xsl:attribute name="value">
											<xsl:value-of select="report/building_id"/>
										</xsl:attribute>
									</input>
									<input id="field_building_name" name="building_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
										<xsl:attribute name="value">
											<xsl:value-of select="report/building_name"/>
										</xsl:attribute>
									</input>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<h4>
										<xsl:value-of select="php:function('lang', 'Resources')" />
									</h4>
								</label>
								<div id="resources_container">
									<span class="select_first_text">
										<xsl:value-of select="php:function('lang', 'Select a building first')" />
									</span>
								</div>
							</div>
						</div>
                            
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'When?')" />
									</h3>
								</legend>
							</div>
							<div class="pure-g">
								<div id="dates-container" class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<div class="date-container">
										<div class="pure-control-group">
											<label for="start_date">
												<h4>
													<xsl:value-of select="php:function('lang', 'From')" />
												</h4>
											</label>
											<input class="datetime pure-input-1-2" id="start_date" name="start_date" type="text" value="{report/start_date}">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="php:function('lang', 'Please enter a from date')" />
												</xsl:attribute>
											</input>
										</div>
										<div class="pure-control-group">
											<label for="end_date">
												<h4>
													<xsl:value-of select="php:function('lang', 'To')" />
												</h4>
											</label>
											<input class="datetime pure-input-1-2" id="end_date" name="end_date" type="text" value="{report/end_date}">
												<xsl:attribute name="data-validation">
													<xsl:text>required</xsl:text>
												</xsl:attribute>
												<xsl:attribute name="data-validation-error-msg">
													<xsl:value-of select="php:function('lang', 'Please enter an end date')" />
												</xsl:attribute>
											</input>
										</div>
									</div>
									
									<div class="pure-g" >
										<div class="pure-u-lg-5-5 pure-u-md-1-1 pure-u-sm-1-1" >
											<h4>
												<xsl:value-of select="php:function('lang', 'start time')" />
											</h4>
										</div>

										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12">
											<input maxlength="2" size="2" id="start_hour" name="start_hour" type="text" placeholder = "00" value="{report/start_hour}"></input>
										</div>
										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12" style="text-align:center;">
											:
										</div>
										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12" >
											<input maxlength="2" size="2" id="start_minute" name="start_minute" type="text" placeholder = "00" value="{report/start_minute}"></input>
										</div>

										<div class="pure-u-lg-5-5 pure-u-md-1-1 pure-u-sm-1-1">
											<h4>
												<xsl:value-of select="php:function('lang', 'end time')" />
											</h4>
										</div>

										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12" >
											<input maxlength="2" size="2" class="pure-input" id="end_hour" name="end_hour" type="text" placeholder = "00" value="{report/end_hour}"></input>
										</div>
										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12" style="text-align:center;">
											:
										</div>
										<div class="pure-u-lg-1-24 pure-u-md-1-12 pure-u-sm-1-12" >
											<input maxlength="2" size="2" class="pure-input" id="end_minute" name="end_minute" type="text" placeholder = "00" value="{report/end_minute}"></input>
										</div>
									</div>

								</div>
							</div>
							<div class="pure-control-group">
								<label for="field_weekday" style="vertical-align:top;">
									<h4>
										<xsl:value-of select="php:function('lang', 'Weekdays')" />
									</h4>
								</label>
								<ul id="field_weekday" style="display:inline-block;list-style:none;padding:0px;margin:0px;">
									<xsl:for-each select="report/days">
										<li>
											<label>
												<input type="checkbox" value="{id}" name="weekdays[]" >
													<xsl:attribute name="data-validation">checkbox_group</xsl:attribute>
													<xsl:attribute name="data-validation-qty">min1</xsl:attribute>
													<!--xsl:if test="selected = 1"-->
													<xsl:attribute name="checked">checked</xsl:attribute>
													<!--/xsl:if-->
													<xsl:attribute name="data-validation-error-msg">
														<xsl:value-of select="php:function('lang', 'Please choose at least 1 weekday')" />
													</xsl:attribute>
												</input>
												<xsl:value-of select="name" />
											</label>
										</li>
									</xsl:for-each>
								</ul>
							</div>
						</div>

						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'variables')" />
									</h3>
								</legend>
							</div>
							<div class="pure-g">
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label>
										<h4>
											<xsl:value-of select="php:function('lang', 'Horizontal')" />
										</h4>
									</label>
									<ul id= "variable_horizontal" style="display:inline-block;list-style:none;padding:0px;margin:0px;">
										<xsl:for-each select="report/variables_horizontal">
											<li>
												<label>
													<input type="radio" value="{id}" name="variable_horizontal" >
														<xsl:if test="selected = 1">
															<xsl:attribute name="checked">checked</xsl:attribute>
														</xsl:if>
													</input>
													<xsl:value-of select="name" />
												</label>
											</li>
										</xsl:for-each>
										<div id="custom_elements_horizontal"></div>
									</ul>
								</div>
								<div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
									<label>
										<h4>
											<xsl:value-of select="php:function('lang', 'vertical')" />
										</h4>
									</label>
									<ul id= "variable_vertical" style="display:inline-block;list-style:none;padding:0px;margin:0px;">
										<xsl:for-each select="report/variables_vertical">
											<li>
												<label>
													<input type="radio" value="{id}" name="variable_vertical" >
														<xsl:if test="selected = 1">
															<xsl:attribute name="checked">checked</xsl:attribute>
														</xsl:if>
													</input>
													<xsl:value-of select="name" />
												</label>
											</li>
										</xsl:for-each>
										<div id = "custom_elements_vertical"></div>
									</ul>
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
					<xsl:value-of select="php:function('lang', 'Create report')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="report/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'From', 'To', 'Resource Type', 'Select a building first')"/>;
	</script>
</xsl:template>
