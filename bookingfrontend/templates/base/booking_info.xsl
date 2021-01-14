<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<span class="d-block">
		<xsl:text>#</xsl:text>
		<xsl:value-of select="booking/id"/>
	</span>
	<h3>
		<xsl:value-of select="booking/group/organization_name"/>
	</h3>
	<div class="mb-3">
		<span class="font-weight-bold text-uppercase">
			<xsl:value-of select="php:function('lang', 'Group (2018)')"/>:
		</span>
		<a href="{booking/group_link}">
			<xsl:value-of select="booking/group/name"/>
		</a>
	</div>
	<span class="d-block">
		<xsl:value-of select="booking/when"/>
	</span>
	<div>
		<span class="font-weight-bold text-uppercase">
			<xsl:value-of select="php:function('lang', 'Place')"/>:
		</span>
		<a href="{booking/building_link}">
			<xsl:value-of select="booking/building_name"/>
		</a>
		(<xsl:value-of select="booking/resource_info"/>)
	</div>
	<!--<div><span class="font-weight-bold text-uppercase"><xsl:value-of select="php:function('lang', 'Activity')"/>: </span>
		<xsl:value-of select="booking/activity_name"/>
	</div>-->
	<xsl:if test="booking/participant_limit > 0">
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
			</span>
			<xsl:value-of select="booking/participant_limit"/>
		</div>
		<div class="actions">
			<a href="{booking/show_link}" target="_blank" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'register participants')"/>
			</a>
		</div>
	</xsl:if>

	<xsl:if test="booking/edit_link">
		<div class="actions">
			<a class="btn btn-light mt-4" href="{booking/edit_link}" target="_blank">
				<xsl:value-of select="php:function('lang', 'Edit booking')"/>
			</a>
			<xsl:if test="user_can_delete_bookings = 1">
				<a class="btn btn-light mt-4" href="{booking/cancel_link}" target="_blank">
					<xsl:value-of select="php:function('lang', 'Cancel booking')"/>
				</a>
			</xsl:if>
		</div>
	</xsl:if>
	<xsl:if test="booking/ical_link">
		<div class="actions">
			<a class="btn btn-light mt-4" href="{booking/ical_link}" target="_blank">
				iCal
			</a>
		</div>
	</xsl:if>
</xsl:template>
