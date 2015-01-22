  <!-- $Id$ -->
	<xsl:template name="vendor_form">
		<xsl:apply-templates select="vendor_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="vendor_data">
		<script type="text/javascript">
			function vendor_lookup()
			{
				TINY.box.show({iframe:'<xsl:value-of select="vendor_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}
		</script>
		<div class="pure-control-group">
			<label for="name">
				<a href="javascript:vendor_lookup()" onMouseover="window.status='{lang_select_vendor_help}';return true;" onMouseout="window.status='';return true;">
					<xsl:value-of select="lang_vendor"/>
				</a>
			</label>
			<input size="5" type="text" id="vendor_id" name="vendor_id" value="{value_vendor_id}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_select_vendor_help"/>
				</xsl:attribute>
			</input>
			<input size="30" type="text" name="vendor_name" value="{value_vendor_name}" onClick="vendor_lookup();" readonly="readonly">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_select_vendor_help"/>
				</xsl:attribute>
			</input>
		</div>
	</xsl:template>
