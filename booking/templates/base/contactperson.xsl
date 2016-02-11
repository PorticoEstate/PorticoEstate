<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="person/contactpersons_link"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Contacts')" />
				</a>
			</li>
			<li>
				<xsl:value-of select="php:function('lang', 'Contact')" />
			</li>
			<li>
				<a href="">
					<xsl:value-of select="person/name"/>
				</a>
			</li>
		</ul>
		<xsl:call-template name="msgbox"/>

		<dl class="proplist">
			<dt>
				<xsl:value-of select="php:function('lang', 'Name')" />
			</dt>
			<dd>
				<xsl:value-of select="person/name"/>
			</dd>

			<dt>
				<xsl:value-of select="php:function('lang', 'Homepage')" />
			</dt>
			<dd>
				<xsl:value-of select="person/homepage"/>
			</dd>

			<dt>
				<xsl:value-of select="php:function('lang', 'Phone')" />
			</dt>
			<dd>
				<xsl:value-of select="person/phone"/>
			</dd>

			<dt>
				<xsl:value-of select="php:function('lang', 'Email')" />
			</dt>
			<dd>
				<xsl:value-of select="person/email"/>
			</dd>

			<dt>
				<xsl:value-of select="php:function('lang', 'Description')" />
			</dt>
			<dd>
				<xsl:value-of select="person/description" disable-output-escaping="yes"/>
			</dd>

		</dl>

		<a class="button">
			<xsl:attribute name="href">
				<xsl:value-of select="person/edit_link"/>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Edit')" />
		</a>
	</div>
</xsl:template>

