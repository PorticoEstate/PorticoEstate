<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
	<div class="content">
		<xsl:for-each select="pathway">
			<ul class="pathway">
				<li>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Home')" />
					</a>
				</li>
				<li>
					<a href="{building_link}">
						<xsl:value-of select="building_name"/>
					</a>
				</li>
				<li>
					<a href="{resource_link}">
						<xsl:value-of select="resource_name"/>
					</a>
				</li>
				<li>
					<xsl:value-of select="lang_schedule"/>
				</li>
			</ul>
		</xsl:for-each>


		<button onclick="window.location.href='{resource/application_link}'">
			<xsl:value-of select="php:function('lang', 'New booking application')" />
		</button>

		<xsl:call-template name="msgbox"/>
		<ul id="week-selector">
			<li>
				<a id="btnPrevWeek" class="moveWeek" onclick="schedule.prevWeek(); return false">
					<xsl:value-of select="php:function('lang', 'Previous week')"/>
				</a>
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
				<a id="btnPrevWeek" class="moveWeek" onclick="schedule.nextWeek(); return false">
					<xsl:value-of select="php:function('lang', 'Next week')"/>
				</a>
			</li>
		</ul>

		<div id="schedule_container"/>
	</div>
	<div id="dialog_schedule"></div>

	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'free')"/>;
		schedule.createDialogSchedule(300);
		$(window).on('load', function(){
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

	</script>

</xsl:template>
