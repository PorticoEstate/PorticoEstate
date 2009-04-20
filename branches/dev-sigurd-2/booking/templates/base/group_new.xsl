<xsl:template match="data">
    <div id="content">

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
				<xsl:attribute name="value"><xsl:value-of select="lang/create"/></xsl:attribute>
			</input>
        </div>
    </form>
    </div>
</xsl:template>
