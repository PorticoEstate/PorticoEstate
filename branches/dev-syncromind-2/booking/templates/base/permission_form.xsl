<xsl:template match="data">
	<xsl:call-template name="msgbox"/>
	<xsl:apply-templates select="permission"/>
</xsl:template>
<xsl:template match="data/permission" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		objectType = "<xsl:value-of select="object_type"/>";
		objectAutocomplete = <xsl:value-of select="inline"/> == 0;
	</script>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="permission" class="booking-container">
				<fieldset>
					<xsl:if test="id">
						<!-- An update, add id column -->
						<input name='field_id' type='hidden'>
							<xsl:attribute name="value">
								<xsl:value-of select="id"/>
							</xsl:attribute>
						</input>
					</xsl:if>
					<!-- Role -->
					<div class="pure-control-group">
						<label for="field_role" style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Role')" />
						</label>
						<div class="pure-custom">
							<span>
								<xsl:value-of select="node()"/>
							</span>
							<div>
								<select name='role' id='field_role' style="display:block;" data-validation="required">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please select a role')" />
									</xsl:attribute>
									<option value=''>
										<xsl:value-of select="php:function('lang', 'Select role...')" />
									</option>
									<xsl:for-each select="available_roles/*">
										<option>
											<xsl:if test="../../role = local-name()">
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
					</div>
					<!-- Subject -->
					<div class="pure-control-group">
						<label for="field_subject_name">
							<xsl:value-of select="php:function('lang', 'Account')" />
						</label>
						<input id="field_subject_name" name="subject_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="subject_name"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter an account')" />
							</xsl:attribute>
						</input>
						<input id="field_subject_id" name="subject_id" type="hidden">
							<xsl:attribute name="value">
								<xsl:value-of select="subject_id"/>
							</xsl:attribute>
						</input>
						<div id="subject_container"></div>
					</div>
					<div class="pure-control-group">
						<label for="field_object_name">
							<xsl:value-of select="php:function('lang', string(object_type_label))" />
						</label>
						<input id="field_object_name" name="object_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="object_name"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
							</xsl:attribute>
							<xsl:if test="inline = '1'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<input id="field_object_id" name="object_id" type="hidden">
							<xsl:attribute name="value">
								<xsl:value-of select="object_id"/>
							</xsl:attribute>
						</input>
						<div id="object_container"></div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="id">
							<xsl:value-of select="php:function('lang', 'Update')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>