
<!-- $Id$ -->
<xsl:template name="contact_form">
	<xsl:apply-templates select="contact_data"/>
</xsl:template>

<!-- New template-->
<xsl:template match="contact_data">
	<script type="text/javascript">
		function <xsl:value-of select="field"/>_contact_lookup()
		{
			TINY.box.show({iframe:'<xsl:value-of select="contact_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<div class="pure-control-group">

		<div class="pure-u-1 pure-u-md-1-3">
			<label for='contact'>
				<a href="javascript:{field}_contact_lookup()" title="{lang_select_contact_help}">
					<xsl:value-of select="lang_contact"/>
				</a>
			</label>
			<div class="pure-u-md-1-3">
				<table id = 'contact'>
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
			</div>
		</div>
	</div>
</xsl:template>
