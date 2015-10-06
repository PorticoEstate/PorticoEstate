<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
            <div id="booking" class="booking-container">
                <fieldset>
                    <h1>#<xsl:value-of select="booking/id"/> (<xsl:value-of select="booking/activity_name"/>)</h1>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'From')" /></label>
                        <xsl:value-of select="php:function('pretty_timestamp', booking/from_)"/>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'To')" /></label>
                        <xsl:value-of select="php:function('pretty_timestamp', booking/to_)"/>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Cost')" /></label>
                        <xsl:value-of select="booking/cost"/>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Season')" /></label>
                        <xsl:value-of select="booking/season_name"/>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Group')" /></label>
                        <xsl:value-of select="booking/group_name"/>
                    </div>
                    <div class="pure-control-group">
                        <label style="vertical-align:top;"><xsl:value-of select="php:function('lang', 'Resources')" /></label>
                        <div id="resources_container" style="display:inline-block;"></div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
    <div class="form-buttons">
        <xsl:if test="booking/permission/write">
            <button class="pure-button pure-button-primary">
                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="booking/edit_link"/>"</xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Edit')" />
            </button> 
            <button class="pure-button pure-button-primary">
                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="booking/delete_link"/>"</xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Delete booking')" />
            </button>
        </xsl:if>
    </div>
    <script type="text/javascript">
        var resourceIds = '<xsl:value-of select="booking/resource_ids"/>';
        var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
        <![CDATA[
            var resourcesURL = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
        ]]>
        var colDefsResources = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}];
        createTable('resources_container',resourcesURL,colDefsResources);
    </script>
</xsl:template>
