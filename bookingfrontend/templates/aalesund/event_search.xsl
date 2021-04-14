<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<div class="event_container">
		<div id="container_event_search col" class="container_event_search">
			<div class="header-container-search">
				<div class="event-title-container">
					<h2 class="upcomming-events">
						<xsl:value-of select="php:function('lang', 'Upcomming Events')"/>
					</h2>
				</div>
			</div>
			<div class="container event-search-container">
				<div class="row justify-content-center">
					<div class="col-8">
						<input id="field_org_name" class="form-control eventSearchInput" aria-label="Large" autocomplete="off">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'Search for organizations')"/>
							</xsl:attribute>
						</input>
						<input id="field_org_id" name="organization_id" type="hidden"/>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="col-4">
						<input id="from" data-bind="" class="form-control dateFilter dateFilterEvent" name="datefilter" aria-label="Large" autocomplete="off">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'From date')"/>
							</xsl:attribute>
						</input>
					</div>
					<div class="col-4">
						<input id="to" data-bind="" class="form-control dateFilter dateFilterEvent" name="datefilter" aria-label="Large" autocomplete="off" value="">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'To date')"/>
							</xsl:attribute>
						</input>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="col-4">
						<input onclick="buildingNameDropDown()" class="form-control dropbtn" id="field_building_name">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'Building or facility name')"/>
							</xsl:attribute>
						</input>
						<div id="buildingNameDropDown" class="dropdown-content">
							<input type="hidden" id="field_building_id"/>
							<div class="dropdown_list_container" id="building_container"/>
						</div>
					</div>
					<div class="col-4">
						<input onclick="buildingTypeDropDown()" class="form-control dropbtn" id="field_type_name" onfocus="this.placeholder.css = ''">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'Building type')"/>
							</xsl:attribute>
						</input>
						<div id="buildingTypeDropDown" class="dropdown-content">
							<input type="hidden" id="field_type_id"/>
							<div class="dropdown_list_container" id="buildingtype_container"></div>
						</div>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="col-4">
						<!-- Remove filter button -->
						<div id="removeEventFilterBtn" class="eventSearch removeFilterBtn" onclick="clearFilters()"><xsl:value-of select="php:function('lang', 'Reset filter')"/></div>
					</div>
				</div>
			</div>
		</div>
		<div class="container event-content-container">
			<div id="event-content">
				<div class="headerText headerResult">
					<div class="headerResultText"><xsl:value-of select="php:function('lang', 'Search results')"/></div>
					<div class="headerResultNumb" data-bind="text: events().length + ' treff'"/>
				</div>
				<div data-bind="foreach: events">
					<div class="eventCard2 row">
						<div class="col-3 col-sm-3 col-md-2 col-lg-2 verticalLineBorder">
							<div class="resultCalIcon"/>
							<span class="resultDate" data-bind="text: formattedDate"/>
							<span class="resultMonth" data-bind="text: monthText"/>
						</div>

						<div class="col-6 col-sm-6 col-md-6 col-lg-7 verticalLineBorder">
							<div class="eventText" data-bind="text: event_name"/>
							<div class="eventTextLocation" data-bind="text: location_name, click:$parent.goToBuilding"/>
						</div>

						<div class="col-3 col-sm-3 col-md-4 col-lg-3">
							<div class="resultClockIcon"/>
							<div class="resultClockText" data-bind="text: event_time"/>
							<div class="orgName" href="#" data-bind="text: org_name, click:$parent.goToOrganization"/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var months = <xsl:value-of select="php:function('js_lang', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')" />;
	</script>
</xsl:template>
