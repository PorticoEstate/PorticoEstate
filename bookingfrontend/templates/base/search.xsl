<xsl:template match="data" xmlns:php="http://php.net/xsl">
   
    <div class="jumbotron jumbotron-fluid">
        <div class="container searchContainer my-container-top-fix">
            <h2 class="text-center font-weight-bold">Bygg og lokaler til utleie</h2>
            
            <p class="text-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <div class="input-group input-group-lg">
                <input type="text" id="mainSearchInput" class="form-control searchInput" aria-label="Large" aria-describedby="inputGroup-sizing-sm" placeholder="Søk sted, hall, aktivitet, utstyr el"/>
                <div class="input-group-prepend">
                    <button class="input-group-text searchBtn" id="inputGroup-sizing-lg" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div id="search-autocomplete"></div>
        </div>
        
    </div>
  
    <div class="container pageResults">
        
        <!-- FILTER BOXES> -->
        <div class="row" data-bind="if: filterboxes().length > 0">
            <div data-bind="foreach: filterboxes">
                    <div class="dropdown d-inline-block mr-2">
                        <button class="btn btn-secondary dropdown-toggle d-inline" data-bind="text: filterboxCaption" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                        </button>
                        <div class="dropdown-menu" data-bind="foreach: filterbox" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" data-bind="text: filterboxOption, id: filterboxOptionId, click: $root.filterboxSelected" href="#"></a>
                        </div>
                    </div>            
            </div>
        </div>

        <div class="row mt-3" data-bind="if: selectedFilterbox">
            <div class="dropdown d-inline-block" data-bind="if: activities().length > 0">
                <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Aktiviteter      
                </button>
                <div class="dropdown-menu" data-bind="foreach: activities" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" data-bind="text: activityOption, id: activityOptionId, click: $root.activitySelected" href="#"></a>
                </div>
            </div>

            <div class="dropdown d-inline-block" data-bind="if: facilities().length > 0">
                <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Fasiliteter      
                </button>
                <div class="dropdown-menu" data-bind="foreach: facilities" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item d-inline" data-bind="text: facilityOption, id: facilityOptionId, click: $root.facilitySelected" href="#">
                         </a><span class="d-inline" data-bind="if: selected">&#10004;</span>
                </div>
            </div>

            <div class="dropdown d-inline-block" data-bind="if: towns().length > 0">
                <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Bydel      
                </button>
                <div class="dropdown-menu" data-bind="foreach: towns" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" data-bind="text: townOption, id: townOptionId, click: $root.townSelected" href="#"></a>
                </div>
            </div>

        </div>

        <!-- UPCOMMING ARRAGEMENTS -->
        <div id="welcomeResult">
            <h1 class="text-center result-title">Dette skjer i Stavanger</h1>

            <div class="row">
                <div class="col-lg-6">
                    <a href="/PorticoEstate/bookingfrontend/?menuaction=bookingfrontend.uibuilding.show" class="custom-card-link">
                        <div class="row custom-card">
                            <div class="col-3 date-circle">
                                <svg width="90" height="90">
                                    <circle cx="45" cy="45" r="37" fill="#008DD1" />
                                    <text x="50%" y="40%" text-anchor="middle" font-size="40px" fill="white" font-family="Arial" font-weight="bold" dy=".3em">
                                        22
                                    </text>
                                    <text x="50%" y="70%" text-anchor="middle" fill="white" font-family="Arial" dy=".3em">
                                        MARS
                                    </text>
                                </svg>
                            </div>
                            <div class="col-8 desc">
                                <h5 class="font-weight-bold">Foballturnering</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                <span>19:00-15:00</span>
                                <span>STED: TASTAHALLEN</span>
                                <span>ARRANGØR: TUFTE IL</span>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-lg-6">
                    <a href="/PorticoEstate/bookingfrontend/?menuaction=bookingfrontend.uibuilding.show" class="custom-card-link">
                        <div class="row custom-card">
                            <div class="col-3 date-circle">
                                <svg width="90" height="90">
                                    <circle cx="45" cy="45" r="37" fill="#008DD1" />
                                    <text x="50%" y="40%" text-anchor="middle" font-size="40px" fill="white" font-family="Arial" font-weight="bold" dy=".3em">
                                        22
                                    </text>
                                    <text x="50%" y="70%" text-anchor="middle" fill="white" font-family="Arial" dy=".3em">
                                        MARS
                                    </text>
                                </svg>
                            </div>
                            <div class="col-8 desc">
                                <h5 class="font-weight-bold">Foballturnering</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                <span>19:00-15:00</span>
                                <span>STED: TASTAHALLEN</span>
                                <span>ARRANGØR: TUFTE IL</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        
        </div>
        
        <!-- SEARCH RESULT -->
        <div id="searchResult" class="invisible">
            <h1 class="text-center result-title">Søkeresultat (<span data-bind="text: items().length"></span>)</h1>
           
            <div class="row" id="result-items" data-bind="foreach: items">
                <div class="col-lg-6">
                    <a class="custom-card-link-href" data-bind="">
                        <div class="row custom-card">
                            <div class="col-3 date-circle">
                                <!--<img width="90" height="90" data-bind="" class="result-icon-image"/>-->
                                
                                <svg width="90" height="90">
                                    <circle cx="45" cy="45" r="37" fill="#008DD1" />
                                    <text x="50%" y="50%" text-anchor="middle" font-size="40px" fill="white" font-family="Arial" font-weight="bold" dy=".3em" data-bind="text: resultType">>
                                        
                                    </text>
                                    
                                </svg>
                                                               
                            </div>
                            <div class="col-8 desc">
                                <span class="font-weight-bold" data-bind="text: name"></span>
                                <h4 class="font-weight-bold" data-bind="text: activity_name"></h4>
                                <div data-bind="foreach: tagItems">
                                    <span class="badge badge-pill badge-default text-uppercase" data-bind="text: $rawData, click: selectThisTag" ></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        
        </div>

        
        <!-- FILTER SEARCH RESULT -->
        <div id="filterSearchResult" data-bind="if: filterSearchItems().length > 0">
            
            <div class="row" data-bind="foreach: filterSearchItems">
                <div class="col-lg-6">
                    <a class="custom-card-link-href" data-bind="">
                        <div class="row custom-card">
                            <div class="col-3 date-circle">
                                <!--<img width="90" height="90" data-bind="" class="result-icon-image"/>-->
                                
                                <svg width="90" height="90">
                                    <circle cx="45" cy="45" r="37" fill="#008DD1" />
                                    <text x="50%" y="50%" text-anchor="middle" font-size="40px" fill="white" font-family="Arial" font-weight="bold" dy=".3em" data-bind="text: resultType">>
                                        
                                    </text>
                                    
                                </svg>
                                                               
                            </div>
                            <div class="col-8 desc">
                                <h4 class="font-weight-bold" data-bind="text: name"></h4>
                                <span data-bind="text: street"></span>
                                <span class="d-block" data-bind="text: postcode"></span>
                            </div>

                        </div>
                        
                    </a>
                    
                    <div class="row" style="width: 100%" data-bind="foreach: filterSearchItemsResources">
                        <div class="custom-subcard">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="font-weight-bold" data-bind="text: name"></h5>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-light float-right">Book</button>
                                        </div>
                                    </div>
                                    <div data-bind="foreach: facilities">
                                        <span class="tagTitle" data-bind="if: $index() == 0">Fasiliteter: </span>
                                        <span class="mr-2 textTagsItems" data-bind="text: name" ></span>
                                    </div>
                                    <div data-bind="foreach: activities">
                                    <span class="tagTitle" data-bind="if: $index() == 0">Aktiviteter: </span>
                                        <span class="mr-2 textTagsItems" data-bind="text: name" ></span>
                                    </div>
                                </div>
                    </div>

                </div>
            </div>
        
        </div>
        
    </div>
          
          
      
        <script type="text/javascript">
            
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/search.js";
            document.head.appendChild(script);			
        </script>
  
</xsl:template>
