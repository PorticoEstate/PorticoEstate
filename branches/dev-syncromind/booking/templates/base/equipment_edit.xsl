<xsl:template match="data">
	<div id="content">

		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/building_link"/>
					</xsl:attribute>
					<xsl:value-of select="lang/buildings"/>
				</a>
			</li>
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="resource/resources_link"/>
					</xsl:attribute>
					<xsl:value-of select="lang/resources"/>
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
					<xsl:value-of select="lang/equipment"/>
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

		<form action="" method="POST" id="form">
			<dl class="form">
				<dt>
					<label for="field_name">
						<xsl:value-of select="lang/name"/>
					</label>
				</dt>
				<dd>
					<input id="field_name" name="name" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="resource/name"/>
						</xsl:attribute>
					</input>
				</dd>
				<dt>
					<label for="field_description">
						<xsl:value-of select="lang/description"/>
					</label>
				</dt>
				<dd>
					<textarea id="field_description" name="description" cols="5" rows="5">
						<xsl:value-of select="resource/description"/>
					</textarea>
				</dd>
				<dt>
					<label for="field_resource_name">
						<xsl:value-of select="lang/resource"/>
					</label>
				</dt>
				<dd>
					<div class="autocomplete">
						<xsl:if test="resource/permission/write/resource_id">
							<input id="field_resource_id" name="resource_id" type="hidden">
								<xsl:attribute name="value">
									<xsl:value-of select="resource/resource_id"/>
								</xsl:attribute>
							</input>
						</xsl:if>
						<input id="field_resource_name" name="resource_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="resource/resource_name"/>
							</xsl:attribute>
							<xsl:if test="not(resource/permission/write/resource_id)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<div id="resource_container"/>
					</div>
				</dd>
			</dl>
			<div class="form-buttons">
				<input type="submit">
					<xsl:attribute name="value">
						<xsl:value-of select="lang/save"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>
