<xsl:template name="control_items" xmlns:php="http://php.net/xsl">

<div class="yui-content tab_content">
	<div>
	   <!-- ===========================  CHOOSE CONTROL ITEMS  =============================== -->	
	   <h2>Velg dine kontrollpunkt</h2>
	   
	   <!-- ==== CHOOSE NONE/ALL ===== -->
	   	<h4 class="expand_header"><div class="expand_all">Vis alle</div><div class="collapse_all">Skjul alle</div></h4>
	   	
		<form id="frm_control_items" action="index.php?menuaction=controller.uicontrol.save_control_items" method="post">	
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			<input type="hidden" name="control_id" value="{$control_id}" />
			
			<xsl:variable name="control_group_ids"><xsl:value-of select="control_group_ids"/></xsl:variable>
			<input type="hidden" name="control_group_ids" value="{control_group_ids}" />		
			
			<ul class="control_items">
				<xsl:for-each select="groups_with_control_items">
					<ul class="itemlist expand_list">
		    		<li>
			         	<xsl:choose>
				         	<xsl:when test="group_control_items/child::node()">
				         		<h4><img src="controller/images/arrow_left.png" width="14"/><span><xsl:value-of select="control_group/group_name"/></span></h4>
				         		<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
					         	<ul>		
									<xsl:for-each select="group_control_items">
										<xsl:variable name="control_item_id"><xsl:value-of select="control_item/id"/></xsl:variable>
										<xsl:choose>
											<xsl:when test="checked = 1">
												<li><xsl:number/>.  <input type="checkbox"  checked="checked" id="ch_{$control_group_id}:{$control_item_id}" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="control_item/title"/></li>
											</xsl:when>
											<xsl:otherwise>
												<li><xsl:number/>.  <input type="checkbox"  id="ch_{$control_group_id}:{$control_item_id}" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="control_item/title"/></li>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:for-each>
								</ul>
							</xsl:when>
						<xsl:otherwise>
							<div class="empty_list"><span><xsl:value-of select="control_group/group_name"/></span></div>
							<div>Ingen kontrollpunkt</div>
						</xsl:otherwise>
						</xsl:choose>
					</li>
				</ul>
				</xsl:for-each>
			</ul>
			
			<div>
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control_items" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>					
	</div>
</div>
</xsl:template>