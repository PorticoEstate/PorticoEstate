<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">

		<h3>
			<xsl:value-of select="php:function('lang', 'New application')"/>
		</h3>
		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id='application_form' enctype='multipart/form-data' name="form">
			<xsl:if test="config/application_new_application">
				<p >
					<xsl:value-of select="config/application_new_application"/>
				</p>
			</xsl:if>
        
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-2">
					<dl class="form-2col">
						<div class="heading">1. <xsl:value-of select="php:function('lang', 'Why?')" /></div>
						<dt>
							<label for="field_activity">
								<xsl:value-of select="php:function('lang', 'Activity')" />
							</label>
							<xsl:if test="config/application_activities">
							<p>
								<xsl:value-of select="config/application_activities"/>
							</p>
							</xsl:if>
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
						</dd>
						<dt>
							<label for="field_description">
								<xsl:value-of select="php:function('lang', 'Information about the event')" />
							</label>
							<xsl:if test="config/application_description">
							<p>
								<xsl:value-of select="config/application_description"/>
							</p>
							</xsl:if>
						</dt>
						<dd>
							<textarea id="field_description" class="full-width" name="description">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a descripction')" />
								</xsl:attribute>
								<xsl:value-of select="application/description"/>
							</textarea>
						</dd>
						<dt>
							<xsl:if test="config/application_equipment">
								<p>
									<xsl:value-of select="config/application_equipment"/>
								</p>
							</xsl:if>
						</dt>
						<dd>
							<textarea id="field_equipment" class="full-width" name="equipment"><xsl:value-of select="application/equipment"/></textarea>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-2">
					<dl class="form-col">
						<div class="heading">2. <xsl:value-of select="php:function('lang', 'How many?')" /></div>
						<xsl:if test="config/application_howmany">
						<p>
							<xsl:value-of select="config/application_howmany"/>
						</p>
						</xsl:if>
						<dt>
							<label for="field_agegroups">
								<xsl:value-of select="php:function('lang', 'Estimated number of participants')" />
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
														<xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" size="4">
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
						</dd>
					</dl>
				</div>
			</div>

			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">3. <xsl:value-of select="php:function('lang', 'Where?')" /></div>
						<xsl:if test="config/application_where">
							<p>
								<xsl:value-of select="config/application_where"/>
							</p>
						</xsl:if>
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<input id="field_building_id" name="building_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="application/building_id"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
									</xsl:attribute>
								</input>
								<input id="field_building_name" name="building_name" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="application/building_name"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
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
						<div class="heading">4. <xsl:value-of select="php:function('lang', 'When?')" /></div>
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
							<xsl:for-each select="application/dates">
								<xsl:variable name="index" select="position()-2" />
								<xsl:choose>
									<xsl:when test="position() > 1">
										<div class="date-container">
											<a href="javascript:void(0);" class="close-btn btnclose">
												<xsl:value-of select="php:function('lang', 'remove date')" />
											</a>
											<dt>
												<label for="start_date_{$index}">
													<xsl:value-of select="php:function('lang', 'From')" />
												</label>
											</dt>
											<dd>
												<input class="newaddedpicker datetime" id="start_date_{$index}" type="text" name="from_[]">
													<xsl:attribute name="value">
														<xsl:value-of select="from_" />
													</xsl:attribute>
													<xsl:attribute name="readonly">
														<xsl:text>readonly</xsl:text>
													</xsl:attribute>
												</input>
											</dd>
											<dt>
												<label for="end_date_{$index}">
													<xsl:value-of select="php:function('lang', 'To')" />
												</label>
											</dt>
											<dd>
												<input class="newaddedpicker datetime" id="end_date_{$index}" type="text" name="to_[]">
													<xsl:attribute name="value">
														<xsl:value-of select="to_"/>
													</xsl:attribute>
													<xsl:attribute name="readonly">
														<xsl:text>readonly</xsl:text>
													</xsl:attribute>
												</input>
											</dd>
										</div>
									</xsl:when>
									<xsl:otherwise>
										<div class="date-container">
											<a href="javascript:void(0);" class="close-btn btnclose">
												<xsl:value-of select="php:function('lang', 'remove date')" />
											</a>
											<dt>
												<label for="start_date">
													<xsl:value-of select="php:function('lang', 'From')" />
												</label>
											</dt>
											<dd>
												<input class="datetime" id="start_date" type="text" name="from_[]">
													<xsl:attribute name="value">
														<xsl:value-of select="from_" />
													</xsl:attribute>
													<xsl:attribute name="readonly">
														<xsl:text>readonly</xsl:text>
													</xsl:attribute>

												</input>
											</dd>
											<dt>
												<label for="end_date">
													<xsl:value-of select="php:function('lang', 'To')" />
												</label>
											</dt>
											<dd>
												<input class="datetime" id="end_date" type="text" name="to_[]">
													<xsl:attribute name="value">
														<xsl:value-of select="to_"/>
													</xsl:attribute>
													<xsl:attribute name="readonly">
														<xsl:text>readonly</xsl:text>
													</xsl:attribute>

												</input>
											</dd>
										</div>
										<!--div id="dtBox"></div-->
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</div>
						<dt>
							<a href="javascript:void(0);" id="add-date-link">
								<xsl:value-of select="php:function('lang', 'Add another date')" />
							</a>
						</dt>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">5. <xsl:value-of select="php:function('lang', 'Who?')" /></div>
						<xsl:if test="config/application_who">
							<p>
								<xsl:value-of select="config/application_who"/>
							</p>
						</xsl:if>
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'Target audience')" />
							</label>
						</dt>
						<dd>
							<!--div id="audience_container">&nbsp;</div-->
							<input type="hidden" data-validation="target_audience">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
								</xsl:attribute>
							</input>
							<ul id= "audience"  style="list-style:none;padding-left:10px;">
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
						</dd>
					</dl>
				</div>
			</div>

			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">6. <xsl:value-of select="php:function('lang', 'Contact information')" /></div>
						<xsl:if test="config/application_contact_information">
							<p>
								<xsl:value-of select="config/application_contact_information"/>
							</p>
						</xsl:if>
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
									<xsl:value-of select="application/contact_name"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_contact_email">
								<xsl:value-of select="php:function('lang', 'E-mail address')" />
							</label>
						</dt>
						<dd>
							<input id="field_contact_email" name="contact_email" type="text">
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
						</dd>
						<dt>
							<label for="field_contact_email2">
								<xsl:value-of select="php:function('lang', 'Confirm e-mail address')" />
							</label>
						</dt>
						<dd>
							<input id="field_contact_email2" name="contact_email2" type="text">
								<xsl:attribute name="data-validation">
									<xsl:text>confirmation</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-confirm">
									<xsl:text>contact_email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'The e-mail addresses you entered do not match')" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:value-of select="application/contact_email2"/>
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
									<xsl:value-of select="application/contact_phone"/>
								</xsl:attribute>
							</input>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">7. <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
						<xsl:if test="config/application_responsible_applicant">
							<p>
								<xsl:value-of select="config/application_responsible_applicant"/>
							</p>
						</xsl:if>
						<xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
						<dt>
							<label for="field_street">
								<xsl:value-of select="php:function('lang', 'Street')"/>
							</label>
						</dt>
						<dd>
							<input id="field_responsible_street" name="responsible_street" type="text" value="{application/responsible_street}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Street')"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_zip_code">
								<xsl:value-of select="php:function('lang', 'Zip code')"/>
							</label>
						</dt>
						<dd>
							<input type="text" name="responsible_zip_code" id="field_responsible_zip_code" value="{application/responsible_zip_code}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Zip code')"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_responsible_city">
								<xsl:value-of select="php:function('lang', 'Postal City')"/>
							</label>
						</dt>
						<dd>
							<input type="text" name="responsible_city" id="field_responsible_city" value="{application/responsible_city}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Postal City')"/>
								</xsl:attribute>
							</input>
						</dd>
						<br />
						<xsl:if test="config/application_invoice_information">
							<p>
								<xsl:value-of select="config/application_invoice_information"/>
							</p>
						</xsl:if>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></div>
						<input type="hidden" data-validation="regulations_documents">
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'You must accept to follow all terms and conditions of lease first')" />
							</xsl:attribute>
						</input>
						<xsl:if test="config/application_terms">
							<p>
								<xsl:value-of select="config/application_terms"/>
							</p>
						</xsl:if>
						<br />
						<div id='regulation_documents'>&nbsp;</div>
						<br />
						<xsl:if test="config/application_terms2">
							<p>
								<xsl:value-of select="config/application_terms2"/>
							</p>
						</xsl:if>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<div class="heading">9. <xsl:value-of select="php:function('lang', 'Attachment')" /></div>

						<dt>
							<label for="field_name">
								<xsl:value-of select="php:function('lang', 'Document')" />
							</label>
						</dt>
						<dd>
							<input name="name" id='field_name' type='file' >
								<xsl:attribute name='title'>
									<xsl:value-of select="document/name"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>mime size</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-allowing">
									<xsl:text>jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-max-size">
									<xsl:text>2M</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:text>Max 2M:: jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
								</xsl:attribute>
							</input>
						</dd>
					</dl>
				</div>

			</div>

			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Send')"/>
					</xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href">
						<xsl:value-of select="application/cancel_link"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
				<p style="margin-top: 10px;">Trykker du <strong>SEND</strong>-knappen får du opp en rød melding øverst om noen opplysninger mangler, er alt OK kommer det opp en grønn melding. Det blir sendt en bekreftelse til din e-post, og en lenke hvor du kan gå inn og se status og legge til ekstra opplysninger i saken.<br />
					<br />
					Trykker du <strong>Avbryt</strong> blir søknaden ikke sendt eller lagret, og du går tilbake til kalenderen.</p>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		var initialAcceptAllTerms = false;
		var initialSelection = <xsl:value-of select="application/resources_json"/>;
		var initialAudience = <xsl:value-of select="application/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
		$('#field_customer_identifier_type').attr("data-validation","customer_identifier").attr("data-validation-error-msg", "<xsl:value-of select="php:function('lang', 'Customer identifier type is required')" />");
	</script>
</xsl:template>
