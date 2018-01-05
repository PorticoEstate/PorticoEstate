
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="datatable_name">
			<h3>
				<xsl:value-of select="datatable_name"/>
			</h3>
		</xsl:when>
	</xsl:choose>
	<xsl:apply-templates select="datatable"/> 
</xsl:template>

<xsl:template match="datatable">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div id="list_flash">
		<xsl:call-template name="msgbox"/>
	</div>
	<div id="message" class='message'/>
	<xsl:call-template name="top-toolbar" />
	<xsl:call-template name="prizebook_table" />
	<xsl:call-template name="wo_hour_table" />
	<xsl:call-template name="end-toolbar" />
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<div class="pure-g">
				<div class="pure-u-1-3">
					<xsl:apply-templates select="//datatable/workorder_data" />
				</div>
				<div class="pure-u-2-3">
					<xsl:for-each select="//top-toolbar/fields/field">
						<xsl:choose>
							<xsl:when test="type='button'">
								<a id="{id}" class="pure-button pure-button-primary" href="{href}" onclick="{onclick}">
									<xsl:value-of select="value"/>
								</a>
							</xsl:when>
						</xsl:choose>									
					</xsl:for-each>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="workorder_data">
	<div>
		<xsl:value-of select="lang_project_id"/>:
		<span>
			<a  href="{link_project}" >
				<xsl:value-of select="project_id"/>
			</a>
		</span>
	</div>
	<div>
		<xsl:value-of select="lang_workorder_id"/>:
		<span>
			<a  href="{link_workorder}" >
				<xsl:value-of select="workorder_id"/>
			</a>
		</span>
	</div>
	<div>
		<xsl:value-of select="lang_workorder_title"/>:
		<span>
			<xsl:value-of select="workorder_title"/>
		</span>
	</div>
	<div>
		<xsl:value-of select="lang_vendor_name"/>:
		<span>
			<xsl:value-of select="vendor_name"/>

		</span>
	</div>
</xsl:template>

<xsl:template name="prizebook_table">
	<xsl:for-each select="//datatable_def">
		<xsl:if test="container = 'datatable-container_0'">
			<xsl:call-template name="table_setup">
				<xsl:with-param name="container" select ='container'/>
				<xsl:with-param name="requestUrl" select ='requestUrl' />
				<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
				<xsl:with-param name="tabletools" select ='tabletools' />
				<xsl:with-param name="data" select ='data' />
				<xsl:with-param name="config" select ='config' />
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="end-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<form class="pure-form pure-form-stacked">
				<div class="pure-g">
					<div class="pure-u-1">
						<xsl:for-each select="//end-toolbar/fields/field">
							<xsl:choose>
								<xsl:when test="type='button'">
									<button id="{id}" type="{type}" class="pure-button pure-button-primary">
										<xsl:value-of select="value"/>
									</button>
								</xsl:when>
								<xsl:when test="type='label'">
									<xsl:value-of select="value"/>
								</xsl:when>
								<xsl:otherwise>
									<input id="{id}" type="{type}" name="{name}" value="{value}">
										<xsl:if test="type = 'checkbox' and checked = '1'">
											<xsl:attribute name="checked">checked</xsl:attribute>
										</xsl:if>
									</input>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</div>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<xsl:template name="wo_hour_table">
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
		<tfoot>
			<tr>
				<th colspan="2" class="dt-right">
					<div id="lang_sum_calculation"></div>
					<xsl:value-of select="//table_sum/lang_sum_calculation"/>
				</th>
				<th colspan="4"></th>
				<th class="dt-right">
					<div id="value_sum_calculation"></div>
				</th>
				<th class="dt-right">
					<div id="sum_deviation"></div>
				</th>
				<th class="dt-right">
					<div id="sum_result"></div>
				</th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<td colspan="2" class="dt-right">
					<div id="lang_addition_rs"></div>
					<xsl:value-of select="//table_sum/lang_addition_rs"/>
				</td>
				<td colspan="6"></td>
				<td class="dt-right">
					<div id="value_addition_rs"></div>
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="dt-right">
					<div id="lang_addition_percentage"></div>
					<xsl:value-of select="//table_sum/lang_addition_percentage"/>
				</td>
				<td colspan="6"></td>
				<td class="dt-right">
					<div id="value_addition_percentage"></div>
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="dt-right">
					<div id="lang_sum_tax"></div>
					<xsl:value-of select="//table_sum/lang_sum_tax"/>
				</td>
				<td colspan="6"></td>
				<td class="dt-right">
					<div id="value_sum_tax"></div>
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th colspan="2" class="dt-right">
					<div id="lang_total_sum"></div>
					<xsl:value-of select="//table_sum/lang_total_sum"/>
				</th>
				<th colspan="6"></th>
				<th class="dt-right">
					<div id="value_total_sum"></div>
				</th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
	<script>

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
	</script>

	<script type="text/javascript" class="init">
		
		var oTable = null;
		$(document).ready(function() {

		var ajax_url = '<xsl:value-of select="source"/>';
		var download_url = '<xsl:value-of select="download"/>';
		var exclude_colvis = [];
		var editor_cols = [];
		var editor_action = '<xsl:value-of select="editor_action"/>';
			
			
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
									{
										extend: 'copy',
										text: "<xsl:value-of select="php:function('lang', 'copy')"/>"
									},
									{
											text: "<xsl:value-of select="php:function('lang', 'select all')"/>",
											action: function () {
												var api = oTable.api();
												api.rows().select();
												$(".mychecks").each(function()
												{
													$(this).prop("checked", true);
												});
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
											}
										},
										'copyHtml5',
										'csvHtml5',
										'excelHtml5'
									//	'pdfFlash'


									<xsl:choose>
										<xsl:when test="download">
										,{
											text: "<xsl:value-of select="php:function('lang', 'download')"/>",
											action: function (e, dt, node, config) {
											var sUrl = '<xsl:value-of select="download"/>';
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
												alert(iframe.src);
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
															enabled: false,
															className: 'record',
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
															enabled: false,
															className: 'record',
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
		/**
		* Add left click action..
		*/
		<xsl:if test="//left_click_action != ''">
			$("#datatable-container").on("click", "tr", function() {
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

		<xsl:for-each select="//end-toolbar/fields/field">
			<xsl:if test="type = 'button'">
				$('#<xsl:value-of select="id"/>').click( function()
				{
				var sUrl = '<xsl:value-of select="url"/>';
				var params = <xsl:value-of select="params"/>;
				$.each(params, function(i, item)
				{
				if($("#"+item.obj).is(':checked'))
				{
				sUrl += '&amp;' + item.param + '=' + 1;
				}
				});
				window.open(sUrl,'_self');
				});
			</xsl:if>
		</xsl:for-each>
			
		var options = {disablePagination:true, disableFilter:true};
		options.TableTools = JqueryPortico.buttons;
			
		var source = "<xsl:value-of select="source"/>";

		oTable = JqueryPortico.inlineTableHelper('datatable-container', source, JqueryPortico.columns, options );
			
<![CDATA[

			function fnGetSelected( )
			{
				var aReturn = new Array();
				 var aTrs = oTable.fnGetNodes();
				 for ( var i=0 ; i < aTrs.length ; i++ )
				 {
					 if ( $(aTrs[i]).hasClass('selected') )
					 {
						 aReturn.push( i );
					 }
				 }
				 return aReturn;
			}

			function execute_ajax(requestUrl, callback, data,type, dataType)
			{                                       
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

			function substr_count( haystack, needle, offset, length )
			{
				var pos = 0, cnt = 0;

				haystack += '';
				needle += '';
				if(isNaN(offset)) offset = 0;
				if(isNaN(length)) length = 0;
				offset--;

				while( (offset = haystack.indexOf(needle, offset+1)) != -1 )
				{
					if(length > 0 && (offset+needle.length) > length)
					{
						return false;
					} else
					{
						cnt++;
					}
				}
				return cnt;
			}


		});

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
