<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
			
		<h1>Kalenderoversikt</h1>
		<fieldset class="check_list_details">
			<div><xsl:value-of select="location_array/loc1_name"/></div>
			<div>Periode: <xsl:value-of select="period"/></div>
		</fieldset>
				
		<h2 style="float:left;">Sjekklister</h2>
		<div style="float:left;margin-top: 30px;margin-left: 585px;"><a class="move_cal_right" href="#">&lt;&lt;</a></div>
		<div style="float:left;margin-top: 30px;margin-left: 95px;"><a class="move_cal_left" href="#">&gt;&gt;</a></div>
		
		<script>
		
		$(document).ready(function() {
  			
  			$(".move_cal_left").click(function(){
  			
  				$("#days_view").animate({
						left: '-=502' 
						}, 800);			
  			});
  			
  			
  			$(".move_cal_right").click(function(){

  				$("#days_view").animate({
						left: '+=502' 
						}, 800);
  			});
  			
  		
  		
		});
		
		</script>
		
		
		
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">
				
				<ul style="clear:left;" class="calendar info">
					<li class="heading">
						<div class="id">ID</div>
						<div class="title">Tittel</div>
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
						<li>
							<xsl:for-each select="heading_array">
								<div><xsl:value-of select="."/></div>
							</xsl:for-each>
						</li>				
						<li>	
							<xsl:for-each select="controls_calendar_array/calendar_array">
								<div>
									<xsl:choose>
										<xsl:when test="calendar_array/child::node() = 1">
											<xsl:value-of select="." />
										</xsl:when>
										<xsl:otherwise>
											<div style="position:relative;">
												<div id="info_box" style="position:absolute;display:none;">
												</div>
												<xsl:choose>
														<xsl:when test="id">
															<xsl:variable name="status"><xsl:value-of select="status"/></xsl:variable>
															<xsl:choose>
																<xsl:when test="status = 1">
																	<img height="15" src="controller/images/status_icon_light_green.png" />	
																</xsl:when>
																<xsl:otherwise>
																 <a class="view_check_list">
																	<xsl:attribute name="href">
																		<xsl:text>index.php?menuaction=controller.uicheck_list.get_check_list_info</xsl:text>
																		<xsl:text>&amp;phpgw_return_as=json</xsl:text>
																		<xsl:text>&amp;check_list_id=</xsl:text>
																		<xsl:value-of select="id"/>
																	</xsl:attribute>
																	<img height="15" src="controller/images/status_icon_red.png" />
																</a>
																</xsl:otherwise>
															</xsl:choose>	
														</xsl:when>
														<xsl:otherwise>
															<img height="15" src="controller/images/status_icon_yellow.png" />
														</xsl:otherwise>
													</xsl:choose>
												</div>
										</xsl:otherwise>
									</xsl:choose>
								</div>
							</xsl:for-each>
						</li>
					</ul>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div>Ingen sjekklister for bygg i angitt periode</div>
			</xsl:otherwise>
		</xsl:choose>
</div>
</xsl:template>