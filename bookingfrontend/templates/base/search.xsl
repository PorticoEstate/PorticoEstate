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
        
        <div class="row justify-content-center">
            <div class="col-auto dropdown">
                <select class="btn btn-default dropdown-toggle custom-select" data-bind="options: firstLevel, optionsText: 'text', value: selectedFirstLevel, optionsCaption: 'Velg'">
                </select>
            </div>
            <div class="col-auto dropdown" data-bind="with: selectedFirstLevel">
                <select class="btn btn-default dropdown-toggle custom-select" data-bind="options: secondLevel, optionsText: 'text', value: $root.selectedFirstList, optionsCaption: 'Velg'">
                </select>
            </div>

        </div>
        

    </div>
  
    <div class="container pageResults">
        
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
        
        <div id="searchResult" class="invisible">
            <h1 class="text-center result-title">Søkeresultat (<span data-bind="text: filteredItems().length"></span>)</h1>
            
            <div class="row filter-bar">
                <div class="col-auto dropdown">
                    <!--Filter based results -->
                    <select id="filterActivity" class="custom-select" data-bind="options: filters, value: filter"></select>
                </div>

                <div class="col-auto dropdown">
                    <!-- Filter based results -->
                    <select id="filterDist" class="custom-select" data-bind="options: filtersDist, value: filterDist"></select>
                </div>

            </div>
            
            <div class="row" id="result-items" data-bind="foreach: filteredItems">
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
        
    </div>
          
          
      
        <script type="text/javascript">
            
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/search.js";
            document.head.appendChild(script);			
        </script>
  
</xsl:template>
