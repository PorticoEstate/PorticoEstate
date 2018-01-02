<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="delegate/tabs"/>
			<div id="delegate" class="booking-container">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organization')" />
						</label>
						<xsl:value-of select="delegate/organization_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Name')" />
						</label>
						<xsl:value-of select="delegate/name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'email')" />
						</label>
						<xsl:value-of select="delegate/email"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'phone')" />
						</label>
						<xsl:value-of select="delegate/phone" />
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<a class="button pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="delegate/edit_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Edit')" />
			</a>
			<input type="button" class="pure-button pure-button-primary" name="cencel">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="delegate/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
