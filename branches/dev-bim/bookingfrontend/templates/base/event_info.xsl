<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div style="float: right"><a onclick="YAHOO.booking.closeOverlay(); return false" href=""><xsl:value-of select="php:function('lang', 'Close')"/></a></div>
	<h3><xsl:value-of select="php:function('lang', 'Event')"/> #<xsl:value-of select="event/id"/></h3>
	<div>
		<xsl:value-of select="event/description" disable-output-escaping="yes"/>
	</div>
	<dl>
		<dt><xsl:value-of select="php:function('lang', 'Where')"/></dt>
		<dd>
			<a href="{event/building_link}"><xsl:value-of select="event/resources[position()=1]/building_name"/></a>
			(<xsl:value-of select="event/resource_info"/>)
		</dd>
		<dt><xsl:value-of select="php:function('lang', 'When')"/></dt>
		<dd><xsl:value-of select="event/when"/></dd>
		<dt><xsl:value-of select="php:function('lang', 'Who')"/></dt>
		<dd>
			<xsl:value-of select="event/contact_name"/>
		</dd>
	</dl>
</xsl:template>