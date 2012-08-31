<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
  <div class="yui-navset yui-navset-top" id="control_component_tabview">
    <xsl:choose>
      <xsl:when test="view = 'view_component_for_control'">
        <div class="identifier-header">
          <h1>
            <xsl:value-of select="php:function('lang', 'component_for_control')"/>
          </h1>
        </div>
        <!-- Prints tabs array -->
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
			 
        <xsl:call-template name="view_component_for_control" />
      </xsl:when>
      <xsl:when test="view = 'add_component_to_control'">
        <div class="identifier-header">
          <h1>
            <xsl:value-of select="php:function('lang', 'Add_component_for_control')"/>
          </h1>
        </div>
        <!-- Prints tabs array -->
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <xsl:call-template name="add_component_to_control" />
      </xsl:when>
    </xsl:choose>
  </div>
	
</xsl:template>
