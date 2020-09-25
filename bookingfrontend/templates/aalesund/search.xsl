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
                        </div>
			<div class="container searchContainer"    style="display:none" >
				<div class="input-group input-group-lg mainpageserchcontainer">
					<input type="text" id="mainSearchInput" class="form-control searchInput" aria-label="Large">
						<xsl:attribute name="placeholder">
                                                    <xsl:value-of select="php:function('lang', 'Search building, resource, organization')"/>
						</xsl:attribute>
					</input>
					
				</div>
				<div id="search-autocomplete"></div>
				<!-- Filter Boxes -->
				<h2 class="mt-5 font-weight-bold">
				   <xsl:value-of select="php:function('lang', 'Choose categories')"/>
				</h2>
                                
				<div class="row mx-auto" data-bind="if: filterboxes().length > 0">
					<div data-bind="foreach: filterboxes">
						<div class="dropdown d-inline-block mr-2">
							<button class="btn btn-secondary dropdown-toggle d-inline" data-bind="text: filterboxCaption" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							</button>
							<div class="dropdown-menu" data-bind="foreach: filterbox" aria-label="Large">
								<a class="dropdown-item" data-bind="html: filterboxOption, id: filterboxOptionId, click: $root.filterboxSelected" href="#"></a>
							</div>
						</div>
					</div>
				</div>
                                <h2 class="mt-5 font-weight-bold">
				   <xsl:value-of select="php:function('lang', 'Choose date-range')"/>
				</h2>
				<div class="row mx-auto">
	 		 <div class="container">
                                                   
Fra: <input type="datetime-local" class="date_availability_filter" id="from_time"
       name="meeting-time" value="2018-06-12T19:30" 
       min="2018-06-07T00:00" max="2018-06-14T00:00" style="border-width: 2px; biorder-color: black"/>

Til: <input type="datetime-local" class="date_availability_filter" id="to_time"
       name="meeting-time" value="2018-06-12T19:30"
       min="2018-06-07T00:00" max="2018-06-14T00:00" style="border-width: 2px; biorder-color: black"/>
                                                </div>
				</div>
				<div class="row mx-auto mt-3" data-bind="if: selectedFilterbox">
					<div class="dropdown d-inline-block" data-bind="if: activities().length > 0">
						<button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<xsl:value-of select="php:function('lang', 'Activities (2018)')"/>
						</button>
						<div class="dropdown-menu" data-bind="foreach: activities" aria-label="Large">
							<a class="dropdown-item" data-bind="html: activityOption, id: activityOptionId, click: $root.activitySelected" href="#"></a>
						</div>
					</div>
					<div class="dropdown d-inline-block" data-bind="if: facilities().length > 0">
						<button class="btn btn-secondary dropdown-toggle d-inline mr-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<xsl:value-of select="php:function('lang', 'Facilities')"/>
						</button>
						<div class="dropdown-menu" data-bind="foreach: facilities" aria-label="Large">
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
						<div class="dropdown-menu" data-bind="foreach: towns" aria-label="Large">
							<div class="dropdown-item d-block">
								<a class="text-dark" data-bind="html: townOption, id: townOptionId, click: $root.townSelected" href="#"></a>
								<span data-bind="if: selected">&#160; &#10004;</span>
							</div>
						</div>
					</div>
				</div>
                                <button class="btn" id="searchButton" type="button">
							Finn tilgjengelig
						</button>
				<div class="row mx-auto mt-5" data-bind="if: selectedTags().length > 0">
					<div data-bind="foreach: selectedTags">
						<div class="d-inline-block mb-2">
							<div class="tags mr-2">
								<span data-bind="html: value, click: $root.clearTag" ></span>
								<a href="" data-bind="click: $root.clearTag">
									<i class="fa fa-times tagsRemoveIcon" aria-hidden="true"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</xsl:template>