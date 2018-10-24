<xsl:template match="data" xmlns:php="http://php.net/xsl">
		<div id="allocation-delete-page-content" class="margin-top-content">
			<div class="container wrapper">
				<div class="location">
					<span><a><xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Home')" />
					</a></span>
					<span><xsl:value-of select="php:function('lang', 'Delete allocation')"/></span>										
				</div>

				<div class="row">
					<form action="" method="POST" class="col-md-8">
						<div class="col mb-4">
							<xsl:call-template name="msgbox"/>
						</div>

						<input type="hidden" name="application_id" value="{allocation/application_id}"/>
						<input id="field_org_id" name="organization_id" type="hidden" value="{allocation/organization_id}" />
						<input id="field_building_id" name="building_id" type="hidden" value="{allocation/building_id}" />
						<input id="field_from" name="from_" type="hidden" value="{allocation/from_}" />
						<input id="field_to" name="to_" type="hidden" value="{allocation/to_}" />

					<div class="form-group">
							<xsl:value-of select="php:function('lang', 'Delete Information')"/>&#160;
							<xsl:value-of select="php:function('lang', 'Delete Information2')"/>
					</div>

							<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></label>
								<xsl:value-of select="allocation/building_name"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
								<xsl:value-of select="allocation/organization_name"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'From')" /></label>
								<xsl:value-of select="allocation/from_"/>
							</div>

							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'To')" /></label>
								<xsl:value-of select="allocation/to_"/>
							</div>
						
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" /></label>
								
								<div>
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
									<xsl:value-of select="allocation/cancel_link"/>
								</xsl:attribute>
								<xsl:value-of select="php:function('lang', 'Cancel')" />
							</a>

					</form>
				</div>

			</div>
		</div>

	<script>
		var season_id = '<xsl:value-of select="allocation/season_id"/>';
		var initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
	</script>
</xsl:template>
