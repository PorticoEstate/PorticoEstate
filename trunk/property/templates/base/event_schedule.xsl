<!-- $Id$ -->
<xsl:template match="data">
	<div id="content">

		<xsl:call-template name="msgbox"></xsl:call-template>
		<xsl:call-template name="yui_property_i18n"></xsl:call-template>

		<h4><xsl:value-of select="lang/resource_schedule"></xsl:value-of></h4>
		<div id="schedule_container"></div>
	</div>

	<script type="text/javascript">
		var id = <xsl:value-of select="resource/id"></xsl:value-of>;
		YAHOO.util.Event.addListener(window, "load", function() {
		<![CDATA[
		var url = 'index.php?menuaction=property.boevent.event_schedule_data&id=' + id + '&phpgw_return_as=json&';
		]]>
		var colDefs = [{key: 'time', label: '#'}, 
		<xsl:for-each select="resource/cols">
			{key: '<xsl:value-of select="key"></xsl:value-of>', label: '<xsl:value-of select="label"></xsl:value-of>', formatter: YAHOO.booking.backendScheduleColorFormatter},
		</xsl:for-each>{hidden: true}];
		YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
		}, true);
		});
	</script>

</xsl:template>
