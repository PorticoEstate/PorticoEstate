	<xsl:template match="menu">
	<table width="100%" align="center" id="legacy-menu">
		<tr >
			<xsl:attribute name="class">
				<xsl:text>row_on</xsl:text>
			</xsl:attribute>
			<td align="left">
				<xsl:for-each select="navigation" >
					<xsl:text>  </xsl:text>
					<xsl:choose>
						<xsl:when test="this=1">
							<a href="{url}">
								<b>
									<xsl:text>[</xsl:text>
									<xsl:value-of select="text"/>
									<xsl:text>]</xsl:text>
								</b>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<a href="{url}">
								<xsl:value-of select="text"/>
							</a>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</td>
		</tr>
		<xsl:for-each select="navigation" >
			<xsl:choose>
				<xsl:when test="children">
					<tr >
						<xsl:attribute name="class">
							<xsl:text>row_off</xsl:text>
						</xsl:attribute>
						<td align="left">
							<xsl:for-each select = "children" >
								<xsl:text>  </xsl:text>
								<xsl:choose>
									<xsl:when test="this=1">
										<a href="{url}">
											<b>
												<xsl:text>[</xsl:text>
												<xsl:value-of select="text"/>
												<xsl:text>]</xsl:text>
											</b>
										</a>
									</xsl:when>
									<xsl:otherwise>
										<a href="{url}">
											<xsl:value-of select="text"/>
										</a>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</td>
					</tr>
					<xsl:for-each select = "children" >
						<xsl:choose>
							<xsl:when test="children">
								<tr >
									<xsl:attribute name="class">
										<xsl:text>row_on</xsl:text>
									</xsl:attribute>
									<td align="left">
										<xsl:for-each select = "children" >
											<xsl:text>  </xsl:text>
											<xsl:choose>
												<xsl:when test="this=1">
													<a href="{url}">
														<b>
															<xsl:text>[</xsl:text>
															<xsl:value-of select="text"/>
															<xsl:text>]</xsl:text>
														</b>
													</a>
												</xsl:when>
												<xsl:otherwise>
													<a href="{url}">
														<xsl:value-of select="text"/>
													</a>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:for-each>
									</td>
								</tr>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</table>
</xsl:template>
