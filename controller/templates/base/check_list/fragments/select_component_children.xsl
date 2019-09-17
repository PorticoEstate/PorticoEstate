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

		<div class="pure-control-group row mt-3">
			<label>
				<xsl:value-of select="php:function('lang', 'equipment')" />
			</label>
			<select id="choose-child-on-component" name = "component" class="pure-input-1-2 select-component">
				<xsl:for-each select="component_children">
					<option>
						<xsl:if test="id = //current_child/id">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="value">
							<xsl:if test="id &gt; 0">
								<xsl:value-of select="location_id"/>
								<xsl:text>_</xsl:text>
								<xsl:value-of select="id"/>
							</xsl:if>
						</xsl:attribute>
						<xsl:value-of select="short_description" />
					</option>
				</xsl:for-each>
			</select>
		</div>

		<div class="pure-control-group row">
			<label>
				<xsl:value-of select="php:function('lang', 'picture')" />
			</label>
			<div class="pure-custom" id="equipment_picture_container"/>
		</div>
		<div id="new_picture" class="pure-control-group row" style="display:none">
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

	<xsl:for-each select="location_children">
		<form class="pure-form pure-form-aligned form_new_component row" method="post">
			<xsl:attribute name="action">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.edit_component_child, phpgw_return_as:json')" />
			</xsl:attribute>
			<fieldset>
				<input type="hidden" name="get_form" value="1" />
				<input type="hidden" name="parent_location_id" value="{parent_location_id}" />
				<input type="hidden" name="parent_component_id" value="{parent_component_id}" />
				<input type="hidden" name="location_id" value="{location_id}" />

				<div class="pure-controls">
					<button id = "submit_component_form" type="submit" class="pure-button pure-button-primary">
						<xsl:value-of select="php:function('lang', 'new')" />
						<xsl:text>: </xsl:text>
						<xsl:value-of select="name" />
					</button>
					<button id = "download_components" type="button" class="pure-button pure-button-primary" onclick="downloadComponents({parent_location_id}, {parent_component_id}, {location_id});">
						<xsl:value-of select="php:function('lang', 'download')" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="name" />
					</button>
				</div>
			</fieldset>
		</form>
	</xsl:for-each>
	<div id = "form_new_component_2" class="row">

	</div>

</xsl:template>
