  <!-- $Id$ -->
	<xsl:template name="contact_form">
		<xsl:apply-templates select="contact_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="contact_data">
		<script type="text/javascript">
			self.name="first_Window";
			function <xsl:value-of select="field"/>_contact_lookup()
			{
				Window1=window.open('<xsl:value-of select="contact_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}
		</script>
		<tr>
			<td valign="top">
				<label>
					<a href="javascript:{field}_contact_lookup()" title="{lang_select_contact_help}">
						<xsl:value-of select="lang_contact"/>
					</a>
				</label>
			</td>
			<td>
				<table>
					<tr>
						<td>
							<input type="hidden" name="{field}" value="{value_contact_id}">
							</input>
							<input size="30" type="text" name="{field}_name" value="{value_contact_name}" onClick="{field}_contact_lookup();" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="lang_select_contact_help"/>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_contact_tel!=''">
							<tr>
								<td>
									<xsl:value-of select="value_contact_tel"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="value_contact_email!=''">
							<tr>
								<td>
									<a href="mailto:{value_contact_email}">
										<xsl:value-of select="value_contact_email"/>
									</a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
			</td>
		</tr>
	</xsl:template>
