  <!-- $Id: location_view2.xsl 10554 2012-11-29 11:23:42Z sigurdne $ -->
	<xsl:template name="location_view2">
		<xsl:apply-templates select="location_data2"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data2">
		<xsl:for-each select="location">
			<xsl:choose>
				<xsl:when test="value !=''">
					<dt>
						<label><xsl:value-of select="name"/></label>
					</dt>
					<dd align="left">
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
					</dd>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
