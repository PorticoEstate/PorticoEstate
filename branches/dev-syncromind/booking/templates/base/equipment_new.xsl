<xsl:template match="data">
	<div id="content">

		<h3>
			<xsl:value-of select="lang/title"/>
		</h3>
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
					<input id="inputs" name="name" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="resource/name"/>
						</xsl:attribute>
					</input>
				</dd>
				<dt>
					<label for="field_name">
						<xsl:value-of select="lang/description"/>
					</label>
				</dt>
				<dd>
					<textarea id="field_name" name="description" cols="5" rows="5">
						<xsl:value-of select="resource/description"/>
					</textarea>
				</dd>
				<dt>
					<label for="field_building">
						<xsl:value-of select="lang/resource"/>
					</label>
				</dt>
				<dd>
					<div class="autocomplete">
						<input id="field_resource_id" name="resource_id" type="hidden">
							<xsl:attribute name="value">
								<xsl:value-of select="resource/resource_id"/>
							</xsl:attribute>
						</input>
						<input id="field_resource_name" name="resource_name" type="text">
							<xsl:attribute name="value">
								<xsl:value-of select="resource/resource_name"/>
							</xsl:attribute>
						</input>
						<div id="resource_container"/>
					</div>
				</dd>
			</dl>
			<div class="form-buttons">
				<input type="submit" id="button">
					<xsl:attribute name="value">
						<xsl:value-of select="lang/create"/>
					</xsl:attribute>
				</input>
			</div>
		</form>
	</div>
</xsl:template>
