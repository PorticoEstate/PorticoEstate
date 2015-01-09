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
								<xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
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
						<xsl:when test="type = 'filter-category'">
							<td valign="top">
								<xsl:variable name="name"><xsl:value-of select="name"/></xsl:variable>

								<select id="{$name}" name="{$name}" onchange="filterByCategory()">
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
									<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
									<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
									<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
								</input>
							</td>
						</xsl:when>
						<xsl:when test="type = 'label'">
							<td valign="top">
								<label><xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute></label>
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
				</tr>
				</xsl:for-each>
			</tbody>
		</table>
		</xsl:if>
	</div>
</xsl:template>

<xsl:template match="form">
	<div id="queryForm">
		<!--xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute-->
		<xsl:apply-templates select="toolbar"/>
	</div>

	<!--form id="update_table_dummy" method='POST' action='' >
	</form-->

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
//		console.log(JqueryPortico.columns);
]]>

	$(document).ready(function() {
		
		oTable = JqueryPortico.inlineTableHelper("datatable-container", '', JqueryPortico.columns);

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
		
		function filterByCategory()
		{
			var data = {"cat_id": $('#cat_id').val(), "head": 1};
			JqueryPortico.execute_ajax(ajax_url,
				function(result){
					alert('mensaje');
				}, data, "GET", "json"
			);
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