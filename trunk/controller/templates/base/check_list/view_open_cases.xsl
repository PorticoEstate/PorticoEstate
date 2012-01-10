<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="error_message_menu">
	<a class="btn" id="register_case">					
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicheck_list.register_case</xsl:text>
			<xsl:text>&amp;check_list_id=</xsl:text>
			<xsl:value-of select="check_list/id"/>
			<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
		</xsl:attribute>
		Registrer sak/måling
	</a>
	<a class="btn">
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
			<xsl:text>&amp;check_list_id=</xsl:text>
			<xsl:value-of select="check_list/id"/>
		</xsl:attribute>
		Registrer avviksmelding
	</a>
</div>
	
<div id="view_errors">
	
	
	<xsl:include href="cases_tab_menu.xsl" />
	
	<div class="tab_menu">
		<a class="active" id="view_open_cases">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_open_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis åpne saker
		</a>
		<a id="view_closed_cases">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_closed_cases</xsl:text>
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
			<xsl:when test="open_check_items_and_cases/child::node()">
				
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
		
			<ul class="check_items">
				<xsl:for-each select="open_check_items_and_cases">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_cases">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
						 		<ul>		
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li><xsl:number/>.  <input type="checkbox"  name="case_ids[]" value="{$cases_id}" /><xsl:value-of select="descr"/>
											<div><xsl:value-of select="location_item_id"/></div>
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
				Ingen registrerte åpne avvik
			</xsl:otherwise>
		</xsl:choose>
	</div>
</div>
</xsl:template>
