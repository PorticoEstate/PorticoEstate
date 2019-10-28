<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container new-application-page pt-5" id="new-application-page">
		<form action="#" data-bind='' method="POST" id='application_form' enctype='multipart/form-data' name="form" novalidate="true" class="needs-validationm">
			<div class="row">

				<div class="col-md-8 offset-md-2">
			
					<a class="exitBtn float-right">
						<xsl:attribute name="href">
							<xsl:value-of select="application/frontpage_link"/>
						</xsl:attribute>
						<i class="fas fa-times" />
						<xsl:value-of select="php:function('lang', 'Exit to homepage')"/>
					</a>
				
					
					<h1 class="font-weight-bold">
						<xsl:value-of select="php:function('lang', 'New application')"/>
					</h1>

<!--					<p>
						<xsl:value-of select="config/application_new_application"/>
					</p>
					<hr class="mt-5 mb-5"></hr>-->

					<div class="mb-4">
						<xsl:call-template name="msgbox"/>
					</div>

					<input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId" />
					<input name="formstage" value="partial1" hidden="hidden"/>
					<h2 class="font-weight-bold mb-4">
						<xsl:value-of select="php:function('lang', 'Choose rent object and rentperiod')" />
					</h2>

					<p>
						<xsl:value-of select="php:function('lang', 'Application for')"/>:
						<xsl:value-of select="application/building_name"/>
						<br/>
					</p>


					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'resources')" />
						</label>
						<select id="resource_id" name="resources[]" class="form-control text-left w-100 custom-select" required="true">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'Choose resource')"/>
							</xsl:attribute>
							<xsl:if test="count(resource_list/options) > 1 ">
								<option value="">
								<xsl:value-of select="php:function('lang', 'No rent object chosen')" />
								</option>
							</xsl:if>
							<xsl:apply-templates select="resource_list/options"/>
						</select>
					</div>

					<!-- Select Time and Date Section -->
					<div class="form-group">
						<!-- Display Time Chosen -->
						<div class="form-group">
							<span class="font-weight-bold d-block mt-2 span-label">
								<xsl:value-of select="php:function('lang', 'Chosen rent period')" />
							</span>
							<div data-bind="foreach: date">
								<div class="d-block">
									<input required="true" name="from_[]" hidden="hidden" data-bind="value: from_"/>
									<input required="true" name="to_[]" hidden="hidden" data-bind="value: to_"/>
									<span data-bind='text: formatedPeriode'></span>
									
									<button class="ml-2" data-bind="click: $parent.removeDate">
										<i class="fas fa-minus-circle"></i>
									</button>
								</div>
							</div>
							<span id="inputTime" data-bind="if: date().length == 0" class="validationMessage applicationSelectedDates">
								<xsl:value-of select="php:function('lang', 'Select a date and time')" />
							</span>
						</div>
						<div class="form-group">
							<div class="row">
								<!-- Date Pick -->
								<div class="form-group col-lg-5 col-sm-12 col-12">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-calendar-alt"></i>
											</span>
										</div>
										<input id="start_date" type="text" onkeydown="return false" class="bookingDate form-control datepicker-btn">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'Date')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
								<!-- From Time Pick -->
								<div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-clock"></i>
											</span>
										</div>
										<input type="text" id="bookingStartTime" onkeydown="return false" disabled="disabled" class="form-control mr-2">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'from')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
								<!-- To Time Pick -->
								<div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-clock"></i>
											</span>
										</div>
										<input type="text" id="bookingEndTime" onkeydown="return false" disabled="disabled" class="form-control">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'to')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!-- Information About Event -->
					<hr class="mt-5 mb-5"></hr>
					<h2 class="font-weight-bold mb-4">
						<xsl:value-of select="php:function('lang', 'Information about the event')" />
					</h2>

					<!-- Target Audience Section-->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Target audience')" />
						</label>
						<div class="form-control text-left dropdown-toggle w-100" id="audienceDropdownBtn" type="input" data-toggle="dropdown">
							<xsl:value-of select="php:function('lang', 'Choose target audience')" />
							<span class="caret"></span>
						</div>

						<ul class="dropdown-menu px-2" data-bind="foreach: audiences" aria-label="Large">
							<li class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected"></li>
							<!-- <a class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected" href="#"></a> -->
						</ul>
						<input class="form-control" id="inputTargetAudience" required="true" type="text" style="display: none" name="audience[]"  data-bind="value: audienceSelectedValue"/>
					</div>		

					<!-- Estimated Number of Participants -->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Estimated number of participants')" />
						</label>
						<div class="p-2 border">
							<div class="row mb-2">
								<div class="col-3">
									<span class="span-label mt-2"></span>
								</div>
								<div class="col-4">
									<span>
										<xsl:value-of select="php:function('lang', 'Male')" />
									</span>
								</div>
								<div class="col-4">
									<xsl:value-of select="php:function('lang', 'Female')" />
								</div>
							</div>

							<div class="row mb-2" data-bind="foreach: agegroup">
								<span data-bind="text: id, visible: false"/>
								<div class="col-3">
									<span class="mt-2" data-bind="text: agegroupLabel"></span>
								</div>
								<div class="col-4">
									<input class="form-input sm-input maleInput" data-bind=""/>
								</div>
								<div class="col-4">
									<input class="form-input sm-input femaleInput" data-bind=""/>
								</div>
							</div>

						</div>
					</div>
					<!-- Upload Attachment -->
					<div id="attachment" class="form-group">
						<div class="textContainer">
							<label>
								<xsl:value-of select="php:function('lang', 'Upload Attachment')" />
							</label>
							<label>
								<xsl:value-of select="php:function('lang', 'optional')" />
							</label>
						</div>
					
						<div id="attachment-upload">
							<label for="field_name" class="upload-button">
								<xsl:value-of select="php:function('lang', 'Upload')" />
							</label>
							
						</div>
						<div id="show-attachment">
							<span id="field_name_input"></span>
							<a style="display: none" id="attachment-remove">Fjern Vedlegg</a>
							<!-- Input -->
							<input name="name" id='field_name' type='file' style="display: none" accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.txt,.pdf,.odt,.ods">
							</input>
						</div>
						<!-- Remove Attachment -->
						
					</div>


					<!-- Terms and Conditions -->
					<div class="form-group termAccept mb-5">
						<label>
							<xsl:value-of select="php:function('lang', 'legal condition')" />
						</label>
						<span data-bind="ifnot: termAccept" class="validationMessage">
							<xsl:value-of select="config/application_terms2"/>
						</span>
						<div class="form-check checkbox" data-bind="foreach: termAcceptDocs">
							<div>
								<label class="check-box-label d-inline">
									<input id="termsInput" class="form-check-input" type="checkbox" data-bind="checked: checkedStatus"/>
									<span class="label-text" data-bind=""></span>		
								</label>
								<a class="d-inline termAcceptDocsUrl" target="_blank" data-bind=""></a>
								<i class="fas fa-external-link-alt"></i>
							</div>
						</div>

					</div>

					<hr class="mt-5 mb-5"></hr>
					<!-- Submit -->
					<div id="submitContainer" class="form-group float-right text-center">
						<button id="submitBtn" class="btn btn-light" type="submit">
							<xsl:value-of select="php:function('lang', 'Next step')" />
						</button>
						<div id="submit-error" style="display: none">Vennligst fyll inn alle feltene!</div>
					</div>
				
				</div>
			</div>
		</form>

		<!--<pre data-bind="text: ko.toJSON(am, null, 2)"></pre>-->

		<div class="push"></div>
	</div>
	<script>
		var initialAcceptAllTerms = false;
		var initialSelection = <xsl:value-of select="application/resources_json"/>;
		var initialAudience = <xsl:value-of select="application/audience_json"/>;
		var initialDates = <xsl:value-of select="application/dates_json"/>;
		var initialAgegroups = <xsl:value-of select="application/agegroups_json"/>;
		var initialAcceptedDocs = <xsl:value-of select="application/accepted_documents_json"/>;
		var errorAcceptedDocs = '<xsl:value-of select="config/application_terms2"/>';
		var cache_refresh_token = "<xsl:value-of select="php:function('get_phpgw_info', 'server|cache_refresh_token')" />";
	</script>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
