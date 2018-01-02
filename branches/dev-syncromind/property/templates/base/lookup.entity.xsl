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
	<xsl:choose>
		<xsl:when test="datatable_name">
			<h3>
				<xsl:value-of select="datatable_name"/>
			</h3>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="datatable" />
</xsl:template>


<xsl:template name="datatable">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:apply-templates select="form" />
	<div id="list_flash">
		<xsl:call-template name="msgbox"/>
	</div>
	<div id="message" class='message'/>
	<xsl:apply-templates select="datatable"/> 
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

		.toggle-box + label + div {
		display: none;
		margin-bottom: 10px;
		}

		.toggle-box:checked + label + div {
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
	
	<input class="toggle-box" id="header1" type="checkbox" />
	<label for="header1">
		<xsl:value-of select="php:function('lang', 'toolbar')"/>
	</label>

	<div id="toolbar">
		<!--xsl:if test="item/text and normalize-space(item/text)"-->
		<xsl:if test="item">
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
							<xsl:variable name="filter_key" select="concat('filter_', name)"/>
							<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
							<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
							<td>
								<xsl:if test="name">
									<label>
										<xsl:attribute name="for">
											<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
										</xsl:attribute>
										<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
									</label>
								</xsl:if>
							</td>

							<xsl:choose>
								<xsl:when test="type = 'date-picker'">
									<td valign="top">
										<div>
											<input id="filter_{name}" name="filter_{name}" type="text"></input>
										</div>
									</td>
								</xsl:when>
								<xsl:when test="type = 'filter'">
									<td valign="top">
										<xsl:variable name="name">
											<xsl:value-of select="name"/>
										</xsl:variable>

										<select id="{$name}" name="{$name}">
											<xsl:for-each select="list">
												<xsl:variable name="id">
													<xsl:value-of select="id"/>
												</xsl:variable>
												<xsl:if test="id = 'NEW'">
													<option value="{$id}" selected="selected">
														<xsl:value-of select="name"/>
													</option>
												</xsl:if>
												<xsl:if test="id != 'NEW'">
													<option value="{$id}">
														<xsl:value-of select="name"/>
													</option>
												</xsl:if>
											</xsl:for-each>
										</select>
									</td>
								</xsl:when>
								<xsl:when test="type = 'filter-category'">
									<td valign="top">
										<xsl:variable name="name">
											<xsl:value-of select="name"/>
										</xsl:variable>

										<select id="{$name}" name="{$name}" onchange="filterByCategory()">
											<xsl:for-each select="list">
												<xsl:variable name="id">
													<xsl:value-of select="id"/>
												</xsl:variable>
												<xsl:choose>
													<xsl:when test="selected">
														<xsl:if test="selected != ''">
															<option value="{$id}" selected="selected">
																<xsl:value-of select="name"/>
															</option>
														</xsl:if>
													</xsl:when>
													<xsl:otherwise>
														<option value="{$id}">
															<xsl:value-of select="name"/>
														</option>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:for-each>
										</select>
									</td>
								</xsl:when>
								<xsl:when test="type = 'link'">
									<td valign="top">
										<a href="{href}">
											<xsl:if test="onclick">
												<xsl:attribute name="onclick">
													<xsl:value-of select="onclick"/>
												</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="value"/>
										</a>
									</td>
								</xsl:when>
								<xsl:when test="type = 'hidden'">
									<td valign="top">
										<input>
											<xsl:attribute name="type">
												<xsl:value-of select="phpgw:conditional(not(type), '', type)"/>
											</xsl:attribute>
											<xsl:attribute name="id">
												<xsl:value-of select="phpgw:conditional(not(id), '', id)"/>
											</xsl:attribute>
											<xsl:attribute name="name">
												<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
											</xsl:attribute>
											<xsl:attribute name="value">
												<xsl:value-of select="phpgw:conditional(not(value), '', value)"/>
											</xsl:attribute>
										</input>
									</td>
								</xsl:when>
								<xsl:when test="type = 'label'">
									<td valign="top">
										<label>
											<xsl:attribute name="id">
												<xsl:value-of select="phpgw:conditional(not(id), '', id)"/>
											</xsl:attribute>
										</label>
									</td>
								</xsl:when>
								<xsl:otherwise>
									<td valign="top">
										<input id="innertoolbar">
											<xsl:attribute name="type">
												<xsl:value-of select="phpgw:conditional(not(type), '', type)"/>
											</xsl:attribute>
											<xsl:attribute name="name">
												<xsl:value-of select="phpgw:conditional(not(name), '', name)"/>
											</xsl:attribute>
											<xsl:attribute name="onclick">
												<xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/>
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
									</td>
								</xsl:otherwise>
							</xsl:choose>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:if>
	</div>
</xsl:template>

<xsl:template match="form">
	<div id="queryForm">
		<xsl:apply-templates select="toolbar"/>
	</div>
</xsl:template>

<xsl:template match="datatable">
	<xsl:call-template name="datasource-definition" />
</xsl:template>

<xsl:template name="datasource-definition">

	<table id="datatable-container" class="display cell-border compact responsive no-wrap" width="100%">
		<thead>
			<xsl:for-each select="//datatable/field">
				<xsl:choose>
					<xsl:when test="hidden">
						<xsl:if test="hidden =0">
							<th>
								<xsl:value-of select="label"/>
							</th>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<th>
							<xsl:value-of select="label"/>
						</th>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</thead>
	</table>
	
	<script>
		var oTable = null;
		var ajax_url = '<xsl:value-of select="source"/>';
		var columns = [
		<xsl:for-each select="//datatable/field">
			{
			data:			"<xsl:value-of select="key"/>",
			class:			"<xsl:value-of select="className"/>",
			orderable:		<xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
			<xsl:choose>
				<xsl:when test="hidden">
					<xsl:if test="hidden =0">
						visible			:true,
					</xsl:if>
					<xsl:if test="hidden =1">
						class:			'none', //FIXME - virker ikke...'responsive' plukker den fram igjen
						visible			:false,
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					visible			:true,
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="formatter">
				render: function (dummy1, dummy2, oData) {
				try {
				var ret = <xsl:value-of select="formatter"/>("<xsl:value-of select="key"/>", oData);
				}
				catch(err) {
				return err.message;
				}
				return ret;
				},

			</xsl:if>
			<xsl:choose>
				<xsl:when test="editor">
					<xsl:if test="editor =0">
						editor:	false,
					</xsl:if>
					<xsl:if test="editor =1">
						editor:	true,
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					editor:	false,
				</xsl:otherwise>
			</xsl:choose>
			defaultContent:	"<xsl:value-of select="defaultContent"/>"
			}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
		</xsl:for-each>
		];
<![CDATA[
		JqueryPortico.columns = [];

		for(i=0;i < columns.length;i++)
		{
			if ( columns[i]['visible'] == true )
			{
				JqueryPortico.columns.push(columns[i]);
			}
		}
]]>
		var lang_ButtonText_columns = "<xsl:value-of select="php:function('lang', 'columns')"/>";

		//			var download_url = '<xsl:value-of select="download"/>';
		var temp_buttons = [];
		var exclude_colvis = [];
		var editor_cols = [];
		var editor_action = '<xsl:value-of select="editor_action"/>';
		var disablePagination = '<xsl:value-of select="disablePagination"/>';
		var initial_search = {"search": "<xsl:value-of select="query"/>" };

			<xsl:choose>
				<xsl:when test="//datatable/actions">
					var button_def = [
//									{
//										extend: 'colvis',
//										exclude: exclude_colvis,
//										text: function ( dt, button, config ) {
//											return dt.i18n( 'buttons.show_hide', 'Show / hide columns' );
//										}
//									},
									<xsl:choose>
										<xsl:when test="new_item">
											{
											text: "<xsl:value-of select="php:function('lang', 'new')"/>",
											sUrl: '<xsl:value-of select="new_item"/>',

											action: function (e, dt, node, config) {
													var sUrl = config.sUrl;
													window.open(sUrl, '_self');
												}
											},
										</xsl:when>
									</xsl:choose>
									{
											text: "<xsl:value-of select="php:function('lang', 'select all')"/>",
											action: function () {
												var api = oTable.api();
												api.rows().select();
												$(".mychecks").each(function()
												{
													$(this).prop("checked", true);
												});
												var selectedRows = api.rows( { selected: true } ).count();
												api.buttons( '.record' ).enable( selectedRows > 0 );
											}
										},
										{
											text: "<xsl:value-of select="php:function('lang', 'select none')"/>",
											action: function () {
												var api = oTable.api();
												api.rows().deselect();
												$(".mychecks").each(function()
												{
													$(this).prop("checked", false);
												});
												api.buttons( '.record' ).enable( false );
											}
										},
										{
											extend: 'copy',
											text: "<xsl:value-of select="php:function('lang', 'copy')"/>"
										},
										'csvFlash',
										'excelFlash',
										'pdfFlash'
									<xsl:choose>
										<xsl:when test="download">
										,{
											text: "<xsl:value-of select="php:function('lang', 'download')"/>",
											className: 'download',
											sUrl: '<xsl:value-of select="download"/>',
											action: function (e, dt, node, config) {
											  var sUrl = config.sUrl;
											<![CDATA[
												var oParams = {};
												oParams.length = -1;
												oParams.columns = null;
												oParams.start = null;
												oParams.draw = null;
												var addtional_filterdata = oTable.dataTableSettings[0]['ajax']['data'];
												for (var attrname in addtional_filterdata)
												{
													oParams[attrname] = addtional_filterdata[attrname];
												}
												var iframe = document.createElement('iframe');
												iframe.style.height = "0px";
												iframe.style.width = "0px";
												iframe.src = sUrl+"&"+$.param(oParams) + "&export=1";
												if(confirm("This will take some time..."))
												{
													document.body.appendChild( iframe );
												}
												]]>
											}

										}
										</xsl:when>
									</xsl:choose>
									<xsl:choose>
										<xsl:when test="//datatable/actions != ''">
											<xsl:choose>
												<xsl:when test="ungroup_buttons=''">
//													,{
//														extend: "div",
//														text: "Knapper nedenfor gjelder pr valgt element "
//													}
												</xsl:when>
											</xsl:choose>
											<xsl:for-each select="//datatable/actions">
												<xsl:choose>
													<xsl:when test="type = 'custom'">
														,{
															text: "<xsl:value-of select="text"/>",
															<xsl:choose>
																<xsl:when test="className">
																	className: "<xsl:value-of select="className"/>",
																</xsl:when>
																<xsl:otherwise>
																	enabled: false,
																	className: 'record',
																</xsl:otherwise>
															</xsl:choose>
															action: function (e, dt, node, config) {
																<xsl:if test="confirm_msg">
																	var confirm_msg = "<xsl:value-of select="confirm_msg"/>";
																	var r = confirm(confirm_msg);
																	if (r != true) {
																		return false;
																	}
																</xsl:if>
																<xsl:value-of select="custom_code"/>
															}

														}
														<!--xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/-->
													</xsl:when>
													<xsl:otherwise>
														,{
															text: "<xsl:value-of select="text"/>",
															<xsl:choose>
																<xsl:when test="className">
																	className: "<xsl:value-of select="className"/>",
																</xsl:when>
																<xsl:otherwise>
																	enabled: false,
																	className: 'record',
																</xsl:otherwise>
															</xsl:choose>
															action: function (e, dt, node, config) {
																var receiptmsg = [];
																var selected = fnGetSelected();
																var numSelected = selected.length;

																if (numSelected ==0){
																	alert('None selected');
																	return false;
																}

																<xsl:if test="confirm_msg">
																	var confirm_msg = "<xsl:value-of select="confirm_msg"/>";
																	var r = confirm(confirm_msg);
																	if (r != true) {
																		return false;
																	}
																</xsl:if>

																var target = "<xsl:value-of select="target"/>";
																if(!target)
																{
																	target = '_self';
																}

																if (numSelected &gt; 1){
																	target = '_blank';
																}

																var n = 0;
																for (; n &lt; numSelected; ) {
																	// console.log(selected[n]);
																	var aData = oTable.fnGetData( selected[n] ); //complete dataset from json returned from server
																	// console.log(aData);

																	//delete stuff comes here
																	var action = "<xsl:value-of select="action"/>";
																	var my_name = "<xsl:value-of select="my_name"/>";

																	<xsl:if test="parameters">
																		var parameters = <xsl:value-of select="parameters"/>;
																		// console.log(parameters.parameter);
																		var i = 0;
																		len = parameters.parameter.length;
																		for (; i &lt; len; ) {
																			action += '&amp;' + parameters.parameter[i]['name'] + '=' + aData[parameters.parameter[i]['source']];
																			i++;
																		}
																	</xsl:if>

																	// look for the word "DELETE" in URL and my_name
																	if(substr_count(action,'delete')>0 || substr_count(my_name,'delete')>0)
																	{
																		action += "&amp;confirm=yes&amp;phpgw_return_as=json";
																		execute_ajax(action, function(result){
																			document.getElementById("message").innerHTML += '<br/>' + result;
																			oTable.fnDraw();
																		});
																	}
																	else if (target == 'ajax')
																	{
																		action += "&amp;phpgw_return_as=json";
																		execute_ajax(action, function(result){
																			document.getElementById("message").innerHTML += '<br/>' + result;
																			oTable.fnDraw();
																		});
																	}
																	else
																	{
																		window.open(action,target);
																	}
																	n++;
																}
															}
														}
														<!--xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/-->
													</xsl:otherwise>
												</xsl:choose>
											</xsl:for-each>
										</xsl:when>
									</xsl:choose>
								];
								<xsl:choose>
									<xsl:when test="group_buttons = '1'">
										var group_buttons = true;
									</xsl:when>
									<xsl:otherwise>
										var group_buttons = false;
									</xsl:otherwise>
								</xsl:choose>

								if($(document).width() &lt; 1000)
								{
									group_buttons = true;
								}

								$.fn.dataTable.Buttons.swfPath = "phpgwapi/js/DataTables/extensions/Buttons/swf/flashExport.swf";


								if(group_buttons === true)
								{
									JqueryPortico.buttons = [
															{
																extend: 'collection',
																text: "<xsl:value-of select="php:function('lang', 'collection')"/>",
																collectionLayout: 'three-column',
																buttons: button_def
															}
														];

								}
								else
								{
									JqueryPortico.buttons = button_def;
								}
				</xsl:when>
				<xsl:otherwise>
					JqueryPortico.buttons = false;
				</xsl:otherwise>
			</xsl:choose>
<![CDATA[
		$(document).ready(function() 
		{
		
			var options ={};
			options.TableTools = JqueryPortico.buttons;
			options.initial_search = initial_search;
			temp_buttons = JqueryPortico.buttons;
			oTable = JqueryPortico.inlineTableHelper("datatable-container", ajax_url, JqueryPortico.columns, options);
]]>
		/**
		* Add left click action..
		*/
		<xsl:if test="//left_click_action != ''">
			$("#datatable-container").on("click", "tbody tr", function() {
			var iPos = oTable.fnGetPosition( this );
			var aData = oTable.fnGetData( iPos ); //complete dataset from json returned from server
			try {
			<xsl:value-of select="//left_click_action"/>
			}
			catch(err) {
			document.getElementById("message").innerHTML = err.message;
			}
			});
		</xsl:if>

		<xsl:for-each select="//form/toolbar/item">
			<xsl:if test="type = 'filter'">
				$('select#<xsl:value-of select="name"/>').change( function()
				{
				filterData('<xsl:value-of select="name"/>', $(this).val());
				<xsl:value-of select="extra"/>
				});
			</xsl:if>
		</xsl:for-each>

<![CDATA[
			
			function searchData(query)
			{
				var api = oTable.api();
				api.search( query ).draw();
			}

			function filterData(param, value)
			{
				oTable.dataTableSettings[0]['ajax']['data'][param] = value;
				oTable.fnDraw();
			}
		});
		
		function filterByCategory()
		{
			var data = {"head": 1};
]]>			
		<xsl:for-each select="//form/toolbar/item">
			data['<xsl:value-of select="name"/>'] = $('#<xsl:value-of select="name"/>').val();
		</xsl:for-each>

<![CDATA[			
			JqueryPortico.execute_ajax(ajax_url,
				function(result)
				{

					/**
					* Sigurd: queryPortico.buttons is passed as reference - and destroyed in the "api.destroy();"
					*/
					var buttons_def_temp = JqueryPortico.buttons;
					var buttons_def = [];
					for (i=0;i<buttons_def_temp.length;i++)
					{
						buttons_def.push(buttons_def_temp[i]);
					}

					api = oTable.api();
					api.buttons(0,null).remove();
					api.destroy();

					//Reset the destroyed values.
					JqueryPortico.buttons = buttons_def;
					$('#' + result.datatable_def.container).empty();
					$('#' + result.datatable_def.container).append(result.datatable_head);

					var download = result.datatable_def.download || false;
					if(download)
					{
						for (i=0;i<buttons_def.length;i++)
						{
							if(typeof(buttons_def[i].className) != 'undefined' && buttons_def[i].className == "download")
							{
								buttons_def[i].sUrl = phpGWLink('index.php',download);
							}
						}
					}

					options ={};
					options.TableTools = buttons_def;
					options.initial_search = initial_search;

					var render;
					var columns = [];
					var PreColumns = result.datatable_def.ColumnDefs;
					for (i=0;i<PreColumns.length;i++)
					{
						if(typeof(PreColumns[i].formatter) != 'undefined' && PreColumns[i].formatter)
						{
							render = eval(PreColumns[i].formatter);
						}
						else
						{
							render = false;
						}

						if (PreColumns[i].hidden == false)
						{
							columns.push({"data":PreColumns[i].key, "class":PreColumns[i].className, "orderable":PreColumns[i].sortable, "render":render} );
						}
					}
					var requestUrl = $('<div/>').html(result.datatable_def.requestUrl).text();
					oTable = JqueryPortico.inlineTableHelper(result.datatable_def.container, requestUrl, columns , options);
				}, data, "GET", "json"
			);
		}


		function execute_ajax(requestUrl, callback, data,type, dataType)
		{
			api = oTable.api();
			type = typeof type !== 'undefined' ? type : 'POST';
			dataType = typeof dataType !== 'undefined' ? dataType : 'html';
			data = typeof data !== 'undefined' ? data : {};

			$.ajax({
				type: type,
				dataType: dataType,
				data: data,
				url: requestUrl,
				success: function(result) {
					callback(result);
				}
			});
		}

]]>
	</script>

	<script>
		<xsl:choose>
			<xsl:when test="//js_lang != ''">
				var lang = <xsl:value-of select="//js_lang"/>;
			</xsl:when>
		</xsl:choose>
	</script>

</xsl:template>