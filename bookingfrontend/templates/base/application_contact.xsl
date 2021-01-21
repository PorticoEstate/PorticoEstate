<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container new-application-page pt-5 my-container-top-fix" id="new-application-partialtwo">
		<a class="btn btn-light">
			<xsl:attribute name="href">
				<xsl:value-of select="application/frontpage_url"/>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Go back')" />
		</a>
		<form action="" method="POST" id='application_form' name="form" class="needs-validation" novalidate="true">
			<div class="row mb-5">
				<div class="col-md-8 offset-md-2" data-bind="visible: !applicationSuccess()">
					<h1 class="font-weight-bold">
						<xsl:value-of select="php:function('lang', 'Contact and invoice information')" />
					</h1>
					<p>
						<xsl:value-of select="config/application_contact"/>
					</p>
					<hr class="mt-5 mb-5"></hr>
					<div class="mb-4">
						<xsl:call-template name="msgbox"/>
					</div>
					<h2 class="font-weight-bold mb-4">
						<xsl:value-of select="php:function('lang', 'applications')" />
					</h2>
					<p class="validationMessage" data-bind="visible: applicationCartItems().length == 0">
						<xsl:value-of select="php:function('lang', 'applicationcart empty')" />
					</p>
					<div data-bind="visible: applicationCartItems().length != 0">
						<div data-bind="foreach: applicationCartItems">
							<div class="applications p-4 mb-2">
								<div class="row">
									<span class="col-5" data-bind="text: building_name"></span>
									<div data-bind="" class="col-5">
										<span class="mr-3" data-bind="text: joinedResources"></span>
									</div>
									<div class="col-2 text-right">
										<span data-bind="click: $parent.deleteItem" class="far fa-trash-alt mr-2"></span>
									</div>
								</div>
								<div class="row" data-bind="foreach: dates">
									<span class="col-5" data-bind="text: date"></span>
									<span class="col-6" data-bind="text: periode"></span>
								</div>
							</div>
						</div>
						<hr class="mt-5 mb-5"></hr>
						<label>
							<xsl:value-of select="php:function('lang', 'invoice information')" />*</label>
						<input type="text" id="customer_identifier_type_hidden_field" hidden="hidden" value="{application/customer_identifier_type}"/>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="customer_identifier_type" id="privateRadio" data-bind="checked: typeApplicationRadio" value="ssn"/>
							<label class="form-check-label" for="privateRadio">
								<xsl:value-of select="php:function('lang', 'Private event')" />
							</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="customer_identifier_type" id="orgRadio" data-bind="checked: typeApplicationRadio" value="organization_number"/>
							<label class="form-check-label" for="orgRadio">
								<xsl:value-of select="php:function('lang', 'organization')" />
							</label>
						</div>
						<p data-bind="ifnot: typeApplicationSelected, visible: typeApplicationValidationMessage" class="isSelected validationMessage">
							<xsl:value-of select="php:function('lang', 'choose a')" />
						</p>
						<!-- Organization Number -->
						<!--						<div class="form-group" data-bind="visible: typeApplicationRadio() === 'organization_number'">
							<label>
								<xsl:value-of select="php:function('lang', 'organization number')" />*</label>
							<input name="customer_organization_number" value="{application/customer_organization_number}" type="text" class="form-control" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi gyldig organisasjonsnummer.
							</div>
						</div>-->

						<div class="form-group" data-bind="visible: typeApplicationRadio() === 'organization_number'">
							<label>
								<xsl:value-of select="php:function('lang', 'organization number')" />*</label>
							<xsl:for-each select="delegate_data">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="customer_organization_number" id="customer_organization_number_{id}" value="{id}_{organization_number}" checked="checked"/>
									<label class="form-check-label" for="exampleRadios1">
										<xsl:value-of select="organization_number"/>
										[ <xsl:value-of select="name"/> ]
									</label>
								</div>
							</xsl:for-each>
							<div class="invalid-feedback">
								Vennligst oppgi gyldig organisasjonsnummer.
							</div>

							<label>
								<a id="add_new_value" href="#" data-toggle="modal" data-target="#new_organization">
									<img src="{add_img}" width="23"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="php:function('lang', 'new organization')"/>
								</a>
							</label>

						</div>

						<!-- Customer Personal Number -->
						<div class="form-group" data-bind="visible: typeApplicationRadio() === 'ssn'">
							<xsl:if test="string-length(application/customer_ssn)=0">
								<label>
									<xsl:value-of select="php:function('lang', 'Ssn')" />
									<xsl:text>*</xsl:text>
								</label>
							</xsl:if>
							<input class="form-control" name="customer_ssn" value="{application/customer_ssn}" required="true">
								<xsl:choose>
									<xsl:when test="application/customer_ssn != ''">
										<xsl:attribute name="type">
											<xsl:text>hidden</xsl:text>
										</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="type">
											<xsl:text>number</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="min">
											<xsl:text>11</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="max">
											<xsl:text>11</xsl:text>
										</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</input>
							<div class="invalid-feedback">
								Vennligst oppgi gyldig personnummer.
							</div>
						</div>
						<!-- Contact Name -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_name')" />*</label>
							<input id="contactName" type="text" class="form-control" name="contact_name" value="{application/contact_name}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi navn.
							</div>
						</div>

						<!-- Street Name -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsible_street')" />*</label>
							<input type="text" class="form-control" name="responsible_street" value="{application/responsible_street}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi gatenavn.
							</div>
						</div>
						<!-- Zip Code -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsible_zip_code')" />*</label>
							<input type="text" class="form-control" name="responsible_zip_code" value="{application/responsible_zip_code}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi postnummer.
							</div>
						</div>
						<!-- City -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsible_city')" />*</label>
							<input type="text" class="form-control" name="responsible_city" value="{application/responsible_city}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi poststed.
							</div>
						</div>
						<!-- Email -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_email')" />*</label>
							<input type="email" class="form-control" name="contact_email" value="{application/contact_email}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi gyldig e-post.
							</div>
						</div>
						<!-- Confirm Email -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Confirm e-mail address')" />*</label>
							<input type="email" class="form-control" name="contact_email2" value="{application/contact_email2}" required="true"/>
							<div class="invalid-feedback">
								Vennligst bekreft e-posten din.
							</div>
						</div>
						<!-- Phone -->
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_phone')" />*</label>
							<input type="number" class="form-control" name="contact_phone" value="{application/contact_phone}" required="true"/>
							<div class="invalid-feedback">
								Vennligst oppgi gyldig telefonnummer.
							</div>
						</div>
						<hr class="mt-5"></hr>
						<button class="btn btn-light mb-5" type="submit">
							<xsl:value-of select="php:function('lang', 'send')" />
						</button>
					</div>
				</div>
			</div>
		</form>
		<!--<div class="mt-5"><pre data-bind="text: ko.toJSON(am, null, 2)"></pre></div> -->
		<div class="push"></div>
		<!-- MODAL INSPECT EQUIPMENT START -->
		<div class="modal fade" id="new_organization" >
			<div class="modal-dialog">
				<div class="modal-content">
					<!-- Modal Header -->
					<div class="modal-header">
						<h4 id="inspection_title" class="modal-title">
							<xsl:value-of select="php:function('lang', 'new organization')" />
						</h4>
					</div>
					<!-- Modal body -->
					<div class="modal-body">
						<xsl:variable name="action_url">
							<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uiorganization.edit,phpgw_return_as:json')" />
						</xsl:variable>
						<form class="frm_register_case" action="{$action_url}" method="post">
							<fieldset>
								<div class="form-group">
									<label for="field_name">
										<h4>
											<xsl:value-of select="php:function('lang', 'Name')" />
										</h4>
									</label>
									<input id="inputs" name="name" type="text" class="form-control">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/name"/>
										</xsl:attribute>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a name')" />
										</xsl:attribute>
									</input>
					
								</div>
								<div class="form-group">
									<label for="field_shortname">
										<xsl:value-of select="php:function('lang', 'Organization shortname')" />
									</label>
									<input id="field_shortname" name="shortname" type="text" class="form-control">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/shortname"/>
										</xsl:attribute>
									</input>
								</div>
								<div class="form-group">
									<label for="field_organization_number">
										<xsl:value-of select="php:function('lang', 'Organization number')" />
									</label>
									<input id="field_organization_number" name="organization_number" type="text" value="{organization/organization_number}" class="form-control"/>
								</div>
								<div class="form-group">
									<label for="field_homepage">
										<xsl:value-of select="php:function('lang', 'Homepage')" />
									</label>
									<input id="field_homepage" name="homepage" type="text" class="form-control">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/homepage"/>
										</xsl:attribute>
									</input>
								</div>
								<div class="form-group">
									<label for="field_phone">
										<xsl:value-of select="php:function('lang', 'Phone')" />
									</label>
									<input id="field_phone" name="phone" type="text" class="form-control">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/phone"/>
										</xsl:attribute>
									</input>
								</div>
								<div class="form-group">
									<label for="field_email">
										<xsl:value-of select="php:function('lang', 'Email')" />
									</label>
									<input id="field_email" name="email" type="text" class="form-control">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/email"/>
										</xsl:attribute>
									</input>
								</div>

								<div class="form-group">
									<xsl:copy-of select="phpgw:booking_customer_identifier(organization)"/>
								</div>
								<div class="form-group">
									<label for="field_customer_internal">
										<xsl:value-of select="php:function('lang', 'Internal Customer')"/>
									</label>
									<!--<xsl:copy-of select="phpgw:option_checkbox(organization/customer_internal, 'customer_internal')"/>-->
								</div>
								<div class="form-group">
									<label for="field_customer_number">
										<xsl:value-of select="php:function('lang', 'Customer number')" />
									</label>
									<xsl:if test="currentapp = 'booking'">
										<input name="customer_number" type="text" id="field_customer_number" value="{organization/customer_number}" class="form-control"/>
									</xsl:if>
									<xsl:if test="currentapp != 'booking'">
										<input name="customer_number" type="text" id="field_customer_number" readonly="true" value="{organization/customer_number}" class="form-control"/>
									</xsl:if>
								</div>
								<div class="form-group">
									<label for="field_street">
										<xsl:value-of select="php:function('lang', 'Street')"/>
									</label>
									<input id="field_street" name="street" type="text" value="{organization/street}" class="form-control"/>
								</div>
								<div class="form-group">
									<label for="field_zip_code">
										<xsl:value-of select="php:function('lang', 'Zip code')"/>
									</label>
									<input type="text" name="zip_code" id="field_zip_code" value="{organization/zip_code}" class="form-control"/>
								</div>
								<div class="form-group">
									<label for="field_city">
										<xsl:value-of select="php:function('lang', 'Postal City')"/>
									</label>
									<input type="text" name="city" id="field_city" value="{organization/city}" class="form-control"/>
								</div>
								<div class="form-group">
									<label for='field_district'>
										<xsl:value-of select="php:function('lang', 'District')"/>
									</label>
									<input type="text" name="district" id="field_district" value="{organization/district}" class="form-control"/>
								</div>
								<input type="text" name="active" id="field_active" value="1"/>

								<div class="form-group">
									<label for="field_show_in_portal">
										<xsl:value-of select="php:function('lang', 'Show in portal')"/>
									</label>
									<select id="field_show_in_portal" name="show_in_portal" class="form-control">
										<option value="0">
											<xsl:if test="organization/show_in_portal=0">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'No')"/>
										</option>
										<option value="1">
											<xsl:if test="organization/show_in_portal=1">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'Yes')"/>
										</option>
									</select>
								</div>
								<div class="form-group">
									<label for="field_activity">
										<xsl:value-of select="php:function('lang', 'Activity')" />
									</label>
									<select name="activity_id" id="field_activity" class="form-control">
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
												<xsl:if test="../organization/activity_id = id">
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
									<label for="field_description">
										<xsl:value-of select="php:function('lang', 'Description')" />
									</label>
									<div style="max-width:650px;">
										<textarea rows="4" id="field_description" name="description" type="text" class="form-control">
											<xsl:value-of select="organization/description"/>
										</textarea>
									</div>
								</div>
								<div class="heading">
									<legend>
										<h3>
											<xsl:value-of select="php:function('lang', 'Admin 1')" />
										</h3>
									</legend>
								</div>
								<div class="form-group">
									<label for="field_admin_name_1">
										<h4>
											<xsl:value-of select="php:function('lang', 'Name')" />
										</h4>
									</label>
									<input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}' class="form-control"/>
									<input type="hidden" name="contacts[0][ssn]" value=""/>
								</div>
								<div class="form-group">
									<label for="field_admin_email_1">
										<h4>
											<xsl:value-of select="php:function('lang', 'Email')" />
										</h4>
									</label>
									<input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}' data-validation="email" class="form-control">
										<xsl:attribute name="data-validation-optional">
											<xsl:text>true</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a valid contact email')" />
										</xsl:attribute>
									</input>
								</div>
								<div class="form-group">
									<label for="field_admin_phone_1">
										<h4>
											<xsl:value-of select="php:function('lang', 'Phone')" />
										</h4>
									</label>
									<input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}' class="form-control"/>
								</div>
								<div class="heading">
									<legend>
										<h3>
											<xsl:value-of select="php:function('lang', 'Admin 2')" />
										</h3>
									</legend>
								</div>
								<div class="form-group">
									<label for="field_admin_name_2">
										<h4>
											<xsl:value-of select="php:function('lang', 'Name')" />
										</h4>
									</label>
									<input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}' class="form-control"/>
									<input type="hidden" name="contacts[1][ssn]" value=""/>
								</div>
								<div class="form-group">
									<label for="field_admin_email_2">
										<h4>
											<xsl:value-of select="php:function('lang', 'Email')" />
										</h4>
									</label>
									<input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}' data-validation="email" class="form-control">
										<xsl:attribute name="data-validation-optional">
											<xsl:text>true</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a valid contact email')" />
										</xsl:attribute>
									</input>
								</div>
								<div class="form-group">
									<label for="field_admin_phone_2">
										<h4>
											<xsl:value-of select="php:function('lang', 'Phone')" />
										</h4>
									</label>
									<input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}' class="form-control"/>
								</div>
							</fieldset>
							<hr class="mt-5"></hr>
							<button class="btn btn-light mb-5" type="submit">
								<xsl:value-of select="php:function('lang', 'send')" />
							</button>

						</form>

					</div>

					<!-- Modal footer -->

					<form method="post" id="set_completed_item">
						<xsl:attribute name="action">
							<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.set_completed_item')" />
						</xsl:attribute>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success ml-5 mr-3">Ferdig (delskjema)</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- MODAL INSPECT EQIPMENT END -->

	</div>
	<script>
		var initialAcceptAllTerms = true;
		var initialSelection = [];
		var lang = <xsl:value-of select="php:function('js_lang', 'Do you want to delete application?')" />;
	</script>
</xsl:template>
