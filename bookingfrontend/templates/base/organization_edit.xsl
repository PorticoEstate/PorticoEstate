<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>
	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="organization-edit-page-content" style="display:none;">
		<xsl:if test="noframework != 1">
			<xsl:attribute name="class" value="margin-top-content"/>
		</xsl:if>

		<div class="container wrapper">
			<xsl:if test="noframework != 1">
				<div class="location mt-5">
					<span>
						<a>
							<xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span>
						<a href="{organization/organization_link}">
							<xsl:value-of select="organization/name"/>
						</a>
					</span>
					<xsl:if test="new_form">
						<span>
							<xsl:value-of select="php:function('lang', 'New Organization')" />
						</span>
					</xsl:if>
					<xsl:if test="not(new_form)">
						<span>
							<xsl:value-of select="php:function('lang', 'Edit Organization')" />
						</span>
					</xsl:if>
				</div>
			</xsl:if>
			<form action="{form_action}" method="POST" id="form" name="form" class="col add_organization_form">
				<div class="row">
					<div class="col-md-6">
						<xsl:if test="new_form">
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'organization type')" />
								</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="organization_type" id="privateRadio" value="customer_ssn">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please select an organization type')" />
										</xsl:attribute>
									</input>
									<label class="form-check-label text-uppercase" for="privateRadio">
										<xsl:value-of select="php:function('lang', 'personal group')" />
									</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="organization_type" id="officialRadio" value="organization_number">
									</input>
									<label class="form-check-label text-uppercase" for="officialRadio">
										<xsl:value-of select="php:function('lang', 'Official organization')" />
									</label>
								</div>
							</div>
						</xsl:if>

						<div id="customer_ssn" class="form-group" style="display: none;">
							<label for="field_customer_ssn" class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'social security number')" />
							</label>
							<input name="customer_ssn" class="form-control" type="text" id="field_customer_ssn" value="{organization/customer_ssn}" readonly="readonly"/>
						</div>

						<input type="hidden" id="field_customer_identifier_type" name="customer_identifier_type" value=""></input>
						<input type="hidden" id="field_customer_organization_number" name="customer_organization_number" value=""></input>
						<div id="organization_number" class="form-group">
							<xsl:if test="new_form">
								<xsl:attribute name="style">
									<xsl:text>display: none;</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<label for="field_organization_number" class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Organization number')" />
							</label>
							<xsl:choose>
								<xsl:when test="count(new_org_list) > 0">
									<select name="organization_number" class="form-control" id="field_organization_number">
										<xsl:choose>
											<xsl:when test="not(new_form)">
												<xsl:attribute name="readonly" value="readonly"/>
											</xsl:when>
										</xsl:choose>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please select an organization')" />
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="php:function('lang', 'select')" />
										</option>
										<xsl:for-each select="new_org_list">
											<option>
												<xsl:if test="(../organization/organization_number = id) or (selected = 1)">
													<xsl:attribute name="selected">selected</xsl:attribute>
												</xsl:if>
												<xsl:attribute name="value">
													<xsl:value-of select="id"/>
												</xsl:attribute>
												<xsl:value-of select="name"/>
											</option>
										</xsl:for-each>
									</select>

								</xsl:when>
								<xsl:when test="not(new_form)">
									<input id="field_organization_number" class="form-control" name="organization_number" type="text" value="{organization/organization_number}">
										<xsl:attribute name="readonly" value="readonly"/>
									</input>
								</xsl:when>
							</xsl:choose>

						</div>

						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'name')" />
							</label>
							<input id="field_name" name="name" class="form-control" type="text">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
								</xsl:attribute>
								<xsl:attribute name="value">
									<xsl:value-of select="organization/name"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Organization shortname')" />
							</label>
							<input id="field_shortname" class="form-control" name="shortname" type="text" maxlength="11">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/shortname"/>
								</xsl:attribute>
							</input>

						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Customer number')" />
							</label>
							<xsl:if test="currentapp = 'booking'">
								<input name="customer_number" class="form-control" type="text" id="field_customer_number" value="{organization/customer_number}"/>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input name="customer_number" class="form-control" type="text" id="field_customer_number" readonly="true" value="{organization/customer_number}"/>
							</xsl:if>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Homepage')" />
							</label>
							<input id="field_homepage" class="form-control" name="homepage" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/homepage"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
							<input id="field_phone" class="form-control" name="phone" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/phone"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
							<input id="field_email" class="form-control" name="email" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/email"/>
								</xsl:attribute>
							</input>
						</div>
						<xsl:if test="not(new_form) and (currentapp = 'booking')">
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" class="form-control" name="active">
									<option value="1">
										<xsl:if test="organization/active=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="organization/active=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</div>
						</xsl:if>
						<div class="form-group">
							<label class="text-uppercase">
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
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
							<textarea id="field_description" class="form-control" name="description" type="text">
								<xsl:value-of select="organization/description"/>
							</textarea>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="field_customer_internal">
								<xsl:value-of select="php:function('lang', 'Internal Customer')"/>
							</label>
							<xsl:copy-of select="phpgw:option_checkbox(organization/customer_internal, 'customer_internal', 'form-control')"/>
						</div>

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
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Street')"/>
							</label>
							<input id="field_street" class="form-control" name="street" type="text" value="{organization/street}"/>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Zip code')"/>
							</label>
							<input type="text" name="zip_code" class="form-control" id="field_zip_code" value="{organization/zip_code}"/>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'Postal City')"/>
							</label>
							<input type="text" name="city" class="form-control" id="field_city" value="{organization/city}"/>
						</div>
						<div class="form-group">
							<label class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'District')"/>
							</label>
							<xsl:if test="currentapp = 'booking'">
								<input type="text" class="form-control" name="district" id="field_district" value="{organization/district}"/>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input type="text" class="form-control" name="district" id="field_district" readonly="true" value="{organization/district}"/>
							</xsl:if>
						</div>
					</div>
					<xsl:if test='new_form or organization/permission/write'>
						<div class="col-md-6 mt-5">
							<h5>
								<xsl:value-of select="php:function('lang', 'Admin 1')" />
							</h5>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
								<input type='text' class="form-control" id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}'/>
							</div>
							<input type="hidden" name="contacts[0][ssn]" value=""/>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<input type='text' class="form-control" id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}'/>
							</div>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<input type='text' class="form-control" id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}'/>
							</div>
						</div>
						<div class="col-md-6 mt-5">
							<h5>
								<xsl:value-of select="php:function('lang', 'Admin 2')" />
							</h5>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
								<input type='text' class="form-control" id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}'/>
							</div>
							<input type="hidden" name="contacts[0][ssn]" value=""/>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<input type='text' class="form-control" id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}'/>
							</div>
							<div class="form-group">
								<label class="text-uppercase">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<input type='text' class="form-control" id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}'/>
							</div>
						</div>
					</xsl:if>
					<script>
						var endpoint = '<xsl:value-of select="module" />';
						var count_new_org_list = <xsl:value-of select="count(new_org_list)" />;
						var personal_org = '<xsl:value-of select="personal_org" />';
					</script>
					<div class="col">
						<div class="form-group">
							<input id="submitBtn" type="submit" class="btn btn-light mr-4">
								<xsl:if test="new_form">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Create')" />
									</xsl:attribute>
								</xsl:if>
								<xsl:if test="not(new_form)">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('lang', 'Save')" />
									</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:if test="organization/cancel_link != '#'">
								<a class="cancel">
									<xsl:attribute name="href">
										<xsl:value-of select="organization/cancel_link"/>
									</xsl:attribute>
									<xsl:value-of select="php:function('lang', 'Cancel')" />
								</a>
							</xsl:if>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="push"></div>
	</div>
	<script>
		$('#organization-edit-page-content').show();
	</script>

</xsl:template>