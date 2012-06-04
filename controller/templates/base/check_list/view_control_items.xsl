<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="data">

<ul>
	<xsl:for-each select="saved_groups_with_items_array">
		<li>
			<h3><xsl:value-of select="control_group/group_name"/></h3>
	
			   	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
				<input type="hidden" name="control_group_id" value="{$control_group_id}" />
		
			 	<ul>
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
														
			 			<li>
			 				<span>
			 					<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
			 				</span>
			 			</li>
					</xsl:for-each>
				</ul>
		</li>
	</xsl:for-each>
</ul>

<a id="print_control_items" class="btn" target="_blank">
	<xsl:attribute name="href">
		<xsl:text>index.php?menuaction=controller.uicheck_list.print_check_list</xsl:text>
		<xsl:text>&amp;check_list_id=</xsl:text>
		<xsl:value-of select="check_list/id"/>
		<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
	</xsl:attribute>
	Skriv ut
</a>

</xsl:template>
