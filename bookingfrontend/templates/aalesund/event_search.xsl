<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<div class="event_container">
		<div id="container_event_search col" class="container_event_search">
			<div class="header-container-search">
				<div class="col">
					<button onclick="toggleMyOrgs()" class="my_orgs_button" id="my_orgs_button" style="display='none';">
						<i id="my_orgs_icon" class="far fa-circle"/>
						<xsl:value-of select="php:function('lang', 'Show my events')" />
					</button>
				</div>
				<div class="event-title-container">
					<h2 class="upcomming-events">
						<xsl:value-of select="php:function('lang', 'Upcomming Events (2021)')"/>
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
					<div class="row eventCard">
						<div class="col-2">
							<div class="cardElementLeft">
								<div class="formattedDate-container">
									<div class="eventCalIcon"/>
									<span class="formattedEventDate"  data-bind="text: formattedDate"/>
									<span class="eventMonthTag" data-bind="text:monthText"/>
								</div>
							</div>
						</div>
						<div class="col-5">
							<div class="verticalLine"/>
							<div class="eventNameContainer">
								<span class="event_name" data-bind="text: event_name"/>
							</div>
						</div>
						<div class="col-2">
							<div class="eventTimeContainer">
								<div class="eventClockIcon"/>
								<span class="event_time" data-bind="text: event_time"/>
							</div>
						</div>
						<div class="col-3">
							<div class="verticalLine"/>
							<div class="locationContainer" >
								<a href="#" data-bind="click:$parent.goToBuilding">
									<span class="locationName" data-bind="text: location_name"/>
								</a>
							</div>
							<div class ="orgNameContainer">
								<a href="#" data-bind="click:$parent.goToOrganization">
									<span class="orgName" data-bind="text: org_name"/>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</xsl:template>
