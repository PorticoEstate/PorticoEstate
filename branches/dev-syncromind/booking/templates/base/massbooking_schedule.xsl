<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style typ="text/css" rel="stylesheet">
        #week-selector {list-style: outside none none;}
        #week-selector li {display: inline-block;}
        #cal_container {margin: 0 20px;}
        #cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
        #cal_container #numberWeek {width: 20px;display: inline-block;}
    </style>
	<!--xsl:call-template name="yui_booking_i18n"/-->
	<iframe id="yui-history-iframe" src="phpgwapi/js/yahoo/history/assets/blank.html" style="position:absolute;top:0; left:0;width:1px; height:1px;visibility:hidden;"></iframe>
	<input id="yui-history-field" type="hidden"/>
	
    <!--div id="content">
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
		<ul id="week-selector">
			<li><a href="#" onclick="YAHOO.booking.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></a></li>
			<li id="cal_container"/>
			<li><a href="#" onclick="YAHOO.booking.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></a></li>
		</ul>

        <div id="schedule_container"/>
    </div-->
    
    
    
    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value="" />
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="building/tabs" />
            <div id="massbooking_schedule">
                <ul id="week-selector">
                    <li><span class="pure-button pure-button-primary" onclick="schedule.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></span></li>
                    <li id="cal_container">
                        <div>
                            <span><xsl:value-of select="php:function('lang', 'Week')" />: </span>
                            <label id="numberWeek"></label>
                            <input type="text" id="datepicker" />
                            <img id="pickerImg" src="/portico/phpgwapi/templates/base/images/cal.png" />
                        </div>
                    </li>
                    <li><span class="pure-button pure-button-primary" onclick="schedule.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></span></li>
                </ul>
                <div id="schedule_container"></div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $(window).load(function() {
            schedule.datasourceUrl = '<xsl:value-of select="building/datasource_url"/>';
            schedule.newApplicationUrl = '<xsl:value-of select="building/application_link"/>';
            schedule.includeResource = true;
            var handleHistoryNavigation = function (state) {
                schedule.date = parseISO8601(state);
                schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, 'frontendScheduleDateColumn', schedule.includeResource);
            };
            
            var initialRequest = getUrlData("date") || '<xsl:value-of select="building/date"/>';
            
            var state = getUrlData("date") || initialRequest;
            if (state){
                handleHistoryNavigation(state);
                schedule.week = $.datepicker.iso8601Week(schedule.date);
                $('#cal_container #numberWeek').text(schedule.week);
                $("#cal_container #datepicker").datepicker("setDate", parseISO8601(state));
            }
        });


        /*
        YAHOO.util.Event.addListener(window, "load", function() {
                YAHOO.booking.setupWeekPicker('cal_container');
                YAHOO.booking.datasourceUrl = '<xsl:value-of select="building/datasource_url"/>';
                YAHOO.booking.newApplicationUrl = '<xsl:value-of select="building/application_link"/>';

            var handleHistoryNavigation = function (state) {
                        YAHOO.booking.date = parseISO8601(state);
                        YAHOO.booking.renderSchedule('schedule_container', YAHOO.booking.datasourceUrl, YAHOO.booking.date, YAHOO.booking.frontendScheduleColorFormatter, true);
            };
            var initialRequest = YAHOO.util.History.getBookmarkedState("date") || '<xsl:value-of select="building/date"/>';
            YAHOO.util.History.register("date", initialRequest, handleHistoryNavigation);
            YAHOO.util.History.onReady(function() {
                        var state = YAHOO.util.History.getBookmarkedState("date") || initialRequest;
                        if(state)
                                handleHistoryNavigation(state);
            });
                YAHOO.util.History.initialize("yui-history-field", "yui-history-iframe");	
        });
        */
        <xsl:if test="backend = 'true'">
            $('#header').hide();
                /*YAHOO.util.Dom.setStyle(('header'), 'display', 'none');*/
        </xsl:if>
    </script>
</xsl:template>
