<xsl:template name="control_items" xmlns:php="http://php.net/xsl">

<div class="yui-content">
	<div>
		  
	   <!-- ===========================  CHOOSE CONTROL ITEMS  =============================== -->
	   <h2>Velg dine kontrollpunkt</h2>
	   
	   	<h4 class="expand_header"><div class="expand_all">Vis alle</div><div class="collapse_all">Skjul alle</div></h4>
		<form id="frm_control_items" action="#" method="post">	
		
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" name="control_id" value="{control_id}" />		
		
		<ul class="control_items">
			<xsl:for-each select="groups_with_control_items">
				<ul class="proplist-col expand_list">
	    		<li>
		         	<h4><img src="controller/images/arrow_left.png" width="14"/><span><xsl:value-of select="control_group/group_name"/></span></h4>
		         	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
		         	<ul>		
						<xsl:for-each select="group_control_items">
							<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
							
			     			<li><xsl:number/>.  <input type="checkbox"  id="ch_{$control_group_id}:{$control_item_id}" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="title"/></li>	
						</xsl:for-each>
					</ul>
				</li>
			</ul>
			</xsl:for-each>
		</ul>
		
		<div class="form-buttons">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control_items" value="{$lang_save}" title = "{$lang_save}" />
		</div>
		</form>
								
	</div>
</div>
</xsl:template>