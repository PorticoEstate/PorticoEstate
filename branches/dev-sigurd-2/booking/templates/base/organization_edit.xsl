<xsl:template match="data">
    <div id="content">
        
    <h3><xsl:value-of select="lang/title" /></h3>
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="lang/name" /></label></dt>
            <dd>
                <input id="inputs" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_homepage"><xsl:value-of select="lang/homepage" /></label></dt>
            <dd>
                <input id="inputs" name="homepage" type="text">
                       <xsl:attribute name="value"><xsl:value-of select="organization/homepage"/></xsl:attribute>
                </input>
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


