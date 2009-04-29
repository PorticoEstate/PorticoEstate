<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="organization/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a href="">
                    <xsl:value-of select="organization/name"/>
                </a>
            </li>
        </ul>
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
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="organization/edit_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Edit')" />
        </a>
    </div>
</xsl:template>
