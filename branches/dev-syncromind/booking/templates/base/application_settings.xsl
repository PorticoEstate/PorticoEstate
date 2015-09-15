<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->

    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

   	<!--dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking application settings')"/></dt>
   	</dl-->

    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
            <div id="settings">
                <div class="pure-control-group">
                    <label for="field_application_new_application">
                        <h4><xsl:value-of select="php:function('lang', 'New application')"/></h4>
                    </label>
                    <textarea id="field_application_new_application" class="full-width settings" name="application_new_application" type="text"><xsl:value-of select="config_data/application_new_application"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_activities">
                        <h4><xsl:value-of select="php:function('lang', 'Activity')"/></h4>
                    </label>
                    <textarea id="field_application_activities" name="application_activities"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_activities"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_description">
                        <h4><xsl:value-of select="php:function('lang', 'Information about the event')" /></h4>
                    </label>
                    <textarea id="field_application_description" name="application_description"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_description"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_equipment">
                        <h4><xsl:value-of select="php:function('lang', 'Extra information for the event')" /></h4>
                    </label>
                    <textarea id="field_application_equipment" name="application_equipment"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_equipment"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_howmany">
                        <h4><xsl:value-of select="php:function('lang', 'How many?')" /></h4>
                    </label>
                    <textarea id="field_application_howmany" name="application_howmany"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_howmany"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_where">
                        <h4><xsl:value-of select="php:function('lang', 'Where?')" /></h4>
                    </label>
                    <textarea id="field_application_where" name="application_where"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_where"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_when">
                        <h4><xsl:value-of select="php:function('lang', 'When?')" /></h4>
                    </label>
                    <textarea id="field_application_when" name="application_when"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_when"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_who">
                        <h4><xsl:value-of select="php:function('lang', 'Who?')" /></h4>
                    </label>
                    <textarea id="field_application_who" name="application_who"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_who"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_contact_information">
                        <h4><xsl:value-of select="php:function('lang', 'Contact information')" /></h4>
                    </label>
                    <textarea id="field_application_contact_information" name="application_contact_information"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_contact_information"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_responsible_applicant">
                        <h4><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /><span> 1</span></h4>
                    </label>
                    <textarea id="field_application_responsible_applicant" name="application_responsible_applicant"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_responsible_applicant"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_invoice_information">
                        <h4><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /><span> 2</span></h4>
                    </label>
                    <textarea id="field_application_invoice_information" name="application_invoice_information"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_invoice_information"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_terms">
                        <h4><xsl:value-of select="php:function('lang', 'Terms and conditions')" /><span> 1</span></h4>
                    </label>                       
                    <textarea id="field_application_terms" name="application_terms"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_terms"/></textarea>
                </div>
                <div class="pure-control-group">
                    <label for="field_application_terms2">
                        <h4><xsl:value-of select="php:function('lang', 'Terms and conditions')" /><span> 2</span></h4>
                    </label>
                    <textarea id="field_application_terms2" name="application_terms2"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_terms2"/></textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="button pure-button pure-button-primary">
            <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
            </input>
        </div>
    </form>
    <!--/div-->

</xsl:template>
