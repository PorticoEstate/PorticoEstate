<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div class="content">
        <p>This is a paragraph <xsl:value-of select="event_search"/> </p>


        <div class="container searchContainer">
            <div class="input-group input-group-lg">
                <input type="text" class="eventsearchbox" aria-label="Large">
                    <xsl:attribute name="placeholder">
                        <xsl:value-of select="php:function('lang', 'Search for events')"/>
                    </xsl:attribute>
                </input>
                <div class="input-group-prepend">
                </div>
            </div>
            <div id="search-autocomplete"></div>
            <h2 class="Kommende-arrangement">Kommende Arrangement</h2>

        </div>
        <div id="welcomeResult" class=" container">
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
                            <h2 class="font-weight-bold title" data-bind="text: name"></h2>
                            <div class="card-text">
                                <span  data-bind="text: datetime_time"></span>
                                <span  data-bind="text: 'STED: ' +building_name"></span>
                                <span class="mb-2" data-bind="text: 'ARRANGØR: ' +organizer"></span>
                                <span class="font-weight-normal" data-bind="visible: homepage != ''"><a class="upcomming-event-href" href="" target="_blank" data-bind="">
                                    Les mer</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</xsl:template>