<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div id="container_event_search">
        <div class="container searchContainer">
            <div class="input-group input-group-lg">
                <input type="text" class="eventsearchbox" id="eventsearchBoxID" aria-label="Large" onclick="coolfunc()">
                    <xsl:attribute name="placeholder">
                        <xsl:value-of select="php:function('lang', 'Search for events')"/>
                    </xsl:attribute>
                </input>
                <div class="input-group-prepend">
                    <button class="input-group-text searchBtn" id="inputGroup-sizing-lg" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="input-group-prepend">
                </div>
            </div>
            <h2 class="Kommende-arrangement">Kommende Arrangement</h2>
            <div id="event-content">
                <ul data-bind="foreach: events">
                    <div class="event-card">
                        <li>
                            <div class="card-element-left date">
                                <div class="cal-img-logo"></div>
                                <div class="formattedDate" data-bind="text: formattedDate"></div>
                                <div class="monthTag" data-bind="text:monthText"></div>
                            </div>
                            <div class="card-element-mid">
                                <div class="event_name" data-bind="text: event_name"></div>
                                <div class="event_time" data-bind="text: event_time"></div>
                            </div>
                            <div class="card-element-right">
                                <div class ="org_name" data-bind="text: org_name"></div>
                                <div class="pin_img_logo"></div>
                                <div class="location_name" data-bind="text: location_name"></div>
                            </div>

                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</xsl:template>