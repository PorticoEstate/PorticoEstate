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
		
		<h1>Sjekkliste</h1>
		
		<fieldset class="check_list_details">
		
			<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
			<input type="hidden" name="check_list_id" value="{$check_list_id}" />	
				
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
				
		<h2 class="check_item_details">Åpne sjekkpunkter</h2>
	
		<xsl:choose>
			<xsl:when test="check_list/check_item_array/child::node()">
				
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
		
				<ul class="check_items expand_list">
					<xsl:for-each select="check_list/check_item_array">
							<li>
							<xsl:if test="status = 0">
								<h4><img src="controller/images/arrow_right.png" width="14"/><xsl:number />. <span><xsl:value-of select="control_item/title"/></span></h4>						
								<form class="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item" method="post">
									<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="check_item_id" value="{$check_item_id}" /> 
									<div class="check_item">
									  <div>
									       <label>Status</label>
									       <select name="status">
									       		<xsl:choose>
									       			<xsl:when test="status = 0">
									       				<option value="0" SELECTED="SELECTED">Feil på sjekkpunkt</option>
									       				<option value="1">Feil fikset</option>
									       			</xsl:when>
									       			<xsl:when test="status = 1">
									       				<option value="0">Feil på sjekkpunkt</option>
									       				<option value="1" SELECTED="SELECTED">Feil fikset</option>
									       			</xsl:when>
									       		</xsl:choose>
										   </select>
								       </div>
								       <div>
								         <label class="comment">Kommentar</label>
								         <textarea name="comment">
											<xsl:value-of select="comment"/>
										 </textarea>
								       </div>
								       <div>
								         <label>Hva skal gjøres</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div>
								         <label>Utførelsesbeskrivelse</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
											<input type="submit" name="save_control" value="{$lang_save}" class="not_active" title="{$lang_save}" />
										</div>
									</div>
								</form>
							</xsl:if>
					    </li>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekkpunkter
				</xsl:otherwise>
		</xsl:choose>
		
		<h2 class="check_item_details">Sjekkpunkter som er fikset</h2>
	
		<xsl:choose>
			<xsl:when test="check_list/check_item_array/child::node()">
				
			<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
				
				<ul class="check_items expand_list">
					<xsl:for-each select="check_list/check_item_array">
							<xsl:if test="status = 1">
							<li>
			    				<h4><img src="controller/images/arrow_right.png" width="14"/><xsl:number/>. <span><xsl:value-of select="control_item/title"/></span></h4>						
								<form class="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item" method="post">
									<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="check_item_id" value="{$check_item_id}" /> 
									<div class="check_item">
									  <div>
									       <label>Status</label>
									       <select name="status">
									       		<xsl:choose>
									       			<xsl:when test="status = 0">
									       				<option value="0" SELECTED="SELECTED">Feil på sjekkpunkt</option>
									       				<option value="1">Feil fikset</option>
									       			</xsl:when>
									       			<xsl:when test="status = 1">
									       				<option value="0">Feil på sjekkpunkt</option>
									       				<option value="1" SELECTED="SELECTED">Feil fikset</option>
									       			</xsl:when>
									       		</xsl:choose>
										   </select>
								       </div>
								       <div>
								         <label class="comment">Kommentar</label>
								         <textarea name="comment">
											<xsl:value-of select="comment"/>
										 </textarea>
								       </div>
								       <div>
								         <label>Hva skal gjøres</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div>
								         <label>Utførelsesbeskrivelse</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
											<input type="submit" name="save_control" value="{$lang_save}" class="not_active" title="{$lang_save}" />
										</div>
									</div>
								</form>
						    </li>
					 	</xsl:if>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekkpunkter
				</xsl:otherwise>
		</xsl:choose>
		
		<h2 class="check_item_details">Kontrollpunkter</h2>
	
		<xsl:choose>
			<xsl:when test="control_items_list/child::node()">
		
				<ul class="check_items expand_list">
					<xsl:for-each select="control_items_list">
						<li>
		    				<h4><img src="controller/images/arrow_right.png" width="14"/><xsl:number/>. <span><xsl:value-of select="title"/></span></h4>						
							<form class="frm_save_control_item" action="index.php?menuaction=controller.uicheck_list.add_check_item_to_list" method="post">
								<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
								<input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
								<input>
							      <xsl:attribute name="name">check_list_id</xsl:attribute>
							      <xsl:attribute name="type">hidden</xsl:attribute>
							      <xsl:attribute name="value">
							      	<xsl:value-of select="//check_list/id"/>
							      </xsl:attribute>
							    </input>
							    <input>
							      <xsl:attribute name="name">status</xsl:attribute>
							      <xsl:attribute name="type">hidden</xsl:attribute>
							      <xsl:attribute name="value">
							      	<xsl:value-of select="0"/>
							      </xsl:attribute>
							    </input>
																
								<div class="check_item">
								  <div>
								       <label>Tittel</label>
								       <input>
									      <xsl:attribute name="name">control_item_title</xsl:attribute>
									      <xsl:attribute name="type">text</xsl:attribute>
									      <xsl:attribute name="value">
									      	<xsl:value-of select="title"/>
									      </xsl:attribute>
									    </input>
							       </div>
							        <div>
								         <label class="comment">Kommentar</label>
								         <textarea name="comment">
											<xsl:value-of select="comment"/>
										 </textarea>
								       </div>
							       <div>
							         <label class="comment">Påkrevd</label>
							           <input>
									      <xsl:attribute name="name">required</xsl:attribute>
									      <xsl:attribute name="type">text</xsl:attribute>
									      <xsl:attribute name="value">
									      	<xsl:value-of select="required"/>
									      </xsl:attribute>
									    </input>
							       </div>
							       <div>
							         <label>Hva skal gjøres</label>
							         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
							       </div>
							       <div>
							         <label>Utførelsesbeskrivelse</label>
							         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
							       </div>
							       <div class="form-buttons">
										<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
										<input type="submit" name="save_control" value="{$lang_save}" class="not_active" title="{$lang_save}" />
									</div>
								</div>
							</form>
					    </li>
					</xsl:for-each>
				</ul>			
				</xsl:when>
				<xsl:otherwise>
					Ingen kontrollpunkter
				</xsl:otherwise>
		</xsl:choose>
			
</div>
</xsl:template>
