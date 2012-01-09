<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="error_message_menu">
	<a class="btn" id="register_errors">					
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicheck_list.register_errors</xsl:text>
			<xsl:text>&amp;check_list_id=</xsl:text>
			<xsl:value-of select="check_list/id"/>
			<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
		</xsl:attribute>
		Registrer sak/måling
	</a>
	<a class="btn">
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicase.create_case</xsl:text>
			<xsl:text>&amp;check_list_id=</xsl:text>
			<xsl:value-of select="check_list/id"/>
		</xsl:attribute>
		Registrer avviksmelding
	</a>
</div>
	
<div id="view_errors">
	
	<div class="tab_menu">
		<a class="active" id="view_open_errors">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_open_errors</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis åpne saker
		</a>
		<a id="view_closed_errors">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_closed_errors</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis lukkede saker
		</a>
		<a id="view_measurements">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_measurements</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis målinger
		</a>
	</div>	
	
	<div class="tab_item active">
		<xsl:choose>
			<xsl:when test="open_check_items/child::node()">
				
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
		
				<ul id="check_list_not_fixed_list" class="check_items expand_list">
					<xsl:for-each select="open_check_items">
							<li>
							<xsl:if test="status = 0">
								<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
								<form id="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item" method="post">
									<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="check_item_id" value="{$check_item_id}" /> 
									<div class="check_item">
									  <div>
									       <label>Status</label>
									       <select name="status">
									       		<xsl:choose>
									       			<xsl:when test="status = 0">
									       				<option value="0" SELECTED="SELECTED">Avvik er åpent</option>
									       				<option value="1">Avvik er håndtert</option>
									       			</xsl:when>
									       			<xsl:when test="status = 1">
									       				<option value="0">Avvik er åpent</option>
									       				<option value="1" SELECTED="SELECTED">Avvik er håndtert</option>
									       			</xsl:when>
									       		</xsl:choose>
										   </select>
								       </div>
								       <div>
								         <label class="comment">Kommentar</label>
								         <textarea name="comment">
											<xsl:value-of select="comment"/>
										 </textarea>
								       </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
											<input style="width: 200px;" type="submit" name="save_control" value="Oppdater registrert avvik" class="not_active" title="{$lang_save}" />
										</div>
									</div>
								</form>
							</xsl:if>
					    </li>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte åpne avvik
				</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</xsl:template>
