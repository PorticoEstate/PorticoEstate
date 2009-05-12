<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                    <xsl:value-of select="season/building_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Season')" /></li>
            <li><a href=""><xsl:value-of select="season/name"/></a></li>
        </ul>

        <xsl:call-template name="msgbox"/>

        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'From')" /></dt>
            <dd><xsl:value-of select="season/from_"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'To')" /></dt>
            <dd><xsl:value-of select="season/to_"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="season/building_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Status')" /></dt>
            <dd><xsl:value-of select="season/status"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Resources')" /></dt>
            <dd><div id="resources_container"/></dd>
        </dl>
        <div class="clr"/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="season/edit_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Edit')" />
        </a>
		<br/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="season/boundaries_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Boundaries')" />
        </a>
		<br/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="season/wtemplate_link"></xsl:value-of></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Week template')" />
        </a>
    </div>

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
YAHOO.util.Event.addListener(window, "load", function() {
    <![CDATA[
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
	]]>
    var colDefs = [{key: 'name', label: '<xsl:value-of select="php:function('lang', 'Name')" />', formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
});
</script>

</xsl:template>
