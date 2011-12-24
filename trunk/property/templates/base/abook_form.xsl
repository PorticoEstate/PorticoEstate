<!-- $Id$ -->

	<xsl:template name="abook_form">
		<xsl:apply-templates select="abook_data"/>
	</xsl:template>

	<xsl:template match="abook_data">
		<script type="text/javascript">
			self.name="first_Window";
			function abook_lookup()
			{
				Window1=window.open('<xsl:value-of select="abook_link"/>',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

		<tr>
			<td valign="top">
				<a href="javascript:abook_lookup()" onMouseover="window.status='{lang_select_abook_help}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="lang_contact"/></a>
			</td>
			<td>
				<input size="5" type="text" name="vendor_id" value="{value_abid}" onClick="vendor_lookup();" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_contact_help"/>
					</xsl:attribute>
				</input>
				<input size="30" type="text" name="vendor_name" value="{value_contact_name}" onClick="abook_lookup();" readonly="readonly"> 
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_contact_help"/>
					</xsl:attribute>
				</input>
			</td>
		</tr>

	</xsl:template>
