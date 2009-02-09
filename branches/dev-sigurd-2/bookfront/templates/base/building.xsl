<xsl:template match="data">
    <h3>Building <xsl:value-of select="building/name"/></h3>

    <dl>
        <dt>Homepage</dt>
        <dd><xsl:value-of select="building/homepage"/></dd>
    </dl>

    <h4>Resources</h4>
    <xsl:apply-templates select="datatable"/>

</xsl:template>
