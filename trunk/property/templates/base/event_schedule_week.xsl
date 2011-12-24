<!-- $Id$ -->
<xsl:template match="data">
	<div id="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/buildings_link"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="lang/buildings"></xsl:value-of>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/building_link"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="resource/building_name"></xsl:value-of>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="resource/resource_link"></xsl:value-of></xsl:attribute>
					<xsl:value-of select="resource/name"></xsl:value-of>
				</a>
			</li>
			<li><xsl:value-of select="lang/schedule"></xsl:value-of></li>
		</ul>

		<xsl:call-template name="msgbox"></xsl:call-template>
		<xsl:call-template name="yui_property_i18n"></xsl:call-template>

		<h4><xsl:value-of select="lang/resource_schedule"></xsl:value-of></h4>
		<ul id="week-selector">
			<li><a><xsl:attribute name="href"><xsl:value-of select="resource/prev_link"></xsl:value-of></xsl:attribute><xsl:value-of select="lang/prev_week"></xsl:value-of></a></li>
			<li><xsl:value-of select="lang/week"></xsl:value-of>: <xsl:value-of select="resource/week"></xsl:value-of></li>
			<li><a><xsl:attribute name="href"><xsl:value-of select="resource/next_link"></xsl:value-of></xsl:attribute><xsl:value-of select="lang/next_week"></xsl:value-of></a></li>
		</ul>

		<div id="schedule_container"></div>
	</div>

	<script type="text/javascript">
		var resource_id = <xsl:value-of select="resource/id"></xsl:value-of>;
		var date = '<xsl:value-of select="resource/date"></xsl:value-of>';
		YAHOO.util.Event.addListener(window, "load", function() {
		<![CDATA[
		var url = 'index.php?menuaction=property.boevent.event_schedule_week_data&date=' + date + '&resource_id=' + resource_id + '&phpgw_return_as=json&';
		]]>
		var colDefs = [{key: 'time', label: '<xsl:value-of select="resource/year"></xsl:value-of>' + '<br></br><xsl:value-of select="lang/time"></xsl:value-of>'}, 
		<xsl:for-each select="resource/days">
			{key: '<xsl:value-of select="key"></xsl:value-of>', label: '<xsl:value-of select="label"></xsl:value-of>', formatter: YAHOO.booking.backendScheduleColorFormatter},
		</xsl:for-each>{hidden: true}];
		YAHOO.booking.inlineTableHelper('schedule_container', url, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
		}, true);
		});
	</script>

</xsl:template>
