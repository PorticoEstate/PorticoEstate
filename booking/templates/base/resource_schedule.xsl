<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style typ="text/css" rel="stylesheet">
		#week-selector {list-style: outside none none;}
		#week-selector li {display: inline-block;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
	</style>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
			<div id="resource_schedule">
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
							<img id="pickerImg" src="{resource/picker_img}" />
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
		</div>
		<div class="form-buttons">
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="resource/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'free')"/>;
		$(window).on('load', function() {
		schedule.setupWeekPicker('cal_container');
		schedule.datasourceUrl = '<xsl:value-of select="resource/datasource_url"/>';
		schedule.newApplicationUrl = '<xsl:value-of select="resource/application_link"/>';
		schedule.includeResource = false;
		schedule.colFormatter = 'backendScheduleDateColumn';
		var handleHistoryNavigation = function (state) {
		schedule.date = parseISO8601(state);
		schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
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
	</script>
</xsl:template>
