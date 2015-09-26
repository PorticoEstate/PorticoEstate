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
            <li><xsl:value-of select="php:function('lang', 'Schedule')"/></li>
        </ul-->

    <xsl:call-template name="msgbox"/>
    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
            <div id="resource_schedule">
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
                
    <!--/div-->

    <script type="text/javascript">
        $(window).load(function() {
            schedule.setupWeekPicker('cal_container');
            schedule.datasourceUrl = '<xsl:value-of select="resource/datasource_url"/>';
            schedule.includeResource = false;
            var handleHistoryNavigation = function (state) {
                schedule.date = parseISO8601(state);
                schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.backendScheduleColorFormatter, false);
            };

            var initialRequest = getUrlData("date") || '<xsl:value-of select="resource/date"/>';

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
            YAHOO.booking.datasourceUrl = '<xsl:value-of select="resource/datasource_url"/>';

        var handleHistoryNavigation = function (state) {
                    YAHOO.booking.date = parseISO8601(state);
                    YAHOO.booking.renderSchedule('schedule_container', YAHOO.booking.datasourceUrl, YAHOO.booking.date, YAHOO.booking.backendScheduleColorFormatter, false);
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
