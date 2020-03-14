<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template name="case_row" xmlns:php="http://php.net/xsl">

	<xsl:param name="control_item_type" />
	<xsl:param name="check_list_id" />
	<xsl:param name="date_format" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>
	<xsl:variable name="get_image_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.get_image,phpgw_return_as:json')" />
	</xsl:variable>
	<li class="check_item_case">
		<xsl:choose>
			<xsl:when test="cases_array/child::node()">
				<h4>
					<span>
						<xsl:value-of select="control_item/title"/>
					</span>
					<xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4' or $control_item_type = 'control_item_type_5'">
						<span style="margin-left:3px;">(Måling)</span>
					</xsl:if>
				</h4>
				<span class="control_item_type ext_info">
					<xsl:value-of select="php:function('lang',$control_item_type)" />
					<!--xsl:value-of select="control_item/type" /-->
				</span>
				<ul>
					<xsl:for-each select="cases_array">
						<xsl:variable name="case_id">
							<xsl:value-of select="id"/>
						</xsl:variable>
						<xsl:variable name="condition_degree">
							<xsl:value-of select="condition_degree"/>
						</xsl:variable>
						<xsl:variable name="consequence">
							<xsl:value-of select="consequence"/>
						</xsl:variable>
						<xsl:variable name="component_child_item_id">
							<xsl:value-of select="component_child_item_id"/>
						</xsl:variable>
						<li>
							<!--  ==================== COL1: ORDERNR ===================== -->
							<div class="col_1">
								<span class="order_nr">
									<xsl:number />
								</span>.
							</div>
							<!--  ==================== COL2: CASE CONTENT ===================== -->
							<div class="col_2">
								<div class="case_info">
									<div class="row">
										<label>
											<xsl:value-of select="php:function('lang','location')" />
										</label>
										<xsl:value-of select="location_code"/>
									</div>

									<xsl:choose>
										<xsl:when test="count(//component_children) > 1">
											<div class="row">
												<label>
													<xsl:value-of select="php:function('lang', 'equipment')" />
												</label>
												<span class="case_component_child">
													<xsl:for-each select="//component_children">
														<xsl:if test="$component_child_item_id = id">
															<xsl:value-of disable-output-escaping="yes" select="short_description"/>
														</xsl:if>
													</xsl:for-each>
												</span>
											</div>

										</xsl:when>
									</xsl:choose>
									<div class="row">
										<label>
											<xsl:value-of select="php:function('lang','date')" />
										</label>
										<xsl:value-of select="php:function('date', $date_format, number(entry_date))"/>
										<xsl:if test="$check_list_id != //check_list/id">
											<xsl:text> (</xsl:text>
											<xsl:value-of select="php:function('lang','other controll')" />
											<xsl:text>: </xsl:text>
											<xsl:value-of  select="$check_list_id"/>
											<xsl:text>)</xsl:text>
										</xsl:if>
									</div>

									<!--									<xsl:choose>
										<xsl:when test="component_descr != ''">
											<div class="row">
												<label>
													<xsl:value-of select="php:function('lang','component')" />
												</label>
											</div>
											<div class="component_descr">
												<xsl:value-of disable-output-escaping="yes" select="component_descr"/>
											</div>
										</xsl:when>
									</xsl:choose>-->

									<!-- STATUS -->

									<div class="row first">
										<label>Status:</label>
										<span class="case_status">
											<xsl:choose>
												<xsl:when test="status = 0">Åpen</xsl:when>
												<xsl:when test="status = 1">Lukket</xsl:when>
												<xsl:when test="status = 2">Venter på tilbakemelding</xsl:when>
												<xsl:when test="status = 3">
													<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
												</xsl:when>
											</xsl:choose>
										</span>
									</div>

									<div class="row">
										<label>Tilstandsgrad:</label>
										<span class="case_condition_degree">
											<xsl:for-each select="//degree_list/options">
												<xsl:if test="$condition_degree = id">
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</xsl:if>
											</xsl:for-each>
										</span>
									</div>
									<div class="row">
										<label>Konsekvens:</label>
										<span class="case_consequence">
											<xsl:for-each select="//consequence_list/options">
												<xsl:if test="$consequence = id">
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</xsl:if>
											</xsl:for-each>
										</span>
									</div>

									<xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4' or $control_item_type = 'control_item_type_5'">

										<!--  MEASUREMENT -->
										<div class="row">
											<label>Måleverdi:</label>
											<span class="measurement">
												<xsl:choose>
													<xsl:when test="$control_item_type = 'control_item_type_5'">
														<xsl:for-each select="measurement">
															<br/>
															<xsl:value-of disable-output-escaping="yes" select="node()"/>
														</xsl:for-each>
													</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="measurement"/>
													</xsl:otherwise>
												</xsl:choose>
											</span>
										</div>
									</xsl:if>

									<!--  DESCRIPTION -->
									<div class="row">
										<label>Beskrivelse:</label>
									</div>
									<div class="case_descr">
										<xsl:value-of select="descr"/>
									</div>

									<div class="row">
										<label>
											<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
											<xsl:text>:</xsl:text>
										</label>
									</div>
									<div class="proposed_counter_measure">
										<xsl:value-of select="proposed_counter_measure"/>
									</div>

									<div class="row">
										<label>Hjemmel:</label>
										<span class="regulation_reference">
											<xsl:value-of select="regulation_reference"/>
										</span>
									</div>

									<xsl:if test="case_files/child::node()">
										<div class="row">
											<label>
												<xsl:value-of select="php:function('lang', 'files')"/>
												<xsl:text>:</xsl:text>
											</label>
										</div>
										<!-- Slideshow container -->
										<div class="row">

											<xsl:variable name="file_count">
												<xsl:value-of select="count(case_files)" />
											</xsl:variable>

											<xsl:for-each select="case_files">

												<!-- Full-width images with number and caption text -->
												<div class="col-md-4">
													<div class="numbertext">
														<xsl:number />	/ <xsl:value-of select="$file_count"/>
													</div>
													<img src="{$get_image_url}&amp;file_id={file_id}" class="img-fluid"/>
													<div class="caption">
														<xsl:value-of select="name"/>
													</div>
												</div>

											</xsl:for-each>
										</div>
										<br/>
									</xsl:if>

									<!-- === QUICK EDIT MENU === -->
									<div class="quick_menu">
										<a class="quick_edit_case first btn btn-primary btn-lg mr-3" href="#">
											endre
										</a>
										<a class="close_case btn btn-primary btn-lg mr-3">
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=controller.uicase.close_case</xsl:text>
												<xsl:text>&amp;case_id=</xsl:text>
												<xsl:value-of select="id"/>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="$check_list_id"/>
												<xsl:text>&amp;phpgw_return_as=json</xsl:text>
												<xsl:value-of select="$session_url"/>
											</xsl:attribute>
											lukk
										</a>
										<!--										<xsl:choose>
											<xsl:when test="location_item_id = 0">
												<a class="delete_case btn btn-primary btn-lg mr-3">
													<xsl:attribute name="href">
														<xsl:text>index.php?menuaction=controller.uicase.delete_case</xsl:text>
														<xsl:text>&amp;case_id=</xsl:text>
														<xsl:value-of select="id"/>
														<xsl:text>&amp;check_list_id=</xsl:text>
														<xsl:value-of select="$check_list_id"/>
														<xsl:text>&amp;phpgw_return_as=json</xsl:text>
														<xsl:value-of select="$session_url"/>
													</xsl:attribute>
													slett
												</a>
											</xsl:when>
										</xsl:choose>-->
									</div>
								</div>

								<!--  =================== UPDATE CASE FORM =================== -->
								<form class="pure-form pure-form-stacked frm_update_case">
									<xsl:attribute name="action">
										<xsl:text>index.php?menuaction=controller.uicase.save_case</xsl:text>
										<xsl:text>&amp;case_id=</xsl:text>
										<xsl:value-of select="id"/>
										<xsl:text>&amp;check_list_id=</xsl:text>
										<xsl:value-of select="$check_list_id"/>
										<xsl:text>&amp;control_item_type=</xsl:text>
										<xsl:value-of select="//control_item/type" />
										<xsl:text>&amp;phpgw_return_as=json</xsl:text>
										<xsl:value-of select="$session_url"/>
									</xsl:attribute>
									<input type="hidden" name="control_item_type">
										<xsl:attribute name="value">
											<xsl:value-of select="//control_item/type" />
										</xsl:attribute>
									</input>
									<xsl:choose>
										<xsl:when test="count(//component_children) > 1">
											<div class="row first">
												<label>
													<xsl:value-of select="php:function('lang', 'equipment')" />
												</label>
												<select name="component_child" class="pure-input-1">
													<xsl:for-each select="//component_children">
														<option>
															<xsl:if test="$component_child_item_id = id">
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
										</xsl:when>
									</xsl:choose>

									<!--  STATUS -->
									<div class="row first">
										<label>Status:</label>
										<select name="case_status" class="pure-input-1">
											<xsl:choose>
												<xsl:when test="status = 0">
													<option value="0" SELECTED="SELECTED">Åpen</option>
													<option value="1" >Lukket</option>
													<option value="2">Venter på tilbakemelding</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
													</option>
												</xsl:when>
												<xsl:when test="status = 1">
													<option value="0">Åpen</option>
													<option value="1" SELECTED="SELECTED">Lukket</option>
													<option value="2">Venter på tilbakemelding</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
													</option>
												</xsl:when>
												<xsl:when test="status = 2">
													<option value="0">Åpen</option>
													<option value="1" >Lukket</option>
													<option value="2" SELECTED="SELECTED">Venter på tilbakemelding</option>
													<option value="3">
														<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
													</option>
												</xsl:when>
												<xsl:when test="status = 3">
													<option value="0">Åpen</option>
													<option value="1" >Lukket</option>
													<option value="2">Venter på tilbakemelding</option>
													<option value="3" SELECTED="SELECTED">
														<xsl:value-of select="php:function('lang', 'corrected on controll')"/>
													</option>
												</xsl:when>
											</xsl:choose>
										</select>
									</div>
									<div class="row first">
										<label>
											<xsl:attribute name="title">
												<xsl:text>Tilstandsgrad iht NS 3424</xsl:text>
											</xsl:attribute>
											<xsl:value-of select="php:function('lang', 'condition degree')"/>
										</label>
										<select name="condition_degree" class="pure-input-1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'select value')"/>
											</xsl:attribute>
											<xsl:apply-templates select="//degree_list/options">
												<xsl:with-param name="selected">
													<xsl:value-of select="condition_degree"/>
												</xsl:with-param>
											</xsl:apply-templates>
										</select>
									</div>
									<div class="row first">
										<label>
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'consequence')"/>
											</xsl:attribute>
											<xsl:value-of select="php:function('lang', 'consequence')"/>
										</label>
										<select name="consequence" class="pure-input-1">
											<xsl:attribute name="title">
												<xsl:value-of select="php:function('lang', 'select value')"/>
											</xsl:attribute>
											<xsl:apply-templates select="//consequence_list/options">
												<xsl:with-param name="selected">
													<xsl:value-of select="consequence"/>
												</xsl:with-param>
											</xsl:apply-templates>
										</select>
									</div>
									<xsl:if test="$control_item_type = 'control_item_type_2' or $control_item_type = 'control_item_type_3' or $control_item_type = 'control_item_type_4' or $control_item_type = 'control_item_type_5'">
										<xsl:choose>
											<xsl:when test="$control_item_type = 'control_item_type_2'">
												<!--  MEASUREMENT -->
												<div class="row">
													<label>Måleverdi:</label>
													<input type="text" name="measurement">
														<xsl:attribute name="value">
															<xsl:value-of select="measurement"/>
														</xsl:attribute>
													</input>
												</div>
											</xsl:when>
											<xsl:when test="$control_item_type = 'control_item_type_3'">
												<!--  MEASUREMENT -->
												<div class="row">
													<label class="comment">Velg verdi fra liste</label>
													<select name="measurement" class="pure-input-1">
														<xsl:for-each select="../control_item/options_array">
															<option>
																<xsl:attribute name="value">
																	<xsl:value-of select="option_value"/>
																</xsl:attribute>
																<xsl:value-of select="option_value"/>
															</option>
														</xsl:for-each>
													</select>
												</div>
											</xsl:when>
											<xsl:when test="$control_item_type = 'control_item_type_4'">
												<!--  MEASUREMENT -->
												<div class="row">
													<label class="comment">Velg verdi fra liste</label>
													<xsl:for-each select="../control_item/options_array">
														<input type="radio" name="measurement" value="female">
															<xsl:attribute name="value">
																<xsl:value-of select="option_value"/>
															</xsl:attribute>
														</input>
														<xsl:value-of select="option_value"/>
													</xsl:for-each>
												</div>
											</xsl:when>
											<xsl:when test="$control_item_type = 'control_item_type_5'">
												<!--  MEASUREMENT -->
												<div class="row pure-form pure-form-aligned">
													<div class="pure-control-group">

														<label class="comment">Velg verdi fra liste</label>
													</div>
													<div class="pure-control-group">

														<xsl:for-each select="../control_item/options_array">
															<label class="pure-checkbox">
																<input type="checkbox" name="measurement[]">
																	<xsl:attribute name="value">
																		<xsl:value-of select="option_value"/>
																	</xsl:attribute>
																</input>
																<xsl:value-of select="option_value"/>
															</label>
														</xsl:for-each>
													</div>
												</div>
											</xsl:when>

										</xsl:choose>
									</xsl:if>

									<!--  DESCRIPTION -->
									<label>Beskrivelse:</label>
									<div class="row">
										<textarea name="case_descr" class="pure-input-1">
											<xsl:value-of select="descr"/>
										</textarea>
									</div>
									<label>
										<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
										<xsl:text>:</xsl:text>
									</label>
									<div class="row">
										<textarea name="proposed_counter_measure" class="pure-input-1">
											<xsl:value-of select="proposed_counter_measure"/>
										</textarea>
									</div>
									<xsl:if test="../control_item/include_regulation_reference = 1">
										<div class="form-group">
											<label>
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'regulation reference')"/>
												</xsl:attribute>
												<xsl:value-of select="php:function('lang', 'regulation reference')"/>
											</label>
											<select name="regulation_reference" class="pure-input-1">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'select value')"/>
												</xsl:attribute>
												<xsl:for-each select="../control_item/regulation_reference_options_array">
													<option>
														<xsl:attribute name="value">
															<xsl:value-of select="option_value"/>
														</xsl:attribute>
														<xsl:value-of select="option_value"/>
													</option>
												</xsl:for-each>
											</select>
										</div>
									</xsl:if>

									<div>
										<input class='btn_m first btn btn-primary btn-lg mr-3' type='submit' value='Oppdater' />
										<input class='btn_m cancel first btn btn-primary btn-lg mr-3' type='button' value='Avbryt' />
									</div>
								</form>
							</div>
							<!--  ==================== COL3: MESSAGE LINK ===================== -->
							<div class="col_3">
								<xsl:choose>
									<xsl:when test="location_item_id > 0">
										<a target="_blank">
											<xsl:attribute name="href">
												<xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
												<xsl:text>&amp;id=</xsl:text>
												<xsl:value-of select="location_item_id"/>
												<xsl:value-of select="$session_url"/>
											</xsl:attribute>
											Vis melding
										</a>
									</xsl:when>
									<xsl:otherwise>
										<span class="message">Ingen melding</span>
									</xsl:otherwise>
								</xsl:choose>
							</div>
						</li>
					</xsl:for-each>
				</ul>
			</xsl:when>
		</xsl:choose>
	</li>
</xsl:template>

<xsl:template match="options">
	<xsl:param name="selected" />
	<option value="{id}">
		<xsl:if test="$selected = id">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
