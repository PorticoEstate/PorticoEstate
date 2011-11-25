<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
			
		<h1>Kalenderoversikt</h1>
		<fieldset class="check_list_details">
			<div><xsl:value-of select="location_array/loc1_name"/></div>
			<div>Periode: <xsl:value-of select="php:function('date', 'd/m-Y', number(from_date))"/> - <xsl:value-of select="php:function('date', 'd/m-Y', number(to_date))"/></div>
		</fieldset>
		
		<h2>Sjekklister</h2>
		
		<ul class="calendar">
			<li class="heading">
				<div>Id</div><div class="title">Tittel</div><div class="date">Startdato</div><div class="date">Sluttdato</div>
				<div class="frequency">Frekvenstype</div><div class="frequency">Frekvensintervall</div>
				<div>Jan</div><div>Feb</div><div>Mar</div><div>Apr</div><div>Mai</div><div>Jun</div>
				<div>Jul</div><div>Aug</div><div>Sep</div><div>Okt</div><div>Nov</div><div>Des</div>
			</li>
			<xsl:choose>
				<xsl:when test="controls_calendar_array/child::node()">
				    <xsl:for-each select="controls_calendar_array">
				    	<li>
				    		<div>
				      			<xsl:value-of select="control/id"/>
							</div>
							<div class="title">
				      			<xsl:value-of select="control/title"/>
							</div>
							<div class="date">
				      			<xsl:value-of select="php:function('date', 'd/m-Y', number(control/start_date))"/>
							</div>
							<div class="date">
								<xsl:choose>
									<xsl:when test="control/end_date != 0">
										<xsl:value-of select="php:function('date', 'd/m-Y', number(control/end_date))"/>
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
							<xsl:for-each select="calendar_array">
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
												 <a class="view_list">
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
							</xsl:for-each>
						</li>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekklister for denne kontrollen
				</xsl:otherwise>
			</xsl:choose>
		</ul>
</div>
</xsl:template>