<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content"-->

	<!--dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Asynchronous Tasks')" />
			</dt>
	</dl-->

	<xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="settings/tabs"/>
			<div id="async_settings">
				<div class="pure-control-group">
					<dt>
						<input type='checkbox' value='1' name="booking_async_task_update_reservation_state_enabled" id="field_booking_async_task_update_reservation_state_enabled">
							<xsl:if test="settings/booking_async_task_update_reservation_state_enabled and settings/booking_async_task_update_reservation_state_enabled='1'">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
							<xsl:if test="not(settings/permission/write)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
					&#160;
						<label for="booking_async_task_update_reservation_state_enabled">
							<xsl:value-of select="php:function('lang', 'booking_async_task_update_reservation_state_enabled')" />
						</label>
					</dt>
				</div>
				<div class="pure-control-group">
					<dt>
						<input type='checkbox' value='1' name="booking_async_task_send_reminder_enabled" id="field_booking_async_task_send_reminder_enabled">
							<xsl:if test="settings/booking_async_task_send_reminder_enabled and settings/booking_async_task_send_reminder_enabled ='1'">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
							<xsl:if test="not(settings/permission/write)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
					&#160;
						<label for="booking_async_task_send_reminder_enabled">
							<xsl:value-of select="php:function('lang', 'booking_async_task_send_reminder_enabled')" />
						</label>
					</dt>
				</div>
				<div class="clr"/>
			</div>
		</div>
		<xsl:if test="settings/permission/write">
			<div class="form-buttons">
				<input type="submit" id="button" class="button pure-button pure-button-primary">
					<xsl:attribute name="value">
						<xsl:choose>
							<xsl:when test="new_form">
								<xsl:value-of select="php:function('lang', 'Create')"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="php:function('lang', 'Update')"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
			</div>
		</xsl:if>
	</form>
	<!--/div-->
</xsl:template>
