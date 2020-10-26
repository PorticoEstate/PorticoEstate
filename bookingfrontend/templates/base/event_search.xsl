<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div id="container_event_search">
        <div class="container searchContainer">
            <div class="input-group input-group-lg mainpageserchcontainer" style="flex-wrap:inherit">
                <input type="text" class="eventsearchbox" id="eventsearchBoxID" aria-label="Large" onclick="coolfunc()" placeholder="søk etter organisasjoner">
                </input>
                <div class="input-group-prepend">
                    <button class="input-group-text searchBtn" id="inputGroup-sizing-lg" type="button" onclick="searchInput()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <h2 class="Kommende-arrangement">Kommende Arrangement</h2>
        </div>
    </div>
    <div id="event-content">
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
</xsl:template>