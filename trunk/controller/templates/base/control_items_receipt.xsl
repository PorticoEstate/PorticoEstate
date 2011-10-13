<xsl:template name="control_items_receipt" xmlns:php="http://php.net/xsl">

<div class="yui-content">
	<div>
	
	  <!-- ===========================  SHOW CONTROL ITEMS RECEIPT   =============================== -->

		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" id="control_id" name="control_id" value="{control_id}" />
		
		<ul>
			<xsl:for-each select="control_receipt_items">
			<form action="" class="frm_save_order">
				<ul class="itemlist control_items">
		    		<li>
			         	<h3><xsl:value-of select="control_group/group_name"/></h3>
			         	
			         	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
						<input type="hidden" name="control_group_id" value="{$control_group_id}" />		
				
			         	<ul id="list">		
							<xsl:for-each select="control_items">
								<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
														
								<xsl:variable name="order_tag">
									<xsl:choose>
										<xsl:when test="order_nr > 0">
											<xsl:value-of select="order_nr"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:number/>
										</xsl:otherwise>
									</xsl:choose>:<xsl:value-of select="id"/>
								</xsl:variable>
								
				     			<li class="list_item"><span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" /></li>
							</xsl:for-each>
						</ul>
						
					</li>
				</ul>      
				<div>
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_order')" /></xsl:variable>
					<input type="submit" id="save_order" name="save_order" value="{$lang_save}" title = "{$lang_save}" />
				</div>	
			</form>
			</xsl:for-each>
		</ul>					
	</div>
</div>
</xsl:template>