<!-- $Id$ -->

	<xsl:template name="b_account_form">
		<xsl:apply-templates select="b_account_data"/>
	</xsl:template>

	<xsl:template match="b_account_data">
		<script type="text/javascript">
			self.name="first_Window";
			function b_account_lookup()
			{
				Window1=window.open('<xsl:value-of select="b_account_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<xsl:choose>
			<xsl:when test="disabled='1'">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_b_account"/>
					</td>
					<td>
						<input size="9" type="text" value="{value_b_account_id}" readonly="readonly"/>
						<input size="30" type="text" value="{value_b_account_name}" readonly="readonly"/>
						<input size="9" type="hidden" name="b_account_id" value="{value_b_account_id}" readonly="readonly"/>
						<input size="30" type="hidden" name="b_account_name" value="{value_b_account_name}" readonly="readonly"/>
					</td>
				</tr>
			</xsl:when>
			<xsl:otherwise>
				<tr>
					<td valign="top">
						<a href="javascript:b_account_lookup()" onMouseover="window.status='{lang_select_b_account_help}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_b_account"/></a>
					</td>
					<td>
						<input size="9" type="text" name="b_account_id" value="{value_b_account_id}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_select_b_account_help"/>
							</xsl:attribute>
						</input>
						<input size="30" type="text" name="b_account_name" value="{value_b_account_name}" onClick="b_account_lookup();" readonly="readonly"> 
							<xsl:attribute name="title">
								<xsl:value-of select="lang_select_b_account_help"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
