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
	<xsl:call-template name="top-toolbar" />
  	<xsl:call-template name="datasource-definition" />
	<xsl:call-template name="down-toolbar" />
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<form class="pure-form pure-form-stacked">
				<div class="pure-g">
					<div class="pure-u-1-3">
						<xsl:apply-templates select="//datatable/workorder_data" />
					</div>
					<div class="pure-u-2-3">
						<xsl:for-each select="//top-toolbar/fields/field">
							<xsl:choose>
								<xsl:when test="type='button'">
									<button id="{id}" type="{type}" class="pure-button pure-button-primary"><xsl:value-of select="value"/></button>
								</xsl:when>
							</xsl:choose>									
						</xsl:for-each>
					</div>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<xsl:template match="workorder_data">
	<div><xsl:value-of select="lang_project_id"/>:<a href="{link_project}"><xsl:value-of select="project_id"/></a></div>
	<div><xsl:value-of select="lang_workorder_id"/>:<a href="{link_workorder}"><xsl:value-of select="workorder_id"/></a></div>
	<div><xsl:value-of select="lang_workorder_title"/>:<xsl:value-of select="workorder_title"/></div>
	<div><xsl:value-of select="lang_vendor_name"/>:<xsl:value-of select="vendor_name"/></div>
</xsl:template>

<xsl:template name="down-toolbar">
	<div class="toolbar-container">
		<div class="toolbar">
			<form class="pure-form pure-form-stacked">
				<div class="pure-g">
					<div class="pure-u-1">
						<xsl:for-each select="//down-toolbar/fields/field">
							<xsl:choose>
								<xsl:when test="type='button'">
									<button id="{id}" type="{type}" class="pure-button pure-button-primary"><xsl:value-of select="value"/></button>
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
				<th colspan="2" class="dt-right"><xsl:value-of select="//table_sum/lang_sum_calculation"/></th>
				<th colspan="4"></th>
				<th class="dt-right"><xsl:value-of select="//table_sum/value_sum_calculation"/></th>
				<th class="dt-right"><xsl:value-of select="//table_sum/sum_deviation"/></th>
				<th class="dt-right"><xsl:value-of select="//table_sum/sum_result"/></th>
				<th></th>
				<th></th>
			</tr>
			<tr>
				<td colspan="2" class="dt-right"><xsl:value-of select="//table_sum/lang_addition_rs"/></td>
				<td colspan="6"></td>
				<td class="dt-right"><xsl:value-of select="//table_sum/value_addition_rs"/></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="dt-right"><xsl:value-of select="//table_sum/lang_addition_percentage"/></td>
				<td colspan="6"></td>
				<td class="dt-right"><xsl:value-of select="//table_sum/value_addition_percentage"/></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="dt-right"><xsl:value-of select="//table_sum/lang_sum_tax"/></td>
				<td colspan="6"></td>
				<td class="dt-right"><xsl:value-of select="//table_sum/value_sum_tax"/></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th colspan="2" class="dt-right"><xsl:value-of select="//table_sum/lang_total_sum"/></th>
				<th colspan="6"></th>
				<th class="dt-right"><xsl:value-of select="//table_sum/value_total_sum"/></th>
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
		
		$(document).ready(function() {

			var ajax_url = '<xsl:value-of select="source"/>';
			var download_url = '<xsl:value-of select="download"/>';
			var exclude_colvis = [];
			var editor_cols = [];
			var editor_action = '<xsl:value-of select="editor_action"/>';
			
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

			<xsl:for-each select="//top-toolbar/fields/field">
				<xsl:if test="type = 'button'">
					$('#<xsl:value-of select="id"/>').click( function() 
					{
						var sUrl = '<xsl:value-of select="url"/>';
						window.open(sUrl,'_self');
					});
				</xsl:if>
			</xsl:for-each>

			<xsl:for-each select="//down-toolbar/fields/field">
				<xsl:if test="type = 'button'">
					$('#<xsl:value-of select="id"/>').click( function() 
					{
						var sUrl = '<xsl:value-of select="url"/>';
						window.open(sUrl,'_self');
					});
				</xsl:if>
			</xsl:for-each>
			
			var options = {disablePagination:true, disableFilter:true};
			options.TableTools = JqueryPortico.TableTools;
			var source = "<xsl:value-of select="source"/>";

			var oTable = JqueryPortico.inlineTableHelper('datatable-container', source, JqueryPortico.columns, options );
			
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
