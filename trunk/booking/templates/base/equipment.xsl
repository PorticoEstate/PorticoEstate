<xsl:template match="data">
	<div id="content">

		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/building_link"/>
					</xsl:attribute>
					<xsl:value-of select="resource/top-nav-bar-buildings"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/resources_link"/>
					</xsl:attribute>
					<xsl:value-of select="resource/top-nav-bar-resources"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/resource_link"/>
					</xsl:attribute>
					<xsl:value-of select="resource/resource_name"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/equipment_link"/>
					</xsl:attribute>
					<xsl:value-of select="resource/top-nav-bar-equipment"/>
				</a>
			</li>
			<li>
				<a href="">
					<xsl:value-of select="resource/name"/>
				</a>
			</li>
		</ul>
		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<dl class="proplist">
			<dt>
				<xsl:value-of select="resource/resource-field"/>
			</dt>
			<dd>
				<xsl:value-of select="resource/resource_name"/>
			</dd>
			<dt>
				<xsl:value-of select="resource/name-field"/>
			</dt>
			<dd>
				<xsl:value-of select="resource/name"/>
			</dd>
			<dt>
				<xsl:value-of select="resource/description-field"/>
			</dt>
			<dd>
				<xsl:value-of select="resource/description"/>
			</dd>
		</dl>
		
		<xsl:if test="resource/permission/write">
			<a class="button">
				<xsl:attribute name="href">
					<xsl:value-of select="resource/edit_link"/>
				</xsl:attribute>
				<xsl:value-of select="resource/edit-link"/>
			</a>
		</xsl:if>
	</div>
</xsl:template>
