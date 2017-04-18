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
	<xsl:param name="separator" select="'_'" />
	
	<xsl:variable name="num">
		<xsl:number value="substring-after($container, $separator)"/>
	</xsl:variable>
	
	<div id='message{$num}' class='message'/>
	
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
		
	var oTable<xsl:number value="$num"/> = null;
		
	<xsl:choose>
			<xsl:when test="$tabletools">
					JqueryPortico.buttons<xsl:number value="$num"/> = 	{
							buttons: 
								[
									<xsl:for-each select="$tabletools">
										<xsl:choose>
											<xsl:when test="my_name = 'select_all'">
												{
													text: "<xsl:value-of select="php:function('lang', 'select all')"/>",
													action: function () {
														var api = oTable<xsl:number value="$num"/>.api();
														api.rows().select();
														$(".mychecks").each(function()
														{
															 $(this).prop("checked", true);
														});
													//	var selectedRows = api.rows( { selected: true } ).count();
														var selectedRows = api.rows('.selected').data().length;

														api.buttons( '.record' ).enable( selectedRows > 0 );
													}
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
											</xsl:when>
											<xsl:when test="my_name = 'excelHtml5'">
												'excelHtml5'
												<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
											</xsl:when>
											<xsl:when test="my_name = 'select_none'">
												{
													text: "<xsl:value-of select="php:function('lang', 'select none')"/>",
													action: function () {
														var api = oTable<xsl:number value="$num"/>.api();
														api.rows().deselect();
														$(".mychecks").each(function()
														{
															 $(this).prop("checked", false);
														});
														api.buttons( '.record' ).enable( false );
													}
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>												
											</xsl:when>
											<xsl:when test="my_name = 'download'">
												{
													text: "<xsl:value-of select="php:function('lang', 'download')"/>",
													className: 'download',
													sUrl: '<xsl:value-of select="download"/>',
													action: function (e, dt, node, config) {
													  var sUrl = config.sUrl;
													  var addtional_filterdata = oTable<xsl:number value="$num"/>.dataTableSettings[0]['ajax']['data'];
													<![CDATA[
														var oParams = {};
														oParams.length = -1;
														oParams.columns = null;
														oParams.start = null;
														oParams.draw = null;
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
												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>												
											</xsl:when>
											<xsl:when test="type = 'custom'">
												{
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

												}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
											</xsl:when>
											<xsl:otherwise>
												{
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
																	var selected = JqueryPortico.fnGetSelected(oTable<xsl:number value="$num"/>);
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
																		var aData = oTable<xsl:number value="$num"/>.fnGetData( selected[n] ); //complete dataset from json returned from server
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
																					document.getElementById("message<xsl:number value="$num"/>").innerHTML += '<br/>' + result;
																					oTable<xsl:number value="$num"/>.fnDraw();
																				});																			
																		}
																		else if (target == 'ajax')
																		{
																				action += "&amp;phpgw_return_as=json";
																				JqueryPortico.execute_ajax(action, function(result){
																					document.getElementById("message<xsl:number value="$num"/>").innerHTML += '<br/>' + result;
																					oTable<xsl:number value="$num"/>.fnDraw();
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
					<xsl:if test="formatter !=''">
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
								editor: false,
							</xsl:if>
							<xsl:if test="editor =1">
								editor: true,
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							editor: false,
						</xsl:otherwise>
					</xsl:choose>
					defaultContent:	"<xsl:value-of select="defaultContent"/>"
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
		];

		var columns<xsl:number value="$num"/> = [];
<![CDATA[
		for(i=0;i < PreColumns.length;i++)
		{
			if ( PreColumns[i]['visible'] == true )
			{
]]>
				columns<xsl:number value="$num"/>.push(PreColumns[i]);
<![CDATA[
			}
		}
]]>

		var options<xsl:number value="$num"/> = {};
		<xsl:for-each select="$config">
			<xsl:if test="allrows">
				options<xsl:number value="$num"/>.allrows = true;
			</xsl:if>
			<xsl:if test="singleSelect">
				options<xsl:number value="$num"/>.singleSelect = true;
			</xsl:if>
			<xsl:if test="disableFilter">
				options<xsl:number value="$num"/>.disableFilter = true;
			</xsl:if>
			<xsl:if test="disablePagination">
				options<xsl:number value="$num"/>.disablePagination = true;
			</xsl:if>
			<xsl:if test="order">
				options<xsl:number value="$num"/>.order = <xsl:value-of select="order" />;
			</xsl:if>
			<xsl:if test="responsive">
				options<xsl:number value="$num"/>.responsive = true;
			</xsl:if>
			<xsl:if test="editor_action">
				options<xsl:number value="$num"/>.editor_action = "<xsl:value-of select="editor_action" />";
			</xsl:if>
			<xsl:if test="rows_per_page">
				options<xsl:number value="$num"/>.rows_per_page = "<xsl:value-of select="rows_per_page" />";
			</xsl:if>
		</xsl:for-each>
		if (JqueryPortico.buttons<xsl:number value="$num"/>)
		{
			options<xsl:number value="$num"/>.TableTools = JqueryPortico.buttons<xsl:number value="$num"/>;
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
//		$(document).ready(function() {
		var paramsTable<xsl:number value="$num"/> = {};
		oTable<xsl:number value="$num"/> = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns<xsl:number value="$num"/>, options<xsl:number value="$num"/> , <xsl:value-of select="$dataset"/>, <xsl:number value="$num"/>);
//	});
	</script>
</xsl:template>