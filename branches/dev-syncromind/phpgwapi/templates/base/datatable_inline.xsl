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

<xsl:template name="table_setup">
	<xsl:param name="container" />
	<xsl:param name="requestUrl" />
	<xsl:param name="ColumnDefs" />
	<xsl:param name="tabletools" />
	<xsl:param name="config" />
	<xsl:param name="data" />
	
	<xsl:variable name="num">
		<xsl:number count="*"/>
	</xsl:variable>
	
	<div id='message{($num - 1)}' class='message'/>
	
	<table id="{$container}" class="display cell-border compact responsive no-wrap" width="100%">
		<thead>
			<tr>
				<xsl:for-each select="$ColumnDefs">
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
			</tr>
		</thead>
		<tfoot>
			<tr>
				<xsl:for-each select="$ColumnDefs">
					<xsl:choose>
						<xsl:when test="hidden">
							<xsl:if test="hidden =0">
								<th>
								</th>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<th>
								<xsl:value-of select="value_footer"/>
							</th>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</tr>
		</tfoot>
	</table>
	
	<script>
		
	var oTable<xsl:number value="($num - 1)"/> = null;
		
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
			<xsl:when test="$tabletools">
					JqueryPortico.TableTools<xsl:number value="($num - 1)"/> = 	{
							"sSwfPath": "phpgwapi/js/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
							"sRowSelect": "multi",
							"aButtons":
								[
									<xsl:for-each select="$tabletools">
										<xsl:choose>
											<xsl:when test="my_name = 'select_all'">
												{
													sExtends: 'select_all',
													fnClick: function (nButton, oConfig, oFlash) {
														TableTools.fnGetInstance('<xsl:value-of select="$container"/>').fnSelectAll();
														//In case there are checkboxes
														$(".mychecks").each(function()
														{
															 $(this).prop("checked", true);
														});
													}
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>												
											</xsl:when>
											<xsl:when test="my_name = 'select_none'">
												{
													sExtends: 'select_none',
													fnClick: function (nButton, oConfig, oFlash) {
														TableTools.fnGetInstance('<xsl:value-of select="$container"/>').fnSelectNone();
														//In case there are checkboxes
														$(".mychecks").each(function()
														{
															 $(this).prop("checked", false);
														});
													}
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>												
											</xsl:when>
											<xsl:when test="my_name = 'download'">
												{
													"sExtends": "download",
													"sButtonText": "Download",
													"sUrl": '<xsl:value-of select="download"/>'
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>												
											</xsl:when>
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
																	var selected = JqueryPortico.fnGetSelected(oTable<xsl:number value="($num - 1)"/>);
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
																		var aData = oTable<xsl:number value="($num - 1)"/>.fnGetData( selected[n] ); //complete dataset from json returned from server
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
																		if(JqueryPortico.substr_count(action,'delete')>0)
																		{               
																				action += "&amp;confirm=yes&amp;phpgw_return_as=json";
																				JqueryPortico.execute_ajax(action, function(result){
																					document.getElementById("message<xsl:number value="($num - 1)"/>").innerHTML += '<br/>' + result;
																					oTable<xsl:number value="($num - 1)"/>.fnDraw();
																				});																			
																		}
																		else if (target == 'ajax')
																		{
																				action += "&amp;phpgw_return_as=json";
																				JqueryPortico.execute_ajax(action, function(result){
																					document.getElementById("message<xsl:number value="($num - 1)"/>").innerHTML += '<br/>' + result;
																					oTable<xsl:number value="($num - 1)"/>.fnDraw();
																				});																				
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
						};
			</xsl:when>
	</xsl:choose>
			
		JqueryPortico.inlineTablesDefined += 1;
		var PreColumns = [
			<xsl:for-each select="$ColumnDefs">
				{
					data:			"<xsl:value-of select="key"/>",
					<xsl:if test="className">
						<xsl:choose>
							<xsl:when test="className='right' or className='center'">
								<xsl:if test="className ='right'">
									class:	'dt-right',
								</xsl:if>
								<xsl:if test="className ='center'">
									class:	'dt-center',
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
									class:	"<xsl:value-of select="className"/>",
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
					orderable:		<xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
					<xsl:choose>
						<xsl:when test="hidden">
							<xsl:if test="hidden =0">
								visible:	true,
							</xsl:if>
							<xsl:if test="hidden =1">
								class:		'none',
								visible:	false,
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
								visible:	true,
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
					defaultContent:	"<xsl:value-of select="defaultContent"/>"
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
		];

		var columns<xsl:number value="($num - 1)"/> = [];
<![CDATA[
		for(i=0;i < PreColumns.length;i++)
		{
			if ( PreColumns[i]['visible'] == true )
			{
]]>
				columns<xsl:number value="($num - 1)"/>.push(PreColumns[i]);
<![CDATA[
			}
		}
]]>

		var options<xsl:number value="($num - 1)"/> = {};
		<xsl:for-each select="$config">
			<xsl:if test="disableFilter">
				options<xsl:number value="($num - 1)"/>.disableFilter = true;
			</xsl:if>
			<xsl:if test="disablePagination">
				options<xsl:number value="($num - 1)"/>.disablePagination = true;
			</xsl:if>
			<xsl:if test="order">
				options<xsl:number value="($num - 1)"/>.order = <xsl:value-of select="order" />;
			</xsl:if>
			<xsl:if test="responsive">
				options<xsl:number value="($num - 1)"/>.responsive = <xsl:value-of select="responsive" />;
			</xsl:if>
		</xsl:for-each>
		if (JqueryPortico.TableTools<xsl:number value="($num - 1)"/>)
		{
			options<xsl:number value="($num - 1)"/>.TableTools = JqueryPortico.TableTools<xsl:number value="($num - 1)"/>;
		}

		<xsl:variable name="dataset">
			<xsl:choose>
			 <xsl:when test="$data !=''">
					<xsl:value-of select="$data" />
			 </xsl:when>
			 <xsl:otherwise>
				   <xsl:text>[]</xsl:text>
			 </xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		oTable<xsl:number value="($num - 1)"/> = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns<xsl:number value="($num - 1)"/>, options<xsl:number value="($num - 1)"/> , <xsl:value-of select="$dataset"/>);
	</script>
</xsl:template>