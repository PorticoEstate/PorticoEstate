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
                <p>test</p>
                <tbody data-bind="foreach: events">
                    <tr>
                        <td data-bind="value: name"></td>
                    </tr>
                </tbody>
            </div>

        </div>
    </div>
</xsl:template>