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
		<xsl:if test="event/application_id!=''">
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'Application')"/>
				</label>
				<a href="{event/application_link}">#<xsl:value-of select="event/application_id"/></a>
			</div>
		</xsl:if>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'Where')"/>
			</label>
			<a href="{event/building_link}">
				<xsl:value-of select="event/resources[position()=1]/building_name"/>
				(<xsl:value-of select="event/resource_info"/>)
			</a>
		</div>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'When')"/>
			</label>
			<xsl:value-of select="event/when"/>
		</div>
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'Who')"/>
			</label>
			<xsl:if test="event/is_public=1">
				<xsl:value-of select="event/contact_name"/>
			</xsl:if>
			<xsl:if test="event/is_public=0">
				<xsl:value-of select="php:function('lang', 'Private event')"/>
			</xsl:if>
		</div>

		<xsl:if test="event/participant_limit">
			<div class="pure-control-group">
				<label for="field_participant_limit">
					<xsl:value-of select="php:function('lang', 'participant limit')" />
				</label>
				<xsl:value-of select="event/participant_limit"/>
			</div>
		</xsl:if>
	</div>
	<xsl:if test="event/edit_link">
		<div class="actions">
			<button onclick="location.href='{event/edit_link}'" class="pure-button pure-button-primary">
				<xsl:value-of select="php:function('lang', 'Edit event')"/>
			</button>
		</div>
	</xsl:if>
</xsl:template>
