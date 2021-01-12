

<xsl:template match="data" xmlns:php="http://php.net/xsl">
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
            <div class="flex-container headerText">
               Finn fasiliteter/etableringer
               <!--	<xsl:value-of disable-output-escaping="yes" select="frontpagetext"/> -->
            </div>
         </div>
         <!-- Search Container -->
		  <div id="searchContainer">
			  <div id="searchContainerContent">
				  <div id="searchWrapper" class="input-group">
					  <input type="text" id="mainSearchInput" class="form-control searchInput" aria-label="Large">
						  <xsl:attribute name="placeholder">
							  <xsl:value-of select="php:function('lang', 'Search building, resource, organization')"/>
						  </xsl:attribute>
					  </input>
				  </div>
				  <div id="dateLocationFilterWrapper">
					  <div id="locationWrapper">
						  <input type="text" id="locationFilter" class="form-control searchInput" placeholder="Sted" aria-label="Large" list="districtDatalist"/>
						  <datalist id="districtDatalist"></datalist>
					  </div>
					  <div  id="dateWrapper">
						  <input type="text" id="dateFilter" class="form-control searchInput dateFilter" name="datefilter" placeholder="Dato" aria-label="Large" autocomplete="off" value="" />
					  </div>
				  </div>
				  <button id="searchBtn" class="greenBtn">Finn tilgjengelige</button>
			  </div>
		  </div>
		  <div class="pageContentWrapper">
            <div class="eventContainer">
               <div class="headerText headerEvent">
                  Dette skjer i Bergen kommune
               </div>
            </div>
			  <div id="event-content" class="col">
				  <ul data-bind="foreach: events">
					  <div class="event-card">
						  <li>
							  <div class="card-element-left">
								  <div class="formattedDate-container">
									  <div class="cal-img-logo"></div>

									  <span class="formattedDate"  data-bind="text: formattedDate"></span>
									  <span class="monthTag" data-bind="text:monthText"></span>

								  </div>
							  </div>
							  <div class="card-element-mid">
								  <div class="event_name-container">
									  <span class="event_name" data-bind="text: event_name"></span>
								  </div>
								  <div class="event_time-container">
									  <span class="event_time" data-bind="text: event_time"></span>
								  </div>
							  </div>
							  <div class="card-element-right">
								  <div class="location_container" >
									  <div class="pin_img_logo"></div>
									  <a href="#" data-bind="click:$parent.goToBuilding">
										  <span class="location_name" data-bind="text: location_name"></span>
									  </a>
								  </div>
								  <div class ="org_name-container">
									  <div class="fas fa-users"></div>
									  <a href="#" data-bind="click:$parent.goToOrganization">
										  <span class="org_name" data-bind="text: org_name"></span>
									  </a>
								  </div>
							  </div>
						  </li>
					  </div>
				  </ul>
			  </div>
            </div>
      </div>
   </div>
</xsl:template>

