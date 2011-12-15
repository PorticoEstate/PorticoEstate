<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
		
	<script>
		$(function() {
			$( "#planned_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'd/m-yy' 
			});
			$( "#completed_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'd/m-yy' 
			});
			$( "#deadline" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'd/m-yy' 
			});		
		});
	</script>
		
	<form id="frm_save_check_items" action="index.php?menuaction=controller.uicheck_list.save_check_items" method="post">
		<h1>Sjekkliste</h1>
		<div class="form-buttons-top">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
		</div>
		<fieldset class="check_list_details">
			<div>
				<label>ID</label>
				<input>
			     <xsl:attribute name="name">check_list_id</xsl:attribute>
			      <xsl:attribute name="value"><xsl:value-of select="check_list/id"/></xsl:attribute>
			    </input>
		    </div>
			<div>
				<label>Status</label>
				<input>
				 <xsl:attribute name="name">check_list_status</xsl:attribute>
				  <xsl:attribute name="value"><xsl:value-of select="check_list/status"/></xsl:attribute>
				</input>
			</div>
			<div>
				<label>Skal utføres innen</label>
				<input>
			      <xsl:attribute name="id">deadline</xsl:attribute>
			      <xsl:attribute name="name">deadline</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/deadline != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/></xsl:attribute>
				  </xsl:if>
			    </input>
			</div>
			<div>
				<label>Planlagt dato</label>
				<input>
			      <xsl:attribute name="id">planned_date</xsl:attribute>
			      <xsl:attribute name="name">planned_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/planned_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    <div>
				<label>Utført dato</label>
				<input>
			      <xsl:attribute name="id">completed_date</xsl:attribute>
			      <xsl:attribute name="name">completed_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
				  <xsl:if test="check_list/completed_date != ''">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
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
		
		
		<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
		<input type="hidden" name="check_list_id" value="{$check_list_id}" />	
		
		<xsl:for-each select="check_list/check_item_array">
			<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
			<input type="hidden" name="check_item_ids[]" value="{$check_item_id}" />		
		</xsl:for-each>
		
		<h4 class="expand_header"><div class="expand_all">Vis alle</div><div class="collapse_all">Skjul alle</div></h4>
		
	<ul class="check_items">
		<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					<xsl:for-each select="check_list/check_item_array">
						<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
			    			<li>
			    				<h4 class="itemlist expand_list"><xsl:number/>. <img src="controller/images/arrow_left.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
								<div class="check_item">
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
						    </li>
					</xsl:for-each>
				</xsl:when>
			</xsl:choose>
		</ul>
		<!-- 
		<ul class="check_items">
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
		 -->
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		
		</form>
		
		<!-- 
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
		   -->
		  
		
</div>
</xsl:template>
