<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <h2><xsl:value-of select="organization/name" /></h2>
        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
            <dd><xsl:value-of select="organization/homepage"/></dd>
        </dl>
    </div>
</xsl:template>
