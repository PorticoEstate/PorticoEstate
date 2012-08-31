<!-- $Id$ -->
<xsl:template name="yui_booking_i18n" xmlns:php="http://php.net/xsl">
  <xsl:if test="yui_booking_i18n">
    <script type="text/javascript">
      YAHOO.portico.i18n = {};
      <xsl:for-each select="yui_booking_i18n/*">
        YAHOO.portico.i18n.
        <xsl:value-of select="local-name()"/> = function(cfg)
        {
        cfg = cfg || {};
        <xsl:for-each select="./*">
          cfg["
          <xsl:value-of select="local-name()"/>"] = 
          <xsl:value-of disable-output-escaping="yes" select="."/>;
        </xsl:for-each>
        };
      </xsl:for-each>
    </script>
  </xsl:if>
</xsl:template>
