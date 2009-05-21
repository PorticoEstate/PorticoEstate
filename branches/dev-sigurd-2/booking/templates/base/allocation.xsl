<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="allocation/allocations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Allocations')" />
                </a>
            </li>
            <li><a href=""><xsl:value-of select="allocation/organization_name"/></a></li>
        </ul>

        <xsl:call-template name="msgbox"/>

        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'From')" /></dt>
            <dd><xsl:value-of select="allocation/from_"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'To')" /></dt>
            <dd><xsl:value-of select="allocation/to_"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Season')" /></dt>
            <dd><xsl:value-of select="allocation/season_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Organization')" /></dt>
            <dd><xsl:value-of select="allocation/organization_name"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Resources')" /></dt>
            <dd><div id="resources_container"/></dd>
        </dl>

        <div class="form-buttons">
	        <button>
	            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/edit_link"/>"</xsl:attribute>
	            <xsl:value-of select="php:function('lang', 'Edit')" />
	        </button>
		</div>
    </div>

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="allocation/resource_ids"/>';
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
    var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
});
]]>
</script>

</xsl:template>
