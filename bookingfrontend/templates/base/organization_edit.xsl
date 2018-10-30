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
	<div id="organization-edit-page-content" class="margin-top-content">  
        <div class="container wrapper">
            <div class="location mt-5">
                <span><a>
                        <xsl:attribute name="href">
                            <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                        </xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Home')" />
                    </a>
                </span>
					<span>
						<a href="{organization/organization_link}"><xsl:value-of select="organization/name"/></a>
					</span>
				<xsl:if test="new_form">
					<span><xsl:value-of select="php:function('lang', 'New Organization')" /></span>
				</xsl:if>
				<xsl:if test="not(new_form)">
					<span><xsl:value-of select="php:function('lang', 'Edit Organization')" /></span>
				</xsl:if>
            </div>

            <form action="" method="POST" id="form" name="form" class="col">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
						<label class="text-uppercase">
							<xsl:value-of select="php:function('lang', 'Organization')" />
						</label>
						<xsl:if test="currentapp = 'booking'">
										<input id="inputs" name="name" class="form-control" type="text">
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
									</xsl:if>
									<xsl:if test="currentapp != 'booking'">
										<input id="inputs" name="name" class="form-control" readonly="true" type="text">
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
									</xsl:if>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization shortname')" /></label>
						<xsl:if test="currentapp = 'booking'">
										<input id="field_shortname" class="form-control" name="shortname" type="text">
											<xsl:attribute name="value">
												<xsl:value-of select="organization/shortname"/>
											</xsl:attribute>
										</input>
									</xsl:if>
									<xsl:if test="currentapp != 'booking'">
										<input id="field_shortname" class="form-control" name="shortname" readonly="true" type="text">
											<xsl:attribute name="value">
												<xsl:value-of select="organization/shortname"/>
											</xsl:attribute>
										</input>
									</xsl:if>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization number')" /></label>
						<xsl:if test="currentapp = 'booking'">
										<input id="field_organization_number" class="form-control" name="organization_number" type="text" value="{organization/organization_number}"/>
									</xsl:if>
									<xsl:if test="currentapp != 'booking'">
										<input id="field_organization_number" class="form-control" name="organization_number" type="text" readonly="true" value="{organization/organization_number}"/>
									</xsl:if>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Customer number')" /></label>
						<xsl:if test="currentapp = 'booking'">
										<input name="customer_number" class="form-control" type="text" id="field_customer_number" value="{organization/customer_number}"/>
									</xsl:if>
									<xsl:if test="currentapp != 'booking'">
										<input name="customer_number" class="form-control" type="text" id="field_customer_number" readonly="true" value="{organization/customer_number}"/>
									</xsl:if>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Homepage')" /></label>
						<input id="field_homepage" class="form-control" name="homepage" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/homepage"/>
										</xsl:attribute>
									</input>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
						<input id="field_phone" class="form-control" name="phone" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/phone"/>
										</xsl:attribute>
									</input>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
						<input id="field_email" class="form-control" name="email" type="text">
										<xsl:attribute name="value">
											<xsl:value-of select="organization/email"/>
										</xsl:attribute>
									</input>
					</div>

					<xsl:if test="not(new_form) and (currentapp = 'booking')">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Active')"/></label>
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
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Description')" /></label>
							<textarea id="field_description" class="form-control" name="description" type="text">
								<xsl:value-of select="organization/description"/>
							</textarea>
						</div>
				</div>

				<div class="col-md-6">
						<xsl:if test="currentapp = 'booking'">
									<xsl:copy-of select="phpgw:booking_customer_identifier(organization)"/>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<xsl:copy-of select="phpgw:booking_customer_identifier_show(organization)"/>
								</xsl:if>
								<xsl:if test="currentapp = 'booking'">
									<dt>
										<label for="field_customer_internal">
											<xsl:value-of select="php:function('lang', 'Internal Customer')"/>
										</label>
									</dt>
									<dd>
										<xsl:copy-of select="phpgw:option_checkbox(organization/customer_internal, 'customer_internal')"/>
									</dd>
								</xsl:if>


						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Street')"/></label>
							<input id="field_street" class="form-control" name="street" type="text" value="{organization/street}"/>
						</div>

						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Zip code')"/></label>
							<input type="text" name="zip_code" class="form-control" id="field_zip_code" value="{organization/zip_code}"/>
						</div>

						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Postal City')"/></label>
							<input type="text" name="city" class="form-control" id="field_city" value="{organization/city}"/>
						</div>

						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'District')"/></label>
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
						<h5><xsl:value-of select="php:function('lang', 'Admin 1')" /></h5>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
							<input type='text' class="form-control" id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}'/>
						</div>
						<input type="hidden" name="contacts[0][ssn]" value=""/>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
							<input type='text' class="form-control" id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}'/>
						</div>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
							<input type='text' class="form-control" id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}'/>
						</div>
					</div>
					<div class="col-md-6 mt-5">
						<h5><xsl:value-of select="php:function('lang', 'Admin 2')" /></h5>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
							<input type='text' class="form-control" id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}'/>
						</div>
						<input type="hidden" name="contacts[0][ssn]" value=""/>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
							<input type='text' class="form-control" id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}'/>
						</div>
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
							<input type='text' class="form-control" id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}'/>
						</div>
					</div>	
					</xsl:if>
					<script type="text/javascript">
						var endpoint = '<xsl:value-of select="module" />';
					</script>
					<div class="col">
						<div class="form-group">
							<input type="submit" class="btn btn-light mr-4">
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
							<a class="cancel">
								<xsl:attribute name="href">
									<xsl:value-of select="organization/cancel_link"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'Cancel')" />
							</a>
						</div>
					</div>
				</div>
			</form>

        	
        </div>
        
        <div class="push"></div>
    </div>
	
</xsl:template>