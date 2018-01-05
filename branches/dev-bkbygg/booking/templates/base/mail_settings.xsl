<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="mail_settings" class="booking-container">
				<fieldset>
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Application email settings')"/>
							</h3>
						</legend>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_systemname">
							<xsl:value-of select="php:function('lang', 'System name')"/>
						</label>
						<input id="field_application_mail_systemname" name="application_mail_systemname" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/application_mail_systemname"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_subject">
							<xsl:value-of select="php:function('lang', 'Subject')"/>
						</label>
						<input id="field_application_mail_subject" name="application_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/application_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_created">
							<xsl:value-of select="php:function('lang', 'Mail text for application created')"/>
						</label>
						<textarea id="field_application_mail_created" class="full-width settings" name="application_mail_created" type="text">
							<xsl:value-of select="config_data/application_mail_created"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_pending">
							<xsl:value-of select="php:function('lang', 'Mail text for application pending')"/>
						</label>
						<textarea id="field_application_mail_pending" class="full-width settings" name="application_mail_pending" type="text">
							<xsl:value-of select="config_data/application_mail_pending"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_accepted">
							<xsl:value-of select="php:function('lang', 'Mail text for application accepted')"/>
						</label>
						<textarea id="field_application_mail_accepted" class="full-width settings" name="application_mail_accepted" type="text">
							<xsl:value-of select="config_data/application_mail_accepted"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_application_notify_on_accepted">
							<xsl:value-of select="php:function('lang', 'notify on accepted')"/>
						</label>
						<input  name="application_notify_on_accepted" type="hidden" value = "0"/>
						<input id="field_application_notify_on_accepted" class="full-width settings" name="application_notify_on_accepted" type="checkbox" value = "1">
							<xsl:if test="config_data/application_notify_on_accepted = '1'">
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="checked">
									<xsl:text>checked</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_rejected">
							<xsl:value-of select="php:function('lang', 'Mail text for application rejected')"/>
						</label>
						<textarea id="field_application_mail_rejected" class="full-width settings" name="application_mail_rejected" type="text">
							<xsl:value-of select="config_data/application_mail_rejected"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_application_mail_signature">
							<xsl:value-of select="php:function('lang', 'Signature')"/>
						</label>
						<input id="field_application_mail_signature" name="application_mail_signature" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/application_mail_signature"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<div class="heading">
							<legend>
								<h3>
									<xsl:value-of select="php:function('lang', 'Application comment email settings')"/>
								</h3>
							</legend>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_application_comment_mail_subject_caseofficer">
							<xsl:value-of select="php:function('lang', 'Subject caseofficer')"/>
						</label>
						<input id="field_application_comment_mail_subject_caseofficer" name="application_comment_mail_subject_caseofficer" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/application_comment_mail_subject_caseofficer"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_application_comment_mail_subject">
							<xsl:value-of select="php:function('lang', 'Subject')"/>
						</label>
						<input id="field_application_comment_mail_subject" name="application_comment_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/application_comment_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_application_comment_added_mail">
							<xsl:value-of select="php:function('lang', 'Mail text for comment added')"/>
						</label>
						<textarea id="field_application_comment_added_mail" class="full-width settings" name="application_comment_added_mail" type="text">
							<xsl:value-of select="config_data/application_comment_added_mail"/>
						</textarea>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons" >
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
