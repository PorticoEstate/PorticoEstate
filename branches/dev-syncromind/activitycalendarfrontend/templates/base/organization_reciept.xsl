<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <h1><xsl:value-of select="php:function('lang', 'edit_organization')" /></h1>
        <div id="details">
            <xsl:if test="message != ''">
                <div class="success">
                    <xsl:value-of select="message" disable-output-escaping="yes" />
                </div>
            </xsl:if>
            <xsl:if test="error != ''">
                <div class="error">
                    <xsl:value-of select="error" disable-output-escaping="yes" />
                </div>
            </xsl:if>
        </div>
    </div>
</xsl:template>