  <!-- $Id$ -->
	<xsl:template name="location_view">
		<xsl:apply-templates select="location_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data">
		<xsl:for-each select="location">
			<xsl:choose>
				<xsl:when test="value !=''">
					<tr>
						<td class="th_text" width="{with}" align="left">
							<label>
								<xsl:value-of select="name"/>
							</label>
						</td>
						<td align="left">
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
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
