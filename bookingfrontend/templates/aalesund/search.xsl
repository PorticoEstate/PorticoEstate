<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<link href='https://fonts.googleapis.com/css?family=Rubik' rel='stylesheet'/>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css"/>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"/>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"/>
	<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"/>


	<div id="search-page-content">
      <div class="headerSection">
         <div class="noteRectangle">
            <div class="noteTitle">
               Korona-situasjonen
            </div>
            <div class="noteBody">
               Idrettsanlegg og kulturbygg i Øygarden kommune er delvis stengt framover, som eit tiltak mot spreiing av koronaviruset.  Og kan kun brukes etter tilrådningar fra Folkehelsedirektoratet og Øygarden kommune.
               <br /><br />
               Søknadar som vert lagt inn i portalen, vert sakshandsama, men vil bli lengre sakshandsamartid. Følg med her for meir informasjon.
               <br /><br />
               Du kan framleis låne/leiga lokale i portalen fram i tid, med forbehold om at bygga vert opna for bruk etter avstengningsperioden.
            </div>
         </div>
         <div class="descriptionRectangle">
            <div class="noteTitle">
               Tittel om hva tjenesten leverer
            </div>
            <div class="noteBody">
               Her finner du informasjon om kommunale bygg, skular, idrettsanlegg, og utstyr som er til utlån/utleige i Øygarden kommune. Ein del private anlegg, forsamlingshus o.l ligg og i portalen, med kontaktinformasjon til eigarar av bygga. Du kan også søke på lag og organisasjoner i kommunen.
            </div>
         </div>
      </div>



      <!-- Content Container -->
      <div class="jumbotron jumbotron-fluid bodySection">
         <!-- Title -->
		  <div class="titleContainer">
			  <div class="headerText">
				  <xsl:value-of select="php:function('lang', 'Find available resources')"/>
			  </div>
		  </div>

		  <!-- Search Container -->
		  <div id="searchContainer">
			  <div class="input-group bk">
				  <input id="mainSearchInput" class="mainSearchInput" aria-label="Large">
					  <xsl:attribute name="placeholder">
						  <xsl:value-of select="php:function('lang', 'Search available resources')"/>
					  </xsl:attribute>
				  </input>
			  </div>
			  <div>
				  <div id="locationWrapper">
					  <select id="locationFilter" class="form-control locationFilter" aria-label="Large" data-bind="options: towns,
                       optionsText: 'name',
                       value: selectedTown,
                       optionsCaption: 'Velg bydel'"/>
				  </div>
				  <div  id="dateWrapper">
					  <input id="dateFilter" data-bind="value: dateFilter" class="form-control dateFilter" name="datefilter" aria-label="Large" autocomplete="off" value="">
						  <xsl:attribute name="placeholder">
							  <xsl:value-of select="php:function('lang', 'Choose date')"/>
						  </xsl:attribute>
					  </input>
				  </div>
			  </div>
			  <div class="input-group bk">
			  <button id="searchBtn" class="greenBtn"><xsl:value-of select="php:function('lang', 'Find available')"/></button>
			  </div>
		  </div>

		  <div class="pageContentWrapper">

			  <!-- Events -->
			  <div class="eventContainer" data-bind="if: showEvents">
				  <div class="headerText headerEvent">
					  <xsl:value-of select="php:function('lang', 'Happening in Bergen')"/>
				  </div>
				  <div id="event-content">
					  <div data-bind="foreach: events">
						  <div class="eventCard">
							  <div class="card-element-left">
								  <div class="formattedDate-container">
									  <div class="eventCalIcon"/>
									  <span class="formattedEventDate"  data-bind="text: formattedDate"/>
									  <span class="eventMonthTag" data-bind="text:monthText"/>
								  </div>
							  </div>
							  <div class="card-element-mid">
								  <div class="verticalLine"/>
								  <div class="eventNameContainer">
									  <span class="event_name" data-bind="text: event_name"/>
								  </div>
								  <div class="eventTimeContainer">
									  <div class="eventClockIcon"/>
									  <span class="event_time" data-bind="text: event_time"/>
								  </div>
							  </div>
							  <div class="card-element-right">
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
					  <div id="allEventsBtn" class="allEventsBtn" href="#" data-bind="click: $root.goToEvents"><xsl:value-of select="php:function('lang', 'View all events')"/><div class="allEventsIcon"/></div>

				  </div>
			  </div>

			  <!-- Search results -->
			  <div id="searchResults">
				  <div class="headerText headerResult">
					  <div class="headerResultText"><xsl:value-of select="php:function('lang', 'Search results')"/></div>
					  <div class="headerResultNumb" data-bind="text: resources().length + ' treff'"/>
				  </div>
				  <div class="searchResultContainer">
					  <div class="filterContainer">

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
							  	<div class="accordionHeaderText"><xsl:value-of select="php:function('lang', 'Town part')"/></div>
							  	<div data-bind="css: townArrowIcon"/>
							  </div>
							  <div data-bind="if: showTown">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent">
									  <div data-bind="foreach: towns">
										  <label  class="checkboxContainer">
											  <input type="checkbox"
												 data-bind="value: id, checked:$root.selectedTownIds" />
											  <div class="checkmark"/>
											  <span class="checkboxText" data-bind="text: name"/>
										  </label>
									  </div>
									  <label data-bind="if: towns().length == 0">
										  <span class="checkboxText checkboxInvalid"><xsl:value-of select="php:function('lang', 'No available options')"/></span>
									  </label>
								  </div>
							  </div>
						  </div>

						  <!-- Facility filter -->
						  <div class="accordionFilter">
							  <div class="accordionHeader" data-bind="click: toggleFacility">
								  <div class="accordionHeaderText"><xsl:value-of select="php:function('lang', 'Facilities')"/></div>
								  <div data-bind="css: facilityArrowIcon"/>
							  </div>
							  <div data-bind="if: showFacility">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent">
									  <div data-bind="foreach: facilities">
										  <label class="checkboxContainer">
											  <input type="checkbox"
													 data-bind="value: id, checked:$root.selectedFacilityIds" />
											  <div class="checkmark"/>
											  <span class="checkboxText" data-bind="text: name"/>
										  </label>
									  </div>
									  <label data-bind="if: facilities().length == 0">
										  <span class="checkboxText checkboxInvalid"><xsl:value-of select="php:function('lang', 'No available options')"/></span>
									  </label>
								  </div>
							  </div>
						  </div>

						  <!-- Activity filter -->
						  <div class="accordionFilter">
							  <div class="accordionHeader" data-bind="click: toggleActivity">
								  <div class="accordionHeaderText"><xsl:value-of select="php:function('lang', 'Activities (2018)')"/></div>
								  <div data-bind="css: activityArrowIcon"/>
							  </div>
							  <div data-bind="if: (showActivity)">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent">
									  <div data-bind="foreach: activities">
										  <label class="checkboxContainer">
											  <input type="checkbox"
													 data-bind="value: id, checked:$root.selectedActivityIds" />
											  <div class="checkmark"/>
											  <span class="checkboxText" data-bind="text: name"/>
										  </label>
									  </div>
									  <label data-bind="if: activities().length == 0">
										  <span class="checkboxText checkboxInvalid"><xsl:value-of select="php:function('lang', 'No available options')"/></span>
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
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent" data-bind="foreach: gear">
									  <label class="checkboxContainer">
										  <input type="checkbox"
												 data-bind="value: id, checked:$root.selectedGearIds" />
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
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent" data-bind="foreach: capacities">
									  <label class="checkboxContainer">
										  <input type="checkbox"
												 data-bind="value: id, checked:$root.selectedCapacityIds" />
										  <div class="checkmark"/>
										  <span class="checkboxText" data-bind="text: name"/>
									  </label>
								  </div>
							  </div>
						  </div>

						  <!-- Apply filter button -->
						  <div id="applyFilterBtn" class="applyFilterBtn" onclick="findSearchMethod()"><xsl:value-of select="php:function('lang', 'Apply filter')"/></div>

						  <!-- Remove filter button -->
						  <div id="removeFilterBtn" class="removeFilterBtn" onclick="resetFilters()"><xsl:value-of select="php:function('lang', 'Reset filter')"/></div>
					  </div>


					  <div class="resultContainer" data-bind="foreach: resources">
						  <div class="resultCard row">
							  <div class="col-2">
								  <div class="resultCalIcon"/>
								  <span class="resultDate" data-bind="text: date"/>
								  <span class="resultMonth" data-bind="text: month"/>
							  </div>
							  <div class="col-7">
								  <div class="resultText" href="#" data-bind="text: name, click:$parent.goToResource"/>
								  <div class="resultTextLocation" data-bind="text: location"/>
							  </div>
							  <div class="col-3">
								  <div class="resultClockIcon"/>
								  <div class="resultClockText" data-bind="text: time"/>
								  <div class ="resultBtnText" href="#" data-bind="click:$parent.goToApplication"><xsl:value-of select="php:function('lang', 'To application site')"/></div>
							  </div>
						  </div>
					  </div>
				  </div>

			  </div>
		  </div>
      </div>
   </div>
</xsl:template>

