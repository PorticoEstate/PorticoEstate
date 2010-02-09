<!-- $Id$ -->

	<xsl:template name="vendor_form">
		<xsl:apply-templates select="vendor_data"/>
	</xsl:template>

	<xsl:template match="vendor_data">
		<script language="JavaScript">
			self.name="first_Window";
			function vendor_lookup()
			{
				Window1=window.open('<xsl:value-of select="vendor_link"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		

		</script>

			<tr>
				<td valign="top">
					<a href="javascript:vendor_lookup()" onMouseover="window.status='{lang_select_vendor_help}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_vendor"/></a>
				</td>
				<td>
					<input size="5" type="text" id = "vendor_id" name="vendor_id" value="{value_vendor_id}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_select_vendor_help"/>
						</xsl:attribute>
					</input>
					<input  type="hidden" id = "vendor_id_trigger" name="vendor_id_trigger" value=""></input>
					<input  size="30" type="text" name="vendor_name" value="{value_vendor_name}"  onClick="vendor_lookup();" readonly="readonly"> 
					<xsl:attribute name="title">
							<xsl:value-of select="lang_select_vendor_help"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>

	</xsl:template>
