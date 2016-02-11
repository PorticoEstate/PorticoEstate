<xsl:template match="data">
	<div id="content">
		<xsl:call-template name="msgbox"/>
		<xsl:apply-templates select="permission"/>
	</div>
</xsl:template>
<xsl:template match="data/permission" xmlns:php="http://php.net/xsl">
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="permission_add">
				<div class="pure-control-group">
					<xsl:if test="id">
						<input name='field_id' type='hidden'>
							<xsl:attribute name="value">
								<xsl:value-of select="id"/>
							</xsl:attribute>
						</input>
					</xsl:if>
					<label for="field_role" style="vertical-align:top;">
						<h4>
							<xsl:value-of select="php:function('lang', 'Role')" />
						</h4>
					</label>
					<div style="display:inline-block;max-width:80%;">
						<span>
							<xsl:value-of select="node()"/>
						</span>
						<br />
						<select name='role' id='field_role'>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
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
				<div class="pure-control-group">
					<!-- Subject -->
					<label for="field_subject_name">
						<h4>
							<xsl:value-of select="php:function('lang', 'Account')" />
						</h4>
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
				</div>
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
			<a class="cancel pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="cancel_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')"/>
			</a>
		</div>
	</form>
</xsl:template>
