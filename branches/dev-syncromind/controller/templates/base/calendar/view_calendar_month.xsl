<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr"/> capitalized</xsl:variable>
<xsl:variable name="session_url"><xsl:text>&amp;</xsl:text><xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
<xsl:variable name="serie_id"><xsl:value-of select="serie_id" /></xsl:variable>

	<script>
<xsl:text>

$(document).ready(function(){

	var oArgs = {menuaction:'property.bolocation.get_locations_by_name'};
	var baseUrl = phpGWLink('index.php', oArgs, false);

	var location_type = $("#loc_type").val();

	$("#search-location-name").autocomplete({
		source: function( request, response ) {
			location_type = $("#loc_type").val();
		
			$.ajax({
				url: baseUrl,
				dataType: "json",
				data: {
					location_name: request.term,
					level: location_type,
					phpgw_return_as: "json"
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						return {
							label: item.name,
							value: item.location_code
						}
					}));
				}
			});
		},
		focus: function (event, ui) {
 			$(event.target).val(ui.item.label);
  			return false;
		},
		minLength: 1,
		select: function( event, ui ) {
		  chooseLocation( ui.item.label, ui.item.value);
		}
	});
});

function chooseLocation( label, value ){
	var currentYear = $("#currentYear").val();
	var currentMonth = $("#currentMonth").val();
	
	var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_month'};
	var baseUrl = phpGWLink('index.php', oArgs, false);
	var requestUrl = baseUrl +  "&amp;location_code=" + value + "&amp;year=" + currentYear + "&amp;month=" + currentMonth;
	
	window.location.replace(requestUrl);
}

</xsl:text>

</script>

	<div id="main_content">
		<div id="control_plan" class="month_view">
			<div class="top">

				<xsl:choose>
					<xsl:when test="location_level = 1">
						<h1>Kontrollplan for eiendom:
							<xsl:value-of select="current_location/loc1_name"/>
						</h1>
					</xsl:when>
					<xsl:otherwise>
						<h1>Kontrollplan for bygg:
							<xsl:value-of select="current_location/loc2_name"/>
						</h1>
					</xsl:otherwise>
				</xsl:choose>

				<h3>Kalenderoversikt for
					<span class="month">
						<xsl:value-of select="php:function('lang', $month_str)" />
					</span>
					<span class="year">
						<xsl:value-of select="current_year"/>
					</span>
				</h3>

				<!-- =====================  SEARCH FOR LOCATION  ================= -->
				<div id="search-location" class="select-box">
					<div id="choose-loc">
						<input id="loc_type" type="hidden" name="loc_type" value="2" />
						<input type="hidden" id="currentYear">
							<xsl:attribute name="value">
								<xsl:value-of select="current_year"/>
							</xsl:attribute>
						</input>
						<input type="hidden" id="currentMonth">
							<xsl:attribute name="value">
								<xsl:value-of select="current_month_nr"/>
							</xsl:attribute>
						</input>
						<label>Søk etter</label>
						<span>
							<a href="loc_type_2" class="btn first active">Bygg</a>
							<a href="loc_type_1" class="btn">Eiendom</a>
						</span>
					</div>
					<input type="text" value="" id="search-location-name" />
				</div>

				<!-- =====================  SELECT LIST FOR MY ASSIGNED LOCATIONS  ================= -->
				<div id="choose-my-location" class="select-box">
					<label>Velg et annet bygg/eiendom du har ansvar for</label>

					<form action="#">
						<input type="hidden" name="period_type" value="view_month" />
						<input type="hidden" name="year">
							<xsl:attribute name="value">
								<xsl:value-of select="current_year"/>
							</xsl:attribute>
						</input>
						<input type="hidden" name="month">
							<xsl:attribute name="value">
								<xsl:value-of select="current_month_nr"/>
							</xsl:attribute>
						</input>

						<select id="choose-my-location" class="select-location">
							<option>Velg bygg</option>
							<xsl:for-each select="my_locations">
								<xsl:choose>
									<xsl:when test="location_code = //current_location/location_code">
										<option selected="SELECTED">
											<xsl:attribute name="value">
												<xsl:value-of disable-output-escaping="yes" select="location_code"/>
											</xsl:attribute>
											<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option>
											<xsl:attribute name="value">
												<xsl:value-of disable-output-escaping="yes" select="location_code"/>
											</xsl:attribute>
											<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</select>
					</form>
				</div>
			</div>
			<div class="middle">

				<!-- =====================  SHOW CALENDAR FOR YEAR  ================= -->
				<a id="showYear">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
						<xsl:text>&amp;year=</xsl:text>
						<xsl:value-of select="current_year"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="current_location/location_code"/>
						<xsl:value-of select="$session_url"/>
					</xsl:attribute>
					<img height="20" src="controller/images/left_arrow_simple_light_blue.png" />Årsoversikt
				</a>

				<!-- =====================  CHOOSE ANOTHER BUILDING ON PROPERTY  ================= -->
				<div id="choose-building" class="select-box">
					<xsl:if test="location_level > 1">
						<a>
							<xsl:attribute name="href">
								<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
								<xsl:text>&amp;year=</xsl:text>
								<xsl:value-of select="current_year"/>
								<xsl:text>&amp;location_code=</xsl:text>
								<xsl:value-of select="current_location/loc1"/>
								<xsl:value-of select="$session_url"/>
							</xsl:attribute>
							Vis kontrollplan for eiendom
						</a>
					</xsl:if>

					<label>Velg en annen lokalisering på eiendommen</label>
					<xsl:call-template name="select_buildings_on_property" />
				</div>

				<!-- =====================  COLOR ICON MAP  ================= -->
				<xsl:call-template name="icon_color_map" />

				<!-- =====================  FILTERS  ================= -->
				<xsl:call-template name="calendar_filters" >
					<xsl:with-param name="view_period">month</xsl:with-param>
				</xsl:call-template>

				<!-- =====================  CALENDAR NAVIGATION  ================= -->
				<xsl:call-template name="nav_calendar_month">
					<xsl:with-param name="view">VIEW_CONTROLS_FOR_LOCATION</xsl:with-param>
				</xsl:call-template>
			</div>

			<div id="cal_wrp">
				<!-- ================================  BUILDINGS TABLE  ====================================  -->
				<h2>Bygg/eiendom</h2>
				<table id="calendar" class="month">
					<tr class="heading">
						<th class="title">
							<span>Tittel</span>
						</th>
						<th class="assigned">
							<span>Tildelt</span>
						</th>
						<th class="frequency">
							<span>Frekvens</span>
						</th>
						<xsl:for-each select="heading_array">
							<th>
								<span>
									<xsl:value-of select="."/>
								</span>
							</th>
						</xsl:for-each>
					</tr>
					<xsl:choose>
						<xsl:when test="controls_calendar_array/child::node()">
							<xsl:for-each select="controls_calendar_array">
								<tr>
									<xsl:choose>
										<xsl:when test="(position() mod 2) != 1">
											<xsl:attribute name="class">odd</xsl:attribute>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="class">even</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
									<td class="title">
										<a class="show-control-details">
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicontrol.get_control_details</xsl:text>
												<xsl:text>&amp;control_id=</xsl:text>
												<xsl:value-of select="control/id"/>
												<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
												<xsl:value-of select="$session_url"/>
											</xsl:attribute>
											<xsl:value-of select="control/title"/>
										</a>
									</td>
									<td class="assigned">
										<span>
											<xsl:value-of select="control/responsibility_name"/>
										</span>
									</td>
									<td class="frequency">
										<span>
											<xsl:choose>
												<xsl:when test="control/repeat_interval = 1">
													<span class="pre">Hver</span>
												</xsl:when>
												<xsl:when test="control/repeat_interval = 2">
													<span class="pre">Annenhver</span>
												</xsl:when>
												<xsl:when test="control/repeat_interval > 2">
													<span class="pre">Hver</span>
													<span>
														<xsl:value-of select="control/repeat_interval"/>.
													</span>
												</xsl:when>
											</xsl:choose>

											<span class="val">
												<xsl:value-of select="control/repeat_type_label"/>
											</span>
										</span>
									</td>

									<xsl:for-each select="calendar_array">
										<td>
											<xsl:call-template name="check_list_status_manager" >
												<xsl:with-param name="location_code">
													<xsl:value-of select="//current_location/location_code"/>
												</xsl:with-param>
												<xsl:with-param name="serie_id"><xsl:value-of select="$serie_id"/></xsl:with-param>
												<xsl:with-param name="session_url"><xsl:value-of select="$session_url"/></xsl:with-param>
											</xsl:call-template>
										</td>
									</xsl:for-each>
								</tr>
							</xsl:for-each>
						</xsl:when>
						<xsl:otherwise>
							<tr class="cal_info_msg">
								<td colspan="3">
									<xsl:value-of select="php:function('lang', 'error_msg_no_controls_in_period')" />
								</td>
							</tr>
						</xsl:otherwise>
					</xsl:choose>
				</table>

				<!-- ================================  COMPONENTS TABLE  ====================================  -->
				<h2 class="components">Komponenter</h2>
				<xsl:choose>
					<xsl:when test="components_calendar_array/child::node()">
						<xsl:for-each select="components_calendar_array">
							<h3>
								<xsl:value-of select="component/xml_short_desc"/>
							</h3>

							<table id="calendar" class="month">
								<tr class="heading">
									<th class="title">
										<span>Tittel</span>
									</th>
									<th class="assigned">
										<span>Tildelt</span>
									</th>
									<th class="frequency">
										<span>Frekvens</span>
									</th>
									<xsl:for-each select="//heading_array">
										<th>
											<span>
												<xsl:value-of select="."/>
											</span>
										</th>
									</xsl:for-each>
								</tr>

								<xsl:for-each select="controls_calendar">
									<xsl:variable name="control_id">
										<xsl:value-of select="control/id"/>
									</xsl:variable>
									<tr>
										<xsl:choose>
											<xsl:when test="(position() mod 2) != 1">
												<xsl:attribute name="class">odd</xsl:attribute>
											</xsl:when>
											<xsl:otherwise>
												<xsl:attribute name="class">even</xsl:attribute>
											</xsl:otherwise>
										</xsl:choose>
										<td class="title">
											<a id="showControlDetails">
												<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicontrol.get_control_details</xsl:text>
													<xsl:text>&amp;control_id=</xsl:text>
													<xsl:value-of select="control/id"/>
													<xsl:value-of select="$session_url"/>
												</xsl:attribute>
												<xsl:value-of select="control/title"/>
											</a>
										</td>
										<td class="assigned">
											<span>
												<xsl:value-of select="control/responsibility_name"/>
											</span>
										</td>
										<td class="frequency">
											<span>
												<xsl:choose>
													<xsl:when test="control/repeat_interval = 1 and control/repeat_type &lt; 3">
														<span class="pre">Hver</span>
													</xsl:when>
													<xsl:when test="control/repeat_interval = 1 and control/repeat_type = 3">
														<span class="pre">Hvert</span>
													</xsl:when>
													<xsl:when test="control/repeat_interval = 2">
														<span class="pre">Annenhver</span>
													</xsl:when>
													<xsl:when test="control/repeat_interval > 2">
														<span class="pre">Hver</span>
														<span>
															<xsl:value-of select="control/repeat_interval"/>.
														</span>
													</xsl:when>
												</xsl:choose>

												<span class="val">
													<xsl:value-of select="control/repeat_type_label"/>
												</span>
											</span>
										</td>
										<xsl:for-each select="calendar_array">
											<td>
												<xsl:call-template name="check_list_status_manager" >
													<xsl:with-param name="location_code">
														<xsl:value-of select="//current_location/location_code"/>
													</xsl:with-param>
													<xsl:with-param name="serie_id"><xsl:value-of select="$serie_id"/></xsl:with-param>
													<xsl:with-param name="session_url"><xsl:value-of select="$session_url"/></xsl:with-param>
												</xsl:call-template>
											</td>
										</xsl:for-each>
									</tr>
								</xsl:for-each>
							</table>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<div id="calendar">
							<p class="no-comp-msg">Ingen komponenter tilknyttet kontroll</p>
						</div>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>
	</div>
</xsl:template>
