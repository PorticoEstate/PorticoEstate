<xsl:template xmlns="http://www.w3.org/1999/XSL/Transform"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://php.net/xsl "
              xmlns:xsl="">
    <xsl:call-template name="jquery_phpgw_i18n"/>
    <div class="content">
        <p>This is a paragraph</p>
        <xsl:value-of select="arrangement/test"/>
    </div>
</xsl:template>