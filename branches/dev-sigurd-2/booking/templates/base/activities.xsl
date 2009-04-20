<xsl:template match="data">
    <div id="content">
        <dl class="proplist">
            <dt><xsl:value-of select="lang/name" /></dt>
            <dd><xsl:value-of select="activities/resource/name"/></dd>
        </dl>
        <dl class="proplist">
            <dt><xsl:value-of select="lang/description" /></dt>
            <dd><xsl:value-of select="activities/resource/description"/></dd>
        </dl>
        <dl class="proplist">
            <dd><a class="button">
            <xsl:attribute name="href"><xsl:value-of select="activities/resource/edit_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="lang/edit" />
        </a></dd>
        <dd><a class="button">
            <xsl:attribute name="href"><xsl:value-of select="activities/resource/cancel_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="lang/cancel" />
        </a></dd>
        </dl>
        
        
    </div>
</xsl:template>
