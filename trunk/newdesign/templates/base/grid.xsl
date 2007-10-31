<xsl:template match="phpgw">
	<xsl:apply-templates /> 
</xsl:template>

<xsl:template name="grid" match="grid">	
	<div id="markup" class=" yui-skin-sam">
		<table border="1" id="grid">
			<thead>
				<xsl:apply-templates select="column_defs" />
			</thead>
			<tbody>
				<xsl:apply-templates select="rows" />
			</tbody>		
		</table>
	</div>
	<xsl:call-template name="javascript_def" />	
</xsl:template>

<xsl:template name="column_defs" match="column_defs">
	<tr>
		<xsl:apply-templates select="column" />
	</tr>
</xsl:template>

<xsl:template name="column" match="column">
	<th>
		<xsl:value-of select="label"/>
	</th>
</xsl:template>

<xsl:template name="rows" match="rows">
	<tr>
		<xsl:apply-templates select="data" />
	</tr>
</xsl:template>

<xsl:template name="data" match="data">
	<td>
		<xsl:value-of select="."/>
	</td>
</xsl:template>

<xsl:template name="javascript_def">
	Javascript def:<br/>
	<pre>
	 <script type="text/javascript">
		var myColumnDefs = [	
		<xsl:for-each select="column_defs/column">	 
		 	{
		 		key: "<xsl:value-of select="key"/>",
		 		label: "<xsl:value-of select="phpgw:or(label,key)"/>",
		 		formater: "<xsl:value-of select="phpgw:or(formater,'text')"/>",
		 		sortable: <xsl:value-of select="phpgw:or(sortable, 'true')"/>
		 	}
		 	<xsl:if test="position() &lt; last()">,</xsl:if>
		</xsl:for-each>			
		]
	</script> 
	</pre>
	end<br /> 
</xsl:template>