<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h3>
		<xsl:value-of select="php:function('lang', 'Booking')"/> #<xsl:value-of select="booking/id"/>
	</h3>

	<span class="d-block"><xsl:value-of select="allocation/when"/></span>
	
	<div><span class="font-weight-bold text-uppercase">STED: </span>
		<a href="{booking/building_link}">
			<xsl:value-of select="booking/building_name"/>
		</a>
		(<xsl:value-of select="booking/resource_info"/>)
	</div>

	<div><span class="font-weight-bold text-uppercase">ARRANGØR: </span>
		<a href="{booking/org_link}">
			<xsl:value-of select="booking/group/organization_name"/>
		</a>:
		<a href="{booking/group_link}">
			<xsl:value-of select="booking/group/name"/>
		</a>
	</div>

	<div><span class="font-weight-bold text-uppercase"><xsl:value-of select="php:function('lang', 'Activity')"/>: </span>
		<xsl:value-of select="booking/activity_name"/>
	</div>
	
	<xsl:if test="booking/edit_link">
		<div class="actions">
			<button class="btn btn-light mt-4" onclick="location.href='{booking/edit_link}'">
				<xsl:value-of select="php:function('lang', 'Edit booking')"/>
			</button>
			<button class="btn btn-light mt-4" onclick="location.href='{booking/cancel_link}'">
				<xsl:value-of select="php:function('lang', 'Cancel booking')"/>
			</button>
		</div>
	</xsl:if>
</xsl:template>
