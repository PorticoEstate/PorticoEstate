<!-- $Id$ -->
<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data">
	<h2>
		<xsl:value-of select="datatable_name"/>
	</h2>
	<div id="receipt"></div>

	<div class="pure-g">
		<div class="pure-u-1 pure-u-md-1-2">
			<xsl:apply-templates select="form" />
		</div>
		<div class="pure-u-1 pure-u-md-1-2">
			<xsl:call-template name="icon_color_map" />
		</div>
	</div>


		<xsl:apply-templates select="paging"/>
		<div id="list_flash">
			<xsl:call-template name="msgbox"/>
		</div>
		<xsl:apply-templates select="datatable"/>
		<xsl:apply-templates select="form/list_actions"/>
	
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
		<xsl:apply-templates select="toolbar"/>
	</form>

	<form id="update_table_dummy" method='POST' action='' >
	</form>

</xsl:template>


<xsl:template match="toolbar">
	<style id='toggle-box-css' type='text/css' scoped='scoped'>
		.toggle-box {
		display: none;
		}

		.toggle-box + label {
		cursor: pointer;
		display: block;
		font-weight: bold;
		line-height: 21px;
		margin-bottom: 5px;
		}

		.toggle-box + label + #toolbar {
		display: none;
		margin-bottom: 10px;
		}

		.toggle-box:checked + label + #toolbar {
		display: block;
		}

		.toggle-box + label:before {
		background-color: #4F5150;
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		border-radius: 10px;
		color: #FFFFFF;
		content: "+";
		display: block;
		float: left;
		font-weight: bold;
		height: 20px;
		line-height: 20px;
		margin-right: 5px;
		text-align: center;
		width: 20px;
		}

		.toggle-box:checked + label:before {
		content: "\2212";
		}
	</style>
	<div id="active_filters"></div>

	<input class="toggle-box" id="header1" type="checkbox" />
	<label for="header1">
		<xsl:value-of select="php:function('lang', 'filter')"/>
	</label>

	<div id="toolbar" xmlns:php="http://php.net/xsl">
		<table id="toolbar_table" class="pure-table">
			<thead>
				<tr>
					<th>
						<xsl:value-of select="php:function('lang', 'name')"/>
					</th>
					<th>
						<xsl:value-of select="php:function('lang', 'item')"/>
					</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="item">
					<tr>
						<td>

							<label>
								<xsl:attribute name="for">
									<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
								</xsl:attribute>
								<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
							</label>
						</td>
						<td>
							<xsl:variable name="filter_key" select="concat('filter_', name)"/>
							<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
							<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
		
							<xsl:choose>
								<xsl:when test="type = 'date-picker'">
									<div class="date-picker">
										<input id="filter_{name}" name="filter_{name}" type="text">
											<xsl:attribute name="value">
												<xsl:value-of select="../../../filters/*[local-name() = $filter_key]"/>
											</xsl:attribute>
										</input>
									</div>
								</xsl:when>
								<xsl:when test="type = 'filter'">
									<xsl:variable name="name">
										<xsl:value-of select="name"/>
									</xsl:variable>
					
									<select id="{$name}" name="{$name}" width="250" style="width: 250px">
										<xsl:attribute name="onchange">
											<xsl:value-of select="phpgw:conditional(not(onchange), '', onchange)"/>
										</xsl:attribute>
										<xsl:for-each select="list">
											<xsl:variable name="id">
												<xsl:value-of select="id"/>
											</xsl:variable>
											<option value="{$id}">
												<xsl:if test="selected = '1'">
													<xsl:attribute name="selected">
														<xsl:text>selected</xsl:text>
													</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="name"/>
											</option>
										</xsl:for-each>
									</select>
								</xsl:when>
								<xsl:otherwise>
									<input id="innertoolbar">
										<xsl:attribute name="type">
											<xsl:value-of select="phpgw:conditional(not(type), '', type)"/>
										</xsl:attribute>
										<xsl:attribute name="name">
											<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
										</xsl:attribute>
										<xsl:attribute name="onclick">
											<xsl:value-of select="phpgw:conditional(not(onclick), '', onclick)"/>
										</xsl:attribute>
										<xsl:attribute name="value">
											<xsl:value-of select="phpgw:conditional(not(value), '', value)"/>
										</xsl:attribute>
										<xsl:attribute name="href">
											<xsl:value-of select="phpgw:conditional(not(href), '', href)"/>
										</xsl:attribute>
										<xsl:attribute name="class">
											<xsl:value-of select="phpgw:conditional(not(class), '', class)"/>
										</xsl:attribute>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</div>
</xsl:template>

<xsl:template match="datatable">
	<style>
		#components {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		width: 100%;
		border-collapse: collapse;
		}

		#components td, #components th {
		font-size: 1em;
		border: 1px solid #98bf21;
		padding: 3px 7px 2px 7px;
		}

		#components th {
		font-size: 1.1em;
		text-align: left;
		padding-top: 5px;
		padding-bottom: 4px;
		background-color: green;
		color: #ffffff;
		}

		#components tr.alt td {
		color: #000000;
		background-color: #EAF2D3;
		}
		#summary {
		font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
		width: 100%;
		border-collapse: collapse;
		}

		#summary td, #summary th {
		font-size: 1em;
		border: 1px solid #98bf21;
		padding: 3px 7px 2px 7px;
		}

		#summary th {
		font-size: 1.1em;
		text-align: left;
		padding-top: 5px;
		padding-bottom: 4px;
		background-color: green;
		color: #ffffff;
		}

		#summary tr.alt td {
		color: #000000;
		background-color: #EAF2D3;
		}
	</style>

	<xsl:call-template name="datasource-definition" />
</xsl:template>


<xsl:template name="datasource-definition">
	<script type="text/javascript">
<![CDATA[

		/**
		* Detect if browsertab is active - and update when revisit
		*/
		var vis = (function(){
			var stateKey, eventKey, keys = {
				hidden: "visibilitychange",
				webkitHidden: "webkitvisibilitychange",
				mozHidden: "mozvisibilitychange",
				msHidden: "msvisibilitychange"
			};
			for (stateKey in keys) {
				if (stateKey in document) {
					eventKey = keys[stateKey];
					break;
				}
			}
			return function(c) {
				if (c) document.addEventListener(eventKey, c);
				return !document[stateKey];
			}
		})();

		vis(function(){
			if(vis())
			{
					update_table();
			}
		});



		$(document).ready(function(){
			update_table();
		});

		deselect_component = function()
		{
			$("[name='filter_component']").val('');
			update_table();
		};
		update_table = function()
		{
			$("#receipt").html('');

			var report_type = $("#report_type").val();
			var user_id = $("#user_id").val();
			var custom_frontend = $("[name='custom_frontend']").val();

			if(custom_frontend ==1)
			{
				$( "#user_id" ).hide();
				$("[for='user_id']").hide();
			}

			if(user_id < 0 || custom_frontend ==1)
			{
				$( "#entity_group_id" ).hide();
				$("[for='entity_group_id']").hide();
				$( "#location_id" ).hide();
				$("[for='location_id']").hide();
				$("[name='all_items']").hide();
				$("[for='all_items']").hide();
				$( "#org_unit_id" ).hide();
				$("[for='org_unit_id']").hide();
				$("[name='user_only']").hide();
				$("[for='user_only']").hide();
			}
			else
			{
				$( "#entity_group_id" ).show();
				$("[for='entity_group_id']").show();
				$( "#location_id" ).show();
				$("[for='location_id']").show();
				$("[name='all_items']").show();
				$("[for='all_items']").show();
				$( "#org_unit_id" ).show();
				$("[for='org_unit_id']").show();
				$("[name='user_only']").show();
				$("[for='user_only']").show();
			}

			if(report_type == 'summary')
			{
				$("[name='user_only']").hide();
				$("[for='user_only']").hide();
			}
			else
			{
				$("[name='user_only']").show();
				$("[for='user_only']").show();
			}

			var requestUrl = $("#queryForm").attr("action");
			requestUrl += '&phpgw_return_as=json' + "&" + $("#queryForm").serialize();

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: requestUrl,
				success: function(data) {
					if( data != null)
					{
						var components_data = data.components;
						var summary_data = data.summary;
						if(data.location_filter)
						{
							var obj = data.location_filter;
							var htmlString  = "<option value=''>" + obj.length + " register funnet</option>";
							var entity_group_id = $("#entity_group_id").val();
							var location_id = $("#location_id").val();

							if(entity_group_id)
							{
								var selected = '';
								if(location_id == -1)
								{
									selected = ' selected';
								}
								htmlString  += "<option value='-1'" + selected + ">Velg alle</option>";
							}

							$.each(obj, function(i)
							{
								var selected = '';
								if(obj[i].selected == 1)
								{
									selected = ' selected';
								}

								htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";

							});

							$("#location_id").html( htmlString );

						}

						if(components_data !==null)
						{
							$("#tbody").html(components_data.tbody);
							var time_sum = components_data.time_sum;
							var time_sum_actual = components_data.time_sum_actual;

							$("#checkall").html(components_data.checkall);
							$("#total_records").html(components_data.total_records);
							$("#sum_text").html('Sum');
							$("#month0").html(time_sum[0] + '/' + time_sum_actual[0]);
							$("#month1").html(time_sum[1] + '/' + time_sum_actual[1]);
							$("#month2").html(time_sum[2] + '/' + time_sum_actual[2]);
							$("#month3").html(time_sum[3] + '/' + time_sum_actual[3]);
							$("#month4").html(time_sum[4] + '/' + time_sum_actual[4]);
							$("#month5").html(time_sum[5] + '/' + time_sum_actual[5]);
							$("#month6").html(time_sum[6] + '/' + time_sum_actual[6]);
							$("#month7").html(time_sum[7] + '/' + time_sum_actual[7]);
							$("#month8").html(time_sum[8] + '/' + time_sum_actual[8]);
							$("#month9").html(time_sum[9] + '/' + time_sum_actual[9]);
							$("#month10").html(time_sum[10] + '/' + time_sum_actual[10]);
							$("#month11").html(time_sum[11] + '/' + time_sum_actual[11]);
							$("#month12").html(time_sum[12] + '/' + time_sum_actual[12]);
						}

						if(summary_data !== null)
						{
							$( "#status_summary" ).show();
							$( "#components" ).hide();
							$( "#status_summary").html(summary_data);
						}
						else
						{
							$( "#status_summary" ).hide();
							$( "#components" ).show();

						}
					}

				}
			});

		};

		add_from_master = function(myclass)
		{
			var myRadio = $('input[name=master_component]');
			var master_component = myRadio.filter(':checked').val();

			if(!master_component)
			{
				alert('velg master');
				return;
			}

			var selected  = new Array();

			$("." + myclass).each(function()
			{
				if($(this).prop("checked"))
				{
					selected.push( $(this).val() );
				}
			});

			oArgs = {menuaction: 'controller.uicomponent.add_controll_from_master'};
			var requestUrl = phpGWLink('index.php', oArgs, true);

			$.ajax({
				type: 'POST',
				data: {master_component:master_component, target:selected},
				dataType: 'json',
				url: requestUrl,
				success: function(data) {
					if( data != null)
					{
//console.log(data);
						var message = data.message;

						htmlString = "";
						var msg_class = "msg_good";
						if(data.status =='error')
						{
							msg_class = "error";
						}
						htmlString += "<div class=\"" + msg_class + "\">";
						htmlString += message;
						htmlString += '</div>';
						update_table();
						$("#receipt").html(htmlString);

					}
				}
			});

		};

		checkAll = function(myclass)
		{
			$("." + myclass).each(function()
			{
				if($(this).prop("checked"))
				{
					$(this).prop("checked", false);
				}
				else
				{
					$(this).prop("checked", true);
				}
			});
		};

]]>
	</script>
	<table id="components">
		<thead>
			<tr>
				<td id='checkall'>
				</td>
				<td id='total_records'>
				</td>
				<td id='sum_text'>
				</td>
				<td id='month0'>
				</td>
				<td id='month1'>
				</td>
				<td id='month2'>
				</td>
				<td id='month3'>
				</td>
				<td id='month4'>
				</td>
				<td id='month5'>
				</td>
				<td id='month6'>
				</td>
				<td id='month7'>
				</td>
				<td id='month8'>
				</td>
				<td id='month9'>
				</td>
				<td id='month10'>
				</td>
				<td id='month11'>
				</td>
				<td id='month12'>
				</td>
			</tr>
		</thead>
		<thead>
			<tr>
				<xsl:for-each select="//datatable/field">
					<th>
						<xsl:value-of select="label"/>
					</th>
				</xsl:for-each>
			</tr>
		</thead>
		<tbody id="tbody"></tbody>
	</table>
	<div id="status_summary">
		
	</div>
	 
</xsl:template>
