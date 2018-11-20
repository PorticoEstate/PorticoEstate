<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<div class="col-md-8 offset-md-2">
		<xsl:if test="backend != 'true'">
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
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="building/building_link"/>
						</xsl:attribute>
						<xsl:value-of select="building/name"/>
					</a>
				</li>
				<li>
					<xsl:value-of select="php:function('lang', 'Schedule')"/>
				</li>
			</ul>

			<xsl:call-template name="msgbox"/>

			<xsl:if test="building/deactivate_application=0">
				<button onclick="schedule.newApplicationForm();">
					<xsl:value-of select="php:function('lang', 'New booking application')" />
				</button>
				- Søk ledig tid
			</xsl:if>
		</xsl:if>
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
					<img id="pickerImg" src="{building/picker_img}" />
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
		var link = document.createElement( "link" );
		link.href =  strBaseURL.split('?')[0] + "bookingfrontend/css/bookingfrontend.css";
		link.type = "text/css";
		link.rel = "stylesheet";
		link.media = "screen,print";
		document.getElementsByTagName( "head" )[0].appendChild( link );

		var link = document.createElement( "link" );
		link.href =  strBaseURL.split('?')[0] + "phpgwapi/templates/aalesund/bootstrap/css/bootstrap.min.css";
		link.type = "text/css";
		link.rel = "stylesheet";
		link.media = "screen,print";
		document.getElementsByTagName( "head" )[0].appendChild( link );
		
		var lang = <xsl:value-of select="php:function('js_lang', 'free')"/>;
		schedule.createDialogSchedule(300);
		$(window).on('load', function() {
		schedule.setupWeekPicker('cal_container');
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
		schedule.state = state;
		if (state){
		handleHistoryNavigation(state);
		schedule.week = $.datepicker.iso8601Week(schedule.date);
		$('#cal_container #numberWeek').text(schedule.week);
		$("#cal_container #datepicker").datepicker("setDate", parseISO8601(state));
		}
		});
		<xsl:if test="backend = 'true'">
			$('header').hide();
		</xsl:if>
	</script>
</xsl:template>
