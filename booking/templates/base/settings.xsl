<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<xsl:call-template name="msgbox"/>
		<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="billing/tabs"/>
				<div id="settings" class="booking-container">
					<div class="pure-control-group">
						<label for="field_user_can_delete_bookings">
							<xsl:value-of select="php:function('lang', 'Frontend users can delete bookings/events')"/>
						</label>
						<select id="field_user_can_delete_bookings" name="config_data[user_can_delete_bookings]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/user_can_delete_bookings='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/user_can_delete_bookings='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Events is deleted from database')"/>
						</label>
						<select id="field_user_can_delete_events" name="config_data[user_can_delete_events]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/user_can_delete_events='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/user_can_delete_events='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_user_can_delete_allocations">
							<xsl:value-of select="php:function('lang', 'Frontend users can delete allocations')"/>
						</label>
						<select id="field_user_can_delete_allocations" name="config_data[user_can_delete_allocations]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/user_can_delete_allocations='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/user_can_delete_allocations='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
							<option value="never">
								<xsl:if test="config_data/user_can_delete_allocations='never'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No action')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_extra_schedule">
							<xsl:value-of select="php:function('lang', 'Activate extra kalendar field on building')"/>
						</label>
						<select id="field_extra_schedule" name="config_data[extra_schedule]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/extra_schedule='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/extra_schedule='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_extra_schedule_ids">
							<xsl:value-of select="php:function('lang', 'Ids that should be included in the calendar')"/>
						</label>
						<input id="field_extra_schedule_ids" type="text" name="config_data[extra_schedule_ids]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/extra_schedule_ids"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Split mail if building contains swiming pools resources')"/>
					</div>
					<div class="pure-control-group">
						<label for="field_split_pool">
							<xsl:value-of select="php:function('lang', 'Split mail when building has swiming pool')"/>
						</label>
						<select id="field_split_pool" name="config_data[split_pool]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/split_pool='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/split_pool='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<label for="field_split_pool_ids">
							<xsl:value-of select="php:function('lang', 'activities that uses swimming pools')"/>
						</label>
						<input id="field_split_pool_ids" type="text" name="config_data[split_pool_ids]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/split_pool_ids"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="fieldsplit_pool2_ids">
							<xsl:value-of select="php:function('lang', 'other activities')"/>
						</label>
						<input id="field_split_pool2_ids" type="text" name="config_data[split_pool2_ids]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/split_pool2_ids"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="fieldsplit_pool3_ids">
							<xsl:value-of select="php:function('lang', 'activities that all should get except those in the next field.')"/>
						</label>
						<input id="field_split_pool3_ids" type="text" name="config_data[split_pool3_ids]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/split_pool3_ids"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="fieldsplit_pool4_ids">
							<xsl:value-of select="php:function('lang', 'activities that never should get mail')"/>
						</label>
						<input id="field_split_pool4_ids" type="text" name="config_data[split_pool4_ids]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/split_pool4_ids"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Who get cancelation mails')"/>
					</div>				
					<div class="pure-control-group">
						<label for="field_mail_users_season">
							<xsl:value-of select="php:function('lang', 'Users of current season or users the last 300 days')"/>
						</label>
						<select id="field_mail_users_season" name="config_data[mail_users_season]" class="pure-input-1-2" >
							<option value="no">
								<xsl:if test="config_data/mail_users_season='no'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Last 300 days')"/>
							</option>
							<option value="yes">
								<xsl:if test="config_data/mail_users_season='yes'">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Season')"/>
							</option>
						</select>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Email warnings')"/>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Cancelation Email Addresses')"/>
					</div>				
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'One e-mail pr. line.')"/>
						</label>
						<textarea id="field_emails" name="config_data[emails]" class="pure-input-1-2" >
							<xsl:value-of select="config_data/emails"/>
						</textarea>
					</div>

					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Front page filtering')"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Title for filter boxes')"/>
						</label>
						<input id="field_frontpage_filterboxtitle" type="text" name="config_data[frontpage_filterboxtitle]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/frontpage_filterboxtitle"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Text and activity IDs for each filter box')"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'One filter box per line')"/>
						</label>
						<textarea id="field_frontpage_filterboxdata" name="config_data[frontpage_filterboxdata]" class="pure-input-1-2">
							<xsl:value-of select="config_data/frontpage_filterboxdata"/>
						</textarea>
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Title for upcoming events')"/>
						</label>
						<input id="field_frontpage_upcomingevents" type="text" name="config_data[frontpage_upcomingevents]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/frontpage_upcomingevents"/>
							</xsl:attribute>
						</input>
					</div>

					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Help texts')"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Help text calendar: Book')"/>
						</label>
						<textarea id="field_help_calendar_book" name="config_data[help_calendar_book]" class="pure-input-1-2">
							<xsl:value-of select="config_data/help_calendar_book"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Help text organization: Edit group')"/>
						</label>
						<textarea id="field_help_group_edit" name="config_data[help_group_edit]" class="pure-input-1-2">
							<xsl:value-of select="config_data/help_group_edit"/>
						</textarea>
					</div>

					<div class="pure-control-group">					
						<xsl:value-of select="php:function('lang', 'Billing sequence numbers')"/>
					</div>
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'Do not change these values unless you know what they are.')"/> 
					</div>				
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Current internal billing sequence number')"/>
						</label>
						<input type="number" name="billing[internal]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="billing/internal"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Current external billing sequence number')"/>
						</label>
						<input type="number" name="billing[external]" class="pure-input-1-2" >
							<xsl:attribute name="value">
								<xsl:value-of select="billing/external"/>
							</xsl:attribute>
						</input>
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
	</div>
</xsl:template>
