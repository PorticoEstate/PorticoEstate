  <!-- $Id$ -->
	<xsl:template name="project_group_form">
		<xsl:apply-templates select="project_group_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template xmlns:php="http://php.net/xsl" match="project_group_data">
		<script type="text/javascript">
			function project_group_lookup()
			{
				TINY.box.show({iframe:'<xsl:value-of select="project_group_url"/>', boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true});
			}
		</script>
		<tr>
			<td align="left" valign="top">
				<a href="javascript:project_group_lookup()" title="{lang_select_project_group_help}">
					<xsl:value-of select="lang_project_group"/>
				</a>
			</td>
			<td align="left">
				<input size="9" type="text" name="project_group" value="{value_project_group}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_project_group_help"/>
					</xsl:attribute>
				</input>
				<input size="30" type="text" name="project_group_descr" value="{value_project_group_descr}" onClick="project_group_lookup();" readonly="readonly">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_select_project_group_help"/>
					</xsl:attribute>
				</input>
				<xsl:choose>
					<xsl:when test="value_project_group_budget != ''">
						<xsl:value-of select="php:function('lang', 'budget')"/>
						<xsl:text>: </xsl:text>
						<xsl:value-of select="value_project_group_budget"/>
					</xsl:when>
				</xsl:choose>
			</td>
		</tr>
		<!--
<xsl:choose>
<xsl:when test="value_project_group_budget != ''">
<tr>
<td>
</td>
<td valign="top">
<xsl:value-of select="php:function('lang', 'budget')" />
<xsl:text>: </xsl:text>
<xsl:value-of select="value_project_group_budget"/>
</td>
</tr>
</xsl:when>
</xsl:choose>
-->
	</xsl:template>
