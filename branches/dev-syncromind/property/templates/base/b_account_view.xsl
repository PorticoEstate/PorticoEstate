
<!-- $Id$ -->
<xsl:template name="b_account_view">
	<xsl:apply-templates select="b_account_data"/>
</xsl:template>

<!-- New template-->
<xsl:template match="b_account_data">
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="lang_b_account"/>
		</label>
		<xsl:value-of select="value_b_account_id"/>
		<xsl:text> [</xsl:text>
		<xsl:value-of select="value_b_account_name"/>
		<xsl:text>]</xsl:text>
	</div>
</xsl:template>
