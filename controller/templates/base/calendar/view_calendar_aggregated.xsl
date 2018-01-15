<!--
	Document   : view_calendar_aggregated.xsl
	Created on : 14. november 2017, 12:13
	Author     : Erik
	Description:
		Purpose of transformation follows.
-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
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
	
	var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_year'};
	var baseUrl = phpGWLink('index.php', oArgs, false);
	var requestUrl = baseUrl +  "&amp;location_code=" + value + "&amp;year=" + currentYear;
	
	window.location.replace(requestUrl);
}

</xsl:text>

</script>

<div class="yui-navset yui-navset-top" id="control_calendar_tabview">
	<xsl:call-template name="view_calendar_for_locations" />
	<xsl:if test="multiple_locations">
		<xsl:call-template name="calendar_location" />
	</xsl:if>
</div>
</xsl:template>


<xsl:template name="view_calendar_for_locations" xmlns:php="http://php.net/xsl">
	<div class="content-wrp">
		<div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
				<div class="body">
							
				<style type="text/css">
					
					</style>
				
					<div id="choose-location">
							<xsl:apply-templates select="filter_form" />
					
					  	<form action="{update_action}" name="acl_form" id="acl_form" method="post">
								<xsl:call-template name="datatable"/>
						</form>
					</div>
				</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
		<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<h4>Velg lokasjoner som du vil se årsplan for</h4>
	<fieldset id="comp-filters">
	  <div class="select-box">
			<div class="filter">
				<label><xsl:value-of select="php:function('lang', 'location type')" /></label>
			  <select id="location_type" name="location_type">
					<xsl:apply-templates select="location_type_list/options"/>
			  </select>
			</div>
		  <div class="filter">
				<label><xsl:value-of select="php:function('lang', 'location category')" /></label>
			  <select id="location_type_category" name="location_type_category"></select>
			</div>
	  </div>
	  
		<div class="select-box">
			<div class="filter">
	  		<label><xsl:value-of select="php:function('lang', 'district')" /></label>
				<select id="district_id" name="district_id">
					<xsl:apply-templates select="district_list/options"/>
			  </select>
	  	</div>
		  <div class="filter">
				<label><xsl:value-of select="php:function('lang', 'part of town')" /></label>
				<select id="part_of_town_id" name="part_of_town_id">
					<xsl:apply-templates select="part_of_town_list/options"/>
			  </select>
			</div>
	  </div>
	</fieldset>
</xsl:template>

<xsl:template name="datatable" xmlns:php="http://php.net/xsl">
  <xsl:variable name="label_show"><xsl:value-of select="php:function('lang', 'show')" /></xsl:variable>
	<input type="submit" name="update_acl" id="frm_update_acl" class="btn" value="{$label_show}"/>

	<div id="receipt"></div>
</xsl:template>

<!-- options for use with select-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<xsl:template name="calendar_location" xmlns:php="http://php.net/xsl">
		<div id="main_content">
		<div id="control_plan">
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
						<label>Søk etter</label>
						<span>
							<a href="loc_type_2" class="btn first active">Bygg</a>
							<a href="loc_type_1" class="btn">Eiendom</a>
						</span>
					</div>
					<input type="text" value="" id="search-location-name" />
				</div>
			</div>

			<div class="middle">

				

				<!-- =====================  COLOR ICON MAP  ================= -->
				<xsl:call-template name="icon_color_map" />

				<!-- =====================  FILTERS  ================= -->
				<xsl:call-template name="calendar_filters" >
					<xsl:with-param name="view_period">year</xsl:with-param>
				</xsl:call-template>

				<!-- =====================  CALENDAR NAVIGATION  ================= -->
				<xsl:call-template name="nav_calendar_year">
					<xsl:with-param name="view">VIEW_CONTROLS_FOR_LOCATION</xsl:with-param>
				</xsl:call-template>
			</div>

			<div id="cal_wrp">
				<h2>Bygg/eiendom</h2>
				<table id="calendar" class="year">
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
								<xsl:variable name="month_str">short_month <xsl:number/> capitalized</xsl:variable>
								<xsl:value-of select="php:function('lang', $month_str)" />
							</th>
						</xsl:for-each>
					</tr>
					<xsl:for-each select="location_calendar_array">
						<xsl:choose>
							<xsl:when test="controls_calendar_array/child::node()">
								<xsl:for-each select="controls_calendar_array">
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
											<a class="show-control-details">
												<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicontrol.get_control_details</xsl:text>
													<xsl:text>&amp;control_id=</xsl:text>
													<xsl:value-of select="control/id"/>
													<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
													<xsl:value-of select="php:function('get_phpgw_session_url')" />
												</xsl:attribute>
												<xsl:variable name="control_info_params">
													<!--<xsl:text>index.php?menuaction=controller.uicontrol.get_control_details, control_id</xsl:text>-->
													<!--<xsl:value-of select="control/id"/>-->
													<!--<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>-->
													<!--<xsl:value-of select="$session_url"/>-->
												</xsl:variable>
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
														<xsl:value-of select="/location_code"/>
													</xsl:with-param>
													<xsl:with-param name="serie_id"><xsl:value-of select="/serie_id" /></xsl:with-param>
													<xsl:with-param name="session_url"><xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:with-param>
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
					</xsl:for-each>
				</table>

				<h2 class="components">Komponenter</h2>
				<xsl:choose>
					<xsl:when test="components_calendar_array/child::node()">
						<xsl:for-each select="components_calendar_array">
							<h3>
								<xsl:value-of select="component/xml_short_desc"/>
							</h3>

							<table id="calendar" class="year">
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
											<a>
												<xsl:attribute name="href">
													<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
													<xsl:text>&amp;year=</xsl:text>
													<xsl:value-of select="//current_year"/>
													<xsl:text>&amp;location_code=</xsl:text>
													<xsl:value-of select="//current_location/location_code"/>
													<xsl:text>&amp;month=</xsl:text>
													<xsl:number/>
													<xsl:value-of select="$session_url"/>
												</xsl:attribute>

												<xsl:variable name="month_str">short_month <xsl:number/> capitalized</xsl:variable>
												<xsl:value-of select="php:function('lang', $month_str)" />
											</a>
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