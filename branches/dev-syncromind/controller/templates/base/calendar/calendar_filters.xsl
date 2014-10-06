<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="calendar_filters" xmlns:php="http://php.net/xsl">

  <xsl:param name="view_period" />
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

  <form id="cal-filters" class="select-box" method="post">
    <xsl:choose>
      <xsl:when test="$view_period = 'month'">
        <xsl:attribute name="action">
	        <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
			<xsl:value-of select="$session_url"/>
        </xsl:attribute>
      </xsl:when>
      <xsl:otherwise>
        <xsl:attribute name="action">
	        <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
			<xsl:value-of select="$session_url"/>
        </xsl:attribute>
      </xsl:otherwise>	
    </xsl:choose>

    <input type="hidden" name="year">
      <xsl:attribute name="value">
        <xsl:value-of select="current_year"/>
      </xsl:attribute>
    </input>
    <xsl:if test="$view_period = 'month'">
      <input type="hidden" name="month">
        <xsl:attribute name="value">
          <xsl:value-of select="current_month_nr"/>
        </xsl:attribute>
      </input>
    </xsl:if>
    <input type="hidden" name="location_code">
      <xsl:attribute name="value">
        <xsl:value-of select="current_location/location_code"/>
      </xsl:attribute>
    </input>
    <input type="hidden" name="repeat_type">
      <xsl:attribute name="value">
        <xsl:value-of select="current_repeat_type"/>
      </xsl:attribute>
    </input>
    <input type="hidden" name="role">
      <xsl:attribute name="value">
        <xsl:value-of select="current_role"/>
      </xsl:attribute>
    </input>
	
    <div class="filter first">
      <label>Filtrer på rolle</label>
      <select id="filter-role">
        <xsl:for-each select="roles_array">
          <xsl:variable name="role_id">
            <xsl:value-of select="id"/>
          </xsl:variable>
          <xsl:choose>
            <xsl:when test="$role_id = //current_role">
              <option value="{$role_id}" selected="selected">
                <xsl:value-of disable-output-escaping="yes" select="name"/>
              </option>
            </xsl:when>
            <xsl:otherwise>
              <option value="{$role_id}">
                <xsl:value-of disable-output-escaping="yes" select="name"/>
              </option>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>
      </select>
    </div>
    <div class="filter">
      <label>Filtrer på frekvenstype</label>
      <select id="filter-repeat_type" name="repeat_type">
        <option value="">Velg frekvenstype</option>
        <xsl:for-each select="repeat_type_array">
          <xsl:choose>
            <xsl:when test="id = //current_repeat_type">
              <option value="{id}" selected="selected">
                <xsl:value-of disable-output-escaping="yes" select="value"/>
              </option>
            </xsl:when>
            <xsl:otherwise>
              <option value="{id}">
                <xsl:value-of disable-output-escaping="yes" select="value"/>
              </option>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>
      </select>
    </div>
  </form>				
</xsl:template>
