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
			
			<a style="font-weight: bold;font-size: 14px;float:left;">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:value-of select="$location_code"/>
				</xsl:attribute>
				Årsoversikt
			</a>
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
			    		<xsl:choose>
							<xsl:when test="status = 'CONTROL_REGISTERED'">
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
							<xsl:when test="status = 'CONTROL_PLANNED'">
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
							<xsl:when test="status = 'CONTROL_NOT_DONE_WITH_PLANNED_DATE'">
								<div>
								<a>
									<xsl:attribute name="href">
										<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
										<xsl:text>&amp;check_list_id=</xsl:text>
										<xsl:value-of select="info/check_list_id"/>
									</xsl:attribute>
									<img height="15" src="controller/images/status_red_cross.png" />
								</a>
								</div>
						</xsl:when>
							<xsl:when test="status = 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS'">
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
							<xsl:when test="status = 'CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS'">
								<div style="position:relative;">
			    					<div id="info_box"></div>
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
							<xsl:when test="status = 'control_accomplished_with_errors'">
								<div style="position:relative;background: url(controller/images/status_icon_red_empty.png) no-repeat 50% 50%;">
									<div id="info_box"></div>
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
										<xsl:value-of select="info/num_open_cases"/>
									</a>
								</div>
							</xsl:when>
							<xsl:when test="status = 'control_not_accomplished_with_info'">
								<div style="position:relative;">
			    					<div id="info_box"></div>
									<a>
									<xsl:attribute name="href">
										<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
										<xsl:text>&amp;check_list_id=</xsl:text>
										<xsl:value-of select="info/check_list_id"/>
									</xsl:attribute>
										<span style="display:none"><xsl:value-of select="info/id"/></span>
										<img height="15" src="controller/images/status_icon_red_cross.png" />
									</a>
								</div>
							</xsl:when>
							<xsl:when test="status = 'control_not_accomplished'">
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
										<img height="15" src="controller/images/status_icon_red_cross.png" />
									</a>
								</div>
							</xsl:when>
							<xsl:when test="status = 'control_canceled'">
								<div>
									<img height="15" src="controller/images/status_icon_red_cross.png" />
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
</div>
</xsl:template>
