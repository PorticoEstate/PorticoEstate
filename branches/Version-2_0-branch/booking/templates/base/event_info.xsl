<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div style="float: right"><a onclick="schedule.closeOverlay(); return false" style="cursor:pointer;"><xsl:value-of select="php:function('lang', 'Close')"/></a></div-->
	<h3>
		<xsl:value-of select="php:function('lang', 'Event')"/> #<xsl:value-of select="event/id"/>
	</h3>
	<xsl:if test="event/is_public=1">
		<div>
			<xsl:value-of select="event/description" disable-output-escaping="yes"/>
		</div>
	</xsl:if>
	<div class="pure-form pure-form-aligned">
		<div class="pure-control-group">
			<label>
				<h4>
					<xsl:value-of select="php:function('lang', 'Where')"/>
				</h4>
			</label>
			<a href="{event/building_link}">
				<xsl:value-of select="event/resources[position()=1]/building_name"/>
				(<xsl:value-of select="event/resource_info"/>)
			</a>
			</div>
		<div class="pure-control-group">
			<label>
				<h4>
					<xsl:value-of select="php:function('lang', 'When')"/>
				</h4>
			</label>
			<xsl:value-of select="event/when"/>
		</div>
		<div class="pure-control-group">
			<label>
				<h4>
					<xsl:value-of select="php:function('lang', 'Who')"/>
				</h4>
			</label>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/contact_name"/>
			</xsl:if>
			<xsl:if test="event/is_public=0">
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
		</div>
	</div>
	<xsl:if test="event/edit_link">
		<div class="actions">
			<button onclick="location.href='{event/edit_link}'" class="pure-button pure-button-primary">
				<xsl:value-of select="php:function('lang', 'Edit event')"/>
			</button>
		</div>
	</xsl:if>
</xsl:template>
