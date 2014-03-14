<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="nav_calendar_month" xmlns:php="http://php.net/xsl">
<xsl:param name="view" />
<xsl:param name="location_code" />

<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
<div id="calNav">
	<xsl:choose>
		<xsl:when test="current_month_nr > 1">
			<a class="showPrev month">
				<xsl:attribute name="href">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="control/id" />
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="$location_code"/>
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;month=</xsl:text>
					<xsl:value-of select="current_month_nr - 1"/>
					<xsl:value-of select="$session_url"/>
				</xsl:attribute>
				<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
				<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr - 1"/> capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
			</a>
		</xsl:when>
		<xsl:otherwise>
			<a class="showPrev month">
				<xsl:attribute name="href">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="control/id" />
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="$location_code"/>
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year - 1"/>
					<xsl:text>&amp;month=12</xsl:text>
					<xsl:value-of select="$session_url"/>
				</xsl:attribute>
				<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
				<xsl:variable name="month_str">month 12 capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
			</a>
		</xsl:otherwise>
	</xsl:choose>
	
	<span class="current">
		<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr"/> capitalized</xsl:variable>
		<xsl:value-of select="php:function('lang', $month_str)" />
	</span>
	<xsl:choose>
		<xsl:when test="12 > current_month_nr">
			<a class="showNext">
				<xsl:attribute name="href">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="control/id" />
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="$location_code"/>
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;month=</xsl:text>
					<xsl:value-of select="current_month_nr + 1"/>
					<xsl:value-of select="$session_url"/>
				</xsl:attribute>
				<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr + 1"/> capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
				<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
			</a>
		</xsl:when>
		<xsl:otherwise>
			<a class="showNext">
				<xsl:attribute name="href">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="control/id" />
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="$location_code"/>
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year + 1"/>
					<xsl:text>&amp;month=1</xsl:text>
					<xsl:value-of select="$session_url"/>
				</xsl:attribute>
				<xsl:variable name="month_str">month 1 capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
				<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
			</a>
		</xsl:otherwise>
	</xsl:choose>
</div>
				
</xsl:template>
