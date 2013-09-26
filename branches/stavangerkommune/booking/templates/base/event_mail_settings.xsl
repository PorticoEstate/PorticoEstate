<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking event email settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form-col">

            <dt><label for="field_event_change_mail_subject"><xsl:value-of select="php:function('lang', 'Event Change Subject')"/></label></dt>
            <dd>
				<input id="field_event_change_mail_subject" name="event_change_mail_subject" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/event_change_mail_subject"/></xsl:attribute>
				</input>
            </dd>

            <dt><label for="field_event_change_mail"><xsl:value-of select="php:function('lang', 'Mail for event change')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_event_change_mail" class="full-width settings" name="event_change_mail" type="text"><xsl:value-of select="config_data/event_change_mail"/></textarea>
			</dd>


            <dt><label for="field_event_conflict_mail_subject"><xsl:value-of select="php:function('lang', 'Event Conflict Subject')"/></label></dt>
            <dd>
				<input id="field_event_conflict_mail_subject" name="event_conflict_mail_subject" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/event_conflict_mail_subject"/></xsl:attribute>
				</input>
            </dd>

            <dt><label for="field_event_mail_contact_active_collision"><xsl:value-of select="php:function('lang', 'Mail text to event contact when collision')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_event_mail_contact_active_collision" class="full-width settings" name="event_mail_contact_active_collision" type="text"><xsl:value-of select="config_data/event_mail_contact_active_collision"/></textarea>
			</dd>
            <dt><label for="field_event_mail_conflict_contact_active_collision"><xsl:value-of select="php:function('lang', 'Mail text to conflicting event contact when collision')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_event_mail_conflict_contact_active_collision" class="full-width settings" name="event_mail_conflict_contact_active_collision" type="text"><xsl:value-of select="config_data/event_mail_conflict_contact_active_collision"/></textarea>
			</dd>
            <dt><label for="field_event_mail_building_active_collision_subject"><xsl:value-of select="php:function('lang', 'Event message to building Subject')"/></label></dt>
            <dd>
				<input id="field_event_mail_building_subject" name="event_mail_building_subject" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/event_mail_building_subject"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_event_mail_building"><xsl:value-of select="php:function('lang', 'Mail text to building contact')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_event_mail_building" class="full-width settings" name="event_mail_building" type="text"><xsl:value-of select="config_data/event_mail_building"/></textarea>
			</dd>

            <dt><label for="field_event_canceled_mail_subject"><xsl:value-of select="php:function('lang', 'Event Canceled Subject')"/></label></dt>
            <dd>
				<input id="field_event_canceled_mail_subject" name="event_canceled_mail_subject" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/event_canceled_mail_subject"/></xsl:attribute>
				</input>
            </dd>

            <dt><label for="field_event_canceled_mail"><xsl:value-of select="php:function('lang', 'Mail for event canceled')"/></label></dt>
			<dd class="yui-skin-sam">
				<textarea id="field_event_canceled_mail" class="full-width settings" name="event_canceled_mail" type="text"><xsl:value-of select="config_data/event_canceled_mail"/></textarea>
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
