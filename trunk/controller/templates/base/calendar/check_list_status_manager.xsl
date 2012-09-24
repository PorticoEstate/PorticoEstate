<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="check_list_status_manager" xmlns:php="http://php.net/xsl">
 
  <xsl:param name="location_code" />
 
  <xsl:choose>
    <xsl:when test="status = 'CONTROL_REGISTERED'">
		<xsl:variable name="url_argument_registered">
          <xsl:text>menuaction:controller.uicheck_list.add_check_list</xsl:text>
          <xsl:text>,deadline_ts:</xsl:text>
          <xsl:value-of select="info/deadline_date_ts"/>
          <xsl:text>,control_id:</xsl:text>
          <xsl:value-of select="info/control_id"/>
          <xsl:text>,type:</xsl:text>
          <xsl:value-of select="info/type"/>
          <xsl:choose>
            <xsl:when test="info/type = 'component'">
              <xsl:text>,location_id:</xsl:text>
              <xsl:value-of select="info/location_id"/>
              <xsl:text>,component_id:</xsl:text>
              <xsl:value-of select="info/component_id"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>,location_code:</xsl:text>
              <xsl:value-of select="info/location_code"/>  
            </xsl:otherwise>
          </xsl:choose>
		</xsl:variable>
      <a>
        <xsl:attribute name="href">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_registered)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_PLANNED'">
        <xsl:variable name="url_argument_planned">
          <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>,check_list_id:</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
        </xsl:variable>
      <a>
        <xsl:attribute name="href">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_planned)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE'">
        <xsl:variable name="url_argument_not_done">
          <xsl:text>menuaction:controller.uicheck_list.add_check_list</xsl:text>
          <xsl:text>,deadline_ts:</xsl:text>
          <xsl:value-of select="info/deadline_date_ts"/>
          <xsl:text>,control_id:</xsl:text>
          <xsl:value-of select="info/control_id"/>
          <xsl:text>,type:</xsl:text>
          <xsl:value-of select="info/type"/>
          <xsl:choose>
            <xsl:when test="info/type = 'component'">
              <xsl:text>,location_id:</xsl:text>
              <xsl:value-of select="info/location_id"/>
              <xsl:text>,component_id:</xsl:text>
              <xsl:value-of select="info/component_id"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>,location_code:</xsl:text>
              <xsl:value-of select="info/location_code"/>  
            </xsl:otherwise>
          </xsl:choose>
        </xsl:variable>
      <a>
        <xsl:attribute name="href">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_not_done)" />
		</xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE_WITH_CHECKLIST'">

        <xsl:variable name="url_argument_not_done_with_checklist">
          <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>,check_list_id:</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
        </xsl:variable>
      <a>
        <xsl:attribute name="href">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_not_done_with_checklist)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_NOT_DONE_WITH_PLANNED_DATE'">
         <xsl:variable name="url_argument_not_done_with_planned_date">
          <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>,check_list_id:</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
        </xsl:variable>
       <a>
        <xsl:attribute name="href">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_not_done_with_planned_date)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS'">
        <xsl:variable name="url_argument_done_in_time_without_errors">
          <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
          <xsl:text>,check_list_id:</xsl:text>
          <xsl:value-of select="info/check_list_id"/>
        </xsl:variable>
      <a>
        <xsl:attribute name="href">
		  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_done_in_time_without_errors)" />
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
          <xsl:variable name="url_argument_done_over_time_without_errors">
            <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
            <xsl:text>,check_list_id:</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
          </xsl:variable>
        <a>
          <xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_done_over_time_without_errors)" />
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
           <xsl:variable name="url_argument_done_with_errors">
            <xsl:text>menuaction:controller.uicheck_list.edit_check_list</xsl:text>
            <xsl:text>,check_list_id:</xsl:text>
            <xsl:value-of select="info/check_list_id"/>
          </xsl:variable>
        <a class="view_info_box">
          <xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_done_with_errors)" />
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
      <img height="15" src="controller/images/status_icon_red_cross.png" />
    </xsl:when>
    <xsl:when test="status = 'CONTROLS_DONE_WITH_ERRORS'">
      <div class="info_box_wrp">
        <div id="info_box"></div>
          <xsl:variable name="url_argument_controls_done_with_errors">
            <xsl:choose> 
              <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'"> 
                <xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text> 
                <xsl:text>,control_id:</xsl:text> 
                <xsl:value-of select="info/control_id"/> 
              </xsl:when> 
              <xsl:otherwise> 
                <xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text> 
                <xsl:text>,location_code:</xsl:text> 
                <xsl:value-of select="info/location_code"/> 
              </xsl:otherwise> 
            </xsl:choose> 
            <xsl:text>,year:</xsl:text> 
            <xsl:value-of select="//current_year"/> 
            <xsl:text>,month;</xsl:text> 
            <xsl:number /> 
           </xsl:variable>
        <a class="view_info_box">
          <xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_controls_done_with_errors)" />
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
        <xsl:variable name="url_argument_controls_not_done">
          <xsl:choose>
            <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'">
              <xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
              <xsl:text>,control_id:</xsl:text>
              <xsl:value-of select="info/control_id"/>
            </xsl:when>
            <xsl:when test="info/view = 'VIEW_CONTROLS_FOR_LOCATION'">
              <xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
              <xsl:text>,location_code:</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:when>
          </xsl:choose>
          <xsl:text>,year:</xsl:text>
          <xsl:value-of select="//current_year"/>
          <xsl:text>,month:</xsl:text>
          <xsl:number />
        </xsl:variable>
      <a>
        <xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_controls_not_done)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_red_cross.png" />
      </a>
    </xsl:when>
    <xsl:when test="status = 'CONTROLS_REGISTERED'">
        <xsl:variable name="url_argument_controls_registered">
          <xsl:choose>
            <xsl:when test="info/view = 'VIEW_LOCATIONS_FOR_CONTROL'">
              <xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
              <xsl:text>,control_id:</xsl:text>
              <xsl:value-of select="info/control_id"/>
            </xsl:when>
            <xsl:when test="info/view = 'VIEW_CONTROLS_FOR_LOCATION'">
              <xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
              <xsl:text>location_code:</xsl:text>
              <xsl:value-of select="info/location_code"/>
            </xsl:when> 
          </xsl:choose>
          <xsl:text>,month:</xsl:text>
          <xsl:value-of select="info/month"/>
          <xsl:text>,year:</xsl:text>
          <xsl:value-of select="info/year"/>
        </xsl:variable>

      <a>
        <xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_controls_registered)" />
        </xsl:attribute>
        <img height="15" src="controller/images/status_icon_yellow_ring.png" />
      </a>
    </xsl:when>
    <xsl:otherwise>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>
