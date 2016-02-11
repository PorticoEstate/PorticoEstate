<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<div id="content">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="reports" class="booking-container">
				<ul style="list-style:outside none none;">
					<xsl:for-each select="reports">
						<li>
							<a class="pure-button pure-button-primary">
								<xsl:attribute name="href">
									<xsl:value-of select="url"/>
								</xsl:attribute>
								<xsl:value-of select="name" />
							</a>
						</li>
					</xsl:for-each>
				</ul>
			</div>
		</div>
	</div>
</xsl:template>
