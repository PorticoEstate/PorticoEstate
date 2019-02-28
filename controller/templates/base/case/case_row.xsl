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
					<xsl:value-of select="control_item/type" />
				</span>
				<ul>
					<xsl:for-each select="cases_array">
						<xsl:variable name="cases_id">
							<xsl:value-of select="id"/>
						</xsl:variable>
						<xsl:variable name="condition_degree">
							<xsl:value-of select="condition_degree"/>
						</xsl:variable>
						<xsl:variable name="consequence">
							<xsl:value-of select="consequence"/>
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

									<xsl:choose>
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
									</xsl:choose>

									<!-- STATUS -->

									<div class="row first">
										<label>Status:</label>
										<span class="case_status">
											<xsl:choose>
												<xsl:when test="status = 0">Åpen</xsl:when>
												<xsl:when test="status = 1">Lukket</xsl:when>
												<xsl:when test="status = 2">Venter på tilbakemelding</xsl:when>
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


									<xsl:if test="case_files/child::node()">
										<div class="row">
											<label>
												<xsl:value-of select="php:function('lang', 'files')"/>
												<xsl:text>:</xsl:text>
											</label>
										</div>
										<!-- Slideshow container -->
										<div class="slideshow-container">

											<xsl:variable name="file_count">
												<xsl:value-of select="count(case_files)" />
											</xsl:variable>

											<xsl:for-each select="case_files">

												<!-- Full-width images with number and caption text -->
												<div class="mySlides fade">
													<div class="numbertext">
														<xsl:number />	/ <xsl:value-of select="$file_count"/>
													</div>
													<img src="{$get_image_url}&amp;file_id={file_id}" style="width:100%"/>
													<div class="text">
														<xsl:value-of select="name"/>
													</div>
												</div>

											</xsl:for-each>
										</div>
										<br/>
									</xsl:if>

									<!-- === QUICK EDIT MENU === -->
									<div class="quick_menu">
										<a class="quick_edit_case first" href="#">
											endre
										</a>
										<a class="close_case">
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
										<xsl:choose>
											<xsl:when test="location_item_id = 0">
												<a class="delete_case">
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
										</xsl:choose>
									</div>
								</div>

								<!--  =================== UPDATE CASE FORM =================== -->
								<form class="frm_update_case">
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

									<!--  STATUS -->
									<div class="row first">
										<label>Status:</label>
										<select name="case_status">
											<xsl:choose>
												<xsl:when test="status = 0">
													<option value="0" SELECTED="SELECTED">Åpen</option>
													<option value="2">Venter på tilbakemelding</option>
												</xsl:when>
												<xsl:when test="status = 1">
													<option value="0">Åpen</option>
													<option value="2">Venter på tilbakemelding</option>
												</xsl:when>
												<xsl:when test="status = 2">
													<option value="0">Åpen</option>
													<option value="2" SELECTED="SELECTED">Venter på tilbakemelding</option>
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
										<select name="condition_degree">
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
										<select name="consequence">
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
													<select name="measurement">
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
														<input type="radio" name="measurement">
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
												<div class="row">
													<label class="comment">Velg verdi fra liste</label>
													<br/>
													<xsl:for-each select="../control_item/options_array">
														<input type="checkbox" name="measurement[]">
															<xsl:attribute name="value">
																<xsl:value-of select="option_value"/>
															</xsl:attribute>
														</input>
														<xsl:value-of select="option_value"/>
														<br/>

													</xsl:for-each>
												</div>
											</xsl:when>
										</xsl:choose>
									</xsl:if>

									<!--  DESCRIPTION -->
									<label>Beskrivelse:</label>
									<div class="row">
										<textarea name="case_descr">
											<xsl:value-of select="descr"/>
										</textarea>
									</div>
									<label>
										<xsl:value-of select="php:function('lang', 'proposed counter measure')"/>
										<xsl:text>:</xsl:text>
									</label>
									<div class="row">
										<textarea name="proposed_counter_measure">
											<xsl:value-of select="proposed_counter_measure"/>
										</textarea>
									</div>
									<div>
										<input class='btn_m' type='submit' value='Oppdater' />
										<input class='btn_m cancel' type='button' value='Avbryt' />
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
