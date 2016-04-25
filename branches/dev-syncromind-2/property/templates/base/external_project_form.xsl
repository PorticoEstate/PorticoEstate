
<!-- $Id: external_project_form.xsl 14719 2016-02-10 19:45:46Z sigurdne $ -->
<xsl:template name="external_project_form">
	<xsl:apply-templates select="external_project_data"/>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" match="external_project_data">
	<script type="text/javascript">
		function external_project_lookup()
		{
			TINY.box.show({iframe:'<xsl:value-of select="external_project_url"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
		}
	</script>
	<div class="pure-control-group">
		<label for="name">
			<a href="javascript:external_project_lookup()" title="{lang_select_external_project_help}">
				<xsl:value-of select="lang_external_project"/>
			</a>
		</label>
		<input size="9" type="text" name="external_project_id" value="{value_external_project_id}">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_select_external_project_help"/>
			</xsl:attribute>
		</input>
		<input size="30" type="text" name="external_project_name" value="{value_external_project_name}" onClick="external_project_lookup();" readonly="readonly">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_select_external_project_help"/>
			</xsl:attribute>
		</input>
		<xsl:choose>
			<xsl:when test="value_external_project_budget != ''">
				<xsl:value-of select="php:function('lang', 'budget')"/>
				<xsl:text>: </xsl:text>
				<xsl:value-of select="value_external_project_budget"/>
			</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>
