<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Cancel allocation')"/>
			</dt>
		</dl>
		<xsl:call-template name="msgbox"/>
		<dl class="form">
			<dd>
				<xsl:value-of select="php:function('lang', 'Cancel Information')"/>
			</dd>
			<dd>
				<xsl:value-of select="php:function('lang', 'Cancel Information2')"/>
			</dd>
		</dl>
		<div class="clr"/>
		<form action="" method="POST">
			<input type="hidden" name="application_id" value="{allocation/application_id}"/>
			<input id="field_org_id" name="organization_id" type="hidden" value="{allocation/organization_id}" />
			<input id="field_building_id" name="building_id" type="hidden" value="{allocation/building_id}" />
			<input id="field_from" name="from_" type="hidden" value="{allocation/from_}" />
			<input id="field_to" name="to_" type="hidden" value="{allocation/to_}" />

			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_building">
								<xsl:value-of select="php:function('lang', 'Building')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<xsl:value-of select="allocation/building_name"/>
							</div>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_org">
								<xsl:value-of select="php:function('lang', 'Organization')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<xsl:value-of select="allocation/organization_name"/>
							</div>
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
								<xsl:value-of select="allocation/from_"/>
							</div>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_to">
								<xsl:value-of select="php:function('lang', 'To')" />
							</label>
						</dt>
						<dd>
							<div>
								<xsl:value-of select="allocation/to_"/>
							</div>
						</dd>
					</dl>
				</div>
			</div>
        
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-2-5 pure-u-lg-1-4">
					<dl class="form-col">
						<dt>
							<label for="field_repeat_until">
								<xsl:value-of select="php:function('lang', 'Recurring allocation deletion')" />
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
								<xsl:value-of select="php:function('lang', 'Delete until')" />
							</label>
						</dd>
						<!--dd class="date-picker">
				<input id="field_repeat_until" name="repeat_until" type="text">
					<xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
				</input>
						</dd-->
						<dd>
							<input class="datetime" id="field_repeat_until" name="repeat_until" type="text">
								<xsl:attribute name="value">
									<xsl:value-of select="repeat_until"/>
								</xsl:attribute>
							</input>
						</dd>
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
								<xsl:value-of select="message"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>

			<div class="pure-g">
				<div class="pure-u-1">
					<div class="form-buttons">
						<input type="submit">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'Cancel allocation')"/>
							</xsl:attribute>
						</input>
						<a class="cancel">
							<xsl:attribute name="href">
								<xsl:value-of select="allocation/cancel_link"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Cancel')" />
						</a>
					</div>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var season_id = '<xsl:value-of select="allocation/season_id" />';
	</script>
</xsl:template>
