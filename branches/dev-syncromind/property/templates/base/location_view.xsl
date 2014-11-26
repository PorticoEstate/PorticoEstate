  <!-- $Id$ -->
	<xsl:template name="location_view">
		<xsl:apply-templates select="location_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data">
		<xsl:for-each select="location">
			<xsl:choose>
				<xsl:when test="value !=''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="name"/>
						</label>
						<xsl:choose>
							<xsl:when test="input_type !='hidden'">
								<xsl:choose>
									<xsl:when test="query_link !=''">
										<xsl:variable name="query_link" select="query_link"/>
										<a href="{$query_link}" class="th_text">
											<xsl:value-of select="value"/>
										</a>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="value"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
						</xsl:choose>
						<xsl:for-each select="extra">
							<xsl:choose>
								<xsl:when test="input_type !='hidden'">
									<xsl:text> </xsl:text>
									<xsl:value-of select="value"/>
								</xsl:when>
							</xsl:choose>
						</xsl:for-each>
					</div>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
