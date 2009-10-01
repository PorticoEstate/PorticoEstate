<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
	<div id="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/buildings_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Buildings')"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/building_link"/></xsl:attribute>
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/resource_link"/></xsl:attribute>
					<xsl:value-of select="resource/name"/>
				</a>
			</li>
            <li><xsl:value-of select="php:function('lang', 'schedule')"/></li>
		</ul>

		<xsl:call-template name="msgbox"/>
		<h4><xsl:value-of select="php:function('lang', 'Resource schedule')"/></h4>
		<ul id="week-selector">
			<li><a><xsl:attribute name="href"><xsl:value-of select="resource/prev_link"/></xsl:attribute><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
			<li><xsl:value-of select="Week"/>: <xsl:value-of select="resource/week"/></li>
			<li><a><xsl:attribute name="href"><xsl:value-of select="resource/next_link"/></xsl:attribute><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
		</ul>

		<div id="schedule_container"/>
		<a href="{resource/application_link}">
			<xsl:value-of select="php:function('lang', 'New booking application')"/>
		</a>
	</div>

<script type="text/javascript">
var resource_id = <xsl:value-of select="resource/id"/>;
var date = '<xsl:value-of select="resource/date"/>';
YAHOO.util.Event.addListener(window, "load", function() {
<![CDATA[
	var url = 'index.php?menuaction=bookingfrontend.uibooking.resource_schedule&date=' + date + '&resource_id=' + resource_id + '&phpgw_return_as=json&';
]]>
	var colDefs = [{key: 'time', label: '<xsl:value-of select="resource/year"/>' + '<br/><xsl:value-of select="Time"/>'}, 
			<xsl:for-each select="resource/days">
				{key: '<xsl:value-of select="key"/>', label: '<xsl:value-of select="label"/>', formatter: YAHOO.booking.frontendScheduleColorFormatter},
			</xsl:for-each>{hidden: true}];
	YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
	}, true);
});
</script>

</xsl:template>
