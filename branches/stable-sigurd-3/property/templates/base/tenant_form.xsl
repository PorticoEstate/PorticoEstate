<!-- $Id$ -->

	<xsl:template name="tenant_form">
		<xsl:apply-templates select="tenant_data"/>
	</xsl:template>

	<xsl:template match="tenant_data">
		<script language="JavaScript">
			self.name="first_Window";
			function tenant_lookup()
			{
				Window1=window.open('<xsl:value-of select="tenant_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

			<tr>
				<td valign="top">
					<a href="javascript:tenant_lookup()" onMouseover="window.status='{lang_select_tenant_help}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_tenant"/></a>
				</td>
				<td colspan = '3'>
					<input size="9" type="text" name="tenant_id" value="{value_tenant_id}" >
					<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_select_tenant_help"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input size="{size_last_name}" type="text" name="last_name" value="{value_last_name}" onClick="tenant_lookup();" readonly="readonly">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_tenant_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
					<input  size="{size_first_name}" type="text" name="first_name" value="{value_first_name}"  onClick="tenant_lookup();" readonly="readonly">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_tenant_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>

	</xsl:template>
