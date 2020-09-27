<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	
    <div id="search-page-content">
		<div class="frontpageimage" id="main-page">
			<div class="header-text"    style="color:#26348B;"  >
				<a href="{site_url}"    >
					<xsl:value-of disable-output-escaping="yes" select="frontimagetext"/>
				</a>
			</div>
		</div>
		<!-- Content Container -->
		<div class="jumbotron jumbotron-fluid">
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
                             <div>
                                 <div  id="locationWrapper">
                                   <input type="text" id="locationFilter" class="form-control searchInput" placeholder="Sted" aria-label="Large"></input>  
                                 </div>
                                 <div  id="dateWrapper">
                                   <input type="text" id="dateFilter" class="form-control searchInput" placeholder="Dato" aria-label="Large"></input>
                                 </div>
                            </div>
                            <button id="searchBtn">Finn tilgjengelige</button> 
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
                        
                        <div id="searchResultsContainer">
                          <div id="searchResutsHeader">  Søkeresultat  <div id="resultCount">6 treff</div></div> 
                          <hr />
                          <div id="searchResultMenu">sidebar</div>
                          <div id="searchResultList">resultatliste</div>
                        </div>
                        </div>
	
		</div>
		
	</div>
</xsl:template>