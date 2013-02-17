<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="select_buildings_on_property">
  <select id="choose-building-on-property" class="view-cases">
    <option value="">Velg bygg</option>
    <xsl:for-each select="buildings_on_property">
      <option>
        <xsl:if test="id = //building_location_code">
          <xsl:attribute name="selected">selected</xsl:attribute>
        </xsl:if>
        <xsl:attribute name="value">
          <xsl:value-of select="id"/>
        </xsl:attribute>
        <xsl:value-of select="name" />
      </option>
    </xsl:for-each>
  </select>					
</xsl:template>
