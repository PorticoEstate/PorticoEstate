<!-- $Id: view_calendar_month.xsl 9200 2012-04-21 20:05:34Z vator $ -->
<xsl:template name="nav_calendar_month" xmlns:php="http://php.net/xsl">

<xsl:param name="view" />

<div id="calNav">
	<xsl:choose>
		<xsl:when test="current_month_nr > 1">
			<xsl:variable name="url_argument_1">
				<xsl:choose>
				 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
							<xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
								<xsl:text>,control_id:</xsl:text>
								<xsl:value-of select="control/id" />
						</xsl:when>
						<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
							<xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
							<xsl:text>,location_code:</xsl:text>
							<xsl:value-of select="//current_location/location_code"/>
					  </xsl:when>
				  </xsl:choose>
				<xsl:text>,year:</xsl:text>
				<xsl:value-of select="current_year"/>
				<xsl:text>,month:</xsl:text>
				<xsl:value-of select="current_month_nr - 1"/>
			</xsl:variable>

			<a class="showPrev month">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_1)" />
				</xsl:attribute>
				<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
				<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr - 1"/> capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
			</a>
		</xsl:when>
		<xsl:otherwise>

				<xsl:variable name="url_argument_2">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>,control_id:</xsl:text>
									<xsl:value-of select="control/id" />
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>,location_code:</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>,year:</xsl:text>
					<xsl:value-of select="current_year - 1"/>
					<xsl:text>,month:12</xsl:text>
				</xsl:variable>

			<a class="showPrev month">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_2)" />
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
				<xsl:variable name="url_argument_3">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>,control_id:</xsl:text>
									<xsl:value-of select="control/id" />
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>,location_code:</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>,year:</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>,month:</xsl:text>
					<xsl:value-of select="current_month_nr + 1"/>
				</xsl:variable>

			<a class="showNext">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_3)" />
				</xsl:attribute>
				<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr + 1"/> capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
				<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
			</a>
		</xsl:when>
		<xsl:otherwise>
				<xsl:variable name="url_argument_4">
					<xsl:choose>
					 		<xsl:when test="$view = 'VIEW_LOCATIONS_FOR_CONTROL'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_month_for_locations</xsl:text>
									<xsl:text>,control_id:</xsl:text>
									<xsl:value-of select="control/id" />
							</xsl:when>
							<xsl:when test="$view = 'VIEW_CONTROLS_FOR_LOCATION'">
								<xsl:text>menuaction:controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>,location_code:</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
						  </xsl:when>
					  </xsl:choose>
					<xsl:text>,year:</xsl:text>
					<xsl:value-of select="current_year + 1"/>
					<xsl:text>,month:1</xsl:text>
				</xsl:variable>

			<a class="showNext">
				<xsl:attribute name="href">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $url_argument_4)" />
				</xsl:attribute>
				<xsl:variable name="month_str">month 1 capitalized</xsl:variable>
				<xsl:value-of select="php:function('lang', $month_str)" />
				<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
			</a>
		</xsl:otherwise>
	</xsl:choose>
</div>
				
</xsl:template>
