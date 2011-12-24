<!-- $Id$ -->
<xsl:template name="values">
	<xsl:for-each select="values">
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
					</xsl:when>
					<xsl:when test="position() mod 2 = 0">
						<xsl:text>row_off</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>row_on</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:for-each select="row">
				<xsl:choose>
					<xsl:when test="link">
						<xsl:choose>
							<xsl:when test="link='dummy'">
								<td>
								</td>
							</xsl:when>
							<xsl:otherwise>
								<td class="small_text" align="center">
									<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text"></xsl:value-of></a>
								</td>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="overlib">
						<td class="small_text" align="center">
							<a href="javascript:void()" onMouseOver="overlib('{statustext}', CAPTION, '{text}')" onMouseOut="nd()">
								<xsl:value-of select="text"></xsl:value-of></a>		
						</td>
					</xsl:when>
					<xsl:otherwise>
						<td class="small_text" align="left">
							<xsl:value-of select="value"></xsl:value-of>					
							<xsl:choose>
								<xsl:when test="//lookup!=''">
									<xsl:if test="position() = last()">
										<!--	<td class="small_text" valign="center"> -->
											<xsl:variable name="select_action"><xsl:value-of select="lookup_action"></xsl:value-of></xsl:variable>
											<xsl:variable name="lang_select"><xsl:value-of select="//lang_select"></xsl:value-of></xsl:variable>
											<form method="post" action="{$select_action}">
												<input type="submit" class="forms" name="select" value="{$lang_select}" onMouseout="window.status='';return true;">
													<xsl:attribute name="onMouseover">
														<xsl:text>window.status='</xsl:text>
														<xsl:value-of select="lang_select_statustext"></xsl:value-of>
														<xsl:text>'; return true;</xsl:text>
													</xsl:attribute>
												</input>
											</form>
											<!--	</td> -->
									</xsl:if>
								</xsl:when>
							</xsl:choose>
						</td>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</tr>
	</xsl:for-each>
</xsl:template>
