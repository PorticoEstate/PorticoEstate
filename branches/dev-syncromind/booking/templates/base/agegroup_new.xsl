<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="agegroup/tabs"/>
            <div id="agegroup_add" class="booking-container">
                <div class="pure-control-group">
                    <label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label>
                    <input id="field_name" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="agegroup/name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_sort"><xsl:value-of select="php:function('lang', 'Sort order')" /></label>
                    <input id="field_sort" name="sort" type="text" value="{agegroup/sort}"/>
                </div>
                <div class="pure-control-group">
                    <label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
                    <textarea rows="5" id="field_description" name="description"><xsl:value-of select="agegroup/description"/></textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
            </input>
            <a class="cancel pure-button pure-button-primary">
                <xsl:attribute name="href"><xsl:value-of select="agegroup/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
</xsl:template>
