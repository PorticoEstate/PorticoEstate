	<xsl:template name="menu">
		<xsl:apply-templates select="links"/>
	</xsl:template>
	<xsl:template match="links">
		<table width="100%" align="center">		
			<tr >
				<xsl:attribute name="class">
					<xsl:text>row_on</xsl:text>
				</xsl:attribute>
				<td align="left">
					<xsl:for-each select="module" >
						<xsl:text>  </xsl:text>
						<xsl:choose>
							<xsl:when test="this=1">
								<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><b><xsl:text>[</xsl:text><xsl:value-of select="text"/><xsl:text>]</xsl:text></b></a>					
							</xsl:when>
							<xsl:otherwise>
								<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>					
							</xsl:otherwise>
						</xsl:choose>

					</xsl:for-each>
				</td>
			</tr>
			<tr>
				<xsl:attribute name="class">
					<xsl:text>row_off</xsl:text>
				</xsl:attribute>
				<td align="left">
					<xsl:for-each select="sub_menu" >
						<xsl:text>  </xsl:text>
						<xsl:choose>
							<xsl:when test="this=1">
								<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><b><xsl:text>[</xsl:text><xsl:value-of select="text"/><xsl:text>]</xsl:text></b></a>					
							</xsl:when>
							<xsl:otherwise>
								<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>					
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</tr>

			<xsl:choose>
				<xsl:when test="sub_menu_2!=''">
					<tr>
						<xsl:attribute name="class">
							<xsl:text>row_off</xsl:text>
						</xsl:attribute>
						<td align="left">
							<xsl:for-each select="sub_menu_2" >
								<xsl:text>  </xsl:text>
								<xsl:choose>
									<xsl:when test="this=1">
										<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><b><xsl:text>[</xsl:text><xsl:value-of select="text"/><xsl:text>]</xsl:text></b></a>					
									</xsl:when>
									<xsl:otherwise>
										<a href="{url}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"/></a>					
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

		</table>
		<hr noshade="noshade" width="100%" align="center" size="1"/>
	</xsl:template>