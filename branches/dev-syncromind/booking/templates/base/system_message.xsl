<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="system_message/tabs"/>
			<div id="system_message" class="booking-container">
				<input name="title" type="hidden" value="{system_message/title}" />
				<input name="message" type="hidden" value="{system_message/message}" />
				<input name="created" type="hidden" value="{system_message/created}" />
				<input name="name" type="hidden" value="{system_message/name}" />
				<input name="phone" type="hidden" value="{system_message/phone}" />
				<input name="email" type="hidden" value="{system_message/email}" />
				<input name="status" type="hidden" value="CLOSED" />
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Created')" />
					</label>
					<xsl:value-of select="system_message/created"/>
				</div>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'Message')" />
					</label>
					<div style="display:inline-block;max-width:80%;">
						<xsl:value-of select="system_message/message" disable-output-escaping="yes"/>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<xsl:value-of select="system_message/name"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Phone')" />
					</label>
					<xsl:value-of select="system_message/phone"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Email')" />
					</label>
					<xsl:value-of select="system_message/email"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Type')" />
					</label>
					<xsl:value-of select="system_message/type"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Status')" />
					</label>
					<xsl:value-of select="system_message/status" />
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="system_message/status = php:function('lang','NEW')">
				<input class="pure-button pure-button-primary" style="margin-right: 10px;" type="submit" value="{php:function('lang', 'Close')}"/>
			</xsl:if>
			<a class="button pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="system_message/back_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Back')" />
			</a>
		</div>
	</form>
</xsl:template>
