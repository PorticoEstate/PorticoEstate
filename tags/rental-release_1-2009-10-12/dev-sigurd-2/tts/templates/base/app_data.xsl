		<xsl:template name="ticket_list">
			<table>
				<thead>
					<tr>
						<xsl:for-each select="headings">
							<th><xsl:value-of select="." /></th>
						</xsl:for-each>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="results">
						<tr id="record_{ticket_id}" onclick="window.location='{view_action}';">
							<xsl:attribute name="class">
									<xsl:choose>
											<xsl:when test="position() mod 2 = 0">
													<xsl:text>row_off</xsl:text>
											</xsl:when>
											<xsl:otherwise>
													<xsl:text>row_on</xsl:text>
											</xsl:otherwise>
									</xsl:choose>
							</xsl:attribute>
							<xsl:for-each select="*">
								<xsl:if test="name() != 'view_action'">
									<td><xsl:value-of select="." /></td>
								</xsl:if>
							</xsl:for-each>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:template>
