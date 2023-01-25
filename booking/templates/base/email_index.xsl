<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
			<div id="building" class="booking-container">
				<input type="hidden" name="step" value="0"/>
				<div class="pure-control-group">
					<label for="field_building_name">
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<input id="field_building_id" name="building_id" type="hidden">
						<xsl:attribute name="value">
							<xsl:value-of select="building/id"/>
						</xsl:attribute>
					</input>
					<input id="field_building_name" name="building_name" type="text" class="pure-input-3-4" >
						<xsl:attribute name="value">
							<xsl:value-of select="building/name"/>
						</xsl:attribute>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'Season')" />
					</label>
					<input type="hidden" data-validation="application_season">
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please choose at least 1 season')" />
						</xsl:attribute>
					</input>
					<div id="season_container" style="display:inline-block;">
						<span class="select_first_text">
							<xsl:value-of select="php:function('lang', 'Select a building first')" />
						</span>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'recipients')"/>
					</label>
					<select id="email_recipients" name="email_recipients[]" multiple="true" class="pure-input-3-4">
						<xsl:attribute name="data-validation">
							<xsl:text>email_recipients</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'select at least one recipient')" />
						</xsl:attribute>
						<xsl:apply-templates select="recipient_list/options"/>
					</select>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'from')"/>
					</label>
					<xsl:value-of select="from"/>
				</div>



				<div class="pure-control-group">
					<label for="field_mailsubject">
						<xsl:value-of select="php:function('lang', 'Mail subject')" />
					</label>
					<input type="text" id="field_mailsubject" name="mailsubject" class="pure-input-3-4" >
						<xsl:attribute name="value">
							<xsl:value-of select="mailsubject"/>
						</xsl:attribute>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a mail subject')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_mailbody">
						<xsl:value-of select="php:function('lang', 'Mail body')" />
					</label>
					<div class="pure-custom pure-input-3-4">
						<textarea id="field_mailbody" name="mailbody">
							<xsl:attribute name="data-validation">
								<xsl:text>mailbody</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a mail body')" />
							</xsl:attribute>
							<xsl:value-of select="mailbody"/>
						</textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Send e-mails')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'Name')"/>;
		var html_editor = '<xsl:value-of select="html_editor"/>';
	</script>
</xsl:template>
