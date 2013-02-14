<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="select_buildings_on_property">

  <form action="#">
    <input type="hidden" name="period_type" value="view_year" />
    <input type="hidden" name="year">
      <xsl:attribute name="value">
        <xsl:value-of select="current_year"/>
      </xsl:attribute>
    </input>
		
    <select id="choose_building_on_property" class="selectLocation">
      <option value="">Velg bygg</option>
      <xsl:for-each select="buildings_on_property">
        <option>
          <xsl:if test="id = //current_location/location_code">
            <xsl:attribute name="selected">selected</xsl:attribute>
          </xsl:if>
          <xsl:attribute name="value">
            <xsl:value-of select="id"/>
          </xsl:attribute>
          <xsl:value-of select="name" />
        </option>
      </xsl:for-each>
    </select>					
  </form>
				
</xsl:template>
