  <xsl:template name="nav_control_plan" xmlns:php="http://php.net/xsl">
  <xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

  <a>
    <xsl:attribute name="href">
      <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
      <xsl:text>&amp;year=</xsl:text>
      <xsl:value-of select="current_year"/>
      <xsl:text>&amp;location_code=</xsl:text>
      <xsl:choose>
        <xsl:when test="type = 'component'">
          <xsl:value-of select="building_location_code"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="location_array/location_code"/>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:value-of select="$session_url"/>
    </xsl:attribute>
    Vis kontrolplan for år
  </a>
  <a class="last">
    <xsl:attribute name="href">
      <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
      <xsl:text>&amp;year=</xsl:text>
      <xsl:value-of select="current_year"/>
      <xsl:text>&amp;month=</xsl:text>
      <xsl:value-of select="current_month_nr"/>
      <xsl:text>&amp;location_code=</xsl:text>
      <xsl:choose>
        <xsl:when test="type = 'component'">
          <xsl:value-of select="building_location_code"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="location_array/location_code"/>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:value-of select="$session_url"/>
    </xsl:attribute>
    Vis kontrolplan for måned
  </a>		
  </xsl:template>