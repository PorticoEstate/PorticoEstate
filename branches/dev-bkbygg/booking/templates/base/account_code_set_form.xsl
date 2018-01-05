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
	<form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="account_code_set/tabs"/>
			<div id="account" class="booking-container">
				<fieldset>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1 pure-u-md-2-3 pure-u-lg-1-2">
							<div class="pure-control-group">
								<label for="field_name">
									<xsl:value-of select="php:function('lang', 'Name')" />
								</label>
								<input name="name" type="text" id="field_name" value="{account_code_set/name}" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a name')" />
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_3">
									<label for="field_object_number">
										<xsl:value-of select="config_data/dim_3" />
									</label>
									<input name="object_number" type="text" id="field_object_number" value="{account_code_set/object_number}" maxlength='8' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an object number')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/article">
									<label for="field_article">
										<xsl:value-of select="config_data/article" />
									</label>
									<input name="article" type="text" id="field_article" value="{account_code_set/article}" maxlength='15' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an article')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_value_1">
									<label for="field_unit_number">
										<xsl:value-of select="config_data/dim_value_1" />
									</label>
									<input name="unit_number" type="text" id="field_unit_number" value="{account_code_set/unit_number}" maxlength='12' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an unit number')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_value_4">
									<label for="field_dim_value_4">
										<xsl:value-of select="config_data/dim_value_4" />
									</label>
									<input name="dim_value_4" type="text" id="field_dim_value_4" value="{account_code_set/dim_value_4}" maxlength='12' class="pure-u-1" />
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_value_5">
									<label for="field_dim_value_5">
										<xsl:value-of select="config_data/dim_value_5" />
									</label>
									<input name="dim_value_5" type="text" id="field_dim_value_5" value="{account_code_set/dim_value_5}" maxlength='12' class="pure-u-1" />
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<!--xsl:if test="config_data/external_format = 'KOMMFAKT'"-->
								<label for="field_unit_prefix">
									<xsl:value-of select="php:function('lang', 'Unit Prefix')" />
								</label>
								<input name="unit_prefix" type="text" id="field_unit_prefix" value="{account_code_set/unit_prefix}" maxlength='1' class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an unit prefix')" />
									</xsl:attribute>
								</input>
								<!--/xsl:if-->
							</div>
						</div>
						<div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4">
							<div class="pure-control-group">
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<select id="field_active" name="active" class="pure-u-1">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select an status')" />
									</xsl:attribute>
									<xsl:if test="new_form">
										<xsl:attribute name="disabled">disabled</xsl:attribute>
									</xsl:if>
									<option value="1">
										<xsl:if test="account_code_set/active=1">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="account_code_set/active=0">
											<xsl:attribute name="selected">selected</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_1">
									<label for="field_responsible_code">
										<xsl:value-of select="config_data/dim_1" />
									</label>
									<input name="responsible_code" type="text" id="field_responsible_code" value="{account_code_set/responsible_code}" maxlength='6' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a responsible code')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_2">
									<label for="field_service">
										<xsl:value-of select="config_data/dim_2" />
									</label>
									<input name="service" type="text" id="field_service" value="{account_code_set/service}" maxlength='8' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a service')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_4">
									<label for="field_dim_4">
										<xsl:value-of select="config_data/dim_4" />
									</label>
									<input name="dim_4" type="text" id="field_dim_4" value="{account_code_set/dim_4}" maxlength='8' class="pure-u-1" />
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<xsl:if test="config_data/dim_5">
									<label for="field_project_number">
										<xsl:value-of select="config_data/dim_5" />
									</label>
									<input name="project_number" type="text" id="field_project_number" value="{account_code_set/project_number}" maxlength='12' class="pure-u-1">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter a project number')" />
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
						</div>
					</div>
					<div class="pure-g">
						<div class="pure-u-1 pure-u-sm-1 pure-u-md-2-3 pure-u-lg-1-2">
							<div class="pure-control-group">
								<label for="field_invoice_instruction">
									<xsl:value-of select="php:function('lang', 'Invoice instruction')" />
								</label>
								<input id="field_invoice_instruction" name="invoice_instruction" value="{account_code_set/invoice_instruction}" class="pure-u-1" />
							</div>
							<!--div class="pure-control-group">
			<xsl:if test="config_data/external_format = 'KOMMFAKT'">
									<label><xsl:value-of select="php:function('lang', 'Reference')" /></label>
			</xsl:if>
								<input size="120" id="field_invoice_instruction" name="invoice_instruction" value="{account_code_set/invoice_instruction}" />
							</div-->
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" value="{php:function('lang', phpgw:conditional(new_form, 'Create', 'Save'))}" class="button pure-button pure-button-primary"/>
			<a class="cancel pure-button pure-button-primary" href="{account_code_set/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</xsl:template>
