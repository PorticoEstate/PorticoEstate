<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="check_list_status_manager" xmlns:php="http://php.net/xsl">

  <xsl:param name="location_code" />
  <xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
  
  <xsl:choose>
    <xsl:when test="status = 'CONTROL_REGISTERED'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.add_check_list</xsl:text>
          <xsl:text>&amp;deadline_ts=</xsl:text>
          <xsl:value-of select="info/deadline_date_ts"/>
          <xsl:text>&amp;control_id=</xsl:text>
          <xsl:value-of select="info/control_id"/>
          <xsl:text>&amp;type=</xsl:text>
          <xsl:value-of select="info/type"/>
          <xsl:choose>
            <xsl:when test="info/type = 'component'">
              <xsl:text>&amp;location_id=</xsl:text>
              <xsl:value-of select="info/location_id"/>
              <xsl:text>&amp;component_id=</xsl:text>
              <xsl:value-of select="info/component_id"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>&amp;location_code=</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:otherwise>
          </xsl:choose>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_PLANNED'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.add_check_list</xsl:text>
          <xsl:text>&amp;deadline_ts=</xsl:text>
          <xsl:value-of select="info/deadline_date_ts"/>
          <xsl:text>&amp;control_id=</xsl:text>
          <xsl:value-of select="info/control_id"/>
          <xsl:text>&amp;type=</xsl:text>
          <xsl:value-of select="info/type"/>
          <xsl:choose>
            <xsl:when test="info/type = 'component'">
              <xsl:text>&amp;location_id=</xsl:text>
              <xsl:value-of select="info/location_id"/>
              <xsl:text>&amp;component_id=</xsl:text>
              <xsl:value-of select="info/component_id"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>&amp;location_code=</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:otherwise>
          </xsl:choose>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_REGISTERED_WITH_CHECKLIST'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE_WITH_PLANNED_DATE'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE_WITH_CHECKLIST'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
            <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <span class="ext_info">
          <xsl:value-of select="info/id"/>
        </span>
        <img height="15" src="controller/images/status_icon_dark_green.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS'">
      <div class="info_box_wrp">
        <div id="info_box"></div>
        <a>
          <xsl:attribute name="href">
            <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
            <xsl:text>&amp;check_list_id=</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
              <xsl:value-of select="$session_url"/>
          </xsl:attribute>
          <span class="ext_info">
            <xsl:value-of select="info/id"/>
          </span>
          <img height="15" src="controller/images/status_icon_light_green.png" />
        </a>
      </div>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_DONE_WITH_ERRORS'">
      <div class="info_box_wrp">
        <div id="info_box"></div>
        <a class="view_info_box">
          <xsl:attribute name="href">
            <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
            <xsl:text>&amp;check_list_id=</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
              <xsl:value-of select="$session_url"/>
          </xsl:attribute>
          <span class="ext_info">
            <xsl:text>&amp;check_list_id=</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
            <xsl:text>&amp;phpgw_return_as=json</xsl:text>
          </span>
          <xsl:value-of select="info/num_open_cases"/>
        </a>
      </div>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_CANCELED'">
      <a>
        <xsl:attribute name="href">
          <xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>&amp;check_list_id=</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
            <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_black_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROLS_DONE_WITH_ERRORS'">
      <div class="info_box_wrp">
        <div id="info_box"></div>
        <a class="view_info_box">
          <xsl:attribute name="href">
            <xsl:choose>
              <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'">
                <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
                <xsl:text>&amp;control_id=</xsl:text>
                <xsl:value-of select="info/control_id"/>
              </xsl:when>
              <xsl:otherwise>
                <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
                <xsl:text>&amp;location_code=</xsl:text>
                <xsl:value-of select="info/location_code"/>
              </xsl:otherwise>
            </xsl:choose>
            <xsl:text>&amp;year=</xsl:text>
            <xsl:value-of select="//current_year"/>
            <xsl:text>&amp;month=</xsl:text>
            <xsl:number />
 		 <xsl:value-of select="$session_url"/>
          </xsl:attribute>
          <span class="ext_info">
            <xsl:text>&amp;check_list_id=</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
            <xsl:text>&amp;phpgw_return_as=json</xsl:text>
          </span>
          <xsl:value-of select="info/agg_open_errors"/>
        </a>
      </div>
    </xsl:when>
    <xsl:when test="status = 'CONTROLS_NOT_DONE'">
      <a>
        <xsl:attribute name="href">
          <xsl:choose>
            <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'">
              <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
              <xsl:text>&amp;control_id=</xsl:text>
              <xsl:value-of select="info/control_id"/>
            </xsl:when>
            <xsl:when test="info/view = 'VIEW_CONTROLS_FOR_LOCATION'">
              <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
              <xsl:text>&amp;location_code=</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:when>
          </xsl:choose>
          <xsl:text>&amp;year=</xsl:text>
          <xsl:value-of select="//current_year"/>
          <xsl:text>&amp;month=</xsl:text>
          <xsl:number />
          <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROLS_REGISTERED'">
      <a>
        <xsl:attribute name="href">
          <xsl:choose>
            <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'">
              <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
              <xsl:text>&amp;control_id=</xsl:text>
              <xsl:value-of select="info/control_id"/>
            </xsl:when>
            <xsl:when test="info/view = 'VIEW_CONTROLS_FOR_LOCATION'">
              <xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
              <xsl:text>&amp;location_code=</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:when>
          </xsl:choose>
          <xsl:text>&amp;month=</xsl:text>
          <xsl:value-of select="info/month"/>
          <xsl:text>&amp;year=</xsl:text>
          <xsl:value-of select="info/year"/>
 		 <xsl:value-of select="$session_url"/>
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      </a>
    </xsl:when>
    <xsl:otherwise>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>
