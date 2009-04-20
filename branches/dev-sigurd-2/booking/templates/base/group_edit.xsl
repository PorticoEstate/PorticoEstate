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

    <form action="" method="POST">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
            <dd>
                <input name="name" type="text">
                     <xsl:attribute name="value"><xsl:value-of select="group/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_organization"><xsl:value-of select="lang/organization" /></label></dt>
            <dd>
                <div class="autocomplete">
                <input id="field_organization_id" name="organization_id" type="hidden">
                     <xsl:attribute name="value"><xsl:value-of select="group/organization_id"/></xsl:attribute>
                </input>
                <input name="organization_name" type="text" id="field_organization_name" >
                   <xsl:attribute name="value"><xsl:value-of select="group/organization_name"/></xsl:attribute>
                </input>
                <div id="organization_container"/>
            </div>
            </dd>
        </dl>
        <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="lang/save"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="lang/cancel" />
            </a>
        </div>
    </form>
    </div>
</xsl:template>
