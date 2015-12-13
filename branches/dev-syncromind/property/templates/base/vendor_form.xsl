  <!-- $Id$ -->
	<xsl:template name="vendor_form">
		<xsl:apply-templates select="vendor_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="vendor_data" xmlns:php="http://php.net/xsl">
		<script type="text/javascript">
			function vendor_lookup()
			{
				TINY.box.show({iframe:'<xsl:value-of select="vendor_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}

		</script>
          <div class="pure-control-group">
			<label>
				<a href="javascript:vendor_lookup()" title="{lang_select_vendor_help}">
					<xsl:value-of select="lang_vendor"/>
				</a>
			</label>
				<input size="5" type="text" id="vendor_id" name="vendor_id" value="{value_vendor_id}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_vendor_help"/>
					</xsl:attribute>
					<xsl:if test="required='1'">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'no vendor')"/>
						</xsl:attribute>
					</xsl:if>
				</input>
				<input size="30" type="text" name="vendor_name" value="{value_vendor_name}" onClick="vendor_lookup();" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_vendor_help"/>
					</xsl:attribute>
				</input>
		  </div>
	</xsl:template>
