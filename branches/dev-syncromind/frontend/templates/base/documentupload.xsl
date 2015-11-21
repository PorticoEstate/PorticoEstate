<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>

	<div class="frontend_body">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:variable name="tab_selected">
				<xsl:value-of select="tab_selected"/>
			</xsl:variable>
			<div id="{$tab_selected}">

				<h3>Vi skal laste opp greier!</h3>
				<img src="frontend/templates/base/images/32x32/page_white.png" class="list_image"/>
				<br/>
				file: <xsl:value-of select="file"/>
				<br/>
				test: <xsl:value-of select="test"/>
				<br/>
				fn: <xsl:value-of select="filename"/>
				<br/>
				stored: <xsl:value-of select="storage"/>
				<br/>
				success: <xsl:value-of select="success"/>
				<br/>
				<form ENCTYPE="multipart/form-data" name="uploadform" method="post" action="{form_action}">
					<dl>
						<dt>
							<input type="file" name="help_filename" id="help_filename"/>
						</dt>
						<dt>
							<input type="submit" value="Last opp" name="file_upload"/>
						</dt>
					</dl>
				</form>
			</div>
		</div>
	</div>
</xsl:template>