<xsl:template match="data">
    <h3>Organization <xsl:value-of select="organization/name"/></h3>

    <dl>
        <dt>Homepage</dt>
        <dd><xsl:value-of select="organization/homepage"/></dd>
    </dl>

</xsl:template>
