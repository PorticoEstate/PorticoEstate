<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="schedule">
			<xsl:apply-templates select="schedule"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="schedule">
    <style typ="text/css" rel="stylesheet">
		#week-selector {list-style: outside none none;}
		#week-selector li {display: inline-block;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
	</style>
    <div id="contract_schedule">
        <ul id="week-selector">
            <li>
                <span class="pure-button pure-button-primary" onclick="schedule.prevWeek(); return false">
                    <xsl:value-of select="php:function('lang', 'Previous week')"/>
                </span>
            </li>
            <li id="cal_container">
                <div>
                    <span>
                        <xsl:value-of select="php:function('lang', 'Week')" />: </span>
                    <label id="numberWeek"></label>
                    <input type="text" id="datepicker" />
                    <img id="pickerImg" src="{picker_img}" />
                </div>
            </li>
            <li>
                <span class="pure-button pure-button-primary" onclick="schedule.nextWeek(); return false">
                    <xsl:value-of select="php:function('lang', 'Next week')"/>
                </span>
            </li>
        </ul>
        <div id="schedule_container"></div>
    </div>
    <script type="text/javascript">
		$(window).load(function() {
            schedule.setupWeekPicker('cal_container');

            var img_src = '<xsl:value-of select="picker_img"/>';

            <![CDATA[
            schedule.datasourceUrl = '/dev-syncromind-2/index.php?menuaction=booking.uibooking.resource_schedule&resource_id=1&phpgw_return_as=json&click_history=69fd120cc81d86e6d90b214b5ab89033';
            schedule.newApplicationUrl = '/dev-syncromind-2/index.php?menuaction=booking.uiapplication.add&building_id=3&building_name=Vitalitetsenteret&activity_id=1&resource=1&click_history=69fd120cc81d86e6d90b214b5ab89033';
            ]]>

            schedule.includeResource = false;
            schedule.colFormatter = '';
            var handleHistoryNavigation = function (state) {
                schedule.date = parseISO8601(state);
                schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
            };

            var initialRequest = getUrlData("date") || '2016-08-15';

            var state = getUrlData("date") || initialRequest;
            if (state){
                handleHistoryNavigation(state);
                schedule.week = $.datepicker.iso8601Week(schedule.date);
                $('#cal_container #numberWeek').text(schedule.week);
                $("#cal_container #datepicker").datepicker("setDate", parseISO8601(state));
            }
		});
	</script>
</xsl:template>