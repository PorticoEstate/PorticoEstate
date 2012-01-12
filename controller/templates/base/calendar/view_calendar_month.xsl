<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>

<div id="main_content">
		
		<div style="float:left;">
			<h1><xsl:value-of select="location_array/loc1_name"/></h1>
			<h3 style="margin:0;font-size:19px;">Kalenderoversikt for <xsl:value-of select="period"/></h3>
		</div>
		
				
		<ul id="icon_color_map">
			<li><img height="15" src="controller/images/status_icon_yellow_ring.png" /><span>Kontroll satt opp</span></li>
			<li><img height="15" src="controller/images/status_icon_yellow.png" /><span>Kontroll har planlagt dato</span></li>
			<li><img height="15" src="controller/images/status_icon_dark_green.png" /><span>Kontroll gjennomført uten feil før frist</span></li>
			<li><img height="15" src="controller/images/status_icon_light_green.png" /><span>Kontroll gjennomført uten feil etter frist</span></li>
			<li><img height="15" src="controller/images/status_icon_red_empty.png" /><span>Kontroll gjennomført med rapporterte feil</span></li>
			<li><img height="15" src="controller/images/status_icon_red_cross.png" /><span>Kontroll ikke gjennomført</span></li>
		</ul>
				
		<div style="float: left;margin-bottom: 10px;margin-left: 735px;margin-top: 30px;"><a class="move_cal_right" href="#"><img src="controller/images/arrow_left.png" width="16"/></a></div>
		<div style="float:left;margin-top: 30px;margin-left: 374px;"><a class="move_cal_left" href="#"><img src="controller/images/arrow_right.png" width="16"/></a></div>
		
		<script>
			$(document).ready(function() {
				$(".move_cal_left").click(function(){
	  			  	var leftStrVal = $("#days_view").css("left");
	  			  	var leftNumVal = leftStrVal.substring(0, leftStrVal.indexOf('px'));
	  			  	
	  				if(leftNumVal == -417){
						$("#days_view").animate({
		                    left: '-=93' 
		                    }, 800);
					}else if(leftNumVal > -417){
						$("#days_view").animate({
		                    left: '-=417' 
		                    }, 800);
					}
	  			});
	  			
	  			$(".move_cal_right").click(function(){
					var leftStrVal = $("#days_view").css("left");
	  			  	var leftNumVal = leftStrVal.substring(0, leftStrVal.indexOf('px'));
	  			  	
	  			  	if( leftNumVal != 0 ){
		  				if(leftNumVal == -93){
							$("#days_view").animate({
			                    left: '+=93' 
			                    }, 800);
						}else if( leftNumVal >= -510 ){
							$("#days_view").animate({
			                    left: '+=417' 
			                    }, 800);
						}
					}
	  			});
			});
		</script>
		
		<xsl:choose>
			<xsl:when test="controls_calendar_array/child::node()">
			<ul style="clear:left;" class="calendar info month">
				<li class="heading">
					<div class="id">ID</div>
					<div class="title">Tittel</div>
					<div class="date">Startdato</div>
					<div class="date">Sluttdato</div>
					<div class="frequency">Frekvenstype</div>
					<div class="frequency">Frekvensintervall</div>
				</li>
			
			  	<xsl:for-each select="controls_calendar_array">
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
					</li>
				</xsl:for-each>
			</ul>
			
			<div id="days_wrp">
				<ul id="days_view" class="calendar days">
					<li class="heading">
						<xsl:for-each select="heading_array">
							<div><xsl:value-of select="."/></div>
						</xsl:for-each>
					</li>				
							
					<xsl:for-each select="controls_calendar_array">
					<li>
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
												<img height="15" src="controller/images/status_icon_red_empty.png" />
											</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 'control_not_accomplished_with_info'">
										<div style="position:relative;">
					    					<div id="info_box" style="position:absolute;display:none;"></div>
											<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
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
											<img height="15" src="controller/images/status_icon_red_cross.png" />
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
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<div>Ingen sjekklister for bygg i angitt periode</div>
		</xsl:otherwise>
	</xsl:choose>
</div>
</xsl:template>
