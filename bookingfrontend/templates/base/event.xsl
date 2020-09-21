<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container wrapper">
		<span class="d-block">
			<xsl:text>#</xsl:text>
			<xsl:value-of select="event/id"/>
		</span>
		<h3>
			<xsl:if test="event/is_public=0">
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/name"/>
			</xsl:if>
		</h3>
		<span class="d-block">
			<xsl:value-of select="event/when"/>
		</span>
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Place')"/>:
			</span>
			<a href="{event/building_link}">
				<xsl:value-of select="event/building_name"/>
			</a>
			(<xsl:value-of select="event/resource_info"/>)
		</div>
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'Organizer')"/>:
			</span>
			<xsl:if test="event/is_public=0">
				<br/>
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/organizer"/>
			</xsl:if>
		</div>
		<xsl:if test="event/is_public=1">
			<div class="tooltip-desc-btn">
				<xsl:if test="event/contact_email != '' or event/contact_phone != ''">
					<span>
						<i class="fas fa-info-circle"></i>
					</span>
				</xsl:if>
				<p class="tooltip-desc">
					<span class="d-block font-weight-normal">
						<xsl:if test="event/contact_email != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_email')"/>: <xsl:value-of select="event/contact_email"/>
						</xsl:if>
						<xsl:if test="event/contact_phone != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_phone')"/>: <xsl:value-of select="event/contact_phone"/>
						</xsl:if>
					</span>
				</p>
			</div>
		</xsl:if>

		<span class="mt-2">
			<xsl:value-of select="php:function('lang', 'number of participants')" />:
			<xsl:value-of select="event/number_of_participants" />
		</span>

		<div class="mt-4">
			<a href="{event/participant_registration_link}">
				<xsl:value-of select="php:function('lang', 'registration')"/>
			</a>
		</div>

		<div class="mt-1">
			<img src="{event/encoded_qr}"/>
		</div>
	</div>
</xsl:template>
