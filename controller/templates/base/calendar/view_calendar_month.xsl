<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>
<xsl:variable name="view_location_code"><xsl:value-of select="view_location_code"/></xsl:variable>

<div id="main_content">
	<div id="control_plan">
		<div class="top">
			<h1><xsl:value-of select="location_array/loc1_name"/></h1>
			<h3 style="margin:0;font-size:19px;">Kalenderoversikt for <xsl:value-of select="period"/><span style="margin-left:5px;"><xsl:value-of select="year"/></span></h3>
		
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
			</form>
		</div>
		
		<div class="middle">
			
					
			<ul id="icon_color_map">
				<li><img height="15" src="controller/images/status_icon_yellow_ring.png" /><span>Kontroll satt opp</span></li>
				<li><img height="15" src="controller/images/status_icon_yellow.png" /><span>Kontroll har planlagt dato</span></li>
				<li><img height="15" src="controller/images/status_icon_dark_green.png" /><span>Kontroll gjennomført uten åpne saker før frist</span></li>
				<li><img height="15" src="controller/images/status_icon_light_green.png" /><span>Kontroll gjennomført uten åpne saker etter frist</span></li>
				<li><img height="15" src="controller/images/status_icon_red_empty.png" /><span>Kontroll gjennomført med åpne saker</span></li>
				<li><img height="15" src="controller/images/status_icon_red_cross.png" /><span>Kontroll ikke gjennomført</span></li>
			</ul>
			
			<a style="display:block;font-weight: bold;font-size: 14px;float:left;">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="$location_code"/>
				</xsl:attribute>
				Årsoversikt
			</a>
			
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
			
		</div>
		
		
		<div id="cal_wrp">
			
			<ul class="calendar month">
				<li class="heading">
					<div class="control_details_wrp">
						<div class="title">Tittel</div>
						<div class="assigned">Tildelt</div>
						<div class="frequency">Frekvens</div>
					</div>
					<div class="days_wrp">
						<xsl:for-each select="heading_array">
							<div><xsl:value-of select="."/></div>
						</xsl:for-each>
					</div>
				</li>
				<xsl:choose>	
					<xsl:when test="controls_calendar_array/child::node()">
			  	<xsl:for-each select="controls_calendar_array">

					<li>				
					<xsl:choose>
				        <xsl:when test="(position() mod 2) != 1">
				            <xsl:attribute name="class">odd</xsl:attribute>
				        </xsl:when>
				        <xsl:otherwise>
				            <xsl:attribute name="class">even</xsl:attribute>
				        </xsl:otherwise>
				    </xsl:choose>
					
					<div class="control_details_wrp">
						<div class="title">
			      			<xsl:value-of select="control/title"/>
						</div>
						<div class="assigned">
			      			<xsl:value-of select="control/responsibility_name"/>
						</div>
						<div class="frequency">
			      			<xsl:value-of select="control/repeat_type"/>
			      			<xsl:value-of select="control/repeat_interval"/>
						</div>
				
			</div>
			<div class="days_wrp">
				<xsl:for-each select="calendar_array">
					
					<xsl:call-template name="check_list_status_checker" >
						<xsl:with-param name="location_code"><xsl:value-of select="$view_location_code"/></xsl:with-param>
					</xsl:call-template>
					
				</xsl:for-each>
				</div>
				</li>
				</xsl:for-each>
				
					</xsl:when>
					<xsl:otherwise>
						<div class="cal_info_msg">Ingen sjekklister for bygg i angitt periode</div>
					</xsl:otherwise>
				</xsl:choose>
			
			</ul>
		</div>
	</div>
</div>
</xsl:template>
