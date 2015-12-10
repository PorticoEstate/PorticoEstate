<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<input type="hidden" name="step" value="{step}"/>
		<input type="hidden" name="seasons" value="{season}"/>
		<input type="hidden" name="building_id" value="{building_id}"/>
		<input type="hidden" name="mailbody" value="{mailbody}"/>
		<input type="hidden" name="mailsubject" value="{mailsubject}"/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
			<div id="building">
				<div class="pure-control-group">
					<label for="field_contacts">
						<h4>
							<xsl:value-of select="php:function('lang', 'Recipients')"/> - (<xsl:value-of select="count(contacts)" />)</h4>
					</label>
					<select id="field_contacts" name="contacts" size="10">
						<xsl:for-each select="contacts">
							<xsl:sort select="name"/>
							<option>
								<xsl:attribute name="value">
									<xsl:value-of select="email"/>
								</xsl:attribute>
								<xsl:value-of select="name"/> &lt;
								<xsl:value-of select="email"/>&gt;
							</option>
						</xsl:for-each>
					</select>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" name="sendmail" class="pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Send e-mails')"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
