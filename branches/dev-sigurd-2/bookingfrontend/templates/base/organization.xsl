<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
     <h2><xsl:value-of select="organization/name"/></h2>
        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
            <dd>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="organization/homepage" /></xsl:attribute>
					<xsl:value-of select="organization/homepage" />
				</a>
			</dd>
            <dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><xsl:value-of select="organization/email"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
            <dd><xsl:value-of select="organization/phone"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="organization/description" disable-output-escaping="yes"/></dd>
        </dl>
    </div>
</xsl:template>
