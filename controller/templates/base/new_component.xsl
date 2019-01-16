<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="new_component">
			<xsl:apply-templates select="new_component"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- new_component-->
<xsl:template match="new_component" xmlns:php="http://php.net/xsl">

	<form class="pure-form pure-form-aligned" action="{action}" onsubmit="return submitNewComponent(event, this);">
		<fieldset>
			<xsl:apply-templates select="custom_attributes/attributes"/>
			<input type="hidden" name="parent_location_id" value="{parent_location_id}" />
			<input type="hidden" name="parent_component_id" value="{parent_component_id}" />
			<input type="hidden" name="location_id" value="{location_id}" />
			<button id = "submit_new_component" type="submit" class="pure-button pure-button-primary">
				<xsl:value-of select="php:function('lang', 'save')" />
			</button>
			<button id = "submit_new_component" type="button" class="pure-button pure-button-primary" onclick="remove_form();">
				<xsl:value-of select="php:function('lang', 'cancel')" />
			</button>
		</fieldset>
	</form>

</xsl:template>

