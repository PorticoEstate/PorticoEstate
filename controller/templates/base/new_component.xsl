<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="new_component">
			<xsl:apply-templates select="new_component"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- new_component-->
<xsl:template match="new_component" xmlns:php="http://php.net/xsl">

	<div class="container">
		<form class="pure-form pure-form-stacked" action="{action}" onsubmit="return submitComponentForm(event, this);">
			<fieldset>

				<xsl:call-template name="attributes_values">
					<xsl:with-param name="supress_history_date" select ='supress_history_date'/>
					<xsl:with-param name="template_set">
						<xsl:value-of select="template_set" />
					</xsl:with-param>
				</xsl:call-template>

				<input type="hidden" name="edit_parent" value="{edit_parent}" />
				<input type="hidden" name="parent_location_id" value="{parent_location_id}" />
				<input type="hidden" name="parent_component_id" value="{parent_component_id}" />
				<input type="hidden" name="location_id" value="{location_id}" />
				<input type="hidden" name="component_id" value="{component_id}" />

				<div class="pure-controls pure-button-group" role="group" aria-label="">
					<xsl:if test="get_form =1 or get_edit_form = 1">
						<button id = "submit_component_form" type="submit" class="pure-button pure-button-primary">
							<xsl:choose>
								<xsl:when test="template_set = 'bootstrap'">
									<xsl:attribute name="class">
										<xsl:text>btn btn-primary btn-sm</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">
										<xsl:text>pure-button pure-button-primary</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:value-of select="php:function('lang', 'save')" />
						</button>
						<button id = "cancel_new_component" type="button"  onclick="remove_component_form(form);">
							<xsl:choose>
								<xsl:when test="template_set = 'bootstrap'">
									<xsl:attribute name="class">
										<xsl:text>btn btn-primary btn-sm ml-2</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">
										<xsl:text>pure-button pure-button-primary</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:value-of select="php:function('lang', 'cancel')" />
						</button>
					</xsl:if>
					<xsl:if test="get_info =1">
						<button id = "submit_component_form" type="button" onclick="get_edit_form();">
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
							<xsl:choose>
								<xsl:when test="template_set = 'bootstrap'">
									<xsl:attribute name="class">
										<xsl:text>btn btn-primary btn-sm</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">
										<xsl:text>pure-button pure-button-primary</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:value-of select="php:function('lang', 'edit')" />
						</button>
					</xsl:if>
					<xsl:if test="get_info =1 and enable_add_case = 1">
						<button id = "perform_control_on_selected" type="button" data-toggle="modal" data-target="#inspectObject">
							<xsl:choose>
								<xsl:when test="template_set = 'bootstrap'">
									<xsl:attribute name="class">
										<xsl:text>btn btn-primary btn-sm ml-2</xsl:text>
									</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">
										<xsl:text>pure-button pure-button-primary</xsl:text>
									</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<xsl:value-of select="php:function('lang', 'perform control on selected')" />
						</button>
					</xsl:if>
				</div>
			</fieldset>
		</form>
	</div>
	<xsl:if test="open_cases !=''">
		<div class="container">
			<div class="row ml-5 mt-3">
				<table class="pure-table">
					<caption>Åpne saker</caption>
					<tr>
						<th>
							<xsl:value-of select="php:function('lang', 'date')" />
						</th>
						<th>
							<xsl:value-of select="php:function('lang', 'proposed counter measure')" />
						</th>
						<th>
							<xsl:value-of select="php:function('lang', 'control')" />
						</th>
					</tr>
					<xsl:for-each select="open_cases">
						<tr>
							<td>
								<xsl:value-of select="modified_date_text" />
							</td>
							<td>
								<xsl:value-of disable-output-escaping="yes" select="descr" />
							</td>
							<td>
								<a href="{open_case_url}" target="_blank">
									<xsl:value-of select="check_list_id" />
								</a>
							</td>
						</tr>
					</xsl:for-each>
				</table>
			</div>
		</div>
	</xsl:if>

</xsl:template>

