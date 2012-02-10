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
						 		<ul>
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li><span class="order_nr"><xsl:number /></span>. <xsl:value-of select="descr"/>
											Status: 
											<xsl:choose>
												<xsl:when test="status = 1">Utført</xsl:when>
												<xsl:when test="status = 2">Venter på tilbakemelding</xsl:when>
											</xsl:choose>
											
											<xsl:choose>
												<xsl:when test="location_item_id > 0">
													<div style="float:right;">
														<a target="_blank">
															<xsl:attribute name="href">
																<xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
																<xsl:text>&amp;id=</xsl:text>
																<xsl:value-of select="location_item_id"/>
															</xsl:attribute>
															Vis melding
														</a>
													</div>
												</xsl:when>
												<xsl:otherwise>
													<div style="float:right;"><span style="color:red">Ingen melding registrert</span></div>
												</xsl:otherwise>
											</xsl:choose>
											
											<div class="quick_menu">
												<a class="quick_edit">
													<xsl:attribute name="href">
														<xsl:text>index.php?menuaction=controller.uicase.save_case</xsl:text>
														<xsl:text>&amp;case_id=</xsl:text>
														<xsl:value-of select="id"/>
														<xsl:text>&amp;check_list_id=</xsl:text>
														<xsl:value-of select="//check_list/id"/>
														<xsl:text>&amp;phpgw_return_as=json</xsl:text>
													</xsl:attribute>
													endre
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
											<div style="display:none;" class="case_info">
												<div class="case_id"><xsl:value-of select="id"/></div>
												<div class="case_descr"><xsl:value-of select="descr"/></div>
												<div class="case_status"><xsl:value-of select="status"/></div>
												<div class="case_measurement"><xsl:value-of select="measurement"/></div>
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
		
		<h2>Saker</h2>
		<xsl:choose>
			<xsl:when test="open_check_items_and_cases/child::node()">
			<h2>Saker</h2>
			<ul class="check_items">
				<xsl:for-each select="open_check_items_and_cases">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_case">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
						 		<ul>
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li><span class="order_nr"><xsl:number /></span>. <xsl:value-of select="descr"/>
											<xsl:choose>
												<xsl:when test="location_item_id > 0">
													<div style="float:right;">
														<a target="_blank">
															<xsl:attribute name="href">
																<xsl:text>index.php?menuaction=property.uitts.view</xsl:text>
																<xsl:text>&amp;id=</xsl:text>
																<xsl:value-of select="location_item_id"/>
															</xsl:attribute>
															Vis melding
														</a>
													</div>
												</xsl:when>
												<xsl:otherwise>
													<div style="float:right;"><span style="color:red">Ingen melding registrert</span></div>
												</xsl:otherwise>
											</xsl:choose>
											<div class="quick_menu">
												<a>
													<xsl:attribute name="href">
														<xsl:text>index.php?menuaction=controller.uicase.edit_case</xsl:text>
														<xsl:text>&amp;case_id=</xsl:text>
														<xsl:value-of select="id"/>
														<xsl:text>&amp;check_list_id=</xsl:text>
														<xsl:value-of select="//check_list/id"/>
														<xsl:text>&amp;phpgw_return_as=json</xsl:text>
													</xsl:attribute>
													endre
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
		<a style="font-size: 11px;margin-top: 20px;padding: 3px 20px;" class="btn focus">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.register_case</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			Registrer melding
		</a>
	</div>
</div>
</xsl:template>
