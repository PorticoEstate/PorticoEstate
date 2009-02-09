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
    <xsl:apply-templates select="form" />
    <xsl:apply-templates select="toolbar"/>
    <xsl:apply-templates select="paging"/>
    <xsl:apply-templates select="datatable"/>
</xsl:template>

<xsl:template match="toolbar">
    <div id="toolbar">
        <xsl:for-each select="item">
            <input>
        		<xsl:attribute name="type"><xsl:value-of select="phpgw:conditional(not(type), '', type)"/></xsl:attribute>
        		<xsl:attribute name="name"><xsl:value-of select="phpgw:conditional(not(name), '', name)"/></xsl:attribute>
        		<xsl:attribute name="value"><xsl:value-of select="phpgw:conditional(not(value), '', value)"/></xsl:attribute>
        		<xsl:attribute name="href"><xsl:value-of select="phpgw:conditional(not(href), '', href)"/></xsl:attribute>
            </input>
        </xsl:for-each>
    </div>
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
</xsl:template>

<xsl:template match="datatable">
    <div id="paginator"/>
    <div id="datatable-container"/>
  	<xsl:call-template name="datasource-definition" />
</xsl:template>

<xsl:template name="datasource-definition">
	<script>
      	<xsl:if test="source">
            YAHOO.booking.dataSourceUrl = '<xsl:value-of select="source"/>';
        </xsl:if>
		YAHOO.booking.columnDefs = [
			<xsl:for-each select="//datatable/field">
				{
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
					className: "<xsl:value-of select="className"/>"
				}<xsl:value-of select="phpgw:conditional(not(position() = last()), ',', '')"/>
			</xsl:for-each>
		];
	</script>
</xsl:template>