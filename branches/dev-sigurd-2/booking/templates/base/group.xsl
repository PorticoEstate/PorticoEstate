<xsl:template match="data">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="lang/organization" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
                    <xsl:value-of select="group/organization_name"/>
                </a>
            </li>
            <li><xsl:value-of select="lang/group" /></li>
            <li>
                <a href="">
                    <xsl:value-of select="group/name"/>
                </a>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt><xsl:value-of select="lang/organization" /></dt>
            <dd><xsl:value-of select="group/organization_name"/></dd>
            <dt><xsl:value-of select="lang/name" /></dt>
            <dd><xsl:value-of select="group/name"/></dd>
        </dl>

        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="group/edit_link"/></xsl:attribute>
            <xsl:value-of select="lang/edit" />
        </a>
    </div>
</xsl:template>
