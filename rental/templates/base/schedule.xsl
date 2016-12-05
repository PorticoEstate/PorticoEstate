<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="schedule">
			<xsl:apply-templates select="schedule"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="schedule" name="schedule">
	<xsl:call-template name="rental_schedule">
		<xsl:with-param name="schedule" select ='schedule'/>
	</xsl:call-template>
	<script type="text/javascript">
		var composite_id = '<xsl:value-of select="schedule/composite_id"/>';
		schedule.rental = {};
		$(window).on('load', function() {

			schedule.params.length = $('#cboNObjects').val();
			schedule.params.search = $('#txtSearchSchedule').val();
			schedule.params.start = 0;
			schedule.params.availability_date_from = "";
			schedule.params.availability_date_to = "";

			schedule.setupWeekPicker('cal_container');

			var img_src = '<xsl:value-of select="schedule/picker_img"/>';

			schedule.datasourceUrl = '<xsl:value-of select="schedule/datasource_url"/>';
			var initialRequest = getUrlData("date") || '<xsl:value-of select="schedule/date"/>';

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
			schedule.toolbar = <xsl:value-of select="schedule/toolbar" />;
		});
	</script>
</xsl:template>