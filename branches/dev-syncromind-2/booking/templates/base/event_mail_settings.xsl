<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="data/tabs"/>
			<div id="event_settings" class="booking-container">
				<fieldset>
					<div class="heading">
						<legend>
							<h3>
								<xsl:value-of select="php:function('lang', 'Application event settings')"/>
							</h3>
						</legend>
					</div>
					<div class="pure-control-group">
						<label for="field_event_change_mail_subject">
							<xsl:value-of select="php:function('lang', 'Event Change Subject')"/>
						</label>
						<input id="field_event_change_mail_subject" name="event_change_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/event_change_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_event_change_mail">
							<xsl:value-of select="php:function('lang', 'Mail for event change')"/>
						</label>
						<textarea id="field_event_change_mail" class="full-width settings" name="event_change_mail" type="text">
							<xsl:value-of select="config_data/event_change_mail"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_event_conflict_mail_subject">
							<xsl:value-of select="php:function('lang', 'Event Conflict Subject')"/>
						</label>
						<input id="field_event_conflict_mail_subject" name="event_conflict_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/event_conflict_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_event_mail_conflict_contact_active_collision">
							<xsl:value-of select="php:function('lang', 'Mail text to conflicting event contact when collision')"/>
						</label>
						<textarea id="field_event_mail_conflict_contact_active_collision" class="full-width settings" name="event_mail_conflict_contact_active_collision" type="text">
							<xsl:value-of select="config_data/event_mail_conflict_contact_active_collision"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_event_mail_building_subject">
							<xsl:value-of select="php:function('lang', 'Event message to building Subject')"/>
						</label>
						<input id="field_event_mail_building_subject" name="event_mail_building_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/event_mail_building_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_event_mail_building">
							<xsl:value-of select="php:function('lang', 'Mail text to building contact')"/>
						</label>
						<textarea id="field_event_mail_building" class="full-width settings" name="event_mail_building" type="text">
							<xsl:value-of select="config_data/event_mail_building"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_event_canceled_mail_subject">
							<xsl:value-of select="php:function('lang', 'Event Canceled Subject')"/>
						</label>
						<input id="field_event_canceled_mail_subject" name="event_canceled_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/event_canceled_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_event_canceled_mail">
							<xsl:value-of select="php:function('lang', 'Mail for event canceled')"/>
						</label>
						<textarea id="field_event_canceled_mail" class="full-width settings" name="event_canceled_mail" type="text">
							<xsl:value-of select="config_data/event_canceled_mail"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_event_edited_mail_subject">
							<xsl:value-of select="php:function('lang', 'Event Edited Subject')"/>
						</label>
						<input id="field_event_edited_mail_subject" name="event_edited_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/event_edited_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_event_edited_mail">
							<xsl:value-of select="php:function('lang', 'Mail for event edited')"/>
						</label>
						<textarea id="field_event_edited_mail" class="full-width settings" name="event_edited_mail" type="text">
							<xsl:value-of select="config_data/event_edited_mail"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<div class="heading">
							<legend>
								<h3>
									<xsl:value-of select="php:function('lang', 'Cancel booking/allocation email settings')"/>
								</h3>
							</legend>
						</div>
					</div>
					<div class="pure-control-group">
						<label for="field_booking_canceled_mail_subject">
							<xsl:value-of select="php:function('lang', 'Booking Canceled Subject')"/>
						</label>
						<input id="field_booking_canceled_mail_subject" name="booking_canceled_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/booking_canceled_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_booking_canceled_mail">
							<xsl:value-of select="php:function('lang', 'Mail for booking canceled')"/>
						</label>
						<textarea id="field_booking_canceled_mail" class="full-width settings" name="booking_canceled_mail" type="text">
							<xsl:value-of select="config_data/booking_canceled_mail"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label for="field_allocation_canceled_mail_subject">
							<xsl:value-of select="php:function('lang', 'Allocation Canceled Subject')"/>
						</label>
						<input id="field_allocation_canceled_mail_subject" name="allocation_canceled_mail_subject" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/allocation_canceled_mail_subject"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_allocation_canceled_mail">
							<xsl:value-of select="php:function('lang', 'Mail for allocation canceled')"/>
						</label>
						<textarea id="field_allocation_canceled_mail" class="full-width settings" name="allocation_canceled_mail" type="text">
							<xsl:value-of select="config_data/allocation_canceled_mail"/>
						</textarea>
					</div>
				</fieldset>
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
