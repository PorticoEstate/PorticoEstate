<!--
	Function
	phpgw:conditional( expression $test, mixed $true, mixed $false )
	Evaluates test expression and returns the contents in the true variable if
	the expression is true and the contents of the false variable if its false

	Returns mixed
-->
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

<xsl:template name="toolbar">
    <div id="toolbar"><table class="toolbartable"><tr>
    	<td class="toolbarlabel"><label><b><xsl:value-of select="./label"/></b></label></td>
        <xsl:for-each select="*">
        	<div class="toolbarelement">
	        	<xsl:if test="control = 'input'">
	        		<td class="toolbarcol">
					<label>
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <input>
			        	<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
			    		<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
			    		<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
			    		<xsl:attribute name="onClick"><xsl:value-of select="phpgw:conditional(not(onClick), '', onClick)"/></xsl:attribute>
			    		<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
			    		<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
			    		<!-- <xsl:attribute name="class">yui-button yui-menu-button yui-skin-sam yui-split-button yui-button-hover button</xsl:attribute> -->
				    </input>
				    </td>
				</xsl:if>
				<xsl:if test="control = 'select'">
					<td class="toolbarcol">
					<label>
				    <xsl:attribute name="for"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
				    <xsl:value-of select="phpgw:conditional(not(text), '', text)"/>
				    </label>
				    <select>
					<xsl:attribute name="id"><xsl:value-of select="phpgw:conditional(not(id), '', id)"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
			   		<xsl:for-each select="keys">
			   			<xsl:variable name="p" select="position()" />
			   			<option>
			   				<xsl:attribute name="value"><xsl:value-of select="text()"/></xsl:attribute>
			   				<xsl:if test="text() = ../default"><xsl:attribute name="default"/></xsl:if>
			   				<xsl:value-of select="../values[$p]"/>
			   			</option>
			   		</xsl:for-each>
			   		</select>
			   		</td>
				</xsl:if>
			</div>
        </xsl:for-each> 
    </tr></table></div>
</xsl:template>

<xsl:template match="form">
	<form id="queryForm">
		<xsl:attribute name="method">
			<xsl:value-of select="phpgw:conditional(not(method), 'GET', method)"/>
		</xsl:attribute>

		<xsl:attribute name="action">
			<xsl:value-of select="phpgw:conditional(not(action), '', action)"/>
		</xsl:attribute>
        <xsl:for-each select="*">
        	<xsl:if test="toolbar">
        		<xsl:call-template name="toolbar"/>
        	</xsl:if>
        </xsl:for-each>
	</form>
</xsl:template>

<xsl:template match="datatable">
	<div class="datatable">
		<div id="paginator"/>
	    <div id="columnshowhide"></div>
		<div id="dt-dlg">
		    <div class="hd">Velg hvilke kolonner du ønsker å se:</div>
		    <div id="dt-dlg-picker" class="bd"></div>
		</div>
    	<div id="datatable-container"/>
  		<xsl:call-template name="datasource-definition" />
  	</div>
</xsl:template>

<xsl:template name="datasource-definition">
	<script>
		YAHOO.rental.setupDatasource = function() {
			<xsl:if test="source">
	            YAHOO.rental.dataSourceUrl = '<xsl:value-of select="source"/>';
	        </xsl:if>
			<xsl:if test="columns">
	            YAHOO.rental.storeColumnsUrl = '<xsl:value-of select="columns"/>';
	        </xsl:if>

			YAHOO.rental.columnDefs = [
				<xsl:for-each select="//datatable/field">
					{
						key: "<xsl:value-of select="key"/>",
						<xsl:if test="label">
						label: "<xsl:value-of select="label"/>",
					    </xsl:if>
						sortable: <xsl:value-of select="phpgw:conditional(not(sortable = 0), 'true', 'false')"/>,
						<xsl:if test="hidden">
						hidden: <xsl:value-of select="hidden"/>,
					    </xsl:if>
						<xsl:if test="formatter">
						formatter: <xsl:value-of select="formatter"/>,
					    </xsl:if>
						className: "<xsl:value-of select="className"/>"
					}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
				</xsl:for-each>
			];
		}
	</script>
</xsl:template>
