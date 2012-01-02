<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
		
	<script>
		$(function() {
			$( "#planned_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy' 
			});
			$( "#completed_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy' 
			});
			$( "#deadline_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy' 
			});
			
			$(".tab_menu a").click(function(){
				var thisTabA = $(this);
				var thisTabMenu = $(this).parent(".tab_menu");
								
				var showId = $(thisTabA).attr("href");
				var hideId = $(".tab_menu a.active").attr("href");
								
				$(".tab_menu a").removeClass("active");
				$(".tab_item").removeClass("active");
				$(thisTabA).addClass('active');
								
				$(hideId).hide();
				$(showId).fadeIn('10', function(){
					$(showId).addClass('active');
					
				});
			
				return false;
			});
						
			$("#reg_errors").click(function(){
				var thisA = $(this);
				var showId = $(thisA).attr("href");
				var hideId = "#view_errors";
									
				$(hideId).hide();
				$(showId).fadeIn('10');
				$(thisA).fadeOut('10');

				return false;
			});
			
		});
	</script>
		
		<h1>Endre sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>
		
		<fieldset class="check_list_details">
		
		<form id="frm_update_check_list" action="index.php?menuaction=controller.uicheck_list.update_check_list" method="post">
				
			<xsl:variable name="check_list_id"><xsl:value-of select="open_check_list_with_check_items/id"/></xsl:variable>
			<input type="hidden" name="check_list_id" value="{$check_list_id}" />
				
			<div>
				<label>ID</label>
				<input>
			     <xsl:attribute name="name">check_list_id</xsl:attribute>
			     <xsl:attribute name="value"><xsl:value-of select="open_check_list_with_check_items/id"/></xsl:attribute>
			    </input>
		    </div>
			<div>
				<label>Status</label>
				<xsl:variable name="status"><xsl:value-of select="open_check_list_with_check_items/status"/></xsl:variable>
				<select name="status">
					<xsl:choose>
						<xsl:when test="open_check_list_with_check_items/status = 0">
							<option value="0" SELECTED="SELECTED">Ikke utført</option>
							<option value="1" >Utført</option>
						</xsl:when>
						<xsl:when test="open_check_list_with_check_items/status = 1">
							<option value="0">Ikke utført</option>
							<option value="1" SELECTED="SELECTED">Utført</option>
						</xsl:when>
					</xsl:choose>
				</select>
			</div>
			<div>
				<label>Skal utføres innen</label>
				<input>
			      <xsl:attribute name="id">deadline_date</xsl:attribute>
			      <xsl:attribute name="name">deadline_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="open_check_list_with_check_items/deadline != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(open_check_list_with_check_items/deadline))"/></xsl:attribute>
				  </xsl:if>
			    </input>
			</div>
			<div>
				<label>Planlagt dato</label>
				<input>
			      <xsl:attribute name="id">planned_date</xsl:attribute>
			      <xsl:attribute name="name">planned_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="open_check_list_with_check_items/planned_date != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(open_check_list_with_check_items/planned_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    <div>
				<label>Utført dato</label>
				<input>
			      <xsl:attribute name="id">completed_date</xsl:attribute>
			      <xsl:attribute name="name">completed_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
				  <xsl:if test="open_check_list_with_check_items/completed_date != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(open_check_list_with_check_items/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
			<div>
				<label class="comment">Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">comment</xsl:attribute>
				  <xsl:value-of select="open_check_list_with_check_items/comment"/>
				</textarea>
			</div>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input class="btn not_active" type="submit" name="save_control" value="{$lang_save}" title="{$lang_save}" />
			</div>
			</form>
		</fieldset>
		
		<div id="error_message_menu">
			<a class="btn">
				<xsl:attribute name="id">
					<xsl:text>reg_errors</xsl:text>
				</xsl:attribute>					
				<xsl:attribute name="href">
					<xsl:text>#register_errors</xsl:text>
				</xsl:attribute>
				Registrer avvik
			</a>
			<a class="btn">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uierror_report_message.create_error_report_message</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="open_check_list_with_check_items/id"/>
				</xsl:attribute>
				Send avviksmelding
			</a>
		</div>
		
		<div id="register_errors">
			<div class="tab_menu"><a class="active">Registrer avvik</a></div>
					
			<div class="tab_item active">
			<h2 class="check_item_details">Velg sjekkpunkter som skal registreres som avvik</h2>

			<xsl:choose>
				<xsl:when test="control_items_not_registered/child::node()">
				
					<ul id="control_items_list" class="check_items expand_list">
						<xsl:for-each select="control_items_not_registered">
							<li>
			    				<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="title"/></span></h4>						
								<form class="frm_save_control_item" action="index.php?menuaction=controller.uicheck_list.add_check_item_to_list" method="post">
									<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
									<input>
								      <xsl:attribute name="name">check_list_id</xsl:attribute>
								      <xsl:attribute name="type">hidden</xsl:attribute>
								      <xsl:attribute name="value">
								      	<xsl:value-of select="//open_check_list_with_check_items/id"/>
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
								         <label class="comment">Påkrevd</label>
								           <input>
										      <xsl:attribute name="name">required</xsl:attribute>
										      <xsl:attribute name="type">checkbox</xsl:attribute>
										      <xsl:attribute name="value">
										      	<xsl:value-of select="required"/>
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
								         <label style="vertical-align:top">Hva skal gjøres</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div>
								         <label style="vertical-align:top">Utførelsesbeskrivelse</label>
								         <textarea><xsl:value-of select="control_item/what_to_do"/></textarea>
								       </div>
								       <div class="form-buttons">
											<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'register_error')" /></xsl:variable>
											<input type="submit" name="save_control" value="{$lang_save}" class="not_active" title="{$lang_save}" />
										</div>
									</div>
								</form>
						    </li>
						</xsl:for-each>
					</ul>			
					</xsl:when>
					<xsl:otherwise>
						Ingen sjekkpunkter
					</xsl:otherwise>
			</xsl:choose>
		</div>
		</div>
		
		<div id="view_errors">
		
		<div class="tab_menu">
			<a class="active" href="#view_open_errors">Vis åpne avvik</a>
			<a href="#view_handled_errors">Vis håndterte avvik</a>
			<a href="#view_measurements">Vis målinger</a>
		</div>	
		
		<div id="view_open_errors" class="tab_item active">
			<xsl:choose>
				<xsl:when test="open_check_list_with_check_items/check_item_array/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
			
					<ul id="check_list_not_fixed_list" class="check_items expand_list">
						<xsl:for-each select="open_check_list_with_check_items/check_item_array">
								<li>
								<xsl:if test="status = 0">
									<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
									<form class="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item" method="post">
										<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
										<input type="hidden" name="check_item_id" value="{$check_item_id}" /> 
										<div class="check_item">
										  <div>
										       <label>Status</label>
										       <select name="status">
										       		<xsl:choose>
										       			<xsl:when test="status = 0">
										       				<option value="0" SELECTED="SELECTED">Avvik er åpent</option>
										       				<option value="1">Avvik er håndtert</option>
										       			</xsl:when>
										       			<xsl:when test="status = 1">
										       				<option value="0">Avvik er åpent</option>
										       				<option value="1" SELECTED="SELECTED">Avvik er håndtert</option>
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
						Ingen registrerte åpne avvik
					</xsl:otherwise>
			</xsl:choose>
		</div>
		
		<div id="view_handled_errors" class="tab_item"> 
			<xsl:choose>
				<xsl:when test="handled_check_list_with_check_items/check_item_array/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
					
					<ul id="check_list_fixed_list" class="check_items expand_list">
						<xsl:for-each select="handled_check_list_with_check_items/check_item_array">
								<xsl:if test="status = 1">
								<li>
				    				<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
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
						Ingen registrerte håndterte avvik
					</xsl:otherwise>
			</xsl:choose>
		</div>
		
		<div id="view_measurements" class="tab_item">
			<xsl:choose>
				<xsl:when test="measurement_check_items/check_item_array/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
			
					<ul id="check_list_not_fixed_list" class="check_items expand_list">
						<xsl:for-each select="handled_check_list_with_check_items/check_item_array">
								<li>
								<xsl:if test="status = 0">
									<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
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
						Ingen registrerte målinger
					</xsl:otherwise>
			</xsl:choose>
		</div>
		
	</div>
</div>
</xsl:template>
