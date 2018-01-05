
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
		function <xsl:value-of select="field"/>_contact_clear(field)
		{
			$("#" + field).val('');
			$("#" + field + "_name").val('');
			$("#" + field + "_contact_tel").html('');
			$("#" + field + "_contact_email").html('');
	//		$("#" + field + "_clear_contact").prop("checked", false);
		}
	</script>
	<div class="pure-control-group">
		<label for='contact'>
			<a href="javascript:{field}_contact_lookup()" title="{lang_select_contact_help}">
				<xsl:value-of select="lang_contact"/>
			</a>
		</label>
		<div class="pure-custom">
			<input type="hidden" id="{field}"  name="{field}" value="{value_contact_id}"></input>
			<input size="30" type="text" id="{field}_name" name="{field}_name" value="{value_contact_name}" onClick="{field}_contact_lookup();" readonly="readonly">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_select_contact_help"/>
				</xsl:attribute>
			</input>
			<xsl:choose>
				<xsl:when test="value_contact_tel!=''">
					<div id="{field}_contact_tel">
						<xsl:value-of select="value_contact_tel"/>
					</div>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_contact_email!=''">
					<div id="{field}_contact_email">
						<a href="mailto:{value_contact_email}">
							<xsl:value-of select="value_contact_email"/>
						</a>
					</div>
				</xsl:when>
			</xsl:choose>
		</div>
		<a onClick="{field}_contact_clear('{field}');">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'clear contact')"/>
			</xsl:attribute>
			<xsl:value-of select="php:function('lang', 'delete')"/>
		</a>
		<!--input type="checkbox" id="{field}_clear_contact" name="clear_contact" value="0" onClick="{field}_contact_clear('{field}');" readonly="readonly">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'clear contact')"/>
			</xsl:attribute>
		</input-->
	</div>
</xsl:template>
