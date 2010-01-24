<xsl:template match="phpgw">
	<xsl:apply-templates />
</xsl:template>

<xsl:template match="view">
	<h2>View</h2>
	<xsl:apply-templates />
</xsl:template>

<xsl:template match="field">
	<label title="{tooltip}">
		<xsl:value-of select="title"/>
	</label>

	<input value="{value}">
	</input>
	<br />
</xsl:template>

