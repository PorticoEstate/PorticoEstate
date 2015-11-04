<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--xsl:call-template name="yui_booking_i18n"/-->
	<iframe id="yui-history-iframe" src="{webserver_url}/phpgwapi/js/yahoo/history/assets/blank.html" style="position:absolute;top:0; left:0;width:1px; height:1px;visibility:hidden;"></iframe>
	<input id="yui-history-field" type="hidden"/>
	
	<div id="content">
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
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

       	<button onclick="window.location.href='{resource/application_link}'"><xsl:value-of select="php:function('lang', 'New booking application')" /></button>

		<xsl:call-template name="msgbox"/>
		<!--ul id="week-selector">
			<li><a href="#" onclick="YAHOO.booking.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
			<li id="cal_container"/>
			<li><a href="#" onclick="YAHOO.booking.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
		</ul-->
        <ul id="week-selector">
            <li><a id="btnPrevWeek" class="moveWeek" onclick="schedule.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
            <li id="cal_container">
                <div>
                    <span><xsl:value-of select="php:function('lang', 'Week')" />: </span>
                    <label id="numberWeek"></label>
                    <input type="text" id="datepicker" />
                    <img id="pickerImg" src="{resource/picker_img}" />
                </div>
            </li>
            <li><a id="btnPrevWeek" class="moveWeek" onclick="schedule.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
        </ul>

		<div id="schedule_container"/>
	</div>
    <div id="dialog_schedule"></div>

<script type="text/javascript">
    schedule.createDialogSchedule(300);
    $(window).load(function(){
        schedule.setupWeekPicker('cal_container');
        schedule.datasourceUrl = '<xsl:value-of select="resource/datasource_url" />';
        schedule.newApplicationUrl = '<xsl:value-of select="resource/application_link" />';
        schedule.includeResource = false;
        schedule.colFormatter = 'frontendScheduleDateColumn';
        var handleHistoryNavigation = function (state) {
            schedule.date = parseISO8601(state);
            schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
        }
        
        var initialRequest = getUrlData("date") || '<xsl:value-of select="resource/date" />';
        
        var state = getUrlData("date") || initialRequest;
        if (state) {
            handleHistoryNavigation(state);
            schedule.week = $.datepicker.iso8601Week(schedule.date);
            $('#cal_container #numberWeek').text(schedule.week);
            $('#cal_container #datepicker').datepicker("setDate", parseISO8601(state));
        }
    });


/*
YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.booking.setupWeekPicker('cal_container');
	YAHOO.booking.datasourceUrl = '<xsl:value-of select="resource/datasource_url"/>';
	YAHOO.booking.newApplicationUrl = '<xsl:value-of select="resource/application_link"/>';
	
    var handleHistoryNavigation = function (state) {
		YAHOO.booking.date = parseISO8601(state);
		YAHOO.booking.renderSchedule('schedule_container', YAHOO.booking.datasourceUrl, YAHOO.booking.date, YAHOO.booking.frontendScheduleColorFormatter, false);
    };
    var initialRequest = YAHOO.util.History.getBookmarkedState("date") || '<xsl:value-of select="resource/date"/>';
    YAHOO.util.History.register("date", initialRequest, handleHistoryNavigation);
    YAHOO.util.History.onReady(function() {
		var state = YAHOO.util.History.getBookmarkedState("date") || initialRequest;
		if(state)
			handleHistoryNavigation(state);
    });
   	YAHOO.util.History.initialize("yui-history-field", "yui-history-iframe");	
});
*/
</script>

</xsl:template>
