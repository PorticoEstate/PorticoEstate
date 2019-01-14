<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="new_component">
			<xsl:apply-templates select="new_component"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- new_component-->
<xsl:template match="new_component"  xmlns:php="http://php.net/xsl">

	<form class="pure-form pure-form-aligned" method="post" action="{action}">
		<fieldset>
			<xsl:apply-templates select="custom_attributes/attributes"/>
			<input type="text" name="check_list_id" value="{check_list_id}" />
			<input type="text" name="parent_location_id" value="{parent_location_id}" />
			<input type="text" name="parent_component_id" value="{parent_component_id}" />
			<input type="text" name="location_id" value="{location_id}" />
			<button id = "submit_new_component" type="submit" class="pure-button pure-button-primary">
				<xsl:value-of select="php:function('lang', 'save')" />
			</button>

		</fieldset>
	</form>

</xsl:template>

