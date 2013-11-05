<!-- $Id: view_calendar_year.xsl 9206 2012-04-23 06:21:38Z vator $ -->
<xsl:template match="data"  xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr"/> capitalized</xsl:variable>
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_code" /></xsl:variable>

<div id="main_content">
	<div id="control_plan">
		<div class="top">
			<h1>Kontrollplan for <xsl:value-of select="control/title"/></h1>
			<h3>Oversikt for <span class="month"><xsl:value-of select="php:function('lang', $month_str)" /></span><span class="year"><xsl:value-of select="current_year"/></span></h3>
				<div id="choose-my-location" class="select-box">
					<label>Velg en lokasjon</label>
					  <form action="#">
						<input type="hidden" name="period_type" value="view_month_for_locations" />
						<input type="hidden" name="year">
						  <xsl:attribute name="value">
							<xsl:value-of select="current_year"/>
						  </xsl:attribute>
						</input>
						<input type="hidden" name="control_id">
						  <xsl:attribute name="value">
							<xsl:value-of select="//control/id"/>
						  </xsl:attribute>
						</input>
						<select id="choose-my-location" class="select-location">
						  <option>Velg bygg</option>
						  <xsl:for-each select="locations_list">
							<option>
							  <xsl:if test="selected = 1">
								<xsl:attribute name="selected">selected</xsl:attribute>
							  </xsl:if>
							  <xsl:attribute name="value">
								<xsl:value-of select="id"/>
							  </xsl:attribute>
								<xsl:value-of select="id"/>
								<xsl:text> - </xsl:text>
								<xsl:value-of disable-output-escaping="yes" select="name"/>
							</option>
						  </xsl:for-each>
						</select>					
					  </form>
				</div>

		</div>
		<div class="middle">
			<!-- =====================  ICON COLOR MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
					
			<!-- =====================  VIEW OVERVIEW FOR YEAR  ================= -->
			<div id="calNav">
				<a class="showPrev month">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_year_for_locations</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="control/id"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="$location_code"/>
						<xsl:value-of select="$session_url"/>
					</xsl:attribute>
					Årsoversikt
				</a>
			</div>
			
			<!-- =====================  CALENDAR NAVIGATION  ================= -->
			<xsl:call-template name="nav_calendar_month">
    		<xsl:with-param name="view">VIEW_LOCATIONS_FOR_CONTROL</xsl:with-param>
    		<xsl:with-param name="location_code"><xsl:value-of select="$location_code"/></xsl:with-param>
  		</xsl:call-template>
		</div>
		
		<!-- =====================  CALENDAR ================= -->
		<div id="cal_wrp">
		<h2>Bygg/Eiendom</h2>
			<table id="calendar" class="month">
				<tr>
					<th class="title">
						<span class="location-code">Lokasjonskode</span>
					</th>
					<th class="title">
						<span class="location-name">Lokasjonsnavn</span>
					</th>
					<xsl:for-each select="heading_array">
						<th>
							<xsl:value-of select="."/>
						</th>
					</xsl:for-each>
				</tr>
			
			<xsl:choose>
				<xsl:when test="locations_with_calendar_array/child::node()">
				
			  	<xsl:for-each select="locations_with_calendar_array">
			  		<tr>				
						<xsl:choose>
					        <xsl:when test="(position() mod 2) != 1">
					            <xsl:attribute name="class">odd</xsl:attribute>
					        </xsl:when>
					        <xsl:otherwise>
					            <xsl:attribute name="class">even</xsl:attribute>
					        </xsl:otherwise>
					    </xsl:choose>
					    
					    <td>
							<xsl:value-of select="location/location_code"/>
						</td>
						<td class="location-name">
							<xsl:value-of select="location/loc_name"/>
						</td>

						<xsl:for-each select="calendar_array">
							<td>
								<xsl:call-template name="check_list_status_manager" >
									<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
								</xsl:call-template>
							</td>
						</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3"><xsl:value-of select="php:function('lang', 'error_msg_no_controls_in_period')" /></td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	
	<h2 class="components">Komponenter</h2>
			<table id="calendar" class="month">
				<tr>
					<th class="title">
						<span class="location-code">Komponent</span>
					</th>
					<th class="title">
						<span class="location-name">Komponenttype</span>
					</th>
					<xsl:for-each select="heading_array">
						<th>
							<xsl:value-of select="."/>
						</th>
					</xsl:for-each>
				</tr>
				
				<xsl:choose>
				<xsl:when test="components_with_calendar_array/child::node()">
				
			  	<xsl:for-each select="components_with_calendar_array">
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
							<xsl:value-of select="component/xml_short_desc"/>
						</td>
						<td class="location-name">
							<xsl:value-of select="component/type_str"/>
						</td>
							
						<xsl:for-each select="calendar_array">
							<td>
								<xsl:call-template name="check_list_status_manager" >
									<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
								</xsl:call-template>
							</td>
						</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3"><xsl:value-of select="php:function('lang', 'error_msg_no_controls_for_component')" /></td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	
	</div>
</div>
</div>
</xsl:template>
