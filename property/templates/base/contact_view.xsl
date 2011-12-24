<!-- $Id$ -->

	<xsl:template name="contact_form">
		<xsl:apply-templates select="contact_data"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="contact_data">
		<tr>
			<td valign="top">
				<xsl:value-of select="lang_contact"></xsl:value-of>
			</td>
			<td>
				<table>
					<tr>
						<td>
							<xsl:value-of select="value_contact_name"></xsl:value-of>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_contact_tel!=''">
							<tr>
								<td>
									<xsl:value-of select="value_contact_tel"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_contact_email!=''">
							<tr>
								<td>
									<a href="mailto:{value_contact_email}"><xsl:value-of select="value_contact_email"></xsl:value-of></a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
			</td>
		</tr>
	</xsl:template>
