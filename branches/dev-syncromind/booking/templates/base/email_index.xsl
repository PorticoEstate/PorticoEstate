<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->

    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
            <div id="building">
                <input type="hidden" name="step" value="0"/>
                <div class="pure-control-group">
                    <label for="field_building_name">
                        <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                    </label>
                    <!--div class="autocomplete"-->
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="building_name"/></xsl:attribute>
                    </input>
                    <!--div id="building_container"/>
                    </div-->
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Season')" /></h4>
                    </label>
                    <!--div id="season_container"--><xsl:value-of select="php:function('lang', 'Select a building first')" /><!--/div-->
                </div>
                <div class="pure-control-group">
                    <label for="field_mailsubject">
                        <h4><xsl:value-of select="php:function('lang', 'Mail subject')" /></h4>
                    </label>
                    <input type="text" id="field_mailsubject" name="mailsubject" class="full-width">
                        <xsl:attribute name="value"><xsl:value-of select="mailsubject"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_mailbody">
                        <h4><xsl:value-of select="php:function('lang', 'Mail body')" /></h4>
                    </label>
                    <textarea id="field_mailbody" name="mailbody" class="full-width"><xsl:value-of select="mailbody"/></textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'preview')"/></xsl:attribute>
            </input>
        </div>
    </form>
    <!--/div-->
</xsl:template>
