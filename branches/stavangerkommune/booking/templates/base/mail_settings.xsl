<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking system settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Application email settings')"/></dt>
       </dl>
       <dl class="form-col">
            <dt><label for="field_application_mail_systemname"><xsl:value-of select="php:function('lang', 'System name')"/></label></dt>
            <dd>
				<input id="field_application_mail_systemname" name="application_mail_systemname" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/application_mail_systemname"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_application_mail_subject"><xsl:value-of select="php:function('lang', 'Subject')"/></label></dt>
            <dd>
				<input id="field_application_mail_subject" name="application_mail_subject" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/application_mail_subject"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_application_mail_created"><xsl:value-of select="php:function('lang', 'Mail text for application created')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_mail_created" class="full-width settings" name="application_mail_created" type="text"><xsl:value-of select="config_data/application_mail_created"/></textarea>
			</dd>
            <dt><label for="field_application_mail_pending"><xsl:value-of select="php:function('lang', 'Mail text for application pending')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_mail_pending" class="full-width settings" name="application_mail_pending" type="text"><xsl:value-of select="config_data/application_mail_pending"/></textarea>
			</dd>
            <dt><label for="field_application_mail_accepted"><xsl:value-of select="php:function('lang', 'Mail text for application accepted')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_mail_accepted" class="full-width settings" name="application_mail_accepted" type="text"><xsl:value-of select="config_data/application_mail_accepted"/></textarea>
			</dd>
            <dt><label for="field_application_mail_rejected"><xsl:value-of select="php:function('lang', 'Mail text for application rejected')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_application_mail_rejected" class="full-width settings" name="application_mail_rejected" type="text"><xsl:value-of select="config_data/application_mail_rejected"/></textarea>
			</dd>
            <dt><label for="field_application_mail_signature"><xsl:value-of select="php:function('lang', 'Signature')"/></label></dt>
            <dd>
				<input id="field_application_mail_signature" name="application_mail_signature" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/application_mail_signature"/></xsl:attribute>
				</input>
            </dd>

        </dl>

        <div class="clr"/>
        <dl class="form">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Application comment email settings')"/></dt>

            <dt><label for="field_application_comment_mail_subject_caseofficer"><xsl:value-of select="php:function('lang', 'Subject caseofficer')"/></label></dt>
            <dd>
                <input id="field_application_comment_mail_subject_caseofficer" name="application_comment_mail_subject_caseofficer" type="text" size="50">
                    <xsl:attribute name="value"><xsl:value-of select="config_data/application_comment_mail_subject_caseofficer"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_application_comment_mail_subject"><xsl:value-of select="php:function('lang', 'Subject')"/></label></dt>
            <dd>
                <input id="field_application_comment_mail_subject" name="application_comment_mail_subject" type="text" size="50">
                    <xsl:attribute name="value"><xsl:value-of select="config_data/application_comment_mail_subject"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_application_comment_added_mail"><xsl:value-of select="php:function('lang', 'Mail text for comment added')"/></label></dt>
            <dd class="yui-skin-sam">
                <textarea id="field_application_comment_added_mail" class="full-width settings" name="application_comment_added_mail" type="text"><xsl:value-of select="config_data/application_comment_added_mail"/></textarea>
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
