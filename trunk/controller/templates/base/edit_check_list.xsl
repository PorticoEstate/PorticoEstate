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
			      <xsl:if test="check_list/deadline != 0">
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
			      <xsl:if test="check_list/planned_date != 0">
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
				  <xsl:if test="check_list/completed_date != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
			<div>
				<label class="comment">Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">check_list_comment</xsl:attribute>
				  <xsl:value-of select="check_list/comment"/>
				</textarea>
			</div>
			
		</fieldset>
				
		<h2 class="check_item_details">Sjekkpunkter</h2>
	
		<xsl:choose>
			<xsl:when test="check_list/check_item_array/child::node()">
		
			<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
			<input type="hidden" name="check_list_id" value="{$check_list_id}" />	
		
			<xsl:for-each select="check_list/check_item_array">
				<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
				<input type="hidden" name="check_item_ids[]" value="{$check_item_id}" />		
			</xsl:for-each>
		
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
		
				<ul class="check_items expand_list">
					<xsl:for-each select="check_list/check_item_array">
						<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
			    			<li>
			    				<h4><img src="controller/images/arrow_right.png" width="14"/><xsl:number/>. <span><xsl:value-of select="control_item/title"/></span></h4>						
								<div class="check_item">
							       <div>
								       <label>Status</label>
								       <select name="status_{$check_item_id}">
									   		<option value="true">Utført</option>
											<option value="false">Ikke utført</option>
									   </select>
							       </div>
							       <div>
							         <label class="comment">Kommentar</label>
							         <textarea name="comment_{$check_item_id}">
										<xsl:value-of select="comment"/>
									 </textarea>
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
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekkpunkter
				</xsl:otherwise>
			</xsl:choose>
		
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>
</div>
</xsl:template>
