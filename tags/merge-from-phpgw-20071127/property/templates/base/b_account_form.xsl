<!-- $Id: b_account_form.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="b_account_form">
		<xsl:apply-templates select="b_account_data"/>
	</xsl:template>

	<xsl:template match="b_account_data">
		<script language="JavaScript">
			self.name="first_Window";
			function b_account_lookup()
			{
				Window1=window.open('<xsl:value-of select="b_account_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

			<tr>
				<td valign="top">
					<a href="javascript:b_account_lookup()" onMouseover="window.status='{lang_select_b_account_help}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_b_account"/></a>
				</td>
				<td>
					<input size="9" type="text" name="b_account_id" value="{value_b_account_id}" >
					<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_b_account_help"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				<input  size="30" type="text" name="b_account_name" value="{value_b_account_name}"  onClick="b_account_lookup();" readonly="readonly"> 
					<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_b_account_help"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

	</xsl:template>
