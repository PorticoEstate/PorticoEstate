<xsl:template name="contactpersonfields" xmlns:php="http://php.net/xsl">
	<dl class="form">
		<dt>
			<label for="field_name">
				<xsl:value-of select="php:function('lang', 'Name')" />
			</label>
		</dt>
		<dd>
			<input id="contact-field-name" name="name" type="text">
				<xsl:attribute name="value">
					<xsl:value-of select="person/name"/>
				</xsl:attribute>
			</input>
		</dd>

		<dt>
			<label for="contact-field-ssn">
				<xsl:value-of select="php:function('lang', 'Social Security Number')" />
			</label>
		</dt>
		<dd>
			<input id="contact-field-ssn" name="ssn" type="text">
				<xsl:attribute name="value">
					<xsl:value-of select="person/ssn"/>
				</xsl:attribute>
			</input>
		</dd>

		<dt>
			<label for="contact-field-homepage">
				<xsl:value-of select="php:function('lang', 'Homepage')" />
			</label>
		</dt>
		<dd>
			<input id="contact-field-homepage" name="homepage" type="text">
				<xsl:attribute name="value">
					<xsl:value-of select="person/homepage"/>
				</xsl:attribute>
			</input>
		</dd>

		<dt>
			<label for="contact-field-phone">
				<xsl:value-of select="php:function('lang', 'Phone')" />
			</label>
		</dt>
		<dd>
			<input id="contact-field-phone" name="phone" type="text">
				<xsl:attribute name="value">
					<xsl:value-of select="person/phone"/>
				</xsl:attribute>
			</input>
		</dd>

		<dt>
			<label for="contact-field-email">
				<xsl:value-of select="php:function('lang', 'Email')" />
			</label>
		</dt>
		<dd>
			<input id="contact-field-email" name="email" type="text">
				<xsl:attribute name="value">
					<xsl:value-of select="person/email"/>
				</xsl:attribute>
			</input>
		</dd>

		<dt>
			<label for="contact-field-description">
				<xsl:value-of select="php:function('lang', 'Description')" />
			</label>
		</dt>
		<dd class="yui-skin-sam">
			<textarea id="contact-field-description" name="description" type="text">
				<xsl:value-of select="person/description"/>
			</textarea>
		</dd>

	</dl>
</xsl:template>

