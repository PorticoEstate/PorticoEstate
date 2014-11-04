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
	<xsl:apply-templates select="form/list_actions"/>
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
toolbar
		<!--xsl:value-of select="php:function('lang', 'toolbar')"/-->
	</label>

	<div id="toolbar">
		  <table >
			<tr>
		<xsl:for-each select="item">
			<xsl:variable name="filter_key" select="concat('filter_', name)"/>
			<xsl:variable name="filter_key_name" select="concat(concat('filter_', name), '_name')"/>
			<xsl:variable name="filter_key_id" select="concat(concat('filter_', name), '_id')"/>
		
			<xsl:choose>
				<xsl:when test="type = 'date-picker'">
					<td valign="top">
					<div class="date-picker">
					<input id="filter_{name}" name="filter_{name}" type="text">
							<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key]"/></xsl:attribute>
						</input>
				</div>
					</td>
				</xsl:when>
				<xsl:when test="type = 'autocomplete'">
					<td class="auto">
						<div class="auto">
							<input id="filter_{name}_name" name="filter_{name}_name" type="text">
								<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key_name]"/></xsl:attribute>
							</input>
							<input id="filter_{name}_id" name="filter_{name}_id" type="hidden">
								<xsl:attribute name="value"><xsl:value-of select="../../../filters/*[local-name() = $filter_key_id]"/></xsl:attribute>
							</input>
							<div id="filter_{name}_container"/>
						</div>
						<script type="text/javascript">	
						YAHOO.util.Event.onDOMReady(function() {
						   var app = "<xsl:value-of select="app"/>";
						   var name = "<xsl:value-of select="name"/>";
							var ui = "<xsl:value-of select="ui"/>";

							var itemSelectCallback = false;
							<xsl:if test="onItemSelect">
								itemSelectCallback = <xsl:value-of select="onItemSelect"/>;
							</xsl:if>

							var onClearSelectionCallback = false;
							<xsl:if test="onClearSelection">
								onClearSelectionCallback = <xsl:value-of select="onClearSelection"/>;
							</xsl:if>

							var requestGenerator = false;
							<xsl:if test="requestGenerator">
								requestGenerator = <xsl:value-of select="requestGenerator"/>;
							</xsl:if>

							<![CDATA[
//							var oAC = YAHOO.portico.autocompleteHelper('index.php?menuaction=booking.ui'+ui+'.index&phpgw_return_as=json&', 
//															 'filter_'+name+'_name', 'filter_'+name+'_id', 'filter_'+name+'_container');

							var oArgs = {menuaction: app + '.ui'+ui+'.index'};
							var requestUrl = phpGWLink('index.php', oArgs, true);
							requestUrl += 'filter_'+name+'_name', 'filter_'+name+'_id', 'filter_'+name+'_container';
						//	alert('FIXME: autocompleteHelper::requestUrl ' + requestUrl );


							if (requestGenerator) {
								oAC.generateRequest = requestGenerator;
							}

							if (itemSelectCallback) {
								oAC.itemSelectEvent.subscribe(itemSelectCallback);
							}

							YAHOO.util.Event.addBlurListener('filter_'+name+'_name', function()
							{
								if (YAHOO.util.Dom.get('filter_'+name+'_name').value == "")
								{
									YAHOO.util.Dom.get('filter_'+name+'_id').value = "";
									if (onClearSelectionCallback) {
										onClearSelectionCallback();
									}
								}
							});

							YAHOO.portico.addPreSerializeQueryFormListener(function(form)
							{
								if (YAHOO.util.Dom.get('filter_'+name+'_name').value == "")
								{
									YAHOO.util.Dom.get('filter_'+name+'_id').value = "";
								} 
							});
							]]>
						});
						</script>
					</td>
				</xsl:when>
				<xsl:when test="type = 'filter'">
					<td valign="top">
					<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>

					<select id="{$name}" name="{$name}">
						<xsl:for-each select="list">
							<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
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
				<xsl:when test="type = 'link'">
					<td valign="top">
					<a href="{href}"><xsl:value-of select="value"/></a>


					</td>
				</xsl:when>
				<xsl:otherwise>
					<td valign="top">
					<input id="innertoolbar">
						<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
						<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
						<xsl:attribute name="onclick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
						<xsl:attribute name="class"><xsl:value-of select="phpgw:conditional(not(class), '', class)"/></xsl:attribute>
					</input>
					</td>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
		  </tr>
			<xsl:if test="item/text and normalize-space(item/text)">
				<thead>
					<tr>
						<xsl:for-each select="item">
							<td>
								<xsl:if test="name">
									<label>
										<xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
										<xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
									</label>
								</xsl:if>
							</td>
						</xsl:for-each>
					</tr>
				</thead>
			</xsl:if>
		</table>
	</div>
</xsl:template>

<xsl:template match="form/list_actions">
	<form id="list_actions_form" method="POST">
		<!-- Form action is set by javascript listener -->
		<div id="list_actions" class='yui-skin-sam'>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<xsl:for-each select="item">
						<td valign="top">
							<input id="innertoolbar">
								<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
								<xsl:attribute name="onclick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
								<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
								<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
							</input>
						</td>
					</xsl:for-each>
				</tr>
			</table>
		</div>
	</form>
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

<xsl:template match="datatable">
  	<xsl:call-template name="datasource-definition" />

</xsl:template>

<xsl:template name="datasource-definition">


	<table id="datatable-container" class="display cell-border compact responsive no-wrap" width="100%">
		<thead>
			<tr>
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
			</tr>
		</thead>
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
													{
														sExtends:		"select",
														sButtonText:	"<xsl:value-of select="text"/>"

													}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
												</xsl:for-each>

												]
											}
                                        ]
								};



				</xsl:when>
				<xsl:otherwise>
					JqueryPortico.TableTools = {};
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
			
			oTable = $('#datatable-container').dataTable( {
				processing:		true,
				serverSide:		true,
				responsive:		true,
				deferRender:	true,
				ajax:			{
					url: ajax_url,
					data: { cat_id: '' },
					type: 'GET'
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
				},
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

	]]>

			/**
			* Add left click action..
			*/
			<xsl:if test="left_click_action != ''">
				$("#datatable-container").on("click", "tr", function() {
					var iPos = oTable.fnGetPosition( this );
					var aData = oTable.fnGetData( iPos ); //complete dataset from json returned from server
					try {
						<xsl:value-of select="left_click_action"/>(aData);
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
						$('select#<xsl:value-of select="name"/>').change( function() {
						oTable.dataTableSettings[0]['ajax']['data']['<xsl:value-of select="name"/>'] = $(this).val();
						oTable.fnDraw();
					} );
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

			function execute_ajax(requestUrl)
			{
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: requestUrl,
					success: function(data) {
						document.getElementById("message").innerHTML += '<br/>' + data.receipt;
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

			function filterData(query)
			{
				var a = $('#datatable-container_filter').find('input');
				var search = {'value': query};
				oTable.dataTableSettings[0]['ajax']['data']['search'] = search;
				oTable.fnDraw();
				a[0].value = query;
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
