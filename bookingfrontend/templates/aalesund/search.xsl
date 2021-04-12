<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="search-page-content">
		<div class="headerSection">
			<xsl:if test="frontimagetext">
				<div class="noteRectangle">
					<div class="noteBody">
						<xsl:value-of disable-output-escaping="yes" select="frontimagetext"/>
					</div>
				</div>
			</xsl:if>
			<div class="descriptionRectangle">
				<div class="noteBody">
					<xsl:value-of disable-output-escaping="yes" select="frontpagetext"/>
				</div>
			</div>
		</div>

		<div class="bodySection">
			<!-- Title -->
			<div class="titleContainer">
				<div class="headerText">
					<xsl:value-of select="php:function('lang', 'Find available resources')"/>
				</div>
			</div>
			<div class="containerSearch">
				<div class="pageContentWrapper">
					<div class="row justify-content-center">
						<div class="bk col-md-6 col-sm-8 col-12">
							<div class="input-group bk">
								<input type="search" id="mainSearchInput" class="mainSearchInput" aria-label="Large">
									<xsl:attribute name="placeholder">
										<xsl:value-of disable-output-escaping="yes" select="frontpagetitle"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
					<div class="row justify-content-center">
						<div class="col-md-3  col-sm-4 col-6">
							<div id="locationWrapper">
								<select id="locationFilter" class="form-control locationFilter" aria-label="Large" data-bind="options: towns,
						   optionsText: 'name',
						   value: selectedTown,
						   optionsCaption: 'Område/bydel'"/>
							</div>
						</div>
						<div class="col-md-3 col-sm-4 col-6">
							<div id="dateWrapper">
								<input id="dateFilter" data-bind="value: dateFilter" class="form-control dateFilter" name="datefilter" aria-label="Large" autocomplete="off"
									   value="">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Choose date')"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
					</div>
					<div class="input-group bk">
						<button id="searchBtn" class="greenBtn">
							<xsl:value-of select="php:function('lang', 'Find available')"/>
						</button>
					</div>
					<div data-bind="if: showResults">
						<div id="clearSearchBtn" class="clearSearchBtn" onclick="clearSearch()">
							<xsl:value-of select="php:function('lang', 'Reset filter')"/>
						</div>
					</div>
				</div>
			</div>

			<!-- Events -->
			<div class="pageContentWrapper">
				<div class="headerText headerEvent">
					<xsl:value-of select="php:function('lang', 'Happening in Bergen')"/>
				</div>
				<div data-bind="foreach: events">
					<div class="eventCard2 row">
						<div class="col-3 col-sm-2 col-md-2 col-lg-1 verticalLineBorder">
							<div class="resultCalIcon"/>
							<span class="resultDate" data-bind="text: formattedDate"/>
							<span class="resultMonth" data-bind="text: monthText"/>
						</div>

						<div class="col-6 col-sm-7 col-md-6 col-lg-8 verticalLineBorder">
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
				<div class="row justify-content-end">
					<div class="col-4">
						<div id="allEventsBtn" class="allEventsBtn" href="#" data-bind="click: $root.goToEvents">
							<xsl:value-of select="php:function('lang', 'View all events')"/>
							<div class="allEventsIcon"/>
						</div>
					</div>
				</div>
				<div id="searchResults">
					<div class="headerText headerResult">
						<div class="headerResultText">
							<xsl:value-of select="php:function('lang', 'Search results')"/>
						</div>
						<div class="headerResultNumb" data-bind="text: resources().length + ' treff'"/>
					</div>
					<div class="row">
						<div class="col-md-4 offset-md-0 col-sm-8 offset-sm-2">
							<div class="filterContainer2">
								<input data-bind="value: dateFilter" class="form-control dateFilterResult" name="datefilter" aria-label="Large" autocomplete="off">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Choose date')"/>
									</xsl:attribute>
								</input>
								<div class="timeContainer">
									<input type="" id="fromTime" class="form-control timeInput" aria-label="Large" autocomplete="off">
										<xsl:attribute name="placeholder">
											<xsl:value-of select="php:function('lang', 'From time')"/>
										</xsl:attribute>
									</input>
									<div class="horizontalgap"></div>
									<input type="" id="toTime" class="form-control timeInput" aria-label="Large" autocomplete="off">
										<xsl:attribute name="placeholder">
											<xsl:value-of select="php:function('lang', 'To time')"/>
										</xsl:attribute>
									</input>
								</div>

								<!-- Town filter -->
								<div class="accordionFilter">
									<div class="accordionHeader" data-bind="click: toggleTown">
										<div class="accordionHeaderText">
											<xsl:value-of select="php:function('lang', 'Town part')"/>
										</div>
										<div data-bind="css: townArrowIcon"/>
									</div>
									<div data-bind="if: showTown">
										<div class="accordionHeaderUnderline"/>
										<div class="accordionContent">
											<div data-bind="foreach: towns">
												<label class="checkboxContainer">
													<input type="checkbox" data-bind="value: id, checked:$root.selectedTownIds"/>
													<div class="checkmark"/>
													<span class="checkboxText" data-bind="text: name"/>
												</label>
											</div>
											<label data-bind="if: towns().length == 0">
												<span class="checkboxText checkboxInvalid">
													<xsl:value-of select="php:function('lang', 'No available options')"/>
												</span>
											</label>
										</div>
									</div>
								</div>

								<!-- Facility filter -->
								<div class="accordionFilter">
									<div class="accordionHeader" data-bind="click: toggleFacility">
										<div class="accordionHeaderText">
											<xsl:value-of select="php:function('lang', 'Facilities')"/>
										</div>
										<div data-bind="css: facilityArrowIcon"/>
									</div>
									<div data-bind="if: showFacility">
										<div class="accordionHeaderUnderline"/>
										<div class="accordionContent">
											<div data-bind="foreach: facilities">
												<label class="checkboxContainer">
													<input type="checkbox" data-bind="value: id, checked:$root.selectedFacilityIds, enable: enabled"/>
													<div class="checkmark"/>
													<span class="checkboxText" data-bind="text: name"/>
												</label>
											</div>
											<label data-bind="if: facilities().length == 0">
												<span class="checkboxText checkboxInvalid">
													<xsl:value-of select="php:function('lang', 'No available options')"/>
												</span>
											</label>
										</div>
									</div>
								</div>

								<!-- Activity filter -->
								<div class="accordionFilter">
									<div class="accordionHeader" data-bind="click: toggleActivity">
										<div class="accordionHeaderText">
											<xsl:value-of select="php:function('lang', 'Activities (2018)')"/>
										</div>
										<div data-bind="css: activityArrowIcon"/>
									</div>
									<div data-bind="if: (showActivity)">
										<div class="accordionHeaderUnderline"/>
										<div class="accordionContent">
											<div data-bind="foreach: activities">
												<label class="checkboxContainer">
													<input type="checkbox" data-bind="value: id, checked:$root.selectedActivityIds, enable: enabled"/>
													<div class="checkmark"/>
													<span class="checkboxText" data-bind="text: name"/>
												</label>
											</div>
											<label data-bind="if: activities().length == 0">
												<span class="checkboxText checkboxInvalid">
													<xsl:value-of select="php:function('lang', 'No available options')"/>
												</span>
											</label>
										</div>
									</div>
								</div>

								<!-- Gear filter -->
								<div class="accordionFilter" data-bind="if: gear().length > 0">
									<div class="accordionHeader" data-bind="click: toggleGear">
										<div class="accordionHeaderText">Utstyr</div>
										<div data-bind="css: gearArrowIcon"/>
									</div>
									<div data-bind="if: showGear">
										<div class="accordionHeaderUnderline"/>
										<div class="accordionContent" data-bind="foreach: gear">
											<label class="checkboxContainer">
												<input type="checkbox"
													   data-bind="value: id, checked:$root.selectedGearIds"/>
												<div class="checkmark"/>
												<span class="checkboxText" data-bind="text: name"/>
											</label>
										</div>
									</div>
								</div>

								<!-- Capacity filter -->
								<div class="accordionFilter" data-bind="if: capacities().length > 0">
									<div class="accordionHeader" data-bind="click: toggleCapacity">
										<div class="accordionHeaderText">Kapasitet</div>
										<div data-bind="css: capacityArrowIcon"/>
									</div>
									<div data-bind="if: showCapacity">
										<div class="accordionHeaderUnderline"/>
										<div class="accordionContent" data-bind="foreach: capacities">
											<label class="checkboxContainer">
												<input type="checkbox"
													   data-bind="value: id, checked:$root.selectedCapacityIds"/>
												<div class="checkmark"/>
												<span class="checkboxText" data-bind="text: name"/>
											</label>
										</div>
									</div>
								</div>

								<!-- Apply filter button -->
								<!--<div id="applyFilterBtn" class="applyFilterBtn" onclick="findSearchMethod()"><xsl:value-of select="php:function('lang', 'Apply filter')"/></div>-->

								<!-- Remove filter button -->
								<div id="removeFilterBtn" class="removeFilterBtn" onclick="resetFilters()">
									<xsl:value-of select="php:function('lang', 'Reset filters')"/>
								</div>
							</div>
						</div>
						<div class="col-md-8 col-sm-12">
							<div class="resultContainer">
								<div data-bind="foreach: resources">
									<div class="resultCard row">
										<div class="col-3 col-sm-2">
											<div class="resultCalIcon"/>
											<span class="resultDate" data-bind="text: date"/>
											<span class="resultMonth" data-bind="text: month"/>
										</div>
										<div class="col-6 col-sm-7 col-md-6 col-lg-7">
											<div class="resultText" href="#" data-bind="text: name, click:$parent.goToResource"/>
											<div class="resultTextLocation" data-bind="text: location"/>
										</div>
										<div class="col-3 col-md-4 col-lg-3">
											<div class="resultClockIcon"/>
											<div class="resultClockText" data-bind="text: time"/>
											<div class="resultBtnText" href="#" data-bind="click:$parent.goToApplication">
												<xsl:value-of select="php:function('lang', 'To application site')"/>
											</div>
										</div>
									</div>
								</div>
								<div class="showMoreContainer" data-bind="if: resources().length === limit">
									<span class="showMoreText" onclick="showMore()">
										<xsl:value-of select="php:function('lang', 'Show more results')"/>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		var months = [
		'<xsl:value-of select="php:function('lang', 'January')"/>', '<xsl:value-of select="php:function('lang', 'February')"/>', '<xsl:value-of
			select="php:function('lang', 'March')"/>',
		'<xsl:value-of select="php:function('lang', 'April')"/>', '<xsl:value-of select="php:function('lang', 'May')"/>', '<xsl:value-of select="php:function('lang', 'June')"/>',
		'<xsl:value-of select="php:function('lang', 'July')"/>', '<xsl:value-of select="php:function('lang', 'August')"/>', '<xsl:value-of
			select="php:function('lang', 'September')"/>',
		'<xsl:value-of select="php:function('lang', 'October')"/>', '<xsl:value-of select="php:function('lang', 'November')"/>', '<xsl:value-of
			select="php:function('lang', 'December')"/>',
		];
	</script>
</xsl:template>

