<xsl:template match="data">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="organization/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="lang/organization" />
                </a>
            </li>
            <li>
                <a href="">
                    <xsl:value-of select="organization/name"/>
                </a>
            </li>
        </ul>
        <dl class="proplist">
            <dt><xsl:value-of select="lang/homepage" /></dt>
            <dd><xsl:value-of select="organization/homepage"/></dd>
        </dl>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="organization/edit_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="lang/edit" />
        </a>
    </div>
</xsl:template>
