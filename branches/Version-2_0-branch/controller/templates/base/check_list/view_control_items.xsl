<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>	

<div id="control-items">
	<h2>Kontrollpunkter</h2>
	
	<ul class="groups">
		<xsl:for-each select="saved_groups_with_items_array">
			<li>
				<h3><xsl:value-of select="control_group/group_name"/></h3>
							
			  <xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
				<input type="hidden" name="control_group_id" value="{$control_group_id}" />
		
				<xsl:choose>
					<xsl:when test="control_items/child::node()">
					 	<ul class="control_items">
							<xsl:for-each select="control_items">
								
								<!-- Control Item Id -->
								<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
								
								<!-- Calculates order nr -->
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
								
								<!-- Prints the row -->								
					 			<li>
					 				<span>
					 					<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
					 				</span>
					 			</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
						<p class="no_items_msg">Ingen kontrollpunkt lagt til fra denne gruppen</p>
					</xsl:otherwise>
				</xsl:choose>
			</li>
		</xsl:for-each>
	</ul>
	
	<a class="btn print" target="_blank">
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicheck_list.print_check_list</xsl:text>
			<xsl:text>&amp;check_list_id=</xsl:text>
			<xsl:value-of select="check_list/id"/>
			<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			<xsl:value-of select="$session_url"/>
		</xsl:attribute>
		Skriv ut
	</a>
</div>
</xsl:template>
