<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="close"><a onclick="YAHOO.booking.closeOverlay(); return false" href=""><xsl:value-of select="php:function('lang', 'Close')"/></a></div>
	<h3><xsl:value-of select="php:function('lang', 'Allocation')"/> #<xsl:value-of select="allocation/id"/></h3>
	<dl>
		<dt><xsl:value-of select="php:function('lang', 'Where')"/></dt>
		<dd>
			<a href="{allocation/building_link}"><xsl:value-of select="allocation/resources[position()=1]/building_name"/></a>
			(<xsl:value-of select="allocation/resource_info"/>)
		</dd>
		<dt><xsl:value-of select="php:function('lang', 'When')"/></dt>
		<dd><xsl:value-of select="allocation/when"/></dd>
		<dt><xsl:value-of select="php:function('lang', 'Who')"/></dt>
		<dd>
			<a href="{allocation/org_link}"><xsl:value-of select="allocation/organization_name"/></a>
		</dd>
	</dl>
	<xsl:if test="allocation/add_link">
		<div class="actions">
			<button onclick="location.href='{allocation/add_link}'"><xsl:value-of select="php:function('lang', 'Create new booking')"/></button>
			<xsl:if test="user_can_delete_allocations != 1">
			<button onclick="location.href='{allocation/cancel_link}'"><xsl:value-of select="php:function('lang', 'Cancel allocation')"/></button>
			</xsl:if>
			<xsl:if test="user_can_delete_allocations != 0">
			<button onclick="location.href='{allocation/cancel_link}'"><xsl:value-of select="php:function('lang', 'Delete allocation')"/></button>
			</xsl:if>
		</div>
	</xsl:if>
</xsl:template>
