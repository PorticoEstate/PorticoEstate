<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="system_message/tabs"/>
			<div id="system_message" class="booking-container">
				<div class="pure-control-group">
					<label for="field_title">
						<xsl:value-of select="php:function('lang', 'Title')" />
					</label>
					<input name="title" type="text" value="{system_message/title}" />
				</div>
				<div class="pure-control-group">
					<label for="field_message">
						<xsl:value-of select="php:function('lang', 'Message')" />
					</label>
					<div class="custom-container">
						<textarea id="field-message" name="message" type="text">
							<xsl:value-of select="system_message/message"/>
						</textarea>
					</div>
				</div>
				<div class="pure-control-group">
					<label for="field_name">
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<input name="name" type="text" value="{system_message/name}" />
				</div>
				<div class="pure-control-group">
					<label for="field_phone">
						<xsl:value-of select="php:function('lang', 'Phone')" />
					</label>
					<input name="phone" type="text" value="{system_message/phone}" />
				</div>
				<div class="pure-control-group">
					<label for="field_email">
						<xsl:value-of select="php:function('lang', 'Email')" />
					</label>
					<input name="email" type="text" value="{system_message/email}" />
				</div>
				<div class="pure-control-group">
					<label for="field_time">
						<xsl:value-of select="php:function('lang', 'Created')" />
					</label>
					<input id="inputs" name="created" readonly="true" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="system_message/created"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<xsl:if test="not(system_message/id)">
				<input class="pure-button pure-button-primary" type="submit" value="{php:function('lang', 'Save')}"/>
			</xsl:if>
			<xsl:if test="system_message/id">
				<input class="pure-button pure-button-primary" type="submit" value="{php:function('lang', 'Save')}"/>
			</xsl:if>
			<a class="cancel pure-button pure-button-primary" href="{system_message/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</xsl:template>
