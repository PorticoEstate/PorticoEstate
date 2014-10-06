<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
<div id="main_content">
		
	  <!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->

		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>	
		<input type="hidden" id="control_id" name="control_id" value="{control_id}" />
		
		<fieldset>
			<label>Tittel</label><xsl:value-of select="control_as_array/title"/><br/>
			<label>Startdato</label><xsl:value-of select="control_as_array/start_date"/><br/>
			<label>Sluttdato</label><xsl:value-of select="control_as_array/end_date"/><br/>
			<label>Syklustype</label><xsl:value-of select="control_as_array/repeat_type"/><br/>
			<label>Syklusfrekvens</label><xsl:value-of select="control_as_array/repeat_interval"/><br/>
		</fieldset>
		
		<ul class="check_list">
			<xsl:for-each select="saved_groups_with_items_array">
				<li>
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
															
				 			<li>
				 				<span class="drag">
				 					<span class="order_nr"><xsl:number/></span>. <xsl:value-of select="title"/><input type="hidden" name="order_nr[]" value="{$order_tag}" />
				 				</span>
				 			</li>
						</xsl:for-each>
					</ul>
				</li>
			</xsl:for-each>
		</ul>
		<div>
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.save_check_list</xsl:text>
					<xsl:text>&amp;control_id=</xsl:text>
					<xsl:value-of select="control_as_array/id"/>
					<xsl:value-of select="$session_url"/>
				</xsl:attribute>
				Lag sjekkliste for kontroll
			</a>		
		</div>
</div>
</xsl:template>
