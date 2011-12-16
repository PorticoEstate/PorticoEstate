<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
			
		<h1>Kalenderoversikt</h1>
		<fieldset class="check_list_details">
			<div><xsl:value-of select="location_array/loc1_name"/></div>
			<div>Periode: <xsl:value-of select="period"/></div>
		</fieldset>
				
		<h2 style="float:left;">Sjekklister</h2>
		<div style="float:left;margin-top: 30px;margin-left: 636px;"><a class="move_cal_right" href="#"><img src="controller/images/arrow_left.png" width="16"/></a></div>
		<div style="float:left;margin-top: 30px;margin-left: 454px;"><a class="move_cal_left" href="#"><img src="controller/images/arrow_right.png" width="16"/></a></div>
		
		<script>
			$(document).ready(function() {
			  			
	  			$(".move_cal_left").click(function(){
	  			  	var leftStrVal = $("#days_view").css("left");
	  			  	var leftNumVal = leftStrVal.substring(0, leftStrVal.indexOf('px'));
	  			  	
	  				if(leftNumVal == -502){
						$("#days_view").animate({
		                    left: '-=110' 
		                    }, 800);
					}else if(leftNumVal > -502){
						$("#days_view").animate({
		                    left: '-=502' 
		                    }, 800);
					}
	  			});
	  			
	  			$(".move_cal_right").click(function(){
		
					var leftStrVal = $("#days_view").css("left");
	  			  	var leftNumVal = leftStrVal.substring(0, leftStrVal.indexOf('px'));
	  			  	
	  				if(leftNumVal == -110){
						$("#days_view").animate({
		                    left: '+=110' 
		                    }, 800);
					}else if(-502 > leftNumVal){
						$("#days_view").animate({
		                    left: '+=502' 
		                    }, 800);
					}
	  			});
			});
		</script>
		
		<xsl:choose>
			<xsl:when test="controls_calendar_array/child::node()">
			
			<ul style="clear:left;" class="calendar info">
				<li class="heading">
					<div class="id">ID</div>
					<div class="title">Tittel</div>
					<div class="date">Start dato</div>
					<div class="date">Slutt dato</div>
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
									<xsl:when test="status = 0">
										<div>
										<a>
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.add_check_list_for_location</xsl:text>
												<xsl:text>&amp;date=</xsl:text>
												<xsl:value-of select="info/date"/>
												<xsl:text>&amp;control_id=</xsl:text>
												<xsl:value-of select="//control/id"/>
												<xsl:text>&amp;location_code=</xsl:text>
												<xsl:value-of select="//location_array/location_code"/>
											</xsl:attribute>
											<img height="15" src="controller/images/status_icon_yellow.png" />
										</a>
										</div>
									</xsl:when>
									<xsl:when test="status = 1">
										<div style="position:relative;">
					    					<div id="info_box" style="position:absolute;display:none;"></div>
											<a class="view_check_list">
											 	<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicheck_list.get_check_list_info</xsl:text>
													<xsl:text>&amp;phpgw_return_as=json</xsl:text>
													<xsl:text>&amp;check_list_id=</xsl:text>
													<xsl:value-of select="info/id"/>
												</xsl:attribute>
												<span style="display:none"><xsl:value-of select="info/id"/></span>
												<img height="15" src="controller/images/status_icon_red.png" />
											</a>
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
