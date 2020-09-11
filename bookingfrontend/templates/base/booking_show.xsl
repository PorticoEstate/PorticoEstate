<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container wrapper">
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
		<xsl:if test="booking/participant_limit > 0">
			<p class="mt-2">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
				<xsl:value-of select="booking/participant_limit"/>
			</p>
		</xsl:if>
		<span class="mt-2">
			<xsl:value-of select="php:function('lang', 'number of participants')" />:
			<xsl:value-of select="booking/number_of_participants" />
		</span>

		<span class="mt-2">
			<xsl:value-of select="booking/participanttext" disable-output-escaping="yes"/>
		</span>

		<div class="mt-4">
			<a href="{booking/participant_registration_link}">
				<xsl:value-of select="php:function('lang', 'registration')"/>
			</a>
		</div>

		<div class="mt-1">
			<img src="{booking/encoded_qr}"/>
		</div>

	</div>
</xsl:template>
