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
	<div id="list_flash">
		<xsl:call-template name="msgbox"/>
	</div>
	<div id="message" class='message'/>
	<xsl:apply-templates select="datatable"/> 
</xsl:template>


<xsl:template match="datatable">
	<xsl:choose>
		<xsl:when test="datatable/actions/form/fields/field!=''">
			<div class="toolbar-container">
				<div class="toolbar">
					<form>
						<xsl:apply-templates select="datatable/actions/form/fields/field" />
					</form>
				</div>
			</div>
		</xsl:when>
	</xsl:choose>
  	<xsl:call-template name="datasource-definition" />
	<xsl:choose>
		<xsl:when test="datatable/actions/down-toolbar/fields/field!=''">
			<div class="toolbar-container">
				<div class="toolbar">
					<form>
						<xsl:apply-templates select="datatable/actions/down-toolbar/fields/field" />
					</form>
				</div>
			</div>
		</xsl:when>
	</xsl:choose>
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
		<tfoot>
			<tr>
				<xsl:for-each select="//datatable/field">
					<xsl:choose>
						<xsl:when test="hidden">
							<xsl:if test="hidden =0">
								<th>
								</th>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<th>
							</th>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
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
//		console.log(JqueryPortico.columns);
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
			
<![CDATA[
			TableTools.BUTTONS.download = {
				"sAction": "text",
				"sTag": "default",
				"sFieldBoundary": "",
				"sFieldSeperator": "\t",
				"sNewLine": "<br>",
				"sToolTip": "",
				"sButtonClass": "DTTT_button_text",
				"sButtonClassHover": "DTTT_button_text_hover",
				"sButtonText": "Download",
				"mColumns": "all",
				"bHeader": true,
				"bFooter": true,
				"sDiv": "",
				"fnMouseover": null,
				"fnMouseout": null,
				"fnClick": function( nButton, oConfig ) {
					var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
					oParams.length = -1;
					oParams.columns = null;
					oParams.start = null;
					oParams.draw = null;
					var iframe = document.createElement('iframe');
					iframe.style.height = "0px";
					iframe.style.width = "0px";
					iframe.src = oConfig.sUrl+"?"+$.param(oParams) + "&export=1";
					if(confirm("This will take some time..."))
					{
						document.body.appendChild( iframe );
					}
				},
				"fnSelect": null,
				"fnComplete": null,
				"fnInit": null
			};
	]]>
		<xsl:choose>
				<xsl:when test="//datatable/actions">
						JqueryPortico.TableTools = 	{
								"sSwfPath": "phpgwapi/js/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
								"sRowSelect": "multi",
								"aButtons":
									[
											{
							                    "sExtends":    "collection",
												"sButtonText": "Operation",
												"aButtons": [
													'copy',
													"select_all",
													"select_none"
												<xsl:choose>
														<xsl:when test="download">
													,{
														"sExtends": "download",
														"sButtonText": "Download",
														"sUrl": '<xsl:value-of select="download"/>'
													}
													</xsl:when>
												</xsl:choose>
												<xsl:choose>
														<xsl:when test="//datatable/actions">
													,
													{
														sExtends: "div",
														sButtonText: "Knapper nedenfor gjelder pr valgt element "
													},
														</xsl:when>
												</xsl:choose>

												<xsl:for-each select="//datatable/actions">
													<xsl:choose>
														<xsl:when test="type = 'custom'">
															{
																sExtends:		"select",
																sButtonText:	"<xsl:value-of select="text"/>",
																fnClick:		function (nButton, oConfig, oFlash) {

																					<xsl:if test="confirm_msg">
																						var confirm_msg = "<xsl:value-of select="confirm_msg"/>";
																						var r = confirm(confirm_msg);
																						if (r != true) {
																							return false;
																						}
																					</xsl:if>

																					var action = "<xsl:value-of select="action"/>";

																					<xsl:if test="parameters">
																						var parameters = <xsl:value-of select="parameters"/>;
																						var i = 0;
																						len = parameters.parameter.length;
																						for (; i &lt; len; ) {
																							action += '&amp;' + parameters.parameter[i]['name'] + '=' + aData[parameters.parameter[i]['source']];
																							i++;
																						}
																					</xsl:if>

																					<xsl:value-of select="custom_code"/>	
																		}

															}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
														</xsl:when>
														<xsl:otherwise>
															{
																sExtends:		"select",
																sButtonText:	"<xsl:value-of select="text"/>",
																fnClick:		function (nButton, oConfig, oFlash) {
																				var receiptmsg = [];
																				var selected = fnGetSelected();
																				var numSelected = 	selected.length;

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

																	//				console.log(selected[n]);
																					var aData = oTable.fnGetData( selected[n] ); //complete dataset from json returned from server
																	//				console.log(aData);

																					//delete stuff comes here
																					var action = "<xsl:value-of select="action"/>";

																					<xsl:if test="parameters">
																						var parameters = <xsl:value-of select="parameters"/>;
																	//						console.log(parameters.parameter);
																						var i = 0;
																						len = parameters.parameter.length;
																						for (; i &lt; len; ) {
																							action += '&amp;' + parameters.parameter[i]['name'] + '=' + aData[parameters.parameter[i]['source']];
																							i++;
																						}
																					</xsl:if>
																					
																					// look for the word "DELETE" in URL
																					if(substr_count(action,'delete')>0)
																					{               
																							action += "&amp;confirm=yes&amp;phpgw_return_as=json";
																							execute_ajax(action, function(result){
																								document.getElementById("message").innerHTML += '<br/>' + result;
																							});
																							oTable.fnDraw();
																					}
																					else if (target == 'ajax')
																					{
																							action += "&amp;phpgw_return_as=json";
																							execute_ajax(action, function(result){
																								document.getElementById("message").innerHTML += '<br/>' + result;
																							});
																							oTable.fnDraw();
																					}
																					else
																					{
																						window.open(action,target);
																					}

																					n++;
																				}
																		}

															}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
														</xsl:otherwise>
													</xsl:choose>
												</xsl:for-each>

												]
											}
                                        ]
								};



				</xsl:when>
				<xsl:otherwise>
					JqueryPortico.TableTools = {
						"sSwfPath": "phpgwapi/js/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
					};
				</xsl:otherwise>
			</xsl:choose>
<![CDATA[

			for(i=0;i < JqueryPortico.columns.length;i++)
			{
				if (JqueryPortico.columns[i]['visible'] != 'undefined' && JqueryPortico.columns[i]['visible'] == false)
				{
					exclude_colvis.push(i);
				}
			}

			for(i=0;i < JqueryPortico.columns.length;i++)
			{
				if (JqueryPortico.columns[i]['editor'] === true)
				{
					editor_cols.push({sUpdateURL:editor_action + '&field_name=' + JqueryPortico.columns[i]['data']});
				} else {
					editor_cols.push(null);
				}
			}

		$(document).ready(function() {
			
			oTable = $('#datatable-container').dataTable( {
				processing:		true,
				serverSide:		true,
				responsive:		true,
				deferRender:	true,
				ajax:			{
					url: ajax_url,
					data: {},
					type: 'GET'
				},
				fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
								if(typeof(aData['priority'])!= undefined && aData['priority'] > 0)
								{
									$('td', nRow).addClass('priority' + aData['priority']);
								}
                },
				fnDrawCallback: function () {
					oTable.makeEditable({
							sUpdateURL: editor_action,
							fnOnEditing: function(input){  
								cell = input.parents("td");
								id = input.parents("tr")
										   .children("td:first")
										   .text();
								return true;
							},
							fnOnEdited: function(status, sOldValue, sNewCellDisplayValue, aPos0, aPos1, aPos2)
							{ 	
								document.getElementById("message").innerHTML += '<br/>' + status;
							},
							oUpdateParameters: { 
								"id": function(){ return id; }
							},
							aoColumns: editor_cols,		
						    sSuccessResponse: "IGNORE",
							fnShowError: function(){ return; }		
					});
					if(typeof(addFooterDatatable) == 'function')
					{
						addFooterDatatable(oTable);
					}
				},
				fnFooterCallback: function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
					if(typeof(addFooterDatatable2) == 'function')
					{
						addFooterDatatable2(nRow, aaData, iStart, iEnd, aiDisplay,oTable);
					}
				},//alternative
				lengthMenu:		JqueryPortico.i18n.lengthmenu(),
				language:		JqueryPortico.i18n.datatable(),
				columns:		JqueryPortico.columns,
				colVis: {
								exclude: exclude_colvis
				},
				dom:			'lCT<"clear">f<"top"ip>rt<"bottom"><"clear">',
				stateSave:		true,
				stateDuration: -1, //sessionstorage
				tabIndex:		1,
				oTableTools: JqueryPortico.TableTools
			} );

});

	]]>

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

			/**
			* Add dbl click action..
			*/
			<xsl:if test="dbl_click_action != ''">
				$("#datatable-container").on("dblclick", "tr", function() {
					var iPos = oTable.fnGetPosition( this );
					var aData = oTable.fnGetData( iPos ); //complete dataset from json returned from server
					try {
						<xsl:value-of select="dbl_click_action"/>(aData);
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
				<xsl:if test="type = 'date-picker'">
					var previous_<xsl:value-of select="id"/>;
					$("#filter_<xsl:value-of select="id"/>").on('keyup change', function ()
					{
						if ( $.trim($(this).val()) != $.trim(previous_<xsl:value-of select="id"/>) ) 
						{
							filterData('<xsl:value-of select="id"/>', $(this).val());
							previous_<xsl:value-of select="id"/> = $(this).val();
						}
					});
				</xsl:if>
			</xsl:for-each>

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


		} );

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


<xsl:template match="field">
	<xsl:variable name="id" select="phpgw:conditional(id, id, generate-id())"/>
	<xsl:variable name="align">
		<xsl:choose>
			<xsl:when test="style='filter'">float:left</xsl:when>
			<xsl:otherwise>float:right</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<div style="{$align}" class="field">
		<xsl:if test="text">
			<label for="{$id}">
				<xsl:value-of select="text"/>
				<xsl:text> </xsl:text>
			</label>
		</xsl:if>

		<xsl:choose>
			<xsl:when test="type='link'">
				<a id="{id}" href="#" onclick="{url}" tabindex="{tab_index}"><xsl:value-of select="value"/></a>
			</xsl:when>
			<xsl:when test="type='label_org_unit'">
				<table><tbody><tr><td><span id="txt_org_unit"></span></td></tr></tbody></table>
			</xsl:when>
			<xsl:when test="type='label_date'">
				<table><tbody><tr><td><span id="txt_start_date"></span></td></tr><tr><td><span id="txt_end_date"></span></td></tr></tbody></table>
			</xsl:when>
			<xsl:when test="type='label'">
				<xsl:value-of select="value"/>
			</xsl:when>
			<xsl:when test="type='img'">
				<img id="{id}" src="{src}" alt="{alt}" title="{alt}" style="cursor:pointer; cursor:hand;" tabindex="{tab_index}" />
			</xsl:when>
			<xsl:when test="type='select'">
				<select id="{id}" name="{name}" alt="{alt}" title="{alt}" style="cursor:pointer; cursor:hand;" tabindex="{tab_index}">
					<xsl:if test="onchange">
						<xsl:attribute name="onchange"><xsl:value-of select="onchange"/></xsl:attribute>
					</xsl:if>
 		     		<xsl:for-each select="values">
						<option value="{id}">
							<xsl:if test="selected != 0">
								<xsl:attribute name="selected" value="selected" />
							</xsl:if>
							<xsl:value-of disable-output-escaping="yes" select="name"/>
						</option>
 		     		</xsl:for-each>
				</select>			
			</xsl:when>
			<xsl:otherwise>
				<input id="{$id}" type="{type}" name="{name}" value="{value}" class="{type}">
					<xsl:if test="size">
						<xsl:attribute name="size"><xsl:value-of select="size"/></xsl:attribute>
					</xsl:if>

					<xsl:if test="tab_index">
						<xsl:attribute name="tabindex"><xsl:value-of select="tab_index"/></xsl:attribute>
					</xsl:if>

					<xsl:if test="type = 'checkbox' and checked = '1'">
						<xsl:attribute name="checked">checked</xsl:attribute>
					</xsl:if>

					<xsl:if test="readonly">
						<xsl:attribute name="readonly">'readonly'</xsl:attribute>
						<xsl:attribute name="onMouseout">window.status='';return true;</xsl:attribute>
					</xsl:if>

					<xsl:if test="onkeypress">
						<xsl:attribute name="onkeypress"><xsl:value-of select="onkeypress"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="class">
						<xsl:attribute name="class"><xsl:value-of select="class"/></xsl:attribute>
					</xsl:if>

				</input>
			</xsl:otherwise>
		</xsl:choose>

	</div>
</xsl:template>