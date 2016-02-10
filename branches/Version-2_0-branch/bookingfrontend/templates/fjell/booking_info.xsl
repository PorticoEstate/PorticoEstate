<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="close"><a onclick="YAHOO.booking.closeOverlay(); return false" href=""><xsl:value-of select="php:function('lang', 'Close')"/></a></div>
	<h3><xsl:value-of select="php:function('lang', 'Booking')"/> #<xsl:value-of select="booking/id"/></h3>
	<dl>
		<dt><xsl:value-of select="php:function('lang', 'Activity')"/></dt>
		<dd><xsl:value-of select="booking/activity_name"/></dd>
		<dt><xsl:value-of select="php:function('lang', 'Where')"/></dt>
		<dd>
			<a href="{booking/building_link}"><xsl:value-of select="booking/resources[position()=1]/building_name"/></a>
			(<xsl:value-of select="booking/resource_info"/>)
		</dd>
		<dt><xsl:value-of select="php:function('lang', 'When')"/></dt>
		<dd><xsl:value-of select="booking/when"/></dd>
		<dt><xsl:value-of select="php:function('lang', 'Who')"/></dt>
		<dd>
			<a href="{booking/org_link}"><xsl:value-of select="booking/group/organization_name"/></a>:
			<a href="{booking/group_link}"><xsl:value-of select="booking/group/name"/></a>
		</dd>
	</dl>
	<xsl:if test="booking/edit_link">
		<div class="actions">
			<button onclick="location.href='{booking/edit_link}'"><xsl:value-of select="php:function('lang', 'Edit booking')"/></button>
			<xsl:if test="user_can_delete_bookings != 1">
				<button onclick="location.href='{booking/cancel_link}'"><xsl:value-of select="php:function('lang', 'Cancel booking')"/></button>
			</xsl:if>
			<xsl:if test="user_can_delete_bookings != 0">
				<button onclick="location.href='{booking/cancel_link}'"><xsl:value-of select="php:function('lang', 'Delete booking')"/></button>
			</xsl:if>
		</div>
	</xsl:if>
</xsl:template>
