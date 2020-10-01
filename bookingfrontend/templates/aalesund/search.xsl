<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	
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
                                                            <div  id="searchWrapper">
                             <input type="text" id="mainSearchInput" class="form-control searchInput" aria-label="Large">
						<xsl:attribute name="placeholder">
                                                    <xsl:value-of select="php:function('lang', 'Search building, resource, organization')"/>
						</xsl:attribute>
					</input>
                            </div>
                             <div id="dateLocationFilterWrapper">
                                 <div  id="locationWrapper">
                                   <input type="text" id="locationFilter" class="form-control searchInput" placeholder="Sted" aria-label="Large"></input>  
                                 </div>
                                 <div  id="dateWrapper">
                                   <input type="text" id="mainDateFilter" class="form-control searchInput dateFilter" placeholder="Dato" aria-label="Large"></input>
                                 </div>
                            </div>
                            <button id="searchBtn" class="greenBtn">Finn tilgjengelige</button> 
                            </div>
                        </div>
                        <div class="pageContentWrapper">
                        <div class="titleContainer">
				<div class="headerText">
                                    Dette skjer i Bergen kommune
				</div>
                        </div>
                        <div class="activityList" data-bind="foreach: upcommingevents">
                            <div class="activityRow">
                             <span class="activityDate activityText boldText activityHeaderSegment"><b class="event_datetime_day"></b>. <b data-bind="text: datetime_month"></b></span>
                             <span class="activityTitle activityText boldText activityHeaderSegment"> 
                                 <a class="upcomming-event-href" href="" target="_blank">
                                     <span  data-bind="text: name"></span>
                                 </a>
                             </span>
                              <span class="activityTime activityHeaderSegment" data-bind="text: datetime_time"></span>
                              <div class="activityLocation activityHeaderSegment"><div data-bind="text: building_name"></div><div data-bind="text: organizer"></div></div>
                            </div>
                        </div>
                        
                        <div id="searchResultsWrapper" style="display:none">
                          <div id="searchResutsHeader">  Søkeresultat  <div id="resultCount">2 treff</div></div> 
                          <hr />
                          <div id="searchResultsContainer">
                          <div id="searchResultMenu">
                              <input type="text" id="sideDateFilter" class="form-control searchInput dateFilter" placeholder="Dato" aria-label="Large" />
                              <div id="timeFilterContainer">
                              <input type="text" id="from_time" class="form-control searchInput timeFilter" placeholder="Fra kl" aria-label="Large" />
                              <input type="text" id="to_time" class="form-control searchInput timeFilter" placeholder="Til kl" aria-label="Large" />
                              </div>
                              <div class="collapseModal">
                                 <div class="modalHeader">Bydel</div>
                                 <div class="modalBody">
                                     <div class="checkWrapper">
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Bydel 1</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Bydel 2</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Bydel 3</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Bydel 4</label></div>
                                     </div>
                                 </div>
                              </div>
                              
                               <div class="collapseModal">
                                 <div class="modalHeader">Fasiliteter</div>
                                 <div class="modalBody">
                                     <div class="checkWrapper">
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Garderobe</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Internett</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Kjøkken</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Rullestolrampe</label></div>
                                         <div><input type="checkbox" value="bydel_value_her" /><label>Kunst og designverksted</label></div>
                                        <div><input type="checkbox" value="bydel_value_her" /><label>Speaker-anlegg</label></div>
                                     </div>
                                 </div>
                              </div>
                              
                             <div class="collapseModal">
                                 <div class="modalHeader">Tilrettelagt for</div>
                                 <div class="modalBody">
                                     
                                 </div>
                              </div>
                            <div class="collapseModal">
                                 <div class="modalHeader">Utstyr</div>
                                 <div class="modalBody">
                                     
                                 </div>
                              </div>
                             <div class="collapseModal">
                                 <div class="modalHeader">Kapasitet</div>
                                 <div class="modalBody">
                                     
                                 </div>
                              </div>
                              <button class="greenBtn filterButton" id="aktiverFilterBtn">Aktiver filter</button>
                              <button class="grayBtn filterButton" id="nullstillFilterBtn">Nullstill filter</button>
                          </div>
                          <div id="searchResultList">    
                                <div class="facilityRow">
                             <div class="activityTitle activityText boldText activityHeaderSegment"> 
                                 Testanlegg
                             </div>
                              <br /><div>Addresse her</div>
                                </div>
                                  <div class="facilityRow">
                             <div class="activityTitle activityText boldText activityHeaderSegment"> 
                                 Testanlegg 2
                             </div>
                              <br /><div>Addresse 2 her</div>
                                </div>
                          </div>
                          </div>
                        </div>
                        </div>
	
		</div>
		
	</div>
</xsl:template>