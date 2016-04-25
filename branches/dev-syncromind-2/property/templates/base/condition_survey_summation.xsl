<!-- $Id: documents_add.xsl 11483 2013-11-24 19:54:40Z sigurdne $ -->

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

<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

		<div class="yui-navset" id="survey_edit_tabview">
		<h1>
			<xsl:value-of select="php:function('lang', 'summation')" />
		</h1>
			<div class="yui-content">
				<dl class="proplist-col">
					<dt>
					<label for="category">
						<xsl:value-of select="php:function('lang', 'condition survey')" />
					</label>
					</dt>
					<dd>
						<select id="survey_id" name="survey_id" onChange="update_summation();" >
							<xsl:apply-templates select="surveys/options"/>
						</select>
					</dd>
					<dt>
					<label for="category">
						<xsl:value-of select="php:function('lang', 'year')" /> 0</label>
					</dt>
					<dd>
						<select id="year" name="year" onChange="update_summation();" >
							<xsl:apply-templates select="years/options"/>
						</select>
					</dd>
				</dl>
			</div>
		<div>
			<xsl:for-each select="datatable_def">
				<xsl:if test="container = 'datatable-container_0'">
					<xsl:call-template name="table_setup">
						<xsl:with-param name="container" select ='container'/>
						<xsl:with-param name="requestUrl" select ='requestUrl' />
						<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
		</div>
		<!--<xsl:call-template name="datasource-definition" />
			<dl class="proplist-col">
				<dt>
					<label><xsl:value-of select="php:function('lang', 'summation')"/></label>
				</dt>
				<dd>
			<table id="datatable-container_0" class="display cell-border compact responsive no-wrap" width="100%">
			</table>					
					<div style="clear:both;" id="datatable-container_0"></div>
				</dd>
			</dl>
		-->
	</div>

</xsl:template>

<xsl:template name="table_setup">
	<xsl:param name="container" />
	<xsl:param name="requestUrl" />
	<xsl:param name="ColumnDefs" />
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
				<th colspan="2" style="text-align:right">Sum:</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
	<script>
		var PreColumns = [
		<xsl:for-each select="$ColumnDefs">
					{
			data:			"<xsl:value-of select="key"/>",
			<xsl:if test="className">
				<xsl:choose>
					<xsl:when test="className">
						<xsl:if test="className ='right'">
							class:	'dt-right',
						</xsl:if>
						<xsl:if test="className ='center'">
							class:	'dt-center',
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						class:	'dt-left',
					</xsl:otherwise>
				</xsl:choose>
						</xsl:if>
			orderable:		<xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
			<xsl:choose>
				<xsl:when test="hidden">
					<xsl:if test="hidden =0">
						visible			:true,
					</xsl:if>
					<xsl:if test="hidden =1">
						class:			'none',
						visible:		false,
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					visible:		true,
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
						<xsl:if test="editor">
						editor: <xsl:value-of select="editor"/>,
					    </xsl:if>
			defaultContent:	"<xsl:value-of select="defaultContent"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
				];
		<![CDATA[
				columns = [];
			
				for(i=0;i < PreColumns.length;i++)
				{
					if ( PreColumns[i]['visible'] == true )
					{
						columns.push(PreColumns[i]);
					}
				}
		]]>

		var options = {disableFilter:true};
		var oTable = JqueryPortico.inlineTableHelper("<xsl:value-of select="$container"/>", <xsl:value-of select="$requestUrl"/>, columns, options);

		$(document).ready(function ()
		{
		var api = oTable.api();
		api.on( 'draw', sum_columns );
		});

		function sum_columns()
		{
		var api = oTable.api();
		// Remove the formatting to get integer data for summation
		var intVal = function ( i ) {
		return typeof i === 'string' ?
		i.replace(/[\$,]/g, '')*1 :
		typeof i === 'number' ?
		i : 0;
		};
			
		var columns = ["2", "3", "4", "5", "6", "7", "8"];
			
		columns.forEach(function(col)
		{
		data = api.column( col, { page: 'current'} ).data();
		pageTotal = data.length ?
		data.reduce(function (a, b){
		return intVal(a) + intVal(b);
		}) : 0;
				
		pageTotal = $.number( pageTotal, 0, ',', '.' );
		$(api.column(col).footer()).html(pageTotal);
  	});
		}

		function update_summation()
		{
		var survey_id = document.getElementById("survey_id").value;
		var year = document.getElementById("year").value;
		var oArgs = {menuaction:'property.uicondition_survey.get_summation', id:survey_id, year: year};
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable, strURL);
		}

	</script>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title">
			<xsl:value-of disable-output-escaping="yes" select="description"/>
		</xsl:attribute>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

