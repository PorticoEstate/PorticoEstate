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
	<div class="container wrapper">
		<div class="col mb-4">
			<xsl:call-template name="msgbox"/>
		</div>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="user/tabs"/>
			<div id="user_edit" class="booking-container">
				<form action="" method="POST" class="col" id="form" name="form" >
					<input type="hidden" name="tab" value=""/>
					<fieldset class="border p-2">
						<legend  class="w-auto">
							<xsl:value-of select="php:function('lang', 'user')" />
						</legend>
						<div class="form-group">
							<label for="inputs">
								<xsl:value-of select="php:function('lang', 'name')" />
							</label>
							<input id="inputs" name="name" type="text" class="form-control">
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
						</div>
						<div class="form-group">
							<label for="field_homepage">
								<xsl:value-of select="php:function('lang', 'Homepage')" />
							</label>
							<input id="field_homepage" name="homepage" type="text" class="form-control">
								<xsl:attribute name="value">
									<xsl:value-of select="user/homepage"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="form-group">
							<label for="field_phone">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
							<input id="field_phone" name="phone" type="text" class="form-control">
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
						<div class="form-group">
							<label for="field_email">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
							<input id="field_email" name="email" type="text" class="form-control">
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
					</fieldset>


					<fieldset class="border p-2">
						<legend  class="w-auto">
							<xsl:value-of select="php:function('lang', 'Invoice information')" />
						</legend>
	
						<div class="form-group">
							<label for="field_customer_ssn">
								<xsl:value-of select="php:function('lang', 'ssn')" />
							</label>
							<xsl:value-of select="substring (user/customer_ssn, 1, 6)" />
							<xsl:text>*****</xsl:text>
						</div>

						<div class="form-group">
							<label for="field_customer number">
								<xsl:value-of select="php:function('lang', 'customer number')" />
							</label>
							<input id="field_customer_number" name="customer_number" type="text" value="{user/customer_number}" class="form-control">
							</input>
						</div>
						<div class="form-group">
							<label for="field_street">
								<xsl:value-of select="php:function('lang', 'Street')"/>
							</label>
							<input id="field_street" name="street" type="text" value="{user/street}" class="form-control">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a value')" />
								</xsl:attribute>
							</input>
						</div>
						<div class="form-group">
							<label for="field_zip_code">
								<xsl:value-of select="php:function('lang', 'Zip code')"/>
							</label>
							<input type="text" name="zip_code" id="field_zip_code" value="{user/zip_code}" class="form-control">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a value')" />
								</xsl:attribute>

							</input>
						</div>
						<div class="form-group">
							<label for="field_city">
								<xsl:value-of select="php:function('lang', 'Postal City')"/>
							</label>
							<input type="text" name="city" id="field_city" value="{user/city}" class="form-control">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a value')" />
								</xsl:attribute>
							</input>
						</div>

						<div class="form-group">
							<xsl:if test="not(new_form) and (currentapp = 'booking')">
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" name="active" class="form-control">
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

					</fieldset>
					<div  class="btn-group" role="group">
						<input type="submit" class="btn btn-secondary">
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
						<a class="btn btn-secondary">
							<xsl:attribute name="href">
								<xsl:value-of select="user/cancel_link"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Cancel')" />
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</xsl:template>
