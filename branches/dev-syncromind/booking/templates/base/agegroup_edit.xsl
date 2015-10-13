<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
            <div id="agegroup_edit" class="booking-container">
                <div class="pure-control-group">
                    <label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label>
                    <input id="field_name" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="resource/name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_active"><xsl:value-of select="php:function('lang', 'Active')" /></label>
                    <select id="field_active" name="active">
                        <option value="1">
                            <xsl:if test="resource/active=1">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Active')" />
                        </option>
                        <option value="0">
                            <xsl:if test="resource/active=0">
                                <xsl:attribute name="selected">checked</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Inactive')" />
                        </option>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label for="field_sort"><xsl:value-of select="php:function('lang', 'Sort order')" /></label>
                    <input id="field_sort" name="sort" type="text" value="{resource/sort}"/>
                </div>
                <div class="pure-control-group">
                    <label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
                    <textarea rows="5" id="field_description" name="description">
                        <xsl:value-of select="resource/description"/>
                    </textarea>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="button pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Update')" /></xsl:attribute>
            </input>
            <a class="cancel pure-button pure-button-primary">
                <xsl:attribute name="href"><xsl:value-of select="resource/cancel_link"></xsl:value-of></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    <script type="text/javascript">
       var initialSelection = <xsl:value-of select="booking/resources_json"/>;
    </script>
</xsl:template>
