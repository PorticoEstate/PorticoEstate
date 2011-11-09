<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
		
	<!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->
		
	<form id="frm_save_check_items" action="index.php?menuaction=controller.uicheck_list.save_check_items" method="post">
		<h1>Sjekkliste</h1>
		<div class="form-buttons-top">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
		</div>
		<fieldset class="check_list_details">
			<div>
				<label>Tittel</label>
				<input>
			     <xsl:attribute name="name">check_list_status</xsl:attribute>
			      <xsl:attribute name="value"><xsl:value-of select="check_list/status"/></xsl:attribute>
			    </input>
		    </div>
		    <div>
				<label>Skal utføres innen</label>
				<xsl:if test="check_list/deadline != ''">
					<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/><br/>
				</xsl:if>
				</div>
				<div>
				<label>Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">check_list_comment</xsl:attribute>
			      <xsl:value-of select="check_list/comment"/>
			    </textarea>
		    </div>
			
		</fieldset>
				
		<h2 class="check_item_details">Sjekkpunkter</h2>
		
		<fieldset class="check_item_details">
			<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
			<input type="hidden" name="check_list_id" value="{$check_list_id}" />	
			
			<xsl:for-each select="check_list/check_item_array">
				<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
				<input type="hidden" name="check_item_ids[]" value="{$check_item_id}" />		
			</xsl:for-each>
					       
			<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					<xsl:for-each select="check_list/check_item_array">
						<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
						
						<div class="check_item">
					       <h3 class="order_nr"><xsl:number/>. <xsl:value-of select="control_item/title"/></h3>
					       <div>
						       <label>Status</label>
						       <span>
							       <select name="status_{$check_item_id}">
								   		<option value="true">Utført</option>
										<option value="false">Ikke utført</option>
								   </select>
							   </span>
					       </div>
					       <div>
					         <label>Kommentar</label>
					         <span>
					         	<textarea name="comment_{$check_item_id}">
									<xsl:value-of select="comment"/>
								</textarea>
							 </span>
					       </div>
					       <div>
					         <label>Hva skal gjøres</label><span><xsl:value-of select="control_item/what_to_do"/></span>
					       </div>
					       <div>
					         <label>Utførelsesbeskrivelse</label><span><xsl:value-of select="control_item/what_to_do"/></span>
					       </div>
					    </div>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		  </fieldset>
		</form>		
</div>
</xsl:template>