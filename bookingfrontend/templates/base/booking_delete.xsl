<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div id="booking-delete-page-content" class="margin-top-content">
   	<div class="container wrapper">
		<div class="location">
			<span><a><xsl:attribute name="href">
				<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Home')" />
			</a></span>
			<span><xsl:value-of select="php:function('lang', 'Delete Booking')"/></span>										
		</div>

       	<div class="row">			

			<form action="" method="POST" class="col-md-8">
				<div class="col mb-4">
					<xsl:call-template name="msgbox"/>
				</div>
				<input type="hidden" name="application_id" value="{booking/application_id}"/>
				<input type="hidden" name="group_id" value="{booking/group_id}" />
				<input type="hidden" name="building_id" value="{booking/building_id}" />
				<input type="hidden" name="season_id" value="{booking/season_id}" />
				<input type="hidden" name="from_" value="{booking/from_}" />
				<input type="hidden" name="to_" value="{booking/to_}" />

					<div class="form-group">
					<xsl:value-of select="php:function('lang', 'Booking Delete Information')"/>&#160;
					<xsl:value-of select="php:function('lang', 'Booking Delete Information2')"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></label>
						<xsl:value-of select="booking/building_name"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Group')"/></label>
						<xsl:value-of select="booking/group_name"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'From')" /></label>
						<xsl:value-of select="booking/from_"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'To')"/></label>
						<xsl:value-of select="booking/to_"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Season')"/></label>
						<xsl:value-of select="booking/season_name"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Delete allocation also')" /></label>
						<input type="checkbox" class="mr-2" name="delete_allocation" id="delete_allocation">
							<xsl:if test="delete_allocation='on'">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						<xsl:value-of select="php:function('lang', 'Delete allocations')" />
					</div>

					<div class="form-group">
						<div>
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" /></label>
							<input type="checkbox" class="mr-2" name="outseason" id="outseason">
								<xsl:if test="outseason='on'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:value-of select="php:function('lang', 'Out season')" />
						</div>
						
						<div>
							<input type="checkbox" class="mr-2" name="recurring" id="recurring">
								<xsl:if test="recurring='on'">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:value-of select="php:function('lang', 'Delete until')" />
						</div>

						<input class="form-control" id="field_repeat_until" name="repeat_until" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="repeat_until"/>
							</xsl:attribute>
						</input>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Interval')" /></label>
						<xsl:value-of select="../field_interval" />
							<select id="field_interval" class="form-control" name="field_interval">
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
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Message')" /></label>
						<textarea id="field-message" class="form-control" name="message" type="text">
							<xsl:value-of select="system_message/message"/>
						</textarea>
					</div>

					<input type="submit" class="btn btn-light mr-4">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Delete')"/>
						</xsl:attribute>
					</input>
					<a class="cancel">
						<xsl:attribute name="href">
							<xsl:value-of select="booking/cancel_link"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Cancel')"/>
					</a>

			</form>
		</div>

	</div>
</div>
<div class="push"></div>
	<script type="text/javascript">
		var season_id = '<xsl:value-of select="booking/season_id" />';
		var group_id = '<xsl:value-of select="booking/group_id" />';
		var initialSelection = '<xsl:value-of select="booking/resources_json" />';
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')" />;
	</script>
</xsl:template>
