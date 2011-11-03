<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">

<div class="main_content">
		
	  <!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->

		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" id="control_id" name="control_id" value="{control_id}" />
		
		<ul class="groups">
			<xsl:for-each select="saved_groups_with_items_array">
				<li class="list_item">
			        <h3><span class="group_order_nr"><xsl:number/></span>. <xsl:value-of select="control_group/group_name"/></h3>
			
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
																
				     			<li class="list_item">
				     				<span class="drag">
				     					<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
				     				</span>
				     			</li>
							</xsl:for-each>
						</ul>
					
				</li>
			</xsl:for-each>
		</ul>					
</div>
</xsl:template>