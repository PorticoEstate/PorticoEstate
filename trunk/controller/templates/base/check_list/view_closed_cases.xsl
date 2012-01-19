<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data">
	
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
										<li><xsl:value-of select="descr"/>
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
				Ingen lukkede saker
			</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</xsl:template>