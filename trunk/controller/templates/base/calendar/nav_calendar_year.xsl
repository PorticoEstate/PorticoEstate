<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="nav_calendar_year" xmlns:php="http://php.net/xsl">

<xsl:param name="view" />

<div id="calNav">

		<xsl:variable name="url_argument_showPrev">
			<xsl:choose>
		 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
					<xsl:text>menuaction:controller.uicalendar.view_calendar_year_for_locations</xsl:text>
						<xsl:text>,control_id:</xsl:text>
						<xsl:value-of select="control/id" />
				</xsl:when>
				<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
					<xsl:text>menuaction:controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>,location_code:</xsl:text>
					<xsl:value-of select="//current_location/location_code"/>
			  </xsl:when>
			</xsl:choose>
			<xsl:text>,year:</xsl:text>
			<xsl:value-of select="current_year - 1"/>
		</xsl:variable>

	<a class="showPrev">
		<xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_showPrev)" />
		</xsl:attribute>
		<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
		<xsl:value-of select="current_year - 1"/>
	</a>
	<span class="current">
			<xsl:value-of select="current_year"/>
	</span>

		<xsl:variable name="url_argument_showNext">
			<xsl:choose>
		 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
					<xsl:text>menuaction:controller.uicalendar.view_calendar_year_for_locations</xsl:text>
						<xsl:text>,control_id:</xsl:text>
						<xsl:value-of select="control/id" />
				</xsl:when>
				<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
					<xsl:text>menuaction:controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>,location_code:</xsl:text>
					<xsl:value-of select="//current_location/location_code"/>
			  </xsl:when>
			</xsl:choose>
			<xsl:text>,year:</xsl:text>
			<xsl:value-of select="current_year + 1"/>
		</xsl:variable>

	<a class="showNext">
			<xsl:attribute name="href">
			  <xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_showNext)" />
		</xsl:attribute>
		<xsl:value-of select="current_year + 1"/>
		<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
	</a>
</div>
				
</xsl:template>
