<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="view_cases">
	
	<xsl:call-template name="cases_tab_menu">
	 	<xsl:with-param name="active_tab">view_open_cases</xsl:with-param>
	</xsl:call-template>
	
	<div class="tab_item active">
	<h2>Målinger</h2>
	<xsl:choose>
			<xsl:when test="open_check_items_and_measurements/child::node()">
			
			<ul class="check_items">
				<xsl:for-each select="open_check_items_and_measurements">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_case">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
						 		<span style="display:none;" class="control_item_type"><xsl:value-of select="control_item/type" /></span>
						 		<ul>
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li>
										
										<!--  ==================== COL1: ORDERNR ===================== -->
										<div class="col_1">
											<span class="order_nr"><xsl:number /></span>.
										</div>
										
										<!--  ==================== COL2: CASE CONTENT ===================== -->
										<div class="col_2">
										<!--  === FORM: UPDATE CASE === -->
										<form style="display:none;" class="frm_update_case">
											<xsl:attribute name="action">
												<xsl:text>index.php?menuaction=controller.uicase.save_case</xsl:text>
												<xsl:text>&amp;case_id=</xsl:text>
												<xsl:value-of select="id"/>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="//check_list/id"/>
												<xsl:text>&amp;phpgw_return_as=json</xsl:text>
											</xsl:attribute>
																						
											<!--  STATUS -->
											<div class="row first">
												<label>Status:</label> 
												<select name="status">
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
											<!--  MEASUREMENT -->
											<div class="row">
												<label>Måleverdi:</label> 
												<input type="text" name="measurement">
												<xsl:attribute name="value"><xsl:value-of select="measurement"/></xsl:attribute>
												</input>
											</div>
											<!--  DESCRIPTION -->
											<label style="font-weight: bold;">Beskrivelse:</label>
											<div class="row"> 
												<textarea name="case_descr"><xsl:value-of select="descr"/></textarea>
											</div>
											<div>
												<input class='btn_m' type='submit' value='Oppdater' /><input class='btn_m cancel' type='button' value='Avbryt' />
											</div>
										</form>
										
										<!--  === CASE INFO === -->
										<div class="case_info">
											<!-- STATUS -->
											<div class="row first">
												<label style="font-weight:bold; margin-right: 5px;">Status:</label> 
												<xsl:choose>
													<xsl:when test="status = 0">Åpen</xsl:when>
													<xsl:when test="status = 1">Lukket</xsl:when>
													<xsl:when test="status = 2">Venter på tilbakemelding</xsl:when>
												</xsl:choose>
											</div>
											<!--  MEASUREMENT -->
											<div class="row">
												<label style="font-weight:bold; margin-right: 5px;">Måleverdi:</label> 
												<span class="measurement"><xsl:value-of select="measurement"/></span>
											</div>
											<!--  DESCRIPTION -->
											<div class="row">
												<label style="font-weight:bold">Beskrivelse:</label> 
											</div>
											<div class="case_descr"><xsl:value-of select="descr"/></div>
											
											<!-- === QUICK EDIT MENU === -->
											<div class="quick_menu">
													<a class="quick_edit" href="">
														endre
													</a>
													<a class="close_case">
														<xsl:attribute name="href">
															<xsl:text>index.php?menuaction=controller.uicase.close_case</xsl:text>
															<xsl:text>&amp;case_id=</xsl:text>
															<xsl:value-of select="id"/>
															<xsl:text>&amp;check_list_id=</xsl:text>
															<xsl:value-of select="//check_list/id"/>
															<xsl:text>&amp;phpgw_return_as=json</xsl:text>
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
																	<xsl:value-of select="//check_list/id"/>
																	<xsl:text>&amp;phpgw_return_as=json</xsl:text>
																</xsl:attribute>
																slett
															</a>
														</xsl:when>
													</xsl:choose>
												</div>
											</div>
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
					 		</li>
					 	</xsl:when>
				 	</xsl:choose>
				</xsl:for-each>
			</ul>
			
			</xsl:when>
			<xsl:otherwise>
				<p>Ingen åpne målinger</p>
			</xsl:otherwise>
		</xsl:choose>
		
		<h2 class="last">Saker</h2>
		<xsl:choose>
			<xsl:when test="open_check_items_and_cases/child::node()">
			<ul class="check_items">
				<xsl:for-each select="open_check_items_and_cases">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_case">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
						 		<ul>
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li>
										<div style="display: inline-block;padding: 2em 2%;vertical-align: top;">
											<span class="order_nr"><xsl:number /></span>.
										</div>
										<div style="border-left: 1px solid #DDDDDD;border-right: 1px solid #DDDDDD;display: inline-block;padding: 1em 2%;width: 73%;">
										
										<form style="display:none;" class="frm_update_case">
											<xsl:attribute name="action">
												<xsl:text>index.php?menuaction=controller.uicase.save_case</xsl:text>
												<xsl:text>&amp;case_id=</xsl:text>
												<xsl:value-of select="id"/>
												<xsl:text>&amp;check_list_id=</xsl:text>
												<xsl:value-of select="//check_list/id"/>
												<xsl:text>&amp;phpgw_return_as=json</xsl:text>
											</xsl:attribute>
										
											<textarea name="case_descr"><xsl:value-of select="descr"/></textarea>
											<div>
												<input class='btn_m' type='submit' value='Oppdater' /><input class='btn_m cancel' type='button' value='Avbryt' />
											</div>
										</form>
										<div class="case_info">
										<div class="case_descr"><xsl:value-of select="descr"/></div>
										<div class="quick_menu">
												<a class="quick_edit first" href="">
													endre
												</a>
												<a class="close_case">
													<xsl:attribute name="href">
														<xsl:text>index.php?menuaction=controller.uicase.close_case</xsl:text>
														<xsl:text>&amp;case_id=</xsl:text>
														<xsl:value-of select="id"/>
														<xsl:text>&amp;check_list_id=</xsl:text>
														<xsl:value-of select="//check_list/id"/>
														<xsl:text>&amp;phpgw_return_as=json</xsl:text>
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
																<xsl:value-of select="//check_list/id"/>
																<xsl:text>&amp;phpgw_return_as=json</xsl:text>
															</xsl:attribute>
															slett
														</a>
													</xsl:when>
												</xsl:choose>
											</div>
											</div>
											</div>
											<div style="display: inline-block;padding: 3%;vertical-align: top;">
												<xsl:choose>
													<xsl:when test="location_item_id > 0">
														<a target="_blank">
																<xsl:attribute name="href">
																	<xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
																	<xsl:text>&amp;id=</xsl:text>
																	<xsl:value-of select="location_item_id"/>
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
					 		</li>
					 	</xsl:when>
				 	</xsl:choose>
				</xsl:for-each>
			</ul>
			</xsl:when>
			<xsl:otherwise>
				<p>Ingen åpne saker</p>
			</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</xsl:template>
