
<!-- $Id$ -->
<xsl:template name="b_account_form">
	<xsl:apply-templates select="b_account_data"/>
</xsl:template>

<!-- New template-->
<xsl:template match="b_account_data" xmlns:php="http://php.net/xsl">
	<script type="text/javascript">
		function b_account_lookup()
		{
			TINY.box.show({iframe:'<xsl:value-of select="b_account_link"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<xsl:choose>
		<xsl:when test="disabled='1'">
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_b_account"/>
				</label>
				<input size="9" type="text" value="{value_b_account_id}" readonly="readonly"/>
				<input size="30" type="text" value="{value_b_account_name}" readonly="readonly"/>
				<input size="9" type="hidden" name="b_account_id" value="{value_b_account_id}" readonly="readonly"/>
				<input size="30" type="hidden" name="b_account_name" value="{value_b_account_name}" readonly="readonly"/>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<div class="pure-control-group">
				<label>
					<a href="javascript:b_account_lookup()" title="{lang_select_b_account_help}">
						<xsl:value-of select="lang_b_account"/>
					</a>
				</label>
				<input size="9" type="text" name="b_account_id" value="{value_b_account_id}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_b_account_help"/>
					</xsl:attribute>
					<xsl:if test="required='1'">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please select a budget account !')"/>
						</xsl:attribute>
					</xsl:if>
				</input>
				<input size="30" type="text" name="b_account_name" value="{value_b_account_name}" onClick="b_account_lookup();" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_b_account_help"/>
					</xsl:attribute>
				</input>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
