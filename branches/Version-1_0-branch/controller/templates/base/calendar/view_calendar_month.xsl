<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	<div id="control_plan">
		<div class="top">
			<h1>Kontrollplan for bygg/eiendom: <xsl:value-of select="current_location/loc1_name"/></h1>
			<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr"/> capitalized</xsl:variable>
			<h3>Kalenderoversikt for <xsl:value-of select="php:function('lang', $month_str)" /><span class="year"><xsl:value-of select="current_year"/></span></h3>
		
			<!-- =====================  SELECT MY LOCATIONS  ================= -->
			<xsl:call-template name="select_my_locations" />
		</div>
		
		<div class="middle">
			
			<!-- =====================  COLOR ICON MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
			
			<a id="showYear">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="current_location/location_code"/>
				</xsl:attribute>
				<img height="20" src="controller/images/left_arrow_simple_light_blue.png" />Årsoversikt
			</a>
			
			<!-- =====================  CALENDAR NAVIGATION  ================= -->
			<div id="calNav">
				<xsl:choose>
					<xsl:when test="current_month_nr > 1">
						<a class="showPrev month">
							<xsl:attribute name="href">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;year=</xsl:text>
								<xsl:value-of select="current_year"/>
								<xsl:text>&amp;month=</xsl:text>
								<xsl:value-of select="current_month_nr - 1"/>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
							</xsl:attribute>
							<img height="17" src="controller/images/left_arrow_simple_light_blue.png" />
							<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr - 1"/> capitalized</xsl:variable>
							<xsl:value-of select="php:function('lang', $month_str)" />
						</a>
					</xsl:when>
					<xsl:otherwise>
						<a class="showPrev month">
							<xsl:attribute name="href">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;year=</xsl:text>
								<xsl:value-of select="current_year - 1"/>
								<xsl:text>&amp;month=12</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
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
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;year=</xsl:text>
								<xsl:value-of select="current_year"/>
								<xsl:text>&amp;month=</xsl:text>
								<xsl:value-of select="current_month_nr + 1"/>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
							</xsl:attribute>
							<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr + 1"/> capitalized</xsl:variable>
							<xsl:value-of select="php:function('lang', $month_str)" />
							<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
						</a>
					</xsl:when>
					<xsl:otherwise>
						<a class="showNext">
							<xsl:attribute name="href">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
								<xsl:text>&amp;year=</xsl:text>
								<xsl:value-of select="current_year + 1"/>
								<xsl:text>&amp;month=1</xsl:text>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="//current_location/location_code"/>
							</xsl:attribute>
							<xsl:variable name="month_str">month 1 capitalized</xsl:variable>
							<xsl:value-of select="php:function('lang', $month_str)" />
							<img height="17" src="controller/images/right_arrow_simple_light_blue.png" />
						</a>
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<!-- 
 				<select id="loc_1" class="choose_loc">
					<xsl:for-each select="property_array">
						<xsl:variable name="loc_code"><xsl:value-of select="location_code"/></xsl:variable>
						<xsl:choose>
							<xsl:when test="location_code = $view_location_code">
								<option value="{$loc_code}" selected="selected">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:when>
							<xsl:otherwise>
								<option value="{$loc_code}">
									<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
								</option>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</select>				
			 -->
		</div>
		
		
		<div id="cal_wrp">
			<table id="calendar" class="month">
				<tr class="heading">
					<th class="title"><span>Tittel</span></th>
					<th class="assigned"><span>Tildelt</span></th>
					<th class="frequency"><span>Frekvens</span></th>
					<xsl:for-each select="heading_array">
						<th><span><xsl:value-of select="."/></span></th>
					</xsl:for-each>
				</tr>
				<xsl:choose>	
					<xsl:when test="controls_calendar_array/child::node()">
			  	<xsl:for-each select="controls_calendar_array">

					<tr>				
					<xsl:choose>
				        <xsl:when test="(position() mod 2) != 1">
				            <xsl:attribute name="class">odd</xsl:attribute>
				        </xsl:when>
				        <xsl:otherwise>
				            <xsl:attribute name="class">even</xsl:attribute>
				        </xsl:otherwise>
				    </xsl:choose>
					
						<td class="title">
			      			<span><xsl:value-of select="control/title"/></span>
						</td>
						<td class="assigned">
			      			<span><xsl:value-of select="control/responsibility_name"/></span>
						</td>
						<td class="frequency">
			      			<span>
				      			<xsl:choose>
				      				<xsl:when test="control/repeat_interval = 1">
				      					<span class="pre">Hver</span>
				      				</xsl:when>
				      				<xsl:when test="control/repeat_interval = 2">
				      					<span class="pre">Annenhver</span>
				      				</xsl:when>
				      				<xsl:when test="control/repeat_interval > 2">
				      					<span class="pre">Hver</span><span><xsl:value-of select="control/repeat_interval"/>.</span>
				      				</xsl:when>
				      			</xsl:choose>
				      			
				      			<span class="val"><xsl:value-of select="control/repeat_type_label"/></span>
			      			</span>
						</td>
				
				<xsl:for-each select="calendar_array">
					
					<xsl:call-template name="check_list_status_checker" >
						<xsl:with-param name="location_code"><xsl:value-of select="//current_location/location_code"/></xsl:with-param>
					</xsl:call-template>
					
				</xsl:for-each>
				</tr>
				</xsl:for-each>
				
					</xsl:when>
					<xsl:otherwise>
						<tr class="cal_info_msg"><td colspan="3">Ingen sjekklister for bygg i angitt periode</td></tr>
					</xsl:otherwise>
				</xsl:choose>
			
			</table>
		</div>
	</div>
</div>
</xsl:template>
