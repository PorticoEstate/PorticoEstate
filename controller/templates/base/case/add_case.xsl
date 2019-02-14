<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<div id="main_content" class="medium">

		<xsl:call-template name="check_list_top_section">
		</xsl:call-template>

		<xsl:choose>
			<xsl:when test="buildings_on_property/child::node()">
				<div id="choose-building-wrp">
					<xsl:call-template name="select_buildings_on_property" />
				</div>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="component_children/child::node()">
				<div id="choose-building-wrp">
					<xsl:call-template name="select_component_children" />
				</div>
			</xsl:when>
		</xsl:choose>

		<div id="view_cases">

			<h3 class="box_header ext">Registrer sak/måling</h3>
			<div class="tab_item active ext">
				<input type="hidden" id="cache_case_id" value=""></input>

				<xsl:variable name="action_url">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.save_case_ajax,phpgw_return_as:json')" />
				</xsl:variable>

				<ul class="add-cases">
					<xsl:for-each select="control_groups_with_items_array">
						<xsl:choose>
							<xsl:when test="control_items/child::node()">
								<li>
									<h3 class="expand-trigger">
										<img src="controller/images/arrow_right.png" />
										<xsl:value-of select="control_group/group_name"/>
									</h3>
									<ul class="expand_list" style="display:none;">
										<xsl:choose>
											<xsl:when test="components_at_location/child::node()">
												<select name="component_at_control_group_{control_group/id}" id="component_at_control_group_{control_group/id}">
													<xsl:apply-templates select="components_at_location/component_options"/>
												</select>
											</xsl:when>
										</xsl:choose>
										<xsl:variable name="control_group_id">
											<xsl:value-of select="control_group/id"/>
										</xsl:variable>
										<xsl:for-each select="control_items">
											<li>
												<h4>
													<img src="controller/images/arrow_right.png" />
													<span>
														<xsl:value-of select="title"/>
													</span>
												</h4>
												<form class="frm_register_case expand_item" action="{$action_url}" method="post">
													<!--input type="hidden" name="location_code"  value="" class="required" /-->
													<input type="hidden" name="location_code"  value="" >
														<xsl:if test="//location_required = 1">
															<xsl:attribute name="class" >
																<xsl:text>required</xsl:text>
															</xsl:attribute>
														</xsl:if>
													</input>
													<input type="hidden" name="control_group_id"  value="{$control_group_id}" />
													<input type="hidden" name="component_location_id">
														<xsl:attribute name="value">
															<xsl:value-of select="//check_list/location_id"/>
														</xsl:attribute>
													</input>
													<input type="hidden" name="component_id">
														<xsl:attribute name="value">
															<xsl:value-of select="//check_list/component_id"/>
														</xsl:attribute>
													</input>
													<xsl:variable name="control_item_id">
														<xsl:value-of select="id"/>
													</xsl:variable>
													<input type="hidden" name="control_item_id" value="{$control_item_id}" />
													<input type="hidden" name="check_list_id">
														<xsl:attribute name="value">
															<xsl:value-of select="//check_list/id"/>
														</xsl:attribute>
													</input>

													<xsl:choose>
														<xsl:when test="what_to_do !=''">
															<!--  WHAT TO DO -->
															<div class="row what-to-do">
																<label>Hva skal sjekkes:</label>
																<div>
																	<xsl:value-of disable-output-escaping="yes" select="what_to_do"/>
																</div>
															</div>
														</xsl:when>
													</xsl:choose>

													<xsl:choose>
														<xsl:when test="how_to_do !=''">
															<!--  HOW TO DO -->
															<div class="row how-to-do">
																<label>Utførelsesbeskrivelse:</label>
																<div>
																	<xsl:value-of disable-output-escaping="yes" select="how_to_do"/>
																</div>
															</div>
														</xsl:when>
													</xsl:choose>
													<xsl:if test="include_condition_degree = 1">
														<div class="row">
															<label>
																<xsl:attribute name="title">
																	<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
																</xsl:attribute>
																<xsl:value-of select="php:function('lang', 'condition degree')"/>
															</label>
															<select name="condition_degree">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'select value')"/>
																</xsl:attribute>
																<xsl:apply-templates select="//degree_list/options"/>
															</select>
														</div>
														<div class="row">
															<label>
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'consequence')"/>
																</xsl:attribute>
																<xsl:value-of select="php:function('lang', 'consequence')"/>
															</label>
															<select name="consequence">
																<xsl:attribute name="title">
																	<xsl:value-of select="php:function('lang', 'select value')"/>
																</xsl:attribute>
																<xsl:apply-templates select="//consequence_list/options"/>
															</select>
														</div>
													</xsl:if>
													<xsl:choose>
														<xsl:when test="type = 'control_item_type_1'">
															<input type="hidden" name="status" value="0" />
															<input type="hidden" name="type" value="control_item_type_1" />
															<div class="row">
																<label>Status</label>
																<select name="status">
																	<option value="0" SELECTED="SELECTED">Åpen</option>
																	<option value="1" >Lukket</option>
																	<option value="2" >Venter på tilbakemelding</option>
																</select>
															</div>
															<div>
																<label class="comment">Beskrivelse av sak</label>
																<textarea name="case_descr" >
																	<xsl:if test="required = 1">
																		<xsl:attribute name="class" >
																			<xsl:text>required</xsl:text>
																		</xsl:attribute>
																	</xsl:if>
																	<xsl:value-of select="comment"/>
																</textarea>
															</div>
															<xsl:if test="include_counter_measure = 1">
																<div>
																	<label class="comment">
																		<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																	</label>
																	<textarea name="proposed_counter_measure" >
																		<xsl:value-of select="proposed_counter_measure"/>
																	</textarea>
																</div>
															</xsl:if>
															<input type="submit" class="btn" name="save_control" value="Lagre sak" />

														</xsl:when>
														<xsl:when test="type = 'control_item_type_2'">
															<input name="type" type="hidden" value="control_item_type_2" />

															<div class="row">
																<label>Status</label>
																<select name="status">
																	<option value="0" SELECTED="SELECTED">Åpen</option>
																	<option value="1" >Lukket</option>
																	<option value="2" >Venter på tilbakemelding</option>
																</select>
															</div>
															<div class="row">
																<label class="comment">Registrer målingsverdi</label>
																<input>
																	<xsl:if test="required = 1">
																		<xsl:attribute name="class" >
																			<xsl:text>required</xsl:text>
																		</xsl:attribute>
																	</xsl:if>

																	<xsl:attribute name="name">measurement</xsl:attribute>
																	<xsl:attribute name="type">text</xsl:attribute>
																	<xsl:attribute name="value">
																		<xsl:value-of select="measurement"/>
																	</xsl:attribute>
																</input>
															</div>
															<div class="row">
																<label class="comment">Beskrivelse av sak</label>
																<textarea name="case_descr">
																	<xsl:value-of select="comment"/>
																</textarea>
															</div>
															<xsl:if test="include_counter_measure = 1">
																<div>
																	<label class="comment">
																		<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																	</label>
																	<textarea name="proposed_counter_measure" >
																		<xsl:value-of select="proposed_counter_measure"/>
																	</textarea>
																</div>
															</xsl:if>
															<xsl:variable name="lang_save">
																<xsl:value-of select="php:function('lang', 'register_error')" />
															</xsl:variable>
															<input type="submit" name="save_control" value="Lagre måling" title="{$lang_save}" />


														</xsl:when>
														<xsl:when test="type = 'control_item_type_3'">
															<input name="type" type="hidden" value="control_item_type_3" />

															<div class="row">
																<label>Status</label>
																<select name="status">
																	<option value="0" SELECTED="SELECTED">Åpen</option>
																	<option value="1" >Lukket</option>
																	<option value="2" >Venter på tilbakemelding</option>
																</select>
															</div>
															<div class="row">
																<label class="comment">Velg verdi fra liste</label>
																<select name="option_value">
																	<xsl:if test="required = 1">
																		<xsl:attribute name="class" >
																			<xsl:text>required</xsl:text>
																		</xsl:attribute>
																	</xsl:if>
																	<option value="" >Velg</option>
																	<xsl:for-each select="options_array">
																		<option>
																			<xsl:attribute name="value">
																				<xsl:value-of select="option_value"/>
																			</xsl:attribute>
																			<xsl:value-of select="option_value"/>
																		</option>
																	</xsl:for-each>
																</select>
															</div>
															<div class="row">
																<label class="comment">Beskrivelse av sak</label>
																<textarea name="case_descr">
																	<xsl:value-of select="comment"/>
																</textarea>
															</div>
															<xsl:if test="include_counter_measure = 1">
																<div>
																	<label class="comment">
																		<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																	</label>
																	<textarea name="proposed_counter_measure" >
																		<xsl:value-of select="proposed_counter_measure"/>
																	</textarea>
																</div>
															</xsl:if>
															<xsl:variable name="lang_save">
																<xsl:value-of select="php:function('lang', 'register_error')" />
															</xsl:variable>
															<input type="submit" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

														</xsl:when>
														<xsl:when test="type = 'control_item_type_4'">
															<input name="type" type="hidden" value="control_item_type_4" />
															<div class="row">
																<label>Status</label>
																<select name="status">
																	<option value="0" SELECTED="SELECTED">Åpen</option>
																	<option value="1" >Lukket</option>
																	<option value="2" >Venter på tilbakemelding</option>
																</select>
															</div>
															<div class="row">
																<label class="comment">Velg verdi fra lister</label>
																<br/>
																<xsl:for-each select="options_array">
																	<input type="radio" name="option_value" value="{option_value}">
																		<xsl:if test="required = 1">
																			<xsl:attribute name="required" >
																				<xsl:text>required</xsl:text>
																			</xsl:attribute>
																		</xsl:if>
																	</input>
																	<xsl:value-of select="option_value"/>
																	<br/>
																</xsl:for-each>
																<!--<select name="option_value">
																	<xsl:if test="required = 1">
																		<xsl:attribute name="class" >
																			<xsl:text>required</xsl:text>
																		</xsl:attribute>
																	</xsl:if>
																	<option value="" >Velg</option>
																	<xsl:for-each select="options_array">
																		<option>
																			<xsl:attribute name="value">
																				<xsl:value-of select="option_value"/>
																			</xsl:attribute>
																			<xsl:value-of select="option_value"/>
																		</option>
																	</xsl:for-each>
																</select>-->
															</div>
															<div class="row">
																<label class="comment">Beskrivelse av sak</label>
																<textarea name="case_descr">
																	<xsl:value-of select="comment"/>
																</textarea>
															</div>
															<xsl:if test="include_counter_measure = 1">
																<div>
																	<label class="comment">
																		<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																	</label>
																	<textarea name="proposed_counter_measure" >
																		<xsl:value-of select="proposed_counter_measure"/>
																	</textarea>
																</div>
															</xsl:if>
															<xsl:variable name="lang_save">
																<xsl:value-of select="php:function('lang', 'register_error')" />
															</xsl:variable>
															<input type="submit" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

														</xsl:when>
														<xsl:when test="type = 'control_item_type_5'">
															<input name="type" type="hidden" value="control_item_type_5" />
															<div class="row">
																<label>Status</label>
																<select name="status">
																	<option value="0" SELECTED="SELECTED">Åpen</option>
																	<option value="1" >Lukket</option>
																	<option value="2" >Venter på tilbakemelding</option>
																</select>
															</div>
															<div class="row">
																<label class="comment">Velg verdi fra lister</label>
																<br/>
																<div>
																	<xsl:if test="required = 1">
																		<xsl:attribute name="class" >
																			<xsl:text>required</xsl:text>
																		</xsl:attribute>
																	</xsl:if>
																	<xsl:for-each select="options_array">
																		<input type="checkbox" name="option_value[]" value="{option_value}">
																		</input>
																		<xsl:value-of select="option_value"/>
																		<br/>
																	</xsl:for-each>
																</div>
															</div>
															<div class="row">
																<label class="comment">Beskrivelse av sak</label>
																<textarea name="case_descr">
																	<xsl:value-of select="comment"/>
																</textarea>
															</div>
															<xsl:if test="include_counter_measure = 1">
																<div>
																	<label class="comment">
																		<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																	</label>
																	<textarea name="proposed_counter_measure" >
																		<xsl:value-of select="proposed_counter_measure"/>
																	</textarea>
																</div>
															</xsl:if>
															<xsl:variable name="lang_save">
																<xsl:value-of select="php:function('lang', 'register_error')" />
															</xsl:variable>
															<input type="submit" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

														</xsl:when>
													</xsl:choose>
													<xsl:variable name="lang_reset_form">
														<xsl:value-of select="php:function('lang', 'reset form')" />
													</xsl:variable>
													<input type="button" name="reset_form" value="{$lang_reset_form}" title="{lang_reset_form}" class="pure-button pure-button-primary" onclick="resetForm(form);"/>

												</form>
												<div class="add_picture_to_case" style="display:none">
													<form class="pure-form pure-form-aligned add_picture_to_case_form" ENCTYPE="multipart/form-data" method="post">
														<xsl:attribute name="action">
															<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.add_case_image, phpgw_return_as:json')" />
														</xsl:attribute>

														<div class="pure-control-group">
															<label>
																<xsl:value-of select="php:function('lang', 'picture')" />
															</label>
															<div class="pure-custom" name="picture_container"/>
														</div>
														<div id="new_picture" class="pure-control-group">
															<label>
																<xsl:value-of select="php:function('lang', 'new picture')" />
															</label>
															<input type="file" id="case_picture_file" name="file">
																<xsl:attribute name="accept">image/*</xsl:attribute>
																<xsl:attribute name="capture">camera</xsl:attribute>
															</input>
															<button id = "submit_update_component" type="submit" class="pure-button pure-button-primary">
																<xsl:value-of select="php:function('lang', 'add picture')" />
															</button>

														</div>
													</form>
												</div>

											</li>
										</xsl:for-each>
									</ul>
								</li>
							</xsl:when>
							<xsl:otherwise>
								<li class="list_item">
									<h3>
										<xsl:value-of select="control_group/group_name"/>
									</h3>
									<div>Ingen kontrollpunkt for denne gruppen</div>
								</li>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</ul>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="component_options">
	<option value="{location_id}_{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of select="id"/> - <xsl:value-of disable-output-escaping="yes" select="short_description"/>
	</option>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
