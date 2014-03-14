<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="nav_calendar_year" xmlns:php="http://php.net/xsl">
<xsl:param name="view" />
<xsl:param name="location_code" />

<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
<div id="calNav">
	<a class="showPrev">
		<xsl:attribute name="href">
			<xsl:choose>
		 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_year_for_locations</xsl:text>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="control/id" />
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="$location_code"/>
				</xsl:when>
				<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="//current_location/location_code"/>
			  </xsl:when>
			</xsl:choose>
			<xsl:text>&amp;year=</xsl:text>
			<xsl:value-of select="current_year - 1"/>
			<xsl:value-of select="$session_url"/>
		</xsl:attribute>
		<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
		<xsl:value-of select="current_year - 1"/>
	</a>
	<span class="current">
			<xsl:value-of select="current_year"/>
	</span>
	<a class="showNext">
			<xsl:attribute name="href">
			<xsl:choose>
		 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_year_for_locations</xsl:text>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="control/id" />
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="$location_code"/>
				</xsl:when>
				<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="//current_location/location_code"/>
			  </xsl:when>
			</xsl:choose>
			<xsl:text>&amp;year=</xsl:text>
			<xsl:value-of select="current_year + 1"/>
			<xsl:value-of select="$session_url"/>
		</xsl:attribute>
		<xsl:value-of select="current_year + 1"/>
		<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
	</a>
</div>
				
</xsl:template>
