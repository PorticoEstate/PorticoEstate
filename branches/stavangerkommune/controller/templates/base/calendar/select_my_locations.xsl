<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="select_my_locations" xmlns:php="http://php.net/xsl">

  <form action="#">
    <input type="hidden" name="period_type" value="view_year" />
    <input type="hidden" name="year">
      <xsl:attribute name="value">
        <xsl:value-of select="current_year"/>
      </xsl:attribute>
    </input>
				
    <select id="choose-my-location" class="select-location">
      <option>Velg bygg</option>
      <xsl:for-each select="my_locations">
        <option>
          <xsl:if test="location_code = current_location/location_code">
            <xsl:attribute name="selected">selected</xsl:attribute>
          </xsl:if>
          <xsl:attribute name="value">
            <xsl:value-of disable-output-escaping="yes" select="location_code"/>
          </xsl:attribute>
          <xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
        </option>
      </xsl:for-each>
    </select>					
  </form>
</xsl:template>
