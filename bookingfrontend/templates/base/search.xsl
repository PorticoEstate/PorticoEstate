<xsl:template match="data" xmlns:php="http://php.net/xsl">
   <div id="search-page-content">
    <div class="jumbotron jumbotron-fluid">
        <div class="container searchContainer">            
            <h2 class="text-center font-weight-bold"><xsl:value-of disable-output-escaping="yes" select="frontpagetitle"/></h2>
            
            <div class="text-center mt-4 mb-5">
				<xsl:value-of disable-output-escaping="yes" select="frontpagetext"/>
			</div>
            <div class="input-group input-group-lg">
					<input type="text" id="mainSearchInput" class="form-control searchInput" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
						<xsl:attribute name="placeholder">
							<xsl:value-of select="php:function('lang', 'Search building, resource, organization')"/>
						</xsl:attribute>
					</input>
                <div class="input-group-prepend">
                    <button class="input-group-text searchBtn" id="inputGroup-sizing-lg" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div id="search-autocomplete"></div>

             <!-- FILTER BOXES> -->
				<h5 class="mt-5 font-weight-bold"><xsl:value-of select="filterboxtitle"/></h5>
            <div class="row mx-auto" data-bind="if: filterboxes().length > 0">
                <div data-bind="foreach: filterboxes">
                        <div class="dropdown d-inline-block mr-2">
                            <button class="btn btn-secondary dropdown-toggle d-inline" data-bind="text: filterboxCaption" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                
                            </button>
                            <div class="dropdown-menu" data-bind="foreach: filterbox" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" data-bind="html: filterboxOption, id: filterboxOptionId, click: $root.filterboxSelected" href="#"></a>
                            </div>
                        </div>            
                </div>
            </div>

            <div class="row mx-auto mt-3" data-bind="if: selectedFilterbox">
                <div class="dropdown d-inline-block" data-bind="if: activities().length > 0">
                    <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<xsl:value-of select="php:function('lang', 'Activities (2018)')"/>
                    </button>
                    <div class="dropdown-menu" data-bind="foreach: activities" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" data-bind="html: activityOption, id: activityOptionId, click: $root.activitySelected" href="#"></a>
                    </div>
                </div>

                <div class="dropdown d-inline-block" data-bind="if: facilities().length > 0">
                    <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<xsl:value-of select="php:function('lang', 'Facilities')"/>
                    </button>
                    <div class="dropdown-menu" data-bind="foreach: facilities" aria-labelledby="dropdownMenuButton">
                        <div class="dropdown-item d-block">
                            <a class="text-dark" data-bind="html: facilityOption, id: facilityOptionId, click: $root.facilitySelected" href="#"></a>
                            <span data-bind="if: selected">&#160; &#10004;</span>
                        </div>
                    </div>
                </div>

                <div class="dropdown d-inline-block" data-bind="if: towns().length > 0">
                    <button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<xsl:value-of select="php:function('lang', 'Part of town (2018)')"/>
                    </button>
                    <div class="dropdown-menu" data-bind="foreach: towns" aria-labelledby="dropdownMenuButton">
                        <div class="dropdown-item d-block">
                            <a class="text-dark" data-bind="html: townOption, id: townOptionId, click: $root.townSelected" href="#"></a>
                            <span data-bind="if: selected">&#160; &#10004;</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mx-auto mt-5" data-bind="if: selectedTags().length > 0">
                <div data-bind="foreach: selectedTags">
                <div class="d-inline-block mb-2">
                    <div class="tags mr-2">
                        <span data-bind="html: value, click: $root.clearTag" ></span>
                        <a href="" data-bind="click: $root.clearTag"><i class="fa fa-times tagsRemoveIcon" aria-hidden="true"></i></a>
                    </div>
                </div>
                </div>
            </div>

        </div>
        
    </div>
  
    <div class="container pageResults">
        <!-- UPCOMMING ARRAGEMENTS -->
        <div id="welcomeResult">
            <h1 class="text-center upcomingevents-header"></h1>

            <div class="row" data-bind="foreach: upcommingevents">
                <div class="col-lg-6 card-positioncorrect">
                        <div class="row custom-card">
                            <div class="col-md-3 col-sm-4 col-4 date-circle">
                                <svg width="90" height="90">
                                    <circle cx="45" cy="45" r="41" class="circle"/>
                                    <text class="event_datetime_day" data-bind="" x="50%" y="43%" text-anchor="middle" font-size="40px" fill="white" font-weight="bold" dy=".3em">                                        
                                    </text>
                                    <text data-bind="text: datetime_month" x="50%" y="68%" text-anchor="middle" fill="white" font-weight="bold" dy=".3em">                                        
                                    </text>
                                </svg>
                            </div>
                            <div class="col-md-9 col-sm-8 col-8 desc">
                                <h5 class="font-weight-bold title" data-bind="text: name"></h5>                                
                                <span class="text-uppercase" data-bind="text: datetime_time"></span>
                                <span class="text-uppercase" data-bind="text: 'STED: ' +building_name"></span>
                                <span class="text-uppercase mb-2" data-bind="text: 'ARRANGØR: ' +organizer"></span>
                                <a class="upcomming-event-href" href="" target="_blank" data-bind=""><span class="text-uppercase font-weight-normal" data-bind="visible: homepage != ''">Les mer</span></a>
                            </div>
                        </div>
                </div>                
            </div>
        
        </div>
        
        <!-- SEARCH RESULT -->
        <div id="searchResult" data-bind="if: notFilterSearch">
				<h1 class="text-center result-title"><xsl:value-of select="php:function('lang', 'Search results')"/> (<span data-bind="text: items().length"></span>)</h1>
           
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
                                    <h4 class="font-weight-bold" data-bind="html: name"></h4>
                                    <span data-bind="html: street"></span>
                                    <span class="d-block" data-bind="html: postcode"></span>
                                </div>
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
        <div id="filterSearchResult" data-bind="if: selectedFilterbox"> 
				<h1 class="text-center result-title"><xsl:value-of select="php:function('lang', 'Search results')"/> (<span data-bind="text: filterSearchItems().length"></span>)</h1>
            <div data-bind="if: filterSearchItems().length > 0">
            
            <div class="row" data-bind="foreach: filterSearchItems">
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
                                <h4 class="font-weight-bold" data-bind="html: name"></h4>
                                <span data-bind="html: street"></span>
                                <span class="d-block" data-bind="html: postcode"></span>
                            </div>

                        </div>
                        
                    </a>
                    
                    <div class="row custom-all-subcard" style="width: 100%" data-bind="foreach: filterSearchItemsResources">
                    
                        <div class="custom-subcard" data-bind="visible: $index() == 0 || $index() == 1">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="font-weight-bold" data-bind="html: name"></h5>
                                        </div>
                                        <div class="col-6">
										<a class="btn btn-light float-right filtersearch-bookBtn" data-bind="">
											<xsl:value-of select="php:function('lang', 'Application')"/>
										</a>
                                        </div>
                                    </div>
                                    <div data-bind="foreach: activities">
									<span class="tagTitle" data-bind="if: $index() == 0">
										<xsl:value-of select="php:function('lang', 'Activities (2018)')"/>:
									</span>
                                        <span class="mr-2 textTagsItems" data-bind="html: name" ></span>
                                    </div>
									<div data-bind="foreach: facilities">
									<span class="tagTitle" data-bind="if: $index() == 0">
										<xsl:value-of select="php:function('lang', 'Facilities')"/>:
									</span>
										<span class="mr-2 textTagsItems" data-bind="html: name" ></span>
									</div>
                        </div>                                
                    </div>
							<div class="filterSearchToggle" data-bind="visible: filterSearchItemsResources().length > 2">
								<i class="fas fa-angle-down"></i>
								<xsl:text> </xsl:text>
								<xsl:value-of select="php:function('lang', 'See')"/>
								<xsl:text> </xsl:text>
								<span data-bind="text: (filterSearchItemsResources().length - 2) "></span>
								<xsl:text> </xsl:text>
								<xsl:value-of select="php:function('lang', 'more')"/>
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
