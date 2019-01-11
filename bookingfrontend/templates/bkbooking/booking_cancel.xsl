<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Cancel bookings')"/>
			</dt>
		</dl>
		<xsl:call-template name="msgbox"/>
		<dl class="form">
			<dd>
				<xsl:value-of select="php:function('lang', 'Booking Cancel Information')"/>
			</dd>
			<dd>
				<xsl:value-of select="php:function('lang', 'Booking Cancel Information2')"/>
			</dd>
		</dl>
		<!--div class="clr"/-->
		<form action="" method="POST">
			<input type="hidden" name="application_id" value="{booking/application_id}"/>
			<input type="hidden" name="group_id" value="{booking/group_id}" />
			<input type="hidden" name="building_id" value="{booking/building_id}" />
			<input type="hidden" name="season_id" value="{booking/season_id}" />
			<input type="hidden" name="from_" value="{booking/from_}" />
			<input type="hidden" name="to_" value="{booking/to_}" />
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="booking/building_name"/>
							</div>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_group">
								<xsl:value-of select="php:function('lang', 'Group')"/>
							</label>
						</dt>
						<dd>
							<xsl:value-of select="booking/group_name"/>
						</dd>
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_from">
								<xsl:value-of select="php:function('lang', 'From')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="booking/from_"/>
							</div>
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_to">
								<xsl:value-of select="php:function('lang', 'To')"/>
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="booking/to_"/>
							</div>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_season">
								<xsl:value-of select="php:function('lang', 'Season')"/>
							</label>
						</dt>
						<dd>
							<xsl:value-of select="booking/season_name"/>
						</dd>
					</dl>
					<dl class="form-col">
						<dt>
							<label for="field_repeat_until">
								<xsl:value-of select="php:function('lang', 'Cancel allocation also')" />
							</label>
						</dt>
						<dd>
							<label>
								<input type="checkbox" name="delete_booking" id="delete_booking">
									<xsl:attribute name="checked">checked</xsl:attribute>
									<xsl:attribute name="disabled">disabled</xsl:attribute>
								</input>
								<xsl:value-of select="php:function('lang', 'Cancel bookings')" />
							</label>
						</dd>
					</dl>
					<dl class="form-col">
						<dd>
							<label>
								<input type="checkbox" name="delete_allocation" id="delete_allocation">
									<xsl:if test="delete_allocation='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Cancel allocations')" />
							</label>
						</dd>
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_repeat_until">
								<xsl:value-of select="php:function('lang', 'Recurring allocation cancelation')" />
							</label>
						</dt>
						<dd>
							<label>
								<input type="checkbox" name="outseason" id="outseason">
									<xsl:if test="outseason='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Out season')" />
							</label>
						</dd>
						<dd>
							<label>
								<input type="checkbox" name="recurring" id="recurring">
									<xsl:if test="recurring='on'">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</input>
								<xsl:value-of select="php:function('lang', 'Cancel until')" />
							</label>
						</dd>
						<dd>
							<input class="datetime" id="field_repeat_until" name="repeat_until" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="repeat_until" />
								</xsl:attribute>
							</input>
						</dd>
						<!--dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
						</dd-->
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<xsl:value-of select="php:function('lang', 'Interval')" />
						</dt>
						<dd>
							<xsl:value-of select="../field_interval" />
							<select id="field_interval" name="field_interval">
								<option value="1">
									<xsl:if test="interval=1">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', '1 week')" />
								</option>
								<option value="2">
									<xsl:if test="interval=2">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', '2 weeks')" />
								</option>
								<option value="3">
									<xsl:if test="interval=3">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', '3 weeks')" />
								</option>
								<option value="4">
									<xsl:if test="interval=4">
										<xsl:attribute name="selected">selected</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', '4 weeks')" />
								</option>
							</select>
						</dd>
					</dl>
				</div>
			</div>
			<div class="pure-g">
				<div class="pure-u-1 pure-u-lg-4-5">
					<dl class="form-col">
						<dt>
							<label for="field_message">
								<xsl:value-of select="php:function('lang', 'Message')" />
							</label>
						</dt>
						<dd>
							<textarea id="field-message" name="message" type="text">
								<xsl:value-of select="system_message/message"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Cancel booking')"/>
					</xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href">
						<xsl:value-of select="booking/cancel_link"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Cancel')"/>
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var season_id = '<xsl:value-of select="booking/season_id" />';
		var group_id = '<xsl:value-of select="booking/group_id" />';
	</script>
</xsl:template>
