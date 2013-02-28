<!-- $Id$ -->
<xsl:template name="control_items" xmlns:php="http://php.net/xsl">

<div class="yui-content tab_content">
	<div>
	   <!-- ===========================  CHOOSE CONTROL ITEMS  =============================== -->	
	   <h2>Velg dine kontrollpunkt</h2>
	   
	   <!-- ==== CHOOSE NONE/ALL ===== -->
	 	<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
	   	
		<xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicontrol.save_control_items')" /></xsl:variable>
		<form id="frm_control_items" action="{$action_url}" method="post">	
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			<input type="hidden" name="control_id" value="{$control_id}" />
			
			<xsl:variable name="control_group_ids"><xsl:value-of select="control_group_ids"/></xsl:variable>
			<input type="hidden" name="control_group_ids" value="{control_group_ids}" />		
			
			
				<xsl:for-each select="groups_with_control_items">
					<ul class="expand_list">
					<li>
					 	<xsl:choose>
						 	<xsl:when test="group_control_items/child::node()">
						 		<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_group/group_name"/></span></h4>
						 		<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
							 	<ul class="expand_item">		
									<xsl:for-each select="group_control_items">
										<xsl:variable name="control_item_id"><xsl:value-of select="control_item/id"/></xsl:variable>
										<xsl:choose>
											<xsl:when test="checked = 1">
												<li><span><xsl:number/>.</span><input type="checkbox"  checked="checked" id="ch_{$control_group_id}:{$control_item_id}" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="control_item/title"/></li>
											</xsl:when>
											<xsl:otherwise>
												<li><span><xsl:number/>.</span><input type="checkbox"  id="ch_{$control_group_id}:{$control_item_id}" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="control_item/title"/></li>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:for-each>
								</ul>
							</xsl:when>
						<xsl:otherwise>
							<div class="empty_list"><h4><xsl:value-of select="control_group/group_name"/></h4><span>(Ingen kontrollpunkt)</span></div>
						</xsl:otherwise>
						</xsl:choose>
					</li>
				</ul>
				</xsl:for-each>
	
			
			<div>
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control_items" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>					
	</div>
</div>
</xsl:template>
