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

		<form action="" method="POST">
			<xsl:call-template name="contactpersonfields">
				<xsl:with-param name="person" select="person"/>
			</xsl:call-template>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Save')"/>
					</xsl:attribute>
				</input>
				<a class="cancel">
					<xsl:attribute name="href">
						<xsl:value-of select="person/cancel_link"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>
</xsl:template>

