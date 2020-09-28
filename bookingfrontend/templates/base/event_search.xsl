<xsl:template match="data" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div id="container_event_search">
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
            <h2 class="Kommende-arrangement">Kommende Arrangement</h2>
            <div id="event-content">
                <ul data-bind="foreach: events">
                    <div class="event-card">
                        <li>
                            <div class="card-element-left date">
                                <div class="cal-img-logo"></div>
                                <span class="formattedDate" data-bind="text: formattedDate"/>
                                <span class="monthTag" data-bind="text:monthText"/>
                            </div>
                            <div class="card-element-mid">
                                <span class="event_name" data-bind="text: event_name"/>
                                <span class="event_time" data-bind="text: event_time"/>
                            </div>
                            <div class="card-element-right">
                                <span data-bind="text: org_name"/>
                                <span data-bind="text: location_name"/>
                            </div>

                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</xsl:template>