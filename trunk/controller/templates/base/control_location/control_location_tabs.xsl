<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="control_location_tabview">
	<xsl:choose>
		<xsl:when test="view = 'view_locations_for_control'">
			<div class="identifier-header">
				<h1><xsl:value-of select="php:function('lang', 'Locations_for_control')"/></h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			 
			<xsl:call-template name="view_locations_for_control" />
		</xsl:when>
		<xsl:when test="view = 'register_control_to_location'">
			<div class="identifier-header">
				<h1>Legg kontroll til bygg</h1>
			</div>
			<!-- Prints tabs array -->
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="register_control_to_location" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
