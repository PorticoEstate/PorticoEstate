<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="reservation/tabs"/>
			<div id="completed_reservation_edit" class="booking-container">
				<fieldset>
					<div class="heading">
						<!--<legend>-->
							<h3>
								<xsl:value-of select="php:function('lang', 'Edit completed reservation')"/>
							</h3>
							<p>
								<xsl:value-of select="php:function('lang', string(reservation/reservation_type))"/> #<xsl:value-of select="reservation/reservation_id"/>
							</p>
						<!--</legend>-->
					</div>
					<xsl:if test="config/activate_application_articles !=''">
						<div id="dates-container">
							<input class="datetime" id="from_" name="from_" type="hidden">
								<xsl:attribute name="value">
									<xsl:value-of select="reservation/from_"/>
								</xsl:attribute>
							</input>
							<input class="datetime" id="to_" name="to_" type="hidden">
								<xsl:attribute name="value">
									<xsl:value-of select="reservation/to_"/>
								</xsl:attribute>
							</input>
						</div>

						<div class="pure-control-group">
							<label for="articles_container">
								<xsl:value-of select="php:function('lang', 'Articles')" />
							</label>
							<div id="articles_container" class="pure-custom" style="display:inline-block;"></div>
						</div>
						<div class="pure-control-group">
							<div id="participant_container"/>
						</div>
					</xsl:if>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_cost">
									<xsl:value-of select="php:function('lang', 'Cost')" />
								</label>
								<input id="field_cost" name="cost" type="text" value="{reservation/cost}" class="pure-u-1">
									<xsl:choose>
										<xsl:when test="config/activate_application_articles">
											<xsl:attribute name="readonly">
												<xsl:text>readonly</xsl:text>
											</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="data-validation">
												<xsl:text>required</xsl:text>
											</xsl:attribute>
											<xsl:attribute name="data-validation-error-msg">
												<xsl:value-of select="php:function('lang', 'Please enter a cost')" />
											</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
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
							<div class="pure-control-group">
								<label for="field_org_name" class="pure-checkbox">
									<xsl:value-of select="php:function('lang', 'Organization')" />
									<xsl:text> </xsl:text>
									<input type="checkbox" id="option_organization">
										<xsl:if test="reservation/organization_name">
											<xsl:attribute name="checked">
												<xsl:text>true</xsl:text>
											</xsl:attribute>
										</xsl:if>

									</input>
								</label>
								<input id="field_org_id" name="organization_id" type="hidden">
									<xsl:attribute name="value">
										<xsl:value-of select="reservation/organization_id"/>
									</xsl:attribute>
								</input>
								<input id="field_org_name" name="organization_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
									<xsl:if test="not(reservation/organization_name)">
										<xsl:attribute name="style">
											<xsl:text>display: none;</xsl:text>
										</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="value">
										<xsl:value-of select="reservation/organization_name"/>
									</xsl:attribute>
								</input>
							</div>
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
	<script type="text/javascript">
		var date_format = 'Y-m-d';
		var template_set = '<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|template_set')" />';
		var tax_code_list = <xsl:value-of select="tax_code_list"/>;
		var reservation_type = '<xsl:value-of select="reservation/reservation_type"/>';
		var reservation_id = '<xsl:value-of select="reservation/reservation_id"/>';
		var initialSelection = <xsl:value-of select="reservation/resources_json"/>;

		var lang = <xsl:value-of select="php:function('js_lang','Name', 'phone', 'email', 'Resource Type', 'quantity', 'from', 'to', 'send sms', 'article', 'Select', 'price', 'unit', 'tax', 'unit cost', 'quantity', 'Selected', 'Delete', 'Sum', 'tax code', 'percent')"/>;

	</script>

</xsl:template>