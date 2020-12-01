<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>
	<xsl:variable name="lang_regulation_reference">
		<xsl:value-of select="php:function('lang', 'regulation reference')"/>
	</xsl:variable>
	<xsl:variable name="lang_new_value">
		<xsl:value-of select="php:function('lang', 'new value')"/>
	</xsl:variable>


	<div id="main_content" class="medium container">
		<a href="#" class="scrollup">
			<xsl:value-of select="php:function('lang', 'scroll to top')" />
		</a>

		<div class="mt-3">
			<xsl:call-template name="check_list_top_section">
				<xsl:with-param name="active_tab">add_case</xsl:with-param>
			</xsl:call-template>
		</div>
		<!--		<xsl:choose>
			<xsl:when test="buildings_on_property/child::node()">
				<div id="choose-building-wrp" class="row mt-3">
					<xsl:call-template name="select_buildings_on_property" />
				</div>
			</xsl:when>
		</xsl:choose>-->

		<!--		<xsl:choose>
			<xsl:when test="component_children/child::node()">
				<div id="choose-building-wrp">
					<xsl:call-template name="select_component_children" />
					<div class="row mt-2">
						<div class="container">
							<h5 class="ml-5">Kontrollert utstyr</h5>
							<ul class="ml-2">
								<xsl:for-each select="completed_list">
									<li style="display: block;">
										<a href="#">
											<img src="{//img_undo}" width="16" class="mr-2" onClick="undo_completed({completed_id})"/>
										</a>
										<img src="{//img_green_check}" width="16" class="mr-2"/>
										<xsl:value-of select="short_description" />
									</li>
								</xsl:for-each>
							</ul>
						</div>
					</div>
				</div>
			</xsl:when>
		</xsl:choose>-->
		<xsl:call-template name="check_list_change_status">
			<xsl:with-param name="active_tab">add_case</xsl:with-param>
		</xsl:call-template>

		<div id="view_cases" class="container mt-4">
			<!--			<xsl:choose>
				<xsl:when test="component_children/child::node() and count(component_children) &gt; 0">
					<xsl:attribute name="style">
						<xsl:text>display:none</xsl:text>
					</xsl:attribute>
				</xsl:when>
			</xsl:choose>-->
			<!-- differ from bootstrap-->
			<!--			<h5>
				<a href="#" data-toggle="modal" data-target="#inspectObject">
					<img src="{img_add2}" width="23" class="mr-2"/>Utfør kontroll</a>
			</h5>-->

			<!-- MODAL INSPECT EQUIPMENT START -->
			<div class="modal fade" id="inspectObject">
				<div class="modal-dialog">
					<div class="modal-content">
						<!-- Modal Header -->
						<div class="modal-header">
							<h4 id="inspection_title" class="modal-title">Utfør kontroll</h4>
						</div>
						<!-- Modal body -->
						<div class="modal-body">

							<div class="tab_item active ext">
								<input type="hidden" id="cache_case_id" value=""></input>

								<xsl:variable name="action_url">
									<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.save_case_ajax,phpgw_return_as:json')" />
								</xsl:variable>

								<xsl:for-each select="control_groups_with_items_array">
									<xsl:choose>
										<xsl:when test="control_items/child::node()">
											<!--<div class="row mt-2">-->
											<h1>
												<xsl:value-of select="control_group/group_name"/>
											</h1>

											<xsl:choose>
												<xsl:when test="components_at_location/child::node()">
													<select name="component_at_control_group_{control_group/id}" id="component_at_control_group_{control_group/id}" class="custom-select">
														<xsl:apply-templates select="components_at_location/component_options"/>
													</select>
												</xsl:when>
											</xsl:choose>
											<xsl:variable name="control_group_id">
												<xsl:value-of select="control_group/id"/>
											</xsl:variable>
											<xsl:for-each select="control_items">
												<fieldset>
													<legend>
														<h3>
															<xsl:value-of select="title"/>
														</h3>
													</legend>
													<form class="frm_register_case" action="{$action_url}" method="post">
														<!--input type="hidden" name="location_code"  value="" class="required" /-->
														<input type="hidden" name="location_code" >
															<xsl:attribute name="value" >
																<xsl:value-of select="//case_location_code"/>
															</xsl:attribute>
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
														<input type="hidden" id="check_list_id" name="check_list_id">
															<xsl:attribute name="value">
																<xsl:value-of select="//check_list/id"/>
															</xsl:attribute>
														</input>

														<xsl:choose>
															<xsl:when test="what_to_do !=''">
																<div class="form-group what-to-do">
																	<label>Hva skal sjekkes:</label>
																	<div>
																		<xsl:value-of disable-output-escaping="yes" select="what_to_do"/>
																	</div>
																</div>
															</xsl:when>
														</xsl:choose>

														<xsl:choose>
															<xsl:when test="how_to_do !=''">
																<div class="form-group how-to-do">
																	<label>Utførelsesbeskrivelse:</label>
																	<div>
																		<xsl:value-of disable-output-escaping="yes" select="how_to_do"/>
																	</div>
																</div>
															</xsl:when>
														</xsl:choose>
														<xsl:if test="include_condition_degree = 1">

															<div class="form-group">
																<label>
																	<xsl:attribute name="title">
																		<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
																	</xsl:attribute>
																	<xsl:value-of select="php:function('lang', 'condition degree')"/>
																</label>
																<select name="condition_degree" class="custom-select">
																	<xsl:attribute name="title">
																		<xsl:value-of select="php:function('lang', 'select value')"/>
																	</xsl:attribute>
																	<xsl:apply-templates select="//degree_list/options"/>
																</select>
															</div>
															<div class="form-group">
																<label>
																	<xsl:attribute name="title">
																		<xsl:value-of select="php:function('lang', 'consequence')"/>
																	</xsl:attribute>
																	<xsl:value-of select="php:function('lang', 'consequence')"/>
																</label>
																<select name="consequence" class="custom-select">
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
																<div class="form-group">
																	<label>Status</label>
																	<select name="status" class="custom-select">
																		<option value="0" SELECTED="SELECTED">Åpen</option>
																		<option value="1" >Lukket</option>
																		<option value="2" >Venter på tilbakemelding</option>
																		<option value="3" >
																			<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
																		</option>
																	</select>
																</div>
																<div class="form-group">
																	<label>Beskrivelse av sak</label>
																	<textarea name="case_descr" class="form-control">
																		<xsl:if test="required = 1">
																			<xsl:attribute name="class" >
																				<xsl:text>required form-control</xsl:text>
																			</xsl:attribute>
																		</xsl:if>

																		<xsl:value-of select="comment"/>
																	</textarea>
																</div>
																<xsl:if test="include_counter_measure = 1">
																	<div class="form-group">
																		<label>
																			<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																		</label>
																		<textarea name="proposed_counter_measure" class="form-control">
																			<xsl:value-of select="proposed_counter_measure"/>
																		</textarea>
																	</div>
																</xsl:if>
																<xsl:call-template name="regulation_reference" >
																	<xsl:with-param name="control_item_id">
																		<xsl:value-of select="$control_item_id" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_regulation_reference">
																		<xsl:value-of select="$lang_regulation_reference" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_new_value">
																		<xsl:value-of select="$lang_new_value" />
																	</xsl:with-param>
																</xsl:call-template>
																<input type="submit" class="btn btn-primary btn-lg mr-3" name="save_control" value="Lagre sak" />

															</xsl:when>
															<xsl:when test="type = 'control_item_type_2'">
																<input name="type" type="hidden" value="control_item_type_2" />

																<div class="form-group">
																	<label>Status</label>
																	<select name="status" class="custom-select">
																		<option value="0" SELECTED="SELECTED">Åpen</option>
																		<option value="1" >Lukket</option>
																		<option value="2" >Venter på tilbakemelding</option>
																		<option value="3" >
																			<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
																		</option>
																	</select>
																</div>
																<div class="form-group">
																	<label>Registrer målingsverdi</label>
																	<input class="form-control">
																		<xsl:if test="required = 1">
																			<xsl:attribute name="class" >
																				<xsl:text>required form-control</xsl:text>
																			</xsl:attribute>
																		</xsl:if>

																		<xsl:attribute name="name">measurement</xsl:attribute>
																		<xsl:attribute name="type">text</xsl:attribute>
																		<xsl:attribute name="value">
																			<xsl:value-of select="measurement"/>
																		</xsl:attribute>
																	</input>
																</div>
																<div class="form-group">
																	<label>Beskrivelse av sak</label>
																	<textarea name="case_descr" class="form-control">
																		<xsl:value-of select="comment"/>
																	</textarea>
																</div>
																<xsl:if test="include_counter_measure = 1">
																	<div>
																		<label>
																			<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																		</label>
																		<textarea name="proposed_counter_measure" class="form-control">
																			<xsl:value-of select="proposed_counter_measure"/>
																		</textarea>
																	</div>
																</xsl:if>
																<xsl:call-template name="regulation_reference" >
																	<xsl:with-param name="control_item_id">
																		<xsl:value-of select="$control_item_id" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_regulation_reference">
																		<xsl:value-of select="$lang_regulation_reference" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_new_value">
																		<xsl:value-of select="$lang_new_value" />
																	</xsl:with-param>
																</xsl:call-template>
																<xsl:variable name="lang_save">
																	<xsl:value-of select="php:function('lang', 'register_error')" />
																</xsl:variable>
																<input type="submit" class="btn btn-primary btn-lg mr-3" name="save_control" value="Lagre måling" title="{$lang_save}" />

															</xsl:when>
															<xsl:when test="type = 'control_item_type_3'">
																<input name="type" type="hidden" value="control_item_type_3" />

																<div class="form-group">
																	<label>Status</label>
																	<select name="status" class="custom-select">
																		<option value="0" SELECTED="SELECTED">Åpen</option>
																		<option value="1" >Lukket</option>
																		<option value="2" >Venter på tilbakemelding</option>
																		<option value="3" >
																			<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
																		</option>
																	</select>
																</div>
																<div class="form-group">
																	<label>Velg verdi fra liste</label>
																	<select name="option_value" class="custom-select">
																		<xsl:if test="required = 1">
																			<xsl:attribute name="class" >
																				<xsl:text>custom-select required</xsl:text>
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
																<div class="form-group">
																	<label>Beskrivelse av sak</label>
																	<textarea name="case_descr" class="form-control">
																		<xsl:value-of select="comment"/>
																	</textarea>
																</div>
																<xsl:if test="include_counter_measure = 1">
																	<div class="form-group">
																		<label>
																			<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																		</label>
																		<textarea name="proposed_counter_measure" class="form-control">
																			<xsl:value-of select="proposed_counter_measure"/>
																		</textarea>
																	</div>
																</xsl:if>
																<xsl:call-template name="regulation_reference" >
																	<xsl:with-param name="control_item_id">
																		<xsl:value-of select="$control_item_id" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_regulation_reference">
																		<xsl:value-of select="$lang_regulation_reference" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_new_value">
																		<xsl:value-of select="$lang_new_value" />
																	</xsl:with-param>
																</xsl:call-template>
																<xsl:variable name="lang_save">
																	<xsl:value-of select="php:function('lang', 'register_error')" />
																</xsl:variable>
																<input type="submit" class="btn btn-primary btn-lg mr-3" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

															</xsl:when>
															<xsl:when test="type = 'control_item_type_4'">

																<input name="type" type="hidden" value="control_item_type_4" />

																<div class="form-group">
																	<label>Status</label>
																	<select name="status" class="custom-select">
																		<option value="0" SELECTED="SELECTED">Åpen</option>
																		<option value="1" >Lukket</option>
																		<option value="2" >Venter på tilbakemelding</option>
																		<option value="3" >
																			<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
																		</option>
																	</select>
																</div>
																<div class="form-group">
																	<label>Velg verdi fra lister</label>
																	<select name="option_value" class="custom-select">
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
																<div class="form-group">
																	<label>Beskrivelse av sak</label>
																	<textarea name="case_descr" class="form-control">
																		<xsl:value-of select="comment"/>
																	</textarea>
																</div>
																<xsl:if test="include_counter_measure = 1">
																	<div class="form-group">
																		<label>
																			<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																		</label>
																		<textarea name="proposed_counter_measure" class="form-control">
																			<xsl:value-of select="proposed_counter_measure"/>
																		</textarea>
																	</div>
																</xsl:if>
																<xsl:call-template name="regulation_reference" >
																	<xsl:with-param name="control_item_id">
																		<xsl:value-of select="$control_item_id" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_regulation_reference">
																		<xsl:value-of select="$lang_regulation_reference" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_new_value">
																		<xsl:value-of select="$lang_new_value" />
																	</xsl:with-param>
																</xsl:call-template>
																<xsl:variable name="lang_save">
																	<xsl:value-of select="php:function('lang', 'register_error')" />
																</xsl:variable>
																<input type="submit" class="btn btn-primary btn-lg mr-3" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

															</xsl:when>
															<xsl:when test="type = 'control_item_type_5'">

																<input name="type" type="hidden" value="control_item_type_5" />
																<input name="status" type="hidden" value="1" />

																<div class="form-group">
																	<label class="pure-checkbox">Velg verdi fra lister</label>
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
																<div class="form-group">
																	<label>
																		<xsl:value-of select="php:function('lang', 'comment')" />
																	</label>
																	<textarea name="case_descr" class="form-control">
																		<xsl:value-of select="comment"/>
																	</textarea>
																</div>
																<xsl:if test="include_counter_measure = 1">
																	<div class="form-group">
																		<label>
																			<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
																		</label>
																		<textarea name="proposed_counter_measure" class="form-control">
																			<xsl:value-of select="proposed_counter_measure"/>
																		</textarea>
																	</div>
																</xsl:if>
																<xsl:call-template name="regulation_reference" >
																	<xsl:with-param name="control_item_id">
																		<xsl:value-of select="$control_item_id" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_regulation_reference">
																		<xsl:value-of select="$lang_regulation_reference" />
																	</xsl:with-param>
																	<xsl:with-param name="lang_new_value">
																		<xsl:value-of select="$lang_new_value" />
																	</xsl:with-param>
																</xsl:call-template>
																<xsl:variable name="lang_save">
																	<xsl:value-of select="php:function('lang', 'register_error')" />
																</xsl:variable>
																<input type="submit" class="btn btn-primary btn-lg mr-3" name="save_control" value="Lagre sak/måling" title="{$lang_save}" />

															</xsl:when>
														</xsl:choose>
														<xsl:variable name="lang_reset_form">
															<xsl:value-of select="php:function('lang', 'reset form')" />
														</xsl:variable>
														<input type="button" name="reset_form" id="reset_form" value="{$lang_reset_form}" title="{lang_reset_form}" class="btn btn-primary btn-lg" onclick="resetForm(form);"/>
													</form>
													<div class="add_picture_to_case" style="display:none">
														<form class="add_picture_to_case_form" ENCTYPE="multipart/form-data" method="post">
															<xsl:attribute name="action">
																<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.add_case_image, phpgw_return_as:json')" />
															</xsl:attribute>

															<div class="form-group">
																<label>
																	<xsl:value-of select="php:function('lang', 'picture')" />
																</label>
																<div class="pure-custom" name="picture_container"/>
															</div>
															<div id="new_picture" class="form-group">
																<div class="input-group">
																	<div class="custom-file">
																		<input type="file" name="file" class="custom-file-input" aria-describedby="submit_update_component">
																			<xsl:attribute name="accept">image/*</xsl:attribute>
																			<xsl:attribute name="capture">camera</xsl:attribute>
																		</input>
																		<label class="custom-file-label" for = "case_picture_file">
																			<xsl:value-of select="php:function('lang', 'new picture')" />
																		</label>
																	</div>
																</div>
																<!--																<button id = "submit_update_component" type="submit" class="btn btn-primary btn-lg mr-3 mt-3">
																	<xsl:value-of select="php:function('lang', 'add picture')" />
																</button>-->
															</div>
														</form>
													</div>
												</fieldset>
											</xsl:for-each>
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

							</div>
						</div>

						<!-- Modal footer -->

						<xsl:choose>
							<xsl:when test="component_children/child::node() and count(component_children) &gt; 0">
								<form method="post" id="set_completed_item">
									<xsl:attribute name="action">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.set_completed_item')" />
									</xsl:attribute>
									<input type="hidden" name ="check_list_id" value="{check_list/id}"></input>
									<input type="hidden" name ="item_string" id="item_string" value=""></input>
									<input type="hidden" name="location_code" >
										<xsl:attribute name="value" >
											<xsl:value-of select="//case_location_code"/>
										</xsl:attribute>
									</input>
									<div class="modal-footer">
										<button type="submit" class="btn btn-success ml-5 mr-3">Ferdig (delskjema)</button>
									</div>
								</form>
							</xsl:when>
						</xsl:choose>
					</div>
				</div>
			</div>
			<!-- MODAL INSPECT EQIPMENT END -->


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

<xsl:template name="regulation_reference" xmlns:php="http://php.net/xsl">
	<xsl:param name="control_item_id" />
	<xsl:param name="lang_regulation_reference" />
	<xsl:param name="lang_new_value" />

	<xsl:if test="include_regulation_reference = 1">
		<div class="form-group">
			<label>
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'regulation reference')"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'regulation reference')"/>
			</label>
			<select id="regulation_reference" name="regulation_reference" class="custom-select">
				<xsl:attribute name="title">
					<xsl:value-of select="php:function('lang', 'select value')"/>
				</xsl:attribute>
				<xsl:for-each select="regulation_reference_options_array">
					<option>
						<xsl:attribute name="value">
							<xsl:value-of select="option_value"/>
						</xsl:attribute>
						<xsl:value-of select="option_value"/>
					</option>
				</xsl:for-each>
			</select>
		</div>
		<div class="form-group">
			<label><xsl:text> </xsl:text></label>
			<a id="add_reference_option" href="#" onClick="addNewValueToRegulationReference('{$control_item_id}', '{$lang_regulation_reference}', '{$lang_new_value}');">
				<img src="{//add_img}" width="23"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="php:function('lang', 'add')"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="php:function('lang', 'regulation reference')"/>
			</a>
		</div>
		<div class="form-group">
			<label><xsl:text> </xsl:text></label>
			<i class="far fa-2x fa-trash-alt"></i>
			<a id="delete_reference_option" href="#" onClick="deleteValueFromRegulationReference('{$control_item_id}');">
				<xsl:text> </xsl:text>
				<xsl:value-of select="php:function('lang', 'delete')"/>
				<xsl:text> </xsl:text>
				<xsl:value-of select="php:function('lang', 'regulation reference')"/>
			</a>
		</div>
	<br/>
	</xsl:if>
</xsl:template>