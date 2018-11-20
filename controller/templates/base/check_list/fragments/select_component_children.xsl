<xsl:template name="select_component_children">
  <form action="#">
    <input type="hidden" name="period_type" value="view_year" />
    <input type="hidden" name="year">
      <xsl:attribute name="value">
        <xsl:value-of select="current_year"/>
      </xsl:attribute>
    </input>

    <select id="choose-child-on-component" class="select-component">
      <option value="">Velg utstyr</option>
      <xsl:for-each select="component_children">
        <option>
          <xsl:if test="id = //current_child/id">
            <xsl:attribute name="selected">selected</xsl:attribute>
          </xsl:if>
          <xsl:attribute name="value">
            <xsl:value-of select="location_id"/>
			<xsl:text>_</xsl:text>
            <xsl:value-of select="id"/>
          </xsl:attribute>
          <xsl:value-of select="short_description" />
        </option>
      </xsl:for-each>
    </select>
  </form>
</xsl:template>
