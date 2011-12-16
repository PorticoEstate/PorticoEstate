<!-- $Id$ -->
<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="year"><xsl:value-of select="year"/></xsl:variable>
<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>

<div id="main_content">
			
		<h1>Kalenderoversikt</h1>
		<fieldset class="check_list_details">
			<div><xsl:value-of select="location_array/loc1_name"/></div>
			<div>Periode: <xsl:value-of select="period"/></div>
		</fieldset>
				
		<h2>Sjekklister</h2>
		
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
					<xsl:for-each select="heading_array">
						<div>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uilocation_check_list.view_calendar_for_month</xsl:text>
									<xsl:text>&amp;year=</xsl:text>
									<xsl:value-of select="$year"/>
									<xsl:text>&amp;month=</xsl:text>
									<xsl:number/>
								</xsl:attribute>
								<xsl:value-of select="."/>
							</a>				
						</div>
					</xsl:for-each>
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
											<xsl:value-of select="$control_id"/>
											<xsl:text>&amp;location_code=</xsl:text>
											<xsl:value-of select="$location_code"/>
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
								<xsl:when test="status = 2">
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
								<xsl:otherwise>
								<div></div>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
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
