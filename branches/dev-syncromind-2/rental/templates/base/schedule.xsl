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
		#week-selector {list-style: outside none none;display: inline-block;vertical-align: middle;}
		#week-selector li {display: inline-block;vertical-align: middle;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
        #scheduleSearchBox {display: inline-block; vertical-align: middle;}
            #scheduleSearchBox label {margin-right: 5px; margin-left: 20px;}
            #scheduleSearchBox #txtSearchSchedule {}
	</style>
    <div id="contract_schedule">
        <div id="shceduleFilters">
            <div id="queryForm">
                <style scoped="scoped" type="text/css" id="toggle-box-css">
                    .toggle-box {display: none;}
                    .toggle-box + label {cursor: pointer;display: block;font-weight: bold;line-height: 21px;margin-bottom: 5px;}
                    .toggle-box + label + #toolbar {display: none;margin-bottom: 10px;}
                    .toggle-box:checked + label + #toolbar {display: block;}
                    .toggle-box + label:before {background-color: #4F5150;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;color: #FFFFFF;content: "+";display: block;float: left;font-weight: bold;height: 20px;line-height: 20px;margin-right: 5px;text-align: center;width: 20px;}
                    .toggle-box:checked + label:before {content: "\2212";}
                </style>
                <input type="checkbox" id="header1" class="toggle-box" />
                <label for="header1">Filter</label>
                <div id="toolbar" class="dtable_custom_controls">
                    <table class="pure-table pure-table-horizontal" id="toolbar_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>!item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="location_id">!t_new_contract</label></td>
                                <td valign="top">
                                    <select style="width: 250px" width="250" name="location_id" id="location_id" class="searchSchedule">
                                        <option value="838">!contract_type_eksternleie</option>
                                    </select>
                                </td>
                            </tr>                            
                            <tr>
                                <td><label for="search_option">!search_where</label></td>
                                <td valign="top">
                                    <select style="width: 250px" width="250" name="search_option" id="search_option" class="searchSchedule">
                                        <option value="all">All</option>
                                        <option value="id">!contract_id</option>
                                        <option value="party_name">!party_name</option>
                                        <option value="composite">!composite_name</option>
                                        <option value="composite_address">!composite_address</option>
                                        <option value="location_code">!object_number</option>
                                    </select>
                                </td>
                            </tr>                            
                            <tr>
                                <td><label for="contract_status">Status</label></td>
                                <td valign="top">
                                    <select style="width: 250px" width="250" name="contract_status" id="contract_status" class="searchSchedule">
                                        <option value="all">All</option>
                                        <option value="under_planning">!under_planning</option>
                                        <option value="active">!active_plural</option>
                                        <option value="under_dismissal">!under_dismissal</option>
                                        <option value="ended">!ended</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="contract_type">!field_of_responsibility</label></td>
                                <td valign="top">
                                    <select style="width: 250px" width="250" name="contract_type" id="contract_type" class="searchSchedule">
                                        <option value="all">All</option>
                                        <option value="836">!contract_type_internleie</option>
                                        <option value="837">!contract_type_innleie</option>
                                        <option value="838">!contract_type_eksternleie</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
        <p id="scheduleSearchBox">
            <label for="txtSearchSchedule">Search: </label>
            <input type="text" id="txtSearchSchedule" class="searchSchedule" />
        </p>
        <div id="schedule_container"></div>
        <p>
            <label for="cboNObjects">Show</label>
            <select name="cboNObjects" id="cboNObjects" class="searchSchedule">
                <option selected="selected" value="15">15</option>
                <option value="30">30</option>
                <option value="45">45</option>
                <option value="0">All</option>
            </select>
            <label for="cboNObjects">Entries</label>
        </p>
    </div>
    <script type="text/javascript">
        var composite_id = '<xsl:value-of select="composite_id"/>';
		$(window).load(function() {
            schedule.setupWeekPicker('cal_container');

            var img_src = '<xsl:value-of select="picker_img"/>';
            //var composite_id = '<xsl:value-of select="composite_id"/>';

            schedule.datasourceUrl = '<xsl:value-of select="datasource_url"/>';
            var initialRequest = getUrlData("date") || '<xsl:value-of select="date"/>';

            schedule.includeResource = false;
            schedule.colFormatter = 'rentalSchedule';
            var handleHistoryNavigation = function (state) {
                schedule.date = parseISO8601(state);
                schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
            };

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