<xsl:template name="rental_schedule">
	<xsl:param name="schedule" />

	<style typ="text/css" rel="stylesheet">
		#schedule_container tbody tr th {background: #eee none repeat scroll 0 0;}
		.schedule_toolbar {margin-bottom: 10px;}
		#week-selector {list-style: outside none none;display: inline-block;vertical-align: middle;}
		#week-selector li {display: inline-block;vertical-align: middle;}
		#cal_container {margin: 0 20px;}
		#cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
		#cal_container #numberWeek {width: 20px;display: inline-block;}
		#schedule_container {display: inline-block;position: relative;}
		#scheduleSearchBox {display: inline-block; vertical-align: middle;}
			#scheduleSearchBox label {margin-right: 5px; margin-left: 20px;}
			#scheduleSearchBox #txtSearchSchedule {}
		.schedule_paginate#schedule-container_paginate {bottom: -50px;position: absolute;right: 0;}
		.schedule_paginate#schedule-container_paginate .ellipsis {padding: 0 1em;}
		.paginate_button {border: 1px solid transparent;border-radius: 2px;box-sizing: border-box;color: #333 !important;cursor: pointer;display: inline-block;margin-left: 2px;min-width: 1.5em;padding: 0.5em 1em;text-align: center;text-decoration: none !important;}
		.paginate_button:hover {background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #585858 0%, #111 100%) repeat scroll 0 0;border: 1px solid #111;color: white !important;}
		.paginate_button:active {background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #2b2b2b 0%, #0c0c0c 100%) repeat scroll 0 0;box-shadow: 0 0 3px #111 inset;outline: medium none;}
		.paginate_button.disabled,
		.paginate_button.disabled:hover,
		.paginate_button.disabled:active {background: transparent none repeat scroll 0 0;border: 1px solid transparent;box-shadow: none;color: #666 !important;cursor: default;}
		.paginate_button.previous {}
		.paginate_button.next {}
		.paginate_button.current,
		.paginate_button.current:hover {background: rgba(0, 0, 0, 0) linear-gradient(to bottom, white 0%, #dcdcdc 100%) repeat scroll 0 0;border: 1px solid #979797;color: #333 !important;}
		tr.trselected td, tr.trselected th {background-color: #acbad4 !important;}
		.toolbar_button {-moz-user-select: none;background-color: #e9e9e9;background-image: linear-gradient(to bottom, white 0%, #e9e9e9 100%);border: 1px solid #999;border-radius: 2px;box-sizing: border-box;color: black;cursor: pointer;display: inline-block;font-size: 0.88em;margin-right: 0.333em;outline: medium none;overflow: hidden;padding: 0.5em 1em;position: relative;text-decoration: none;white-space: nowrap;font-weight: normal;box-shadow: none;}
		.toolbar_button[disabled] {background-color: #f9f9f9;background-image: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);border: 1px solid #d0d0d0;color: #999;cursor: default;}
		.toolbar_button:hover:not([disabled]) {background-color: #e0e0e0;background-image: linear-gradient(to bottom, #f9f9f9 0%, #e0e0e0 100%);border: 1px solid #666;}
		.toolbar_button:active:not([disabled]) {background-color: #e2e2e2;background-image: linear-gradient(to bottom, #f3f3f3 0%, #e2e2e2 100%);box-shadow: 1px 1px 3px #999999 inset;}
		.toolbar_button:focus:not([disabled]) {background-color: #79ace9;background-image: linear-gradient(to bottom, #bddef4 0%, #79ace9 100%);border: 1px solid #426c9e;outline: medium none;text-shadow: 0 1px 0 #c4def1;}
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
								<th>item</th>
							</tr>
						</thead>
						<tbody>
							<script type="text/javascript">
								schedule.params = {};
							</script>
							<xsl:for-each select="schedule/filters">
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
					<img id="pickerImg" src="{schedule/picker_img}" />
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
		<p style="height: 50px; line-height: 50px;">
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
					schedule.params.length = $(this).val();
				});
			</script>
		</p>
	</div>
</xsl:template>