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
                            <script type="text/javascript">
                                schedule.params = {};
                            </script>
                            <xsl:for-each select="filters">
                                <tr>
                                    <td><xsl:value-of select="text" /></td>
                                    <td>                           
                                        <xsl:variable name="name">
											<xsl:value-of select="name"/>
										</xsl:variable>
                                        <xsl:if test="type = 'filter'">
                                            <select id="{$name}" name="{$name}" class="searchSchedule" width="250" style="width: 250px">                                                
                                                <xsl:for-each select="list">
                                                    <xsl:variable name="id">
                                                        <xsl:value-of select="id"/>
                                                    </xsl:variable>
                                                    <xsl:choose>
                                                        <xsl:when test="id = 'NEW'">
                                                            <option value="{$id}" selected="selected">
                                                                <xsl:value-of select="name"/>
                                                            </option>
                                                        </xsl:when>
                                                        <xsl:otherwise>
                                                            <xsl:choose>
                                                                <xsl:when test="selected = 'selected'">
                                                                    <option value="{$id}" selected="selected">
                                                                        <xsl:value-of select="name"/>
                                                                    </option>
                                                                </xsl:when>
                                                                <xsl:when test="selected = '1'">
                                                                    <option value="{$id}" selected="selected">
                                                                        <xsl:value-of select="name"/>
                                                                    </option>
                                                                </xsl:when>
                                                                <xsl:otherwise>
                                                                    <option value="{$id}">
                                                                        <xsl:value-of select="name"/>
                                                                    </option>
                                                                </xsl:otherwise>
                                                            </xsl:choose>
                                                        </xsl:otherwise>
                                                    </xsl:choose>
                                                </xsl:for-each>
                                            </select>
                                        </xsl:if>
                                    </td>
                                </tr>
                                <script type="text/javascript">
                                    schedule.params.<xsl:value-of select="name"/> = $('select#<xsl:value-of select="name"/>').val();
                                    $('select#<xsl:value-of select="name"/>').change( function()
                                    {
                                        schedule.params.<xsl:value-of select="name"/> = $(this).val();
                                    });
                                </script>
                            </xsl:for-each>
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
            <script type="text/javascript">
                schedule.params.n_objects = $('select#cboNObjects').val();
                $('select#cboNObjects').change( function()
                {
                    schedule.params.n_objects = $(this).val();
                });
            </script>
        </p>
    </div>
    <script type="text/javascript">
        var composite_id = '<xsl:value-of select="composite_id"/>';
		$(window).load(function() {
            
            schedule.params.n_objects = $('#cboNObjects').val();
            schedule.params.search = $('#txtSearchSchedule').val();
        
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