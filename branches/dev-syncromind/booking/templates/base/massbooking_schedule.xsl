<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content_overlay"></div-->
	<style typ="text/css" rel="stylesheet">
		#week-selector {list-style: outside none none;}
		#week-selector li {display: inline-block;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
	</style>
	<!--xsl:call-template name="yui_booking_i18n"/-->
	
	<ul class="pathway">
		<li>
			<xsl:value-of select="building/name"/>
		</li>
		<li>
			<xsl:value-of select="php:function('lang', 'Schedule')"/>
		</li>
	</ul>

      
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value="" />
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="building/tabs" />
			<div id="massbooking_schedule">
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
							<img id="pickerImg" src="{building/picker_img}" />
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
	</form>
	<div id="dialog_schedule"></div>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'free')"/>;
		schedule.createDialogSchedule(300);

		$(window).on('load', function() {
		$('body').prepend($('#content_overlay'));
		schedule.datasourceUrl = '<xsl:value-of select="building/datasource_url"/>';
		schedule.newApplicationUrl = '<xsl:value-of select="building/application_link"/>';
		schedule.includeResource = true;
		schedule.colFormatter = 'frontendScheduleDateColumn';
		var handleHistoryNavigation = function (state) {
		schedule.date = parseISO8601(state);
		schedule.renderSchedule('schedule_container', schedule.datasourceUrl, schedule.date, schedule.colFormatter, schedule.includeResource);
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

		<xsl:if test="backend = '1'">
			$('#header').hide();
		</xsl:if>
	</script>
</xsl:template>
