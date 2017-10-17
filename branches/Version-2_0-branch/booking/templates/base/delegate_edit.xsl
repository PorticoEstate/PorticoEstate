<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="delegate/tabs"/>
			<div id="delegate_edit" class="booking-container">
				<fieldset>
					<div class="heading">
						<legend>
							<h3>
								<xsl:if test="not(delegate/id)">
									<xsl:value-of select="php:function('lang', 'New delegate')" />
								</xsl:if>
								<xsl:if test="delegate/id">
									<xsl:value-of select="php:function('lang', 'Edit delegate')" />
								</xsl:if>
							</h3>
						</legend>
					</div>
					<div class="pure-control-group">
						<label for="name">
							<xsl:value-of select="php:function('lang', 'name')" />
						</label>
						<input id="name" name="name" type="text" value="{delegate/name}" >
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a name')" />
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_ssn">
							<xsl:value-of select="php:function('lang', 'ssn')" />
						</label>
						<input type='text' id='field_ssn' name="ssn" value='{delegate/ssn}'/>
					</div>
					<div class="pure-control-group">
						<label for="field_email">
							<xsl:value-of select="php:function('lang', 'Email')" />
						</label>
						<input type='text' id='field_email' name="email" value='{delegate/email}' data-validation="email">
							<xsl:attribute name="data-validation-optional">
								<xsl:text>true</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a valid contact email')" />
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_phone">
							<xsl:value-of select="php:function('lang', 'Phone')" />
						</label>
						<input type='text' id='field_phone' name="phone" value='{delegate/phone}'/>
					</div>
	
					<div class="pure-control-group">
						<label for="field_organization_name">
							<xsl:value-of select="php:function('lang', 'Organization')" />
						</label>
						<input id="field_organization_id" name="organization_id" type="hidden" value="{delegate/organization_id}"/>
						<input name="organization_name" type="text" id="field_organization_name" value="{delegate/organization_name}">
							<xsl:if test="delegate/organization_id">
								<xsl:attribute name='disabled'>disabled</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter an organization name')" />
							</xsl:attribute>
						</input>
						<div id="organization_container"></div>
					</div>
		

					<div class="pure-control-group">
						<xsl:if test="delegate/id">
							<label for="field_active">
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</label>
							<select id="field_active" name="active">
								<option value="1">
									<xsl:if test="delegate/active=1">
										<xsl:attribute name="selected">checked</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</option>
								<option value="0">
									<xsl:if test="delegate/active=0">
										<xsl:attribute name="selected">checked</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', 'Inactive')"/>
								</option>
							</select>
						</xsl:if>
					</div>

				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="not(delegate/id)">
				<input type="submit" value="{php:function('lang', 'Add')}" class="button pure-button pure-button-primary" />
			</xsl:if>
			<xsl:if test="delegate/id">
				<input type="submit" value="{php:function('lang', 'Save')}" class="button pure-button pure-button-primary"/>
			</xsl:if>
			<a class="cancel pure-button pure-button-primary" href="{delegate/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	<script type="text/javascript">
		var endpoint = '<xsl:value-of select="module" />';
        <![CDATA[
            $(document).ready(function() {
                JqueryPortico.autocompleteHelper('index.php?menuaction=' + endpoint + '.uiorganization.index&phpgw_return_as=json&',
                                                 'field_organization_name', 'field_organization_id', 'organization_container');
            });
        ]]>
	</script>
</xsl:template>
