<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="settings" class="booking-container">
				<div class="pure-control-group">
					<label for="field_application_new_application">
						<xsl:value-of select="php:function('lang', 'New application')"/>
					</label>
					<textarea id="field_application_new_application" class="pure-input-1-2" name="application_new_application" type="text">
						<xsl:value-of select="config_data/application_new_application"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_contact">
						<xsl:value-of select="php:function('lang', 'Contact and invoice information')"/>
					</label>
					<textarea id="field_application_contact" class="pure-input-1-2" name="application_contact" type="text">
						<xsl:value-of select="config_data/application_contact"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_activities">
						<xsl:value-of select="php:function('lang', 'Activity')"/>
					</label>
					<textarea id="field_application_activities" name="application_activities"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_activities"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_description">
						<xsl:value-of select="php:function('lang', 'Information about the event')" />
					</label>
					<textarea id="field_application_description" name="application_description"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_description"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_equipment">
						<xsl:value-of select="php:function('lang', 'Extra information for the event')" />
					</label>
					<textarea id="field_application_equipment" name="application_equipment"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_equipment"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_howmany">
						<xsl:value-of select="php:function('lang', 'How many?')" />
					</label>
					<textarea id="field_application_howmany" name="application_howmany"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_howmany"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_where">
						<xsl:value-of select="php:function('lang', 'Where?')" />
					</label>
					<textarea id="field_application_where" name="application_where"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_where"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_when">
						<xsl:value-of select="php:function('lang', 'When?')" />
					</label>
					<textarea id="field_application_when" name="application_when"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_when"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_who">
						<xsl:value-of select="php:function('lang', 'Who?')" />
					</label>
					<textarea id="field_application_who" name="application_who"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_who"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_contact_information">
						<xsl:value-of select="php:function('lang', 'Contact information')" />
					</label>
					<textarea id="field_application_contact_information" name="application_contact_information"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_contact_information"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_responsible_applicant">
						<xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" />
						<span> 1</span>
					</label>
					<textarea id="field_application_responsible_applicant" name="application_responsible_applicant"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_responsible_applicant"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_invoice_information">
						<xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" />
						<span> 2</span>
					</label>
					<textarea id="field_application_invoice_information" name="application_invoice_information"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_invoice_information"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_terms">
						<xsl:value-of select="php:function('lang', 'Terms and conditions')" />
						<span> 1</span>
					</label>
					<textarea id="field_application_terms" name="application_terms"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_terms"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_application_terms2">
						<xsl:value-of select="php:function('lang', 'Terms and conditions')" />
						<span> 2</span>
					</label>
					<textarea id="field_application_terms2" name="application_terms2"  class="pure-input-1-2" type="text">
						<xsl:value-of select="config_data/application_terms2"/>
					</textarea>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
