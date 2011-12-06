<xsl:template match="data" name="view_check_lists" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>
<xsl:variable name="year"><xsl:value-of select="year"/></xsl:variable>

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
					<div class="id">ID</div><div class="title">Tittel</div><div class="frequency">Frekvenstype</div><div class="frequency">Frekvensintervall</div>
					<xsl:for-each select="heading_array">
						<div>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uilocation_check_list.view_agg_check_lists_for_location</xsl:text>
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
			  		<xsl:variable name="repeat_type"><xsl:value-of select="control/repeat_type"/></xsl:variable>
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
						<xsl:for-each select="calendar_array">
				    		<div>
				    		<xsl:choose>
					    		<xsl:when test="$repeat_type = 0">
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
				</xsl:for-each>	
			</xsl:when>
			<xsl:otherwise>
				<div>Ingen sjekklister for bygg i angitt periode</div>
			</xsl:otherwise>
		</xsl:choose>
	</ul>
</div>
</xsl:template>