<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="new_component">
			<xsl:apply-templates select="new_component"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- new_component-->
<xsl:template match="new_component" xmlns:php="http://php.net/xsl">

	<form class="pure-form pure-form-aligned" action="{action}" onsubmit="return submitComponentForm(event, this);">
		<fieldset>
			<xsl:apply-templates select="attributes_general/attributes"/>

			<input type="hidden" name="edit_parent" value="{edit_parent}" />
			<input type="hidden" name="parent_location_id" value="{parent_location_id}" />
			<input type="hidden" name="parent_component_id" value="{parent_component_id}" />
			<input type="hidden" name="location_id" value="{location_id}" />
			<input type="hidden" name="component_id" value="{component_id}" />
			<xsl:if test="get_form =1 or get_edit_form = 1">
				<button id = "submit_component_form" type="submit" class="pure-button pure-button-primary">
					<xsl:value-of select="php:function('lang', 'save')" />
				</button>
			</xsl:if>
			<xsl:if test="get_info =1">
				<button id = "submit_component_form" type="button" class="pure-button pure-button-primary" onclick="get_edit_form();">
					<xsl:choose>
						<xsl:when test="edit_parent !=1">
							<xsl:attribute name="onclick">
								<xsl:text>get_edit_form();</xsl:text>
							</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="onclick">
								<xsl:text>get_parent_component_edit_form();</xsl:text>
							</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="php:function('lang', 'edit')" />
				</button>
			</xsl:if>
			<button id = "cancel_new_component" type="button" class="pure-button pure-button-primary" onclick="remove_component_form(form);">
				<xsl:value-of select="php:function('lang', 'cancel')" />
			</button>

		</fieldset>
	</form>

</xsl:template>

