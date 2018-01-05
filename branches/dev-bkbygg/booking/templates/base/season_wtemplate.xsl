<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style typ="text/css" rel="stylesheet">
		#week-selector {list-style: outside none none;}
		#week-selector li {display: inline-block;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
	</style>
    
	<ul class="pathway">
		<li>
			<a href="{season/buildings_link}">
				<xsl:value-of select="php:function('lang', 'Buildings')" />
			</a>
		</li>
		<li>
			<a href="{season/building_link}">
				<xsl:value-of select="season/building_name"/>
			</a>
		</li>
		<li>
			<xsl:value-of select="php:function('lang', 'Season')" />
		</li>
		<li>
			<a href="{season/season_link}">
				<xsl:value-of select="season/name"/>
			</a>
		</li>
	</ul>

	<xsl:call-template name="msgbox"/>
		
	<div class="pure-form">
		<input type="hidden" name="tab" value="" />
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="season/tabs" />
			<div id="season_wtemplate">
				<fieldset>
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading"></div>
							<div clas="pure-control-group">
								<!--ul id="week-selector">
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
								</ul-->
								<div id="schedule_container"></div>
							</div>
							<form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form"></form>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<xsl:if test="season/permission/write">	
			<div class="pure-control-group">
				<div class="form-buttons">
					<input type="button" class="pure-button pure-button-primary" name="new" onclick="schedule.newAllocationForm('')">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'New allocation')" />
						</xsl:attribute>
					</input>
					<input type="button" class="pure-button pure-button-primary" name="generate_allocations">
						<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/generate_url"/>"</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Generate allocations')" />
						</xsl:attribute>
					</input>
					<input type="button" class="pure-button pure-button-primary" name="cancel">
						<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/cancel_link"/>"</xsl:attribute>
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Cancel')" />
						</xsl:attribute>
					</input>
				</div>
			</div>
		</xsl:if>

	</div>

	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'free')"/>;
		var season_id = <xsl:value-of select="season/id"/>;
		var resource_ids = <xsl:value-of select="season/resources_json"/>;
		var r = [{n: 'ResultSet'},{n: 'Result'}];
        <![CDATA[
	var weekUrl = 'index.php?menuaction=booking.uiseason.wtemplate_json&id=' + season_id + '&phpgw_return_as=json&';
        ]]>
		var colDefs = [];
		$(window).on('load', function() {
		colDefs = [
		{key: 'time', label: '<xsl:value-of select="php:function('lang', 'Time')" />', type: 'th'},
		{key: 'resource', label: '<xsl:value-of select="php:function('lang', 'Resources')" />', formatter: 'scheduleResourceColumn'},
		{key: '1', label: '<xsl:value-of select="php:function('lang', 'Monday')" />', formatter: 'seasonDateColumn'},
		{key: '2', label: '<xsl:value-of select="php:function('lang', 'Tuesday')" />', formatter: 'seasonDateColumn'},
		{key: '3', label: '<xsl:value-of select="php:function('lang', 'Wednesday')" />', formatter: 'seasonDateColumn'},
		{key: '4', label: '<xsl:value-of select="php:function('lang', 'Thursday')" />', formatter: 'seasonDateColumn'},
		{key: '5', label: '<xsl:value-of select="php:function('lang', 'Friday')" />', formatter: 'seasonDateColumn'},
		{key: '6', label: '<xsl:value-of select="php:function('lang', 'Saturday')" />', formatter: 'seasonDateColumn'},
		{key: '7', label: '<xsl:value-of select="php:function('lang', 'Sunday')" />', formatter: 'seasonDateColumn'}
		];
		createTableSchedule('schedule_container', weekUrl, colDefs, r, 'pure-table' );
		});
	</script>

</xsl:template>
