

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<link href='https://fonts.googleapis.com/css?family=Rubik' rel='stylesheet'/>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
				  Finn fasiliteter/etableringer
							  <!--	<xsl:value-of disable-output-escaping="yes" select="frontpagetext"/> -->
			  </div>
		  </div>

		  <!-- Search Container -->
		  <div id="searchContainer">
			  <div class="input-group bk">
				  <input id="mainSearchInput" class="mainSearchInput" aria-label="Large">
					  <xsl:attribute name="placeholder">
						  <xsl:value-of select="php:function('lang', 'Search building, resource, organization')"/>
					  </xsl:attribute>
				  </input>
			  </div>
			  <div>
				  <div id="locationWrapper">
					  <input id="locationFilter" class="form-control locationFilter" placeholder="Bydel" aria-label="Large" list="townDatalist"/>
					  <datalist id="townDatalist" data-bind="foreach: towns">
						  <option data-bind="text: name"></option>
					  </datalist>
				  </div>
				  <div  id="dateWrapper">
					  <input id="dateFilter" class="form-control dateFilter" name="datefilter" placeholder="Velg dato" aria-label="Large" autocomplete="off" value="" />
				  </div>
			  </div>
			  <button id="searchBtn" class="greenBtn">Finn tilgjengelig</button>
		  </div>

		  <div class="pageContentWrapper">

			  <!-- Events -->
			  <div class="eventContainer" data-bind="if: showEvents">
				  <div class="headerText headerEvent">
					  Dette skjer i Bergen kommune
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
					  <div id="allEventsBtn" class="allEventsBtn" href="#" data-bind="click: $root.goToEvents">Se alle arrangement <div class="allEventsIcon"/></div>

				  </div>
			  </div>

			  <!-- Search results -->
			  <div id="searchResults">
				  <div class="headerText headerResult">
					  <div class="headerResultText">Søkeresultat</div>
					  <div class="headerResultNumb" data-bind="text: resources().length + ' treff'"/>
				  </div>
				  <div class="searchResultContainer">
					  <div class="filterContainer">

						  <input id="dateFilter2" class="form-control dateFilterResult" name="datefilter" aria-label="Large" autocomplete="off" placeholder="Velg dato"/>

						  <div class="timeContainer">
							  <input type="" id="fromTime" class="form-control timeInput" name="timefilter" aria-label="Large" autocomplete="off" placeholder="Fra kl"/>
							  <div class="horizontalgap"></div>
							  <input type="" id="toTime" class="form-control timeInput" name="timefilter" aria-label="Large" autocomplete="off" placeholder="Til kl"/>
						  </div>

						  <!-- Town filter -->
						  <div class="accordionFilter">
							  <div class="accordionHeader" data-bind="click: toggleTown">
							  	<div class="accordionHeaderText">Bydel</div>
							  	<div data-bind="css: townArrowIcon"/>
							  </div>
							  <div data-bind="if: showTown">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent" data-bind="foreach: towns">
									  <label class="checkboxContainer">
										  <input type="checkbox"
											 data-bind="value: id, checked:$root.selectedTownIds" />
										  <div class="checkmark"/>
										  <span class="checkboxText" data-bind="text: name"/>
									  </label>
								  </div>
							  </div>
							  <ul data-bind="foreach: selectedTowns">
								  <li data-bind="text: name"/>
							  </ul>
						  </div>

						  <!-- Facility filter -->
						  <div class="accordionFilter">
							  <div class="accordionHeader" data-bind="click: toggleFacility">
								  <div class="accordionHeaderText">Fasiliteter</div>
								  <div data-bind="css: facilityArrowIcon"/>
							  </div>
							  <div data-bind="if: showFacility">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent" data-bind="foreach: facilities">
									  <label class="checkboxContainer">
										  <input type="checkbox"
												 data-bind="value: id, checked:$root.selectedFacilityIds" />
										  <div class="checkmark"/>
										  <span class="checkboxText" data-bind="text: name"/>
									  </label>
								  </div>
							  </div>

							  <ul data-bind="foreach: selectedFacilities">
								  <li data-bind="text: name"/>
							  </ul>
						  </div>

						  <!-- Activity filter -->
						  <div class="accordionFilter">
							  <div class="accordionHeader" data-bind="click: toggleActivity">
								  <div class="accordionHeaderText">Tilrettelagt for</div>
								  <div data-bind="css: activityArrowIcon"/>
							  </div>
							  <div data-bind="if: showActivity">
								  <div class="accordionHeaderUnderline" />
								  <div class="accordionContent" data-bind="foreach: activities">
									  <label class="checkboxContainer">
										  <input type="checkbox"
												 data-bind="value: id, checked:$root.selectedActivityIds" />
										  <div class="checkmark"/>
										  <span class="checkboxText" data-bind="text: name"/>
									  </label>
								  </div>
							  </div>
						  </div>

						  <!-- Gear filter -->
						  <div class="accordionFilter">
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
						  <div class="accordionFilter">
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
						  <div id="applyFilterBtn" class="applyFilterBtn">Aktiver filter</div>

						  <!-- Remove filter button -->
						  <div id="removeFilterBtn" class="removeFilterBtn">Nullstill filter</div>
					  </div>


					  <div class="resultContainer" data-bind="foreach: resources">
						  <div class="resultCard">
							  <div class="resultText">Tilgjengelig lokale innenfor satte filter</div>
							  <div class="resultTextLocation" data-bind="text: location"></div>
						  </div>
					  </div>
				  </div>

			  </div>
		  </div>

		  <div id="searchResult" data-bind="if: showSearchText">
			  <h1 class="text-center result-title">
				  <xsl:value-of select="php:function('lang', 'Search results')"/> (<span data-bind="text: items().length"></span>)</h1>
			  <div class="row" id="result-items" data-bind="foreach: items">
				  <div class="col-lg-6 card-positioncorrect">
					  <a class="custom-card-link-href" data-bind="">
						  <div class="row custom-card">
							  <div class="col-3 date-circle">
								  <!--<img width="90" height="90" data-bind="" class="result-icon-image"/>-->
								  <svg width="90" height="90">
									  <circle cx="45" cy="45" r="37" class="circle" />
									  <text x="50%" y="50%" text-anchor="middle" font-size="14px" fill="white" font-family="Arial" font-weight="bold" dy=".3em" data-bind="text: resultType">>
									  </text>
								  </svg>
							  </div>
							  <div class="col-9 desc">
								  <div class="desc">
									  <h2 class="font-weight-bold" data-bind="html: name"></h2>
									  <span data-bind="html: street"></span>
									  <span class="d-block" data-bind="html: postcode"></span>
								  </div>
								  <div data-bind="foreach: tagItems">
									  <span class="badge badge-pill badge-default" data-bind="text: $rawData, click: selectThisTag" ></span>
								  </div>
							  </div>
						  </div>
					  </a>
				  </div>
			  </div>
		  </div>
      </div>
   </div>
</xsl:template>

