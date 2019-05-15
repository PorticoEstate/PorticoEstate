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
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-stacked" id="form" name="form" >
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="user/tabs"/>
			<div id="user_edit" class="booking-container">
				<fieldset>
					<div class="heading">
						<legend>
							<h3>
								<xsl:if test="new_form">
									<xsl:value-of select="php:function('lang', 'New user')" />
								</xsl:if>
								<xsl:if test="not(new_form)">
									<xsl:value-of select="php:function('lang', 'Edit user')" />
								</xsl:if>
							</h3>
						</legend>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_name">
									<h4>
										<xsl:value-of select="php:function('lang', 'user')" />
									</h4>
								</label>
								<label for="inputs">
									<xsl:value-of select="php:function('lang', 'name')" />
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input id="inputs" name="name" type="text" class="pure-u-1">
										<xsl:attribute name="value">
											<xsl:value-of select="user/name"/>
										</xsl:attribute>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a name')" />
										</xsl:attribute>
									</input>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input id="inputs" name="name" readonly="true" type="text" class="pure-u-1">
										<xsl:attribute name="value">
											<xsl:value-of select="user/name"/>
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label for="field_homepage">
									<xsl:value-of select="php:function('lang', 'Homepage')" />
								</label>
								<input id="field_homepage" name="homepage" type="text" class="pure-u-1">
									<xsl:attribute name="value">
										<xsl:value-of select="user/homepage"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_phone">
									<xsl:value-of select="php:function('lang', 'Phone')" />
								</label>
								<input id="field_phone" name="phone" type="text" class="pure-u-1">
									<xsl:attribute name="value">
										<xsl:value-of select="user/phone"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a value')" />
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_email">
									<xsl:value-of select="php:function('lang', 'Email')" />
								</label>
								<input id="field_email" name="email" type="text" class="pure-u-1">
									<xsl:attribute name="value">
										<xsl:value-of select="user/email"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a value')" />
									</xsl:attribute>
								</input>
							</div>

						</div>
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_customer_ssn">
									<h4>
										<xsl:value-of select="php:function('lang', 'Invoice information')" />
									</h4>
								</label>

								<label for="field_customer_ssn">
									<xsl:value-of select="php:function('lang', 'ssn')" />
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input name="customer_ssn" type="text" id="field_customer_ssn" value="{user/customer_ssn}" class="pure-u-1">
										<xsl:if test="currentapp != 'booking'">
											<xsl:attribute name="readonly">
												<xsl:text>readonly</xsl:text>
											</xsl:attribute>
										</xsl:if>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a value')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>

							<div class="pure-control-group">
								<label for="field_customer number">
									<xsl:value-of select="php:function('lang', 'customer number')" />
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input id="field_customer_number" name="customer_number" type="text" value="{user/customer_number}" class="pure-u-1">
										<xsl:if test="currentapp != 'booking'">
											<xsl:attribute name="readonly">
												<xsl:text>readonly</xsl:text>
											</xsl:attribute>
										</xsl:if>

									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label for="field_street">
									<xsl:value-of select="php:function('lang', 'Street')"/>
								</label>
								<input id="field_street" name="street" type="text" value="{user/street}" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a value')" />
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_zip_code">
									<xsl:value-of select="php:function('lang', 'Zip code')"/>
								</label>
								<input type="text" name="zip_code" id="field_zip_code" value="{user/zip_code}" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a value')" />
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_city">
									<xsl:value-of select="php:function('lang', 'Postal City')"/>
								</label>
								<input type="text" name="city" id="field_city" value="{user/city}" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a value')" />
									</xsl:attribute>
								</input>
							</div>

							<div class="pure-control-group">
								<xsl:if test="not(new_form) and (currentapp = 'booking')">
									<label for="field_active">
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</label>
									<select id="field_active" name="active" class="pure-u-1">
										<option value="1">
											<xsl:if test="user/active=1">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'Active')"/>
										</option>
										<option value="0">
											<xsl:if test="user/active=0">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'Inactive')"/>
										</option>
									</select>
								</xsl:if>
							</div>
						</div>
					</div>

				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
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
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="user/cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</xsl:template>
