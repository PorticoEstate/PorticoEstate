<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div>
        <h1><xsl:value-of select="php:function('lang', 'edit_organization')" /></h1>
        <div id="details">
            <xsl:choose>
                <xsl:when test="message">
                    <div class="success">
                        <xsl:value-of select="message" />
                    </div>
                </xsl:when>
                <xsl:when test="error">
                    <div class="error">
                        <xsl:value-of select="error" />
                    </div>
                </xsl:when>
            </xsl:choose>
        </div>
    </div>
</xsl:template>