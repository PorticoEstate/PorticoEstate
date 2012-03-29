<!-- $Id$ -->
<xsl:template name="sort_check_list" xmlns:php="http://php.net/xsl">

<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>

<div class="yui-content tab_content">
		
	  <!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->
		<form action="#" id="frmSaveOrder">
			<input type="hidden" id="control_id" name="control_id" value="{$control_id}" /> 
			<ul class="groups">
				<xsl:for-each select="saved_groups_with_items_array">
				<xsl:choose>
					<xsl:when test="control_items/child::node()">
						<li class="drag_group list_item">
							<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
							<input type="hidden" name="control_group_id" value="{$control_group_id}" />
							
							<h3><a href="#"><span class="group_order_nr"><xsl:number/></span>. <xsl:value-of select="control_group/group_name"/><input type="hidden" name="group_id" value="{$control_group_id}" /></a></h3>				
							<ul class="items">
								<xsl:for-each select="control_items">
									<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
																								
									<li class="drag_item list_item">
										<a href="#"><span class="item_order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="item_id" value="{$control_item_id}" /></a>
									</li>
								</xsl:for-each>
							</ul>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<li class="drag_group list_item">
							<h3><span class="group_order_nr"><xsl:number/></span>. <xsl:value-of select="control_group/group_name"/></h3>
							<div>Ingen kontrollpunkt for denne gruppen</div>
						</li>
					</xsl:otherwise>
				</xsl:choose>
				</xsl:for-each>
			</ul>
			<input type="submit" value="Lagre rekkefølge" />
		</form>
</div>
</xsl:template>
