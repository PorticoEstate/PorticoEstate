<!-- $Id$ -->
<xsl:template match="data">
	<div id="content">

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_property_i18n"/>

		<h4><xsl:value-of select="lang/resource_schedule"/></h4>
		<div id="schedule_container"/>
	</div>

	<script type="text/javascript">
		var id = <xsl:value-of select="resource/id"/>;
		YAHOO.util.Event.addListener(window, "load", function() {
		<![CDATA[
		var url = 'index.php?menuaction=property.boevent.event_schedule_data&id=' + id + '&phpgw_return_as=json&';
		]]>
		var colDefs = [{key: 'time', label: '#'}, 
		<xsl:for-each select="resource/cols">
			{key: '<xsl:value-of select="key"/>', label: '<xsl:value-of select="label"/>', formatter: YAHOO.booking.backendScheduleColorFormatter},
		</xsl:for-each>{hidden: true}];
		YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
		}, true);
		});
	</script>

</xsl:template>
