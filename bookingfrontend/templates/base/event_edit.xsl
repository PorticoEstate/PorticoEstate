<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="event-edit-page-content" class="margin-top-content">
        	<div class="container wrapper">
				<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span><xsl:value-of select="php:function('lang', 'Edit Events')" /></span>
					<span>#<xsl:value-of select="event/id"/></span>
															
				</div>

            	<div class="row">					

					<form action="" method="POST" id="event_form" name="form" class="col-md-8">

						<div class="col mb-4">
							<xsl:call-template name="msgbox"/>
						</div>

					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></h5>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></label>
						<div class="autocomplete">
							<input id="field_building_id" class="form-control" name="building_id" type="hidden">
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
							<input id="field_building_name" class="form-control" name="building_name" type="text">
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
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Resource (2018)')" /></label>
						<button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
							<xsl:value-of select="php:function('lang', 'choose')" />
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu px-2 resourceDropdown" data-bind="foreach: bookableresource">
							<li>
								<div class="form-check checkbox checkbox-primary">
									<label class="check-box-label">
										<input class="form-check-input choosenResource" type="checkbox" name="resources[]" data-bind="textInput: id, checked: selected" />
										<span class="label-text" data-bind="text: name"></span>
									</label>
								</div>
							</li>
						</ul>
					</div>

					<div class="form-group">
						<span class="font-weight-bold d-block mt-2 span-label">
							<xsl:value-of select="php:function('lang', 'Chosen resources (2018)')" />
						</span>
						<div data-bind="foreach: bookableresource">
							<span class="selectedItems mr-2" data-bind='text: selected() ? name : "", visible: selected()'></span>
						</div>
						<span data-bind="ifnot: isResourceSelected" class="isSelected validationMessage">
							<xsl:value-of select="php:function('lang', 'No resource chosen (2018)')" />
						</span>
					</div>
					<div class="row">
							<div class="form-group col-lg-6">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'From')" /></label>
								<xsl:value-of select="event/from_"/>
								<br />
								<input name="org_from" class="form-control" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="event/from_"/>
									</xsl:attribute>
								</input>
								<!--div class="time-picker">
									<input id="field_from" name="from_" type="text">
										<xsl:attribute name="value"><xsl:value-of select="event/from_"/></xsl:attribute>
									</input>
								</div-->
								<input class="form-control from_" name="from_" type="text">
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
							</div>

							<div class="form-group col-lg-6">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'To')" /></label>
								<xsl:value-of select="event/to_"/>
								<br />
								<input name="org_to" class="form-control" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="event/to_"/>
									</xsl:attribute>
								</input>
								<!--div class="time-picker">
									<input id="field_to" name="to_" type="text">
										<xsl:attribute name="value"><xsl:value-of select="event/to_"/></xsl:attribute>
									</input>
								</div-->
								<input class="form-control to_" name="to_" type="text">
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
							</div>
						</div>

					<hr class="mt-5 mb-5"></hr>

					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Information about the event (edit)')" /></h5>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
						<div class="dropdown d-inline-block">
							<button class="btn btn-secondary dropdown-toggle d-inline mr-2 btn-sm" id="audienceDropdownBtn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<xsl:value-of select="php:function('lang', 'choose')" />
							</button>
							<div class="dropdown-menu" data-bind="foreach: audiences" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected" href="#"></a>
							</div>
							<input type="text" name="audience[]" hidden="hidden" data-bind="value: audienceSelectedValue" />
						</div>
					</div>

							<div class="form-group">
								<label class="text-uppercase" for="field_activity">
									<xsl:value-of select="php:function('lang', 'Activity')" />
								</label>
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

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Event type')"/></label>
								<select id="field_public" class="form-control" name="is_public">
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

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Event name')" /></label>
						<input type="text" class="form-control" name="name" value="{event/name}"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organizer')" /></label>
						<input type="text" class="form-control" name="organizer" value="{event/organizer}"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Homepage for the event')" /></label>
						<input type="text" class="form-control" name="homepage" value="{event/homepage}"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'description')" /></label>
						<textarea id="field_description" class="form-control" rows="3" name="description">
							<xsl:value-of select="event/description"/>
						</textarea>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="config/application_equipment"/></label>
						<textarea class="form-control" name="equipment">
							<xsl:value-of select="event/equipment"/>
						</textarea>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Estimated number of participants')" /></label>
						<div class="p-2 border">
							<div class="row mb-2">
								<div class="col-3">
									<span class="span-label mt-2"></span>
								</div>
								<div class="col-4">
									<span><xsl:value-of select="php:function('lang', 'Male')" /></span>
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
									<input class="form-control sm-input maleInput" data-bind=""/>
								</div>
								<div class="col-4">
									<input class="form-control sm-input femaleInput" data-bind=""/>
								</div>
							</div>
						</div>
					</div>


					<hr class="mt-5 mb-5"></hr>

					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Contact and invoice information')" /></h5>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
								<input id="field_contact_name" class="form-control" name="contact_name" type="text">
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

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
								<input id="field_contact_mail" class="form-control" name="contact_email" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="event/contact_email"/>
									</xsl:attribute>
								</input>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
								<input id="field_contact_phone" class="form-control" name="contact_phone" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="event/contact_phone"/>
									</xsl:attribute>
								</input>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Cost')" /></label>
								<input id="field_cost" class="form-control" name="cost" type="text" readonly="readonly">
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

					<input type="text" id="customer_identifier_type_hidden_field" hidden="hidden" value="{event/customer_identifier_type}"/>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="customer_identifier_type" id="privateRadio" data-bind="checked: typeApplicationRadio" value="ssn"/>
						<label class="form-check-label" for="privateRadio"><xsl:value-of select="php:function('lang', 'Private event')" /></label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="customer_identifier_type" id="orgRadio" data-bind="checked: typeApplicationRadio" value="organization_number"/>
						<label class="form-check-label" for="orgRadio"><xsl:value-of select="php:function('lang', 'organization')" /></label>
					</div>
					<p data-bind="ifnot: typeApplicationSelected, visible: typeApplicationValidationMessage" class="isSelected validationMessage mt-2 mb-2"><xsl:value-of select="php:function('lang', 'choose a')" /></p>

					<div class="form-group mt-2" data-bind="visible: typeApplicationRadio() === 'organization_number'">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'organization number')" /></label>
						<input name="customer_organization_number" value="{event/customer_organization_number}" type="text" class="form-control"/>
					</div>

					<div class="form-group mt-2" data-bind="visible: typeApplicationRadio() === 'ssn'">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Ssn')" /></label>
						<input type="text" class="form-control" name="customer_ssn" value="{event/customer_ssn}"/>
					</div>


						<div class="form-group mt-5">
							<input type="submit" class="btn btn-light mr-4">
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
            
        	</div>
    	
	</div>
    <div class="push"></div>

	<script type="text/javascript">
		var initialSelection = <xsl:value-of select="event/resources_json" />;
		var initialSelectionAudience = <xsl:value-of select="event/audiences_json" />;
		var initialSelectionAgegroup = <xsl:value-of select="event/agegroups_json" />;
		var building_id = <xsl:value-of select="event/building_id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resources Type')" />;
		
		$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }");
  		$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }");
		
		EventEditModel = GenerateUIModelForResourceAudienceAndAgegroup();
		eem = new EventEditModel();
        ko.applyBindings(eem, document.getElementById("event-edit-page-content"));
		
		AddBookableResourceData(building_id, initialSelection, eem.bookableresource);
		AddAudiencesAndAgegroupData(building_id, eem.agegroup, initialSelectionAgegroup, eem.audiences, initialSelectionAudience);
		eem.audienceSelectedValue(<xsl:value-of select="event/audience" />);
		eem.typeApplicationRadio($("#customer_identifier_type_hidden_field").val());
		YUI({ lang: 'nb-no' }).use(
			'aui-timepicker',
			function(Y) {
			new Y.TimePicker(
				{
				trigger: '.to_, .from_',
				popover: {
					zIndex: 99999
				},
				mask: '%H:%M',
				on: {
					selectionChange: function(event) { 
						new Date(event.newSelection);
						$(this).val(event.newSelection);
					}
				}
				}
			);
			}
		);
	</script>
</xsl:template>
