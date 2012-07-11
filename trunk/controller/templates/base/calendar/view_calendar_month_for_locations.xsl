<!-- $Id: view_calendar_year.xsl 9206 2012-04-23 06:21:38Z vator $ -->
<xsl:template match="data"  xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	<div id="control_plan">
		<div class="top">
			<h1>Kontrollplan for <xsl:value-of select="control/title"/></h1>
			<h3>Periode: <xsl:value-of select="current_year"/></h3>
			
			<form action="#">
				<input type="hidden" name="period_type" value="view_year" />
				<input type="hidden" name="year">
			      <xsl:attribute name="value">
			      	<xsl:value-of select="year"/>
			      </xsl:attribute>
				</input>

				<select id="choose_my_location">
					<xsl:for-each select="my_locations">
						<xsl:variable name="loc_code"><xsl:value-of select="location_code"/></xsl:variable>
						<option value="{$loc_code}">
							<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
						</option>
					</xsl:for-each>
				</select>					
			</form>
		</div>
		<div class="middle">
			<!-- =====================  ICON COLOR MAP  ================= -->
			<xsl:call-template name="icon_color_map" />
					
					
			<!-- =====================  CALENDAR NAVIGATION  ================= -->
			<div id="calNav">
				<a class="showPrev month">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_month_for_locations</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;month=</xsl:text>
						<xsl:value-of select="current_month"/>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="control/id"/>
					</xsl:attribute>
					Årsoversikt
				</a>
			</div>
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
							<xsl:value-of select="location/loc1_name"/>
						</td>

						<xsl:for-each select="calendar_array">
							<xsl:call-template name="check_list_status_checker" >
								<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
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
							<xsl:call-template name="check_list_status_checker" >
								<xsl:with-param name="location_code"><xsl:value-of select="//location"/></xsl:with-param>
							</xsl:call-template>
						</xsl:for-each>
					</tr>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<tr class="cal_info_msg"><td colspan="3">Ingen sjekklister for komponent i angitt periode</td></tr>
			</xsl:otherwise>
		</xsl:choose>
	</table>
	
	</div>
</div>
</div>
</xsl:template>
