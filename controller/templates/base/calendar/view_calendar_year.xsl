<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="year"><xsl:value-of select="year"/></xsl:variable>
<xsl:variable name="view_location_code"><xsl:value-of select="view_location_code"/></xsl:variable>

<div id="main_content">

	<div id="control_plan">
		<div class="col_1">
			<xsl:choose>
				<xsl:when test="show_location">
					<h1><xsl:value-of select="control_name"/></h1>
				</xsl:when>
				<xsl:otherwise>
					<h1><xsl:value-of select="location_array/loc1_name"/></h1>
				</xsl:otherwise>
			</xsl:choose>
			<h3 style="margin:0;font-size:19px;">Kalenderoversikt for <xsl:value-of select="period"/></h3>
		</div>

		<div class="col_2">
			<xsl:choose>
				<xsl:when test="show_location">&nbsp;</xsl:when>
				<xsl:otherwise>
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
				</xsl:otherwise>
			</xsl:choose>
					
			<ul id="icon_color_map">
				<li><img height="15" src="controller/images/status_icon_yellow_ring.png" /><span>Kontroller satt opp</span></li>
				<li><img height="15" src="controller/images/status_icon_dark_green.png" /><span>Kontroller gjennomført uten feil</span></li>
				<li><img height="15" src="controller/images/status_icon_red_empty.png" /><span>Kontroller gjennomført med rapporterte feil</span></li>
				<li><img height="15" src="controller/images/status_icon_red_cross.png" /><span>Kontroller ikke gjennomført</span></li>
			</ul>
		</div>
		
		<ul class="calendar">
				<li class="heading">
					<xsl:if test="show_location">
					<div class="location">Lokasjon</div>
					</xsl:if>
					<div class="title">Tittel</div>
					<div class="assigned">Tildelt</div>
					<div class="date">Start dato</div>
					<div class="date">Slutt dato</div>
					<div class="frequency">Frekvens</div>
					<div class="months">
					<xsl:for-each select="heading_array">
						<div>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="$year"/>
									<xsl:text>&amp;location_code=</xsl:text>
									<xsl:value-of select="//location_code"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
								</xsl:attribute>
								<xsl:value-of select="."/>
							</a>				
						</div>
					</xsl:for-each>
					</div>
				</li>
			
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">
				
			  	<xsl:for-each select="controls_calendar_array">
			  		<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
					<li>
						<xsl:if test="//show_location">
							<div class="location">
								<xsl:value-of select="control/location_name"/>
							</div>
						</xsl:if>
						<div class="title">
			      			<xsl:value-of select="control/title"/>
						</div>
						<div class="assigned">
			      			<xsl:value-of select="control/responsibility_name"/>
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
						<div class="months">
						<xsl:for-each select="calendar_array">
							<xsl:choose>
									<xsl:when test="status = 'control_registered'">
										<div>
										<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list.add_check_list</xsl:text>
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
									<xsl:when test="status = 'controls_registered'">
										<div>
											<img height="15" src="controller/images/status_icon_yellow_ring.png" />
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_planned'">
										<div>
										<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
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
												<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
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
												<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="info/check_list_id"/>
											</xsl:attribute>
												<span style="display:none"><xsl:value-of select="info/id"/></span>
												<img height="15" src="controller/images/status_icon_light_green.png" />
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'controls_accomplished_without_errors'">
										<div>
											<img height="15" src="controller/images/status_icon_dark_green.png" />
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_accomplished_with_errors'">
										<div style="position:relative;">
					    					<div id="info_box" style="position:absolute;display:none;"></div>
											<a class="view_check_list">
											 	<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
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
									<xsl:when test="status = 'controls_accomplished_with_errors'">
										<div style="position:relative;background: url(controller/images/status_icon_red_empty.png) no-repeat 50% 50%;">
											<a class="view_check_list">
											 	<xsl:value-of select="info"/>
											</a>
										</div>
									
									</xsl:when>
									<xsl:when test="status = 'control_canceled'">
										<div>
											<img height="12" src="controller/images/status_icon_red_cross.png" />
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_not_accomplished' or status = 'controls_not_accomplished'">
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
				<div class="cal_info_msg">Ingen sjekklister for bygg i angitt periode</div>
			</xsl:otherwise>
		</xsl:choose>
	</ul>
</div>
</div>
</xsl:template>
