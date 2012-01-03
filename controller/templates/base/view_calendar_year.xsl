<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="year"><xsl:value-of select="year"/></xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>

<div id="main_content">
		
		<h1>Kalenderoversikt for <xsl:value-of select="period"/></h1>
		
		<div style="float:left;">			
			<fieldset class="location_details">
				<h3 style="margin:0;font-size:19px;"><xsl:value-of select="location_array/loc1_name"/></h3>
			</fieldset>
		</div>
		
	<ul id="icon_color_map">
			<li><img height="13" src="controller/images/status_icon_yellow_ring.png" /><span>Kontroll satt opp</span></li>
			<li><img height="13" src="controller/images/status_icon_yellow.png" /><span>Kontroll har planlagt dato</span></li>
			<li><img height="13" src="controller/images/status_icon_dark_green.png" /><span>Kontroll gjennomført uten feil før frist</span></li>
			<li><img height="13" src="controller/images/status_icon_light_green.png" /><span>Kontroll gjennomført uten feil etter frist</span></li>
			<li><img height="13" src="controller/images/status_icon_red_empty.png" /><span>Kontroll gjennomført med rapporterte feil</span></li>
			<li><img height="11" src="controller/images/status_icon_red_cross.png" /><span>Kontroll ikke gjennomført</span></li>
		</ul>
		
		<ul class="calendar">
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">

				<li class="heading">
					<div class="id">ID</div>
					<div class="title">Tittel</div>
					<div class="date">Start dato</div>
					<div class="date">Slutt dato</div>
					<div class="frequency">Frekvenstype</div>
					<div class="frequency">Frekvensintervall</div>
					<div class="months">
					<xsl:for-each select="heading_array">
						<div>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="$year"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
								</xsl:attribute>
								<xsl:value-of select="."/>
							</a>				
						</div>
					</xsl:for-each>
					</div>
				</li>
			
			  	<xsl:for-each select="controls_calendar_array">
			  		<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
					<li>
			    		<div class="id">
			      			<xsl:value-of select="control/id"/>
						</div>
						<div class="title">
			      			<xsl:value-of select="control/title"/>
						</div>
						<div class="date">
			      			<xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/>
						</div>
						<div class="date">
							<xsl:choose>
								<xsl:when test="control/end_date != 0">
				      				<xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/>
				      			</xsl:when>
				      			<xsl:otherwise>
				      				Løpende
				      			</xsl:otherwise>
			      			</xsl:choose>
						</div>
						<div class="frequency">
			      			<xsl:value-of select="control/repeat_type"/>
						</div>
						<div class="frequency">
			      			<xsl:value-of select="control/repeat_interval"/>
						</div>							
						<div class="months">
						<xsl:for-each select="calendar_array">
						<xsl:choose>
									<xsl:when test="status = 'control_registered'">
										<div>
										<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.add_check_list_for_location</xsl:text>
												<xsl:text>&amp;date=</xsl:text>
												<xsl:value-of select="info/date"/>
												<xsl:text>&amp;control_id=</xsl:text>
												<xsl:value-of select="info/control_id"/>
												<xsl:text>&amp;location_code=</xsl:text>
												<xsl:value-of select="$location_code"/>
											</xsl:attribute>
											<img height="15" src="controller/images/status_icon_yellow_ring.png" />
										</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_planned'">
										<div>
										<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="info/check_list_id"/>
											</xsl:attribute>
											<img height="15" src="controller/images/status_icon_yellow.png" />
										</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_accomplished_in_time_without_errors'">
										<div>
											<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="info/check_list_id"/>
											</xsl:attribute>
												<span style="display:none"><xsl:value-of select="info/id"/></span>
												<img height="15" src="controller/images/status_icon_dark_green.png" />
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_accomplished_over_time_without_errors'">
										<div style="position:relative;">
					    					<div id="info_box" style="position:absolute;display:none;"></div>
											<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="info/check_list_id"/>
											</xsl:attribute>
												<span style="display:none"><xsl:value-of select="info/id"/></span>
												<img height="15" src="controller/images/status_icon_light_green.png" />
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_accomplished_with_errors'">
										<div style="position:relative;">
					    					<div id="info_box" style="position:absolute;display:none;"></div>
											<a class="view_check_list">
											 	<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
													<xsl:text>&amp;check_list_id=</xsl:text>
													<xsl:value-of select="info/check_list_id"/>
												</xsl:attribute>
												<span style="display:none">
													<xsl:text>&amp;check_list_id=</xsl:text><xsl:value-of select="info/check_list_id"/>
													<xsl:text>&amp;phpgw_return_as=json</xsl:text>
												</span>
												<img height="15" src="controller/images/status_icon_red.png" />
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_agg_accomplished_with_errors'">
										<div style="background: url(controller/images/status_icon_red_empty.png) no-repeat 50% 50%;">
					    					<a style="color:#fff;font-weight:bold;text-decoration: none;font-size:10px;" class="view_check_list">
											 	<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicheck_list.get_check_list_info</xsl:text>
													<xsl:text>&amp;phpgw_return_as=json</xsl:text>
													<xsl:text>&amp;check_list_id=</xsl:text>
													<xsl:value-of select="info/id"/>
												</xsl:attribute>
												<span><xsl:value-of select="info"/></span>
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_canceled'">
										<div>
											<img height="12" src="controller/images/status_icon_red_cross.png" />
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_not_accomplished'">
										<div>
											<img height="12" src="controller/images/status_icon_red_cross.png" />
										</div>
									</xsl:when>
									<xsl:otherwise>
									<div></div>
									</xsl:otherwise>
								</xsl:choose>
								
						</xsl:for-each>
						</div>
					</li>	
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<div>Ingen sjekklister for bygg i angitt periode</div>
			</xsl:otherwise>
		</xsl:choose>
	</ul>
</div>
</xsl:template>
