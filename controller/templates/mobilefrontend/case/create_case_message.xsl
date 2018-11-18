<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">

	<div id="main_content" class="medium">

		<div id="check-list-heading">
			<div class="box-1">
				<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
				<xsl:choose>
					<xsl:when test="type = 'component'">
						<h2>
							<xsl:value-of select="component_array/xml_short_desc"/>
						</h2>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="location_level = 1">
								<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
							</xsl:when>
							<xsl:otherwise>
								<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
			</div>

			<!-- ==================  CHANGE STATUS FOR CHECKLIST  ===================== -->
			<div class="box-2 select-box">
				<xsl:variable name="action_url">
					<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicheck_list.update_status,phpgw_return_as:json')" />
				</xsl:variable>
				<form id="update-check-list-status" class="done" action="{$action_url}" method="post">
					<input type="hidden" name="check_list_id" value="{check_list/id}" />
					<xsl:choose>
						<xsl:when test="check_list/status = 0">
							<input id='update-check-list-status-value' type="hidden" name="status" value="1" />
							<input id="status_submit" type="submit" class="pure-button pure-button-primary bigmenubutton">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'set status: done')" />
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input id='update-check-list-status-value' type="hidden" name="status" value="0" />
							<input type="submit" class="pure-button pure-button-primary bigmenubutton">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'is_executed')" />
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
				</form>
			</div>
			<!-- ==================  CHECKLIST TAB MENU  ===================== -->

			<div class="pure-menu pure-menu-horizontal pure-menu-scrollable">
				<ul class="pure-menu-list">
					<xsl:call-template name="check_list_menu" />
					<xsl:choose>
						<xsl:when test="type = 'component'">
							<li class="pure-menu-item">

								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicomponent.index' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;month=</xsl:text>
										<xsl:value-of select="current_month_nr"/>
										<xsl:text>&amp;location_id=</xsl:text>
										<xsl:value-of select="component_array/location_id"/>
										<xsl:text>&amp;component_id=</xsl:text>
										<xsl:value-of select="component_array/id"/>
										<xsl:text>&amp;get_locations=</xsl:text>
										<xsl:value-of select="get_locations"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrollplan for komponent (år)
								</a>
							</li>
						</xsl:when>
						<xsl:otherwise>
							<li class="pure-menu-item">
								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_year' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;location_code=</xsl:text>
										<xsl:value-of select="location_array/location_code"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrollplan for bygg/eiendom (år)
								</a>
							</li>
							<li class="pure-menu-item">

								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_month' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;month=</xsl:text>
										<xsl:value-of select="current_month_nr"/>
										<xsl:text>&amp;location_code=</xsl:text>
										<xsl:value-of select="location_array/location_code"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrolplan for bygg/eiendom (måned)
								</a>
							</li>
						</xsl:otherwise>
					</xsl:choose>
				</ul>
			</div>



		</div>

		<!-- =======================  INFO ABOUT MESSAGE  ========================= -->
		<h3 class="box_header ext">Registrer melding</h3>
		<div id="caseMessage" class="box ext">
			<xsl:choose>
				<xsl:when test="check_items_and_cases/child::node()">

					<xsl:variable name="action_url">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.send_case_message')" />
					</xsl:variable>

					<form ENCTYPE="multipart/form-data" id="frmRegCaseMessage" action="{$action_url}" method="post" class="pure-form pure-form-stacked">
						<input>
							<xsl:attribute name="name">check_list_id</xsl:attribute>
							<xsl:attribute name="type">hidden</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:value-of select="check_list/id"/>
							</xsl:attribute>
						</input>
						<input>
							<xsl:attribute name="name">location_code</xsl:attribute>
							<xsl:attribute name="type">hidden</xsl:attribute>
							<xsl:attribute name="value">
								<xsl:choose>
									<xsl:when test="type = 'component'">
										<xsl:value-of select="component_array/location_code"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="location_array/location_code"/>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:attribute>
						</input>

						<!-- === TITLE === -->

						<label>Tittel på melding:</label>
						<input name="message_title" type="text" class="pure-input-1-2 required" required="required"/>

						<!-- === CATEGORY === -->
						<label>Kategori:</label>
						<select name="message_cat_id" class="pure-input-1-2 required" required="required">
							<option value="">Velg kategori</option>
							<xsl:for-each select="categories/cat_list">
								<xsl:variable name="cat_id">
									<xsl:value-of select="./cat_id"/>
								</xsl:variable>
								<option value="{$cat_id}">
									<xsl:value-of select="./name"/>
								</option>
							</xsl:for-each>
						</select>
						<!-- === UPLOAD FILE === -->
						<label>Filvedlegg:</label>
						<input type="file" id="file" name="file" >
							<xsl:attribute name="accept">image/*</xsl:attribute>
							<xsl:attribute name="capture">camera</xsl:attribute>
						</input>

						<h3>Velg hvilke saker meldingen gjelder</h3>
						<ul class="cases">
							<xsl:for-each select="check_items_and_cases">
								<xsl:choose>
									<xsl:when test="cases_array/child::node()">
										<li class="check_item">
											<h4>
												<span>
													<xsl:value-of select="control_item/control_group_name"/>
													<xsl:text>::</xsl:text>
													<xsl:value-of select="control_item/title"/>

												</span>
											</h4>
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
														<input type="checkbox"  name="case_ids[]" value="{$cases_id}" />
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
														<div class="row">
															<label>Beskrivelse:</label>
														</div>
														<div class="case_descr">
															<xsl:value-of select="descr"/>
														</div>
													</li>
												</xsl:for-each>
											</ul>
										</li>
									</xsl:when>
								</xsl:choose>
							</xsl:for-each>
						</ul>

						<div class="form-buttons">
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'save')" />
							</xsl:variable>
							<input class="pure-button pure-button-primary" type="submit" name="save_control" value="Send melding" title="{$lang_save}" />
						</div>
					</form>
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte saker eller det er blitt registrert en melding for alle registrerte saker
				</xsl:otherwise>
			</xsl:choose>
		</div>

	</div>
</xsl:template>
