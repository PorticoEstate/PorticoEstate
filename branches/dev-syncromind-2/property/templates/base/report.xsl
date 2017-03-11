
<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<div id="document_edit_tabview">

		<xsl:value-of select="validator"/>
		
			<div id="tab-content">					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
						
				<div id="report">
					<form name="form" class="pure-form pure-form-aligned" id="form" action="" method="post">								
					</form>
				</div>
		
			</div>
	</div>

</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
