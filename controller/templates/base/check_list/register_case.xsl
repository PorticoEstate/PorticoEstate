<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="main_content">
	<h1>Registrer avviksmelding</h1>
	
	<xsl:call-template name="check_list_tab_menu" />

	<div id="view_cases">

		<h3 class="box_header">Registrer sak/måling</h3>
		<div class="tab_item active">
		
		<xsl:choose>
			<xsl:when test="control_items_for_check_list/child::node()">
			
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
			
				<ul id="control_items_list" class="check_items expand_list">
					<xsl:for-each select="control_items_for_check_list">
						<li>
		    				<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="title"/></span></h4>	
							<xsl:choose>
								<xsl:when test="type = 'control_item_type_1'">
									<form id="frm_register_case" action="index.php?menuaction=controller.uicase.register_case&amp;phpgw_return_as=json" method="post">
										<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
										<input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
										<input name="check_list_id" type="hidden"><xsl:attribute name="value"><xsl:value-of select="//check_list/id"/></xsl:attribute></input>
									    <input name="status" type="hidden" value="0" />
										<input name="type" type="hidden" value="control_item_type_1" />
										    
										<div class="check_item">
									       <div>
										         <label class="comment">Beskrivelse av sak</label>
										         <textarea name="case_descr">
													<xsl:value-of select="comment"/>
												 </textarea>
										   </div>
									       <div class="form-buttons">
												<input type="submit" name="save_control" value="Registrer sak" class="not_active"  />
											</div>
										</div>
									</form>
								</xsl:when>
								<xsl:when test="type = 'control_item_type_2'">
								<form id="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item&amp;phpgw_return_as=json" method="post">
									<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
									<input name="status" type="hidden" value="1" />
									<input name="type" type="hidden" value="control_item_type_2" />
									<input name="check_list_id" type="hidden"><xsl:attribute name="value"><xsl:value-of select="//check_list/id"/></xsl:attribute></input>
							    	
									<div class="check_item">
								       <div>
								         <label class="comment">Registrer målingsverdi</label>
								           <input>
										      <xsl:attribute name="name">measurement</xsl:attribute>
										      <xsl:attribute name="type">text</xsl:attribute>
										      <xsl:attribute name="value">
										      	<xsl:value-of select="measurement"/>
										      </xsl:attribute>
										    </input>
								       </div>
								       <div>
									         <label class="comment">Beskrivelse av sak</label>
									         <textarea name="case_descr">
												<xsl:value-of select="comment"/>
											 </textarea>
									   </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'register_error')" /></xsl:variable>
											<input type="submit" name="save_control" value="Registrer måling" class="not_active" title="{$lang_save}" />
										</div>
									</div>
									</form>
								</xsl:when>
							</xsl:choose>														
					    </li>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Alle sjekkpunkter for kontroll er registert som åpent/håndtert avvik eller måling 
				</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</div>
</xsl:template>
