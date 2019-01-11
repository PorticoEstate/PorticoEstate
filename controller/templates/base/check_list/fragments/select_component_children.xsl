<xsl:template name="select_component_children" xmlns:php="http://php.net/xsl">
	<form class="pure-form pure-form-aligned" ENCTYPE="multipart/form-data" method="post" id="frm_add_picture">
		<xsl:attribute name="action">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.add_component_image, phpgw_return_as:json')" />
		</xsl:attribute>

		<input type="hidden" name="period_type" value="view_year" />
		<input type="hidden" name="year">
			<xsl:attribute name="value">
				<xsl:value-of select="current_year"/>
			</xsl:attribute>
		</input>

		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'equipment')" />
			</label>
			<select id="choose-child-on-component" name = "component" class="select-component">
				<option value="">Velg utstyr</option>
				<xsl:for-each select="component_children">
					<option>
						<xsl:if test="id = //current_child/id">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="value">
							<xsl:value-of select="location_id"/>
							<xsl:text>_</xsl:text>
							<xsl:value-of select="id"/>
						</xsl:attribute>
						<xsl:value-of select="short_description" />
					</option>
				</xsl:for-each>
			</select>
		</div>

		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'picture')" />
			</label>
			<div class="pure-custom" id="equipment_picture_container"/>
		</div>
		<div id="new_picture" class="pure-control-group" style="display:none">
			<label>
				<xsl:value-of select="php:function('lang', 'new picture')" />
			</label>
			<input type="file" id="component_picture_file" name="file" onchange="show_picture_submit();">
				<xsl:attribute name="accept">image/*</xsl:attribute>
				<xsl:attribute name="capture">camera</xsl:attribute>
			</input>
		<button id = "submit_update_component" type="submit" class="pure-button pure-button-primary">
			<xsl:value-of select="php:function('lang', 'add picture')" />
		</button>

		</div>
	</form>
</xsl:template>
