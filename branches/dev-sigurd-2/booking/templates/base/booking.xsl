<xsl:template match="data">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="booking/bookings_link"/></xsl:attribute>
                    <xsl:value-of select="lang/title" />
                </a>
            </li>
            <li><a href=""><xsl:value-of select="booking/name"/></a></li>
        </ul>

        <xsl:call-template name="msgbox"/>

        <dl class="proplist-col">
            <dt><xsl:value-of select="lang/from" /></dt>
            <dd><xsl:value-of select="booking/from_"/></dd>
            <dt><xsl:value-of select="lang/to" /></dt>
            <dd><xsl:value-of select="booking/to_"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="lang/season" /></dt>
            <dd><xsl:value-of select="booking/season_name"/></dd>
            <dt><xsl:value-of select="lang/group" /></dt>
            <dd><xsl:value-of select="booking/group_name"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="lang/resources" /></dt>
            <dd><div id="resources_container"/></dd>
        </dl>

        <div class="clr"/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="booking/edit_link"/></xsl:attribute>
            <xsl:value-of select="lang/edit" />
        </a>
    </div>

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="booking/resource_ids"/>';
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
    var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
});
]]>
</script>

</xsl:template>
