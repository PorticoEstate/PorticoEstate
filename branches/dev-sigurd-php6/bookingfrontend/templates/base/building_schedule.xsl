<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')"/>
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/building_link"/></xsl:attribute>
                    <xsl:value-of select="building/name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Schedule')"/></li>
        </ul>

        <xsl:call-template name="msgbox"/>
        <h4><xsl:value-of select="php:function('lang', 'Building schedule')"/></h4>
		<ul id="week-selector">
			<li><a><xsl:attribute name="href"><xsl:value-of select="building/prev_link"/></xsl:attribute><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
			<li><xsl:value-of select="php:function('lang', 'Week')"/>: <xsl:value-of select="building/week"/></li>
			<li><a><xsl:attribute name="href"><xsl:value-of select="building/next_link"/></xsl:attribute><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
		</ul>

        <div id="schedule_container"/>
		<a href="{building/application_link}">
			<xsl:value-of select="php:function('lang', 'New booking application')"/>
		</a>
    </div>

<script type="text/javascript">
var building_id = <xsl:value-of select="building/id"/>;
var date = '<xsl:value-of select="building/date"/>';
YAHOO.util.Event.addListener(window, "load", function() {
    <![CDATA[
    var url = 'index.php?menuaction=bookingfrontend.uibooking.building_schedule&date=' + date + '&building_id=' + building_id + '&phpgw_return_as=json&';
]]>
    var colDefs = [{key: 'time', label: '<xsl:value-of select="building/year"/>' + '<br/><xsl:value-of select="php:function('lang', 'Time')"/>'}, 
                   {key: 'resource', label: '<xsl:value-of select="php:function('lang', 'Resource')"/>', formatter: YAHOO.booking.scheduleResourceColFormatter},
			<xsl:for-each select="building/days">
				{key: '<xsl:value-of select="key"/>', label: '<xsl:value-of select="label"/>', formatter: YAHOO.booking.frontendScheduleColorFormatter},
			</xsl:for-each>{hidden: true}];
    YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
        formatRow: YAHOO.booking.scheduleRowFormatter
    });
});
</script>

</xsl:template>
