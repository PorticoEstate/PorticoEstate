<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<span class="d-block">
		<xsl:text>#</xsl:text>
		<xsl:value-of select="allocation/id"/>
	</span>
	<h3>
		<!--<xsl:value-of select="php:function('lang', 'Allocation')"/> #
		<xsl:value-of select="allocation/id"/>-->
		<!--<a href="{allocation/org_link}">-->
		<xsl:value-of select="allocation/organization_name"/>
		<!--</a>-->
	</h3>
	<span class="d-block">
		<xsl:value-of select="allocation/when"/>
	</span>

	<div>
		<span class="font-weight-bold text-uppercase">
			<xsl:value-of select="php:function('lang', 'Place')"/>:
		</span>
		<a href="{allocation/building_link}">
			<xsl:value-of select="allocation/building_name"/>
		</a>
		(<xsl:value-of select="allocation/resource_info"/>)
	</div>
	<xsl:if test="allocation/contact_email != '' or allocation/contact_phone != '' or orginfo/name != ''">
		<div class="tooltip-desc-btn">
			<span>
				<i class="fas fa-info-circle"></i>
			</span>
			<p class="tooltip-desc">
				<span class="d-block font-weight-normal">
					<xsl:value-of select="allocation/description" disable-output-escaping="yes"/>
					<xsl:if test="allocation/is_public=1">
						<xsl:if test="orginfo">
							<a href="{orginfo/link}">
								<xsl:value-of select="orginfo/name"/>
							</a>:
						</xsl:if>
						<xsl:value-of select="allocation/contact_name"/>
						<xsl:if test="allocation/contact_email != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_email')"/>: <xsl:value-of select="allocation/contact_email"/>
						</xsl:if>
						<xsl:if test="allocation/contact_phone != ''">
							<br/>
							<xsl:value-of select="php:function('lang', 'contact_phone')"/>: <xsl:value-of select="allocation/contact_phone"/>
						</xsl:if>
					</xsl:if>
					<xsl:if test="allocation/is_public=0">
						<xsl:value-of select="php:function('lang', 'Private event')"/>
					</xsl:if>
				</span>
			</p>
		</div>
	</xsl:if>

	<xsl:if test="allocation/participant_limit > 0">
		<div>
			<span class="font-weight-bold text-uppercase">
				<xsl:value-of select="php:function('lang', 'participant limit')" />:
			</span>
			<xsl:value-of select="allocation/participant_limit"/>
		</div>
		<div class="actions">
			<a href="{allocation/show_link}" target="_blank" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'register participants')"/>
			</a>
		</div>
	</xsl:if>


	<xsl:if test="allocation/add_link">
		<div class="actions">
			<a href="{allocation/add_link}" target="_blank" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'Create new booking')"/>
			</a>
			<xsl:if test="user_can_delete_allocations = 1">
				<a href="{allocation/cancel_link}" target="_blank" class="btn btn-light mt-4">
					<xsl:value-of select="php:function('lang', 'Cancel allocation')"/>
				</a>
			</xsl:if>
		</div>
	</xsl:if>

</xsl:template>
