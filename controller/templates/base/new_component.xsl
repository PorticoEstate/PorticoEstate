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
			<xsl:apply-templates select="attributes_general/attributes"/>

			<input type="hidden" name="parent_location_id" value="{parent_location_id}" />
			<input type="hidden" name="parent_component_id" value="{parent_component_id}" />
			<input type="hidden" name="location_id" value="{location_id}" />
			<input type="hidden" name="component_id" value="{component_id}" />
			<xsl:if test="get_form =1 or get_edit_form = 1">
				<button id = "submit_new_component" type="submit" class="pure-button pure-button-primary">
					<xsl:value-of select="php:function('lang', 'save')" />
				</button>
				<button id = "cance_new_component" type="button" class="pure-button pure-button-primary" onclick="remove_form();">
					<xsl:value-of select="php:function('lang', 'cancel')" />
				</button>
			</xsl:if>
			<xsl:if test="get_info =1">
				<button id = "submit_new_component" type="button" class="pure-button pure-button-primary" onclick="get_edit_form();">
					<xsl:value-of select="php:function('lang', 'edit')" />
				</button>
			</xsl:if>
		</fieldset>
	</form>

</xsl:template>

