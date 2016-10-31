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
	<div class="content">
        
		<div class="pure-g">
			<div class="pure-u-1">
				<dl class="form">
					<dt class="heading">
						<xsl:if test="new_form">
							<xsl:value-of select="php:function('lang', 'New Organization')" />
						</xsl:if>
						<xsl:if test="not(new_form)">
							<xsl:value-of select="php:function('lang', 'Edit Organization')" />
						</xsl:if>
					</dt>
				</dl>
			</div>
		</div>
		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id="form" name="form">
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
						<dt>
							<label for="field_name">
								<xsl:value-of select="php:function('lang', 'Organization')" />
							</label>
						</dt>
						<dd>
							<xsl:if test="currentapp = 'booking'">
								<input id="inputs" name="name" type="text">
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
								<input id="inputs" name="name" readonly="true" type="text">
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
						</dd>
						<dt>
							<label for="field_shortname">
								<xsl:value-of select="php:function('lang', 'Organization shortname')" />
							</label>
						</dt>
						<dd>
							<xsl:if test="currentapp = 'booking'">
								<input id="field_shortname" name="shortname" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="organization/shortname"/>
									</xsl:attribute>
								</input>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input id="field_shortname" name="shortname" readonly="true" type="text">
									<xsl:attribute name="value">
										<xsl:value-of select="organization/shortname"/>
									</xsl:attribute>
								</input>
							</xsl:if>
						</dd>
						<dt>
							<label for="field_organization_number">
								<xsl:value-of select="php:function('lang', 'Organization number')" />
							</label>
						</dt>
						<dd>
							<xsl:if test="currentapp = 'booking'">
								<input id="field_organization_number" name="organization_number" type="text" value="{organization/organization_number}"/>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input id="field_organization_number" name="organization_number" type="text" readonly="true" value="{organization/organization_number}"/>
							</xsl:if>
						</dd>
						<dd>
						</dd>

						<dt>
							<label for="field_customer_number">
								<xsl:value-of select="php:function('lang', 'Customer number')" />
							</label>
						</dt>
						<dd>
							<xsl:if test="currentapp = 'booking'">
								<input name="customer_number" type="text" id="field_customer_number" value="{organization/customer_number}"/>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input name="customer_number" type="text" id="field_customer_number" readonly="true" value="{organization/customer_number}"/>
							</xsl:if>
						</dd>
						<dt>
							<label for="field_homepage">
								<xsl:value-of select="php:function('lang', 'Homepage')" />
							</label>
						</dt>
						<dd>
							<input id="field_homepage" name="homepage" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/homepage"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_phone">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
						</dt>
						<dd>
							<input id="field_phone" name="phone" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/phone"/>
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_email">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
						</dt>
						<dd>
							<input id="field_email" name="email" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="organization/email"/>
								</xsl:attribute>
							</input>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
					<dl class="form-col">
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
						<dt>
							<label for="field_street">
								<xsl:value-of select="php:function('lang', 'Street')"/>
							</label>
						</dt>
						<dd>
							<input id="field_street" name="street" type="text" value="{organization/street}"/>
						</dd>

						<dt>
							<label for="field_zip_code">
								<xsl:value-of select="php:function('lang', 'Zip code')"/>
							</label>
						</dt>
						<dd>
							<input type="text" name="zip_code" id="field_zip_code" value="{organization/zip_code}"/>
						</dd>

						<dt>
							<label for="field_city">
								<xsl:value-of select="php:function('lang', 'Postal City')"/>
							</label>
						</dt>
						<dd>
							<input type="text" name="city" id="field_city" value="{organization/city}"/>
						</dd>

						<dt>
							<label for='field_district'>
								<xsl:value-of select="php:function('lang', 'District')"/>
							</label>
						</dt>
						<dd>
							<xsl:if test="currentapp = 'booking'">
								<input type="text" name="district" id="field_district" value="{organization/district}"/>
							</xsl:if>
							<xsl:if test="currentapp != 'booking'">
								<input type="text" name="district" id="field_district" readonly="true" value="{organization/district}"/>
							</xsl:if>
						</dd>
						<xsl:if test="not(new_form) and (currentapp = 'booking')">
							<dt>
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
							</dt>
							<dd>
								<select id="field_active" name="active">
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
							</dd>
						</xsl:if>
						<!--<xsl:if test="not(new_form) and (currentapp = 'booking')">-->
						<dt>
							<label for="field_show_in_portal">
								<xsl:value-of select="php:function('lang', 'Show in portal')"/>
							</label>
						</dt>
						<dd>
							<select id="field_show_in_portal" name="show_in_portal">
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
						</dd>
						<!--</xsl:if>-->
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1">
					<dl class="form-col">
						<dt style="margin-top: 40px;">
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
						</dd>
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1 pure-u-lg-4-5">
					<div class="form-col">
						<dt>
							<label for="field_description">
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
						</dt>
						<dd>
							<textarea id="field_description" name="description" type="text">
								<xsl:value-of select="organization/description"/>
							</textarea>
						</dd>
					</div>
				</div>
			</div>
            
			<xsl:if test='new_form or organization/permission/write'>
				<div class="pure-g">
					<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
						<dl class="form-col">
							<dt class='heading'>
								<xsl:value-of select="php:function('lang', 'Admin 1')" />
							</dt>

							<dt>
								<label for="field_admin_name_1">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
								<br />
							</dt>
							<dd>
								<input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}'/>
							</dd>

							<input type="hidden" name="contacts[0][ssn]" value=""/>

							<dt>
								<label for="field_admin_email_1">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<br />
							</dt>
							<dd>
								<input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}'/>
							</dd>

							<dt>
								<label for="field_admin_phone_1">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<br />
							</dt>
							<dd>
								<input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}'/>
							</dd>
						</dl>
					</div>
					<div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
						<dl class="form-col">
							<dt class='heading'>
								<xsl:value-of select="php:function('lang', 'Admin 2')" />
							</dt>

							<dt>
								<label for="field_admin_name_2">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
							</dt>
							<dd>
								<input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}'/>
							</dd>

							<input type="hidden" name="contacts[1][ssn]" value=""/>

							<dt>
								<label for="field_admin_email_2">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<br />
							</dt>
							<dd>
								<input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}'/>
							</dd>

							<dt>
								<label for="field_admin_phone_2">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<br />
							</dt>
							<dd>
								<input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}'/>
							</dd>
						</dl>
					</div>
				</div>
			</xsl:if>
			<script type="text/javascript">
				var endpoint = '<xsl:value-of select="module" />';
			</script>
			<div class="form-buttons">
				<input type="submit">
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
		</form>
	</div>

</xsl:template>
