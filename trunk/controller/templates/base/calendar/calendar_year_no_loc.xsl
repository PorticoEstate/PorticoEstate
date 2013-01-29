<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
<xsl:variable name="month_str">month <xsl:value-of select="current_month_nr"/> capitalized</xsl:variable>
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

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

	var oArgs = {menuaction:'controller.uicalendar.view_calendar_for_year'};
	var baseUrl = phpGWLink('index.php', oArgs, false);
	var requestUrl = baseUrl +  "&amp;location_code=" + value + "&amp;year=" + currentYear + "&amp;month=" + currentMonth;

	window.location.replace(requestUrl);
}

</xsl:text>
</script>

<div id="main_content">
	<div id="control_plan" class="month_view">
		<div class="top">
      
      <h1>Eiendom/bygg ikke valgt</h1>  
			<h3>Årsoversikt</h3>

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

					<select id="choose_my_location" class="selectLocation">
						<option>Velg bygg</option>
						<xsl:for-each select="my_locations">
								<xsl:choose>
									<xsl:when test="location_code = //current_location/location_code">
										<option selected="SELECTED">
											<xsl:attribute name="value"><xsl:value-of disable-output-escaping="yes" select="location_code"/></xsl:attribute>
											<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option>
											<xsl:attribute name="value"><xsl:value-of disable-output-escaping="yes" select="location_code"/></xsl:attribute>
											<xsl:value-of disable-output-escaping="yes" select="loc1_name"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>
						</xsl:for-each>
					</select>
				</form>
			</div>
		</div>
	</div>
</div>
</xsl:template>
