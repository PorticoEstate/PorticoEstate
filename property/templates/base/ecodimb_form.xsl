
<!-- $Id$ -->
<xsl:template name="ecodimb_form">
	<xsl:param name="class" />

	<xsl:apply-templates select="ecodimb_data">
		<xsl:with-param name="class">
			<xsl:value-of select="$class"/>
		</xsl:with-param>
	</xsl:apply-templates>
</xsl:template>

<!-- New template-->
<xsl:template match="ecodimb_data" xmlns:php="http://php.net/xsl">
	<xsl:param name="class" />
	<script type="text/javascript">
		function ecodimb_lookup()
		{
		TINY.box.show({iframe:'<xsl:value-of select="ecodimb_url"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<xsl:choose>
		<xsl:when test="disabled='1'">
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="lang_ecodimb"/>
				</label>
				<input size="9" type="text" value="{value_ecodimb}" readonly="readonly"/>
				<input size="30" type="text" value="{value_ecodimb_descr}" readonly="readonly"/>
				<input size="9" type="hidden" name="ecodimb" value="{value_ecodimb}" readonly="readonly"/>
				<input size="30" type="hidden" name="ecodimb_descr" value="{value_ecodimb_descr}" readonly="readonly"/>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<div class="pure-control-group">
				<label>
					<a href="javascript:ecodimb_lookup()" title="{lang_select_ecodimb_help}">
						<xsl:value-of select="lang_ecodimb"/>
					</a>
				</label>
				<div class="{$class} pure-custom">
					<input size="9" type="text" id="field_ecodimb" name="ecodimb" value="{value_ecodimb}" class ="pure-u-1-5">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_select_ecodimb_help"/>
						</xsl:attribute>
						<xsl:if test="required='1'">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select a value !')"/>
							</xsl:attribute>
						</xsl:if>
					</input>
					<input size="30" type="text" name="ecodimb_descr" value="{value_ecodimb_descr}" onClick="ecodimb_lookup();" readonly="readonly" class ="pure-u-4-5">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_select_ecodimb_help"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>
