<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking application settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form-col">
            <dt><label for="field_application_new_application"><xsl:value-of select="php:function('lang', 'New application')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_new_application" class="full-width settings" name="application_new_application" type="text"><xsl:value-of select="config_data/application_new_application"/></textarea>
			</dd>
            <dt><label for="field_application_activities"><xsl:value-of select="php:function('lang', 'Activity')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_activities" name="application_activities"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_activities"/></textarea>
			</dd>
			<dt>
				<label for="field_application_description"><xsl:value-of select="php:function('lang', 'Information about the event')" /></label>
			</dt>
			<dd class="yui-skin-sam">
			<textarea id="field_application_description" name="application_description"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_description"/></textarea>
			</dd>
            <dt>
               <label for="field_application_equipment"><xsl:value-of select="php:function('lang', 'Extra information for the event')" /></label>
            </dt>
            <dd class="yui-skin-sam">
               <textarea id="field_application_equipment" name="application_equipment"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_equipment"/></textarea>
            </dd>
            <dt><label for="field_application_howmany"><xsl:value-of select="php:function('lang', 'How many?')" /></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_howmany" name="application_howmany"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_howmany"/></textarea>
			</dd>
            <dt><label for="field_application_where"><xsl:value-of select="php:function('lang', 'Where?')" /></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_where" name="application_where"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_where"/></textarea>
			</dd>
            <dt><label for="field_application_when"><xsl:value-of select="php:function('lang', 'When?')" /></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_when" name="application_when"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_when"/></textarea>
			</dd>
            <dt><label for="field_application_who"><xsl:value-of select="php:function('lang', 'Who?')" /></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_who" name="application_who"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_who"/></textarea>
			</dd>
            <dt><label for="field_application_contact_information"><xsl:value-of select="php:function('lang', 'Contact information')" /></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_contact_information" name="application_contact_information"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_contact_information"/></textarea>
			</dd>
            <dt><label for="field_application_responsible_applicant"><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /> 1</label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_responsible_applicant" name="application_responsible_applicant"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_responsible_applicant"/></textarea>
			</dd>
            <dt><label for="field_application_invoice_information"><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /> 2</label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_invoice_information" name="application_invoice_information"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_invoice_information"/></textarea>
			</dd>
            <dt><label for="field_application_terms"><xsl:value-of select="php:function('lang', 'Terms and conditions')" /> 1</label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_terms" name="application_terms"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_terms"/></textarea>
			</dd>
            <dt><label for="field_application_terms2"><xsl:value-of select="php:function('lang', 'Terms and conditions')" /> 2</label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_terms2" name="application_terms2"  class="full-width settings" type="text"><xsl:value-of select="config_data/application_terms2"/></textarea>
			</dd>
        </dl>
		<div class="form-buttons">
			<input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>

</xsl:template>
