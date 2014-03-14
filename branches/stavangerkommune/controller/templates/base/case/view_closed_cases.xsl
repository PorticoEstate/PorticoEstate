<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>	

<div id="main_content" class="medium">
 <xsl:call-template name="check_list_top_section" />

	<xsl:choose>
		<xsl:when test="buildings_on_property/child::node()">
  			<div id="choose-building-wrp">
				<xsl:call-template name="select_buildings_on_property" />
			</div>
		</xsl:when>  
  </xsl:choose>

 
<div id="view_cases">
	<xsl:call-template name="cases_tab_menu">
	 	<xsl:with-param name="active_tab">view_closed_cases</xsl:with-param>
	</xsl:call-template>	
	
	<div class="tab_item active">
		<xsl:choose>
			<xsl:when test="closed_check_items_and_cases/child::node()">
			<ul class="check_items">
				<xsl:for-each select="closed_check_items_and_cases">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_case">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
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
										
											<!--  =============== SHOW CASE INFO ============= -->
											<div class="case_info">
																								
												 <xsl:choose>
													  <xsl:when test="component_descr != ''">
														  <div class="row">
															<label>
																<xsl:value-of select="php:function('lang','component')" />
															</label> 
														  </div>
														   <div class="component_descr">
															<xsl:value-of select="component_descr"/>
														  </div>
													 </xsl:when>
												</xsl:choose>

											  <!--  DESCRIPTION -->
											  <div class="row">
												<label>Beskrivelse:</label> 
											  </div>

												<!--  DESCRIPTION -->
												<div class="case_descr"><xsl:value-of select="descr"/></div>
												<!-- === QUICK EDIT MENU === -->
												<div class="quick_menu">
													<a class="open_case">
														<xsl:attribute name="href">
															<xsl:text>index.php?menuaction=controller.uicase.open_case</xsl:text>
															<xsl:text>&amp;case_id=</xsl:text>
															<xsl:value-of select="id"/>
															<xsl:text>&amp;check_list_id=</xsl:text>
															<xsl:value-of select="//check_list/id"/>
															<xsl:text>&amp;phpgw_return_as=json</xsl:text>
															<xsl:value-of select="$session_url"/>
														</xsl:attribute>
														åpne
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
																	<xsl:value-of select="$session_url"/>
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
					 		</li>
					 	</xsl:when>
				 	</xsl:choose>
				</xsl:for-each>
			</ul>
			</xsl:when>
			<xsl:otherwise>
				<p>Ingen lukkede saker</p>
			</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</div>
</xsl:template>
