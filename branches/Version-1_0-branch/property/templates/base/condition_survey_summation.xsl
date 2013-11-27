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
		<xsl:call-template name="yui_phpgw_i18n"/>

		<div class="yui-navset" id="survey_edit_tabview">
		<h1>
			<xsl:value-of select="php:function('lang', 'summation')" />
		</h1>
			<div class="yui-content">

				<dl class="proplist-col">

					<dt>
						<label for="category"><xsl:value-of select="php:function('lang', 'condition survey')" /></label>
					</dt>
					<dd>
						<select id="survey_id" name="survey_id" onChange="update_summation();" >
							<xsl:apply-templates select="surveys/options"/>
						</select>
					</dd>

					<dt>
						<label for="category"><xsl:value-of select="php:function('lang', 'year')" /> 0</label>
					</dt>
					<dd>
						<select id="year" name="year" onChange="update_summation();" >
							<xsl:apply-templates select="years/options"/>
						</select>
					</dd>

				</dl>
			</div>
			<xsl:call-template name="datasource-definition" />
			<dl class="proplist-col">
				<dt>
					<label><xsl:value-of select="php:function('lang', 'summation')"/></label>
				</dt>
				<dd>
					<div style="clear:both;" id="datatable-container_0"></div>
				</dd>
			</dl>
	</div>


	<script>
	function update_summation()
	{
	   	var survey_id = document.getElementById("survey_id").value;
	   	var year = document.getElementById("year").value;
		var oArgs = {menuaction:'property.uicondition_survey.get_summation', id:survey_id, year: year};
		var strURL = phpGWLink('index.php', oArgs, true);
		YAHOO.portico.updateinlineTableHelper('datatable-container_0', strURL);
	}


  	addFooterDatatable0 = function(paginator,datatable)
  	{
  		//call YAHOO.portico.getTotalSum(name of column) in property.js
  		tmp_sum1 = YAHOO.portico.getTotalSum('period_1',0,paginator,datatable);
  		tmp_sum2 = YAHOO.portico.getTotalSum('period_2',0,paginator,datatable);
  		tmp_sum3 = YAHOO.portico.getTotalSum('period_3',0,paginator,datatable);
  		tmp_sum4 = YAHOO.portico.getTotalSum('period_4',0,paginator,datatable);
 		tmp_sum5 = YAHOO.portico.getTotalSum('period_5',0,paginator,datatable);
  		tmp_sum6 = YAHOO.portico.getTotalSum('period_6',0,paginator,datatable);
 		tmp_sum7 = YAHOO.portico.getTotalSum('sum',0,paginator,datatable);

  		if(typeof(tableYUI0)=='undefined')
  		{
			tableYUI0 = YAHOO.util.Dom.getElementsByClassName("yui-dt-data","tbody")[0].parentNode;
			tableYUI0.setAttribute("id","tableYUI0");
  		}
  		else
  		{
  			tableYUI0.deleteTFoot();
  		}

		//Create ROW
		newTR = document.createElement('tr');

		YAHOO.portico.td_empty(1);
		YAHOO.portico.td_sum('Sum');
		YAHOO.portico.td_sum(tmp_sum1);
		YAHOO.portico.td_sum(tmp_sum2);
		YAHOO.portico.td_sum(tmp_sum3);
		YAHOO.portico.td_sum(tmp_sum4);
		YAHOO.portico.td_sum(tmp_sum5);
		YAHOO.portico.td_sum(tmp_sum6);
		YAHOO.portico.td_sum(tmp_sum7);

		myfoot = tableYUI0.createTFoot();
		myfoot.setAttribute("id","myfoot");
		myfoot.appendChild(newTR);
	}


  </script>


	</xsl:template>

<xsl:template name="datasource-definition">
	<script>
		var columnDefs = [];
		YAHOO.util.Event.onDOMReady(function(){
			<xsl:for-each select="datatable_def">
				columnDefs = [
					<xsl:for-each select="ColumnDefs">
					{
						resizeable: true,
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
						</xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: true,
						</xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
						</xsl:if>
						<xsl:if test="editor">
						editor: <xsl:value-of select="editor"/>,
					    </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
				];
			
			YAHOO.portico.inlineTableHelper("<xsl:value-of select="container"/>", <xsl:value-of select="requestUrl"/>, columnDefs);
		</xsl:for-each>

		var PaginatorName0 = 'paginator_containerdatatable-container_0';
		var DatatableName0 = 'datatable_containerdatatable-container_0';

		var  myPaginator_0 = YAHOO.portico.Paginator[PaginatorName0];
		var  myDataTable_0 = YAHOO.portico.DataTable[DatatableName0];

		myDataTable_0.subscribe("renderEvent", function()
		{
			addFooterDatatable0(myPaginator_0,myDataTable_0);
		});

  	});
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

