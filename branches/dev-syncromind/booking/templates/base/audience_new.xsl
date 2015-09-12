<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">
    <h3><xsl:value-of select="php:function('lang', 'New target audience')" /></h3-->
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->
    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="audience/tabs"/>
            <div id="audience_add">
                <div class="pure-control-group">
                    <label for="field_name">
                        <h4><xsl:value-of select="php:function('lang', 'Target audience')" /></h4>
                    </label>
                    <input id="field_name" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="audience/name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_sort">
                        <h4><xsl:value-of select="php:function('lang', 'Sort order')" /></h4>
                    </label>
                    <input id="field_sort" name="sort" type="text" value="{audience/sort}"/>
                </div>
                <div class="pure-control-group">
                    <label for="field_description">
                        <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                    </label>
                    <textarea rows="5" id="field_description" name="description"><xsl:value-of select="audience/description"/></textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
            </input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="audience/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    <!--/div-->
</xsl:template>
