<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<xsl:call-template name="msgbox"/>
		<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="meta/tabs"/>
				<div id="meta" class="booking-container">
					<div class="pure-control-group">
						<label for="field_metatag_author">
							<xsl:value-of select="php:function('lang', 'Author')"/>
						</label>
						<input id="field_metatag_author" name="metatag_author" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/metatag_author"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_metatag_robots">
							<xsl:value-of select="php:function('lang', 'Robots')"/>
						</label>
						<input id="field_metatag_robots" name="metatag_robots" type="text" size="50">
							<xsl:attribute name="value">
								<xsl:value-of select="config_data/metatag_robots"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="pure-control-group">
						<label for="field_frontpagetext">
							<xsl:value-of select="php:function('lang', 'Frontpage text')"/>
						</label>
						<div class="pure-custom">
							<textarea id="field_frontpagetext" class="full-width" name="frontpagetext">
								<xsl:value-of disable-output-escaping="yes" select="config_data/frontpagetext"/>
							</textarea>
						</div>
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








