<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<h3>
		<xsl:value-of select="php:function('lang', 'Event')"/> #<xsl:value-of select="event/id"/>
	</h3>

	<span class="d-block"><xsl:value-of select="event/when"/></span>
	
	<div><span class="font-weight-bold text-uppercase">STED: </span>
		<a href="{event/building_link}">
				<xsl:value-of select="event/building_name"/>
			</a>
		(<xsl:value-of select="event/resource_info"/>)
	</div>

	<div class="tooltip-desc-btn">
		<span><i class="fas fa-info-circle"></i></span>
		<p class="tooltip-desc">
    	<span class="d-block font-weight-normal">
			<xsl:if test="event/is_public=1">
				<xsl:if test="orginfo">
						<a href="{orginfo/link}">
							<xsl:value-of select="orginfo/name"/>
						</a>:
					</xsl:if>
					<xsl:value-of select="event/contact_name"/>
					<xsl:if test="event/contact_email != ''">
						<br/>
						E-post: <xsl:value-of select="event/contact_email"/>
					</xsl:if>
					<xsl:if test="event/contact_phone != ''">
						<br/>
						Telefon: <xsl:value-of select="event/contact_phone"/>
					</xsl:if>
			</xsl:if>
			<xsl:if test="event/is_public=0">
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
			<xsl:if test="event/is_public=1">
				<div>
					<xsl:value-of select="event/description" disable-output-escaping="yes"/>
				</div>
			</xsl:if>
		</span>
		</p>
	</div>	
	
	<xsl:if test="event/edit_link">
		<div class="actions">
			<button onclick="location.href='{event/edit_link}'" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'Edit event')"/>
			</button>
			<button onclick="location.href='{event/cancel_link}'" class="btn btn-light mt-4">
				<xsl:value-of select="php:function('lang', 'Cancel event')"/>
			</button>
		</div>
	</xsl:if>
		
</xsl:template>
