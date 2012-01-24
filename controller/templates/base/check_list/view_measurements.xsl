<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data">
	
<div id="view_cases">
	
	<xsl:call-template name="cases_tab_menu">
	 	<xsl:with-param name="active_tab">view_measurements</xsl:with-param>
	</xsl:call-template>
	
	<div class="tab_item">
		<xsl:choose>
			<xsl:when test="measurement_check_items/child::node()">
				
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
		
				<ul id="check_list_not_fixed_list" class="check_items expand_list">
					<xsl:for-each select="measurement_check_items">
							<li>
								<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
								<form id="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list_for_location.save_check_item" method="post">
									<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="check_item_id" value="{$check_item_id}" />
									<input type="hidden" name="type" value="control_item_type_2" />
									 
									<div class="check_item">
									   <div>
										<label>Status</label>
										<select name="status">
											<xsl:choose>
												<xsl:when test="status = 0">
													<option value="0" SELECTED="SELECTED">Ikke utført</option>
													<option value="1" >Utført</option>
												</xsl:when>
												<xsl:when test="status = 1">
													<option value="0">Ikke utført</option>
													<option value="1" SELECTED="SELECTED">Utført</option>
												</xsl:when>
											</xsl:choose>
										</select>
									  </div>
									  <div>
									       <label>Målingsverdi</label>
									       <input>
										      <xsl:attribute name="name">measurement</xsl:attribute>
										      <xsl:attribute name="type">text</xsl:attribute>
										      <xsl:attribute name="value">
										        <xsl:if test="measurement > 0">
										        	<xsl:value-of select="measurement"/>
										        </xsl:if>
										      </xsl:attribute>
										    </input>
								       </div>
								       <div>
								         <label class="comment">Kommentar</label>
								         <textarea name="comment">
											<xsl:value-of select="comment"/>
										 </textarea>
								       </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
											<input type="submit" name="save_control" value="Oppdater måling" class="not_active" title="{$lang_save}" />
										</div>
									</div>
								</form>
						</li>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte målinger
				</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</xsl:template>
