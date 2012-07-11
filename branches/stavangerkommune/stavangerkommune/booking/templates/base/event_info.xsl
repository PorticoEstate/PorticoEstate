<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div style="float: right"><a onclick="YAHOO.booking.closeOverlay(); return false" href=""><xsl:value-of select="php:function('lang', 'Close')"/></a></div>
	<h3><xsl:value-of select="php:function('lang', 'Event')"/> #<xsl:value-of select="event/id"/></h3>
	<xsl:if test="event/is_public=1">
		<div>
			<xsl:value-of select="event/description" disable-output-escaping="yes"/>
		</div>
	</xsl:if>
	<dl>
		<dt><xsl:value-of select="php:function('lang', 'Where')"/></dt>
		<dd>
			<a href="{event/building_link}"><xsl:value-of select="event/resources[position()=1]/building_name"/></a>
			(<xsl:value-of select="event/resource_info"/>)
		</dd>
		<dt><xsl:value-of select="php:function('lang', 'When')"/></dt>
		<dd><xsl:value-of select="event/when"/></dd>
		<dt><xsl:value-of select="php:function('lang', 'Who')"/></dt>
		<xsl:if test="event/is_public=1">
			<dd>
				<xsl:value-of select="event/contact_name"/>
			</dd>
		</xsl:if>
		<xsl:if test="event/is_public=0">
			<dd>
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</dd>
		</xsl:if>
	</dl>
	<xsl:if test="event/edit_link">
		<div class="actions">
			<button onclick="location.href='{event/edit_link}'"><xsl:value-of select="php:function('lang', 'Edit event')"/></button>
		</div>
	</xsl:if>

</xsl:template>
