<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
                    <xsl:value-of select="group/organization_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Group')" /></li>
            <li>
                <a href="">
                    <xsl:value-of select="group/name"/>
                </a>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
            <dd><xsl:value-of select="group/organization_name"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
            <dd><xsl:value-of select="group/name"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Contacts')" /></dt>
            <dd>
                <ul>
                    <li><xsl:value-of select="group/contact_primary/name" /></li>
                    <li>
                        <xsl:if test="group/contact_secondary/name">
                            <xsl:value-of select="group/contact_secondary/name" />
                        </xsl:if>
                    </li>
                </ul>
            </dd>

            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="group/description" disable-output-escaping="yes"/></dd>


        </dl>

        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="group/edit_link"/></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Edit')" />
        </a>
    </div>
</xsl:template>
