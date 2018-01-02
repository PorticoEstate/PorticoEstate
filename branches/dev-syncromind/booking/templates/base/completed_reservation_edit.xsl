<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="reservation/tabs"/>
			<div id="completed_reservation_edit" class="booking-container">
				<fieldset>
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Edit completed reservation')"/>
							</h3>
						</legend>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_cost">
									<xsl:value-of select="php:function('lang', 'Cost')" />
								</label>
								<input id="field_cost" name="cost" type="text" value="{reservation/cost}" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a cost')" />
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_customer_type">
									<xsl:value-of select="php:function('lang', 'Customer Type')" />
								</label>
								<select name='customer_type' id='field_customer_type' class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select a customer type')" />
									</xsl:attribute>
									<option value=''>
										<xsl:value-of select="php:function('lang', 'Select...')" />
									</option>
									<xsl:for-each select="reservation/customer_types/*">
										<option>
											<xsl:if test="../../customer_type = local-name()">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:attribute name="value">
												<xsl:value-of select="local-name()"/>
											</xsl:attribute>
											<xsl:value-of select="php:function('lang', string(node()))"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
						</div>
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<xsl:copy-of select="phpgw:booking_customer_identifier(reservation, 'Customer ID')"/>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_article_description">
									<xsl:value-of select="php:function('lang', 'Article Description')" />
								</label>
								<input type='text' id='article_description' name="description" value='{reservation/article_description}' maxlength='35' class="pure-u-1"/>
							</div>
							<div class="pure-control-group">
								<label for="field_description">
									<xsl:value-of select="php:function('lang', 'Description')" />
								</label>
								<input type='text' id='field_description' name="description" value='{reservation/description}' maxlength='60' class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a description')" />
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" value="{php:function('lang', 'Save')}" class="pure-button pure-button-primary"/>
			<a class="cancel pure-button pure-button-primary" href="{reservation/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')"/>
			</a>
		</div>
	</form>
</xsl:template>