<!-- $Id: ecodimb_form.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="ecodimb_form">
		<xsl:apply-templates select="ecodimb_data"/>
	</xsl:template>

	<xsl:template match="ecodimb_data">
		<script language="JavaScript">
			self.name="first_Window";
			function ecodimb_lookup()
			{
				Window1=window.open('<xsl:value-of select="ecodimb_url"/>',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");
			}		
		</script>

			<tr>
				<td valign="top">
					<a href="javascript:ecodimb_lookup()" title="{lang_select_ecodimb_help}" ><xsl:value-of select="lang_ecodimb"/>
					</a>
				</td>
				<td>
					<input size="9" type="text" name="ecodimb" value="{value_ecodimb}">
						<xsl:attribute name="title">
								<xsl:value-of select="lang_select_ecodimb_help"/>
						</xsl:attribute>
					</input>
					<input  size="30" type="text" name="ecodimb_descr" value="{value_ecodimb_descr}"  onClick="ecodimb_lookup();" readonly="readonly"> 
						<xsl:attribute name="title">
								<xsl:value-of select="lang_select_ecodimb_help"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>

	</xsl:template>
