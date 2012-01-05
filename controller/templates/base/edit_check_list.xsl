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
				var hideId = $(thisTabMenu).find("a.active").attr("href");
							
				$(thisTabMenu).find("a").removeClass("active");
				$(thisTabA).addClass('active');
												
				$(hideId).hide();
				$(hideId).removeClass("active")
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
				$(thisA).hide();
				$("a#view_errors_measurements").css("display", "block");
			
				return false;
			});
			
			$("#view_errors_measurements").click(function(){
				var thisA = $(this);
				var showId = $(thisA).attr("href");
				var hideId = "#register_errors";
									
				$(hideId).hide();
				$(showId).fadeIn('10');
				$(thisA).hide();
				$("a#reg_errors").css("display", "block");
			
				return false;
			});
			
		});
	</script>
		
		<h1>Sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>
		
		<div id="edit_check_list_menu" class="hor_menu">
			<a class="active" id="view_check_list" href="#view_check_list">Vis info om sjekkliste</a>
			<a id="view_control_details">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.view_control_info</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Vis info om kontroll
			</a>
		</div>
		
		
		<div class="tab_menu"><a class="active">Sjekklistedetaljer</a></div>
		
		<fieldset class="check_list_details">
			<form id="frm_update_check_list" action="index.php?menuaction=controller.uicheck_list.update_check_list" method="post">
				
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
				<xsl:variable name="status"><xsl:value-of select="check_list/status"/></xsl:variable>
				<select name="status">
					<xsl:choose>
						<xsl:when test="check_list/status = 0">
							<option value="0" SELECTED="SELECTED">Ikke utført</option>
							<option value="1" >Utført</option>
						</xsl:when>
						<xsl:when test="check_list/status = 1">
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
				  <xsl:attribute name="name">comment</xsl:attribute>
				  <xsl:value-of select="check_list/comment"/>
				</textarea>
			</div>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input style="width: 170px;" class="btn not_active" type="submit" name="save_control" value="Lagre detaljer" />
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
				Registrer avvik/måling
			</a>
			<a class="btn">
				<xsl:attribute name="id">
					<xsl:text>view_errors_measurements</xsl:text>
				</xsl:attribute>					
				<xsl:attribute name="href">
					<xsl:text>#view_errors</xsl:text>
				</xsl:attribute>
				Vis avvik/måling
			</a>
			<a class="btn">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uierror_report_message.create_error_report_message</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Registrer avviksmelding
			</a>
		</div>
		
		<div id="register_errors">
			<div class="tab_menu"><a class="active">Registrer sak/måling</a></div>
					
			<div class="tab_item active">
			
			<xsl:choose>
				<xsl:when test="control_items_not_registered/child::node()">
				
					<ul id="control_items_list" class="check_items expand_list">
						<xsl:for-each select="control_items_not_registered">
							<li>
			    				<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="title"/></span></h4>						
								<form class="frm_save_control_item" action="index.php?menuaction=controller.uicheck_list.add_check_item_to_list" method="post">
									<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
									<input type="hidden" name="control_item_id" value="{$control_item_id}" /> 
									<input name="check_list_id" type="hidden">
								      <xsl:attribute name="value">
								      	<xsl:value-of select="//check_list/id"/>
								      </xsl:attribute>
								    </input>
								    <input name="status" type="hidden" value="0" />
								      
								<xsl:choose>
									<xsl:when test="type = 'control_item_type_1'">
										<input name="type" type="hidden" value="control_item_type_1" />
									    
										<div class="check_item">
									       <div>
										         <label class="comment">Kommentar</label>
										         <textarea name="comment">
													<xsl:value-of select="comment"/>
												 </textarea>
										   </div>
									       <div class="form-buttons">
												<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'register_error')" /></xsl:variable>
												<input type="submit" name="save_control" value="{$lang_save}" class="not_active" title="{$lang_save}" />
											</div>
										</div>
									</xsl:when>
									<xsl:when test="type = 'control_item_type_2'">
										<input name="type" type="hidden" value="control_item_type_2" />
										<div class="check_item">
									         <div>
									         <label class="comment">Registrer målingsverdi</label>
									           <input>
											      <xsl:attribute name="name">measurement</xsl:attribute>
											      <xsl:attribute name="type">text</xsl:attribute>
											      <xsl:attribute name="value">
											      	<xsl:value-of select="measurement"/>
											      </xsl:attribute>
											    </input>
									       </div>
									       <div>
										         <label class="comment">Kommentar</label>
										         <textarea name="comment">
													<xsl:value-of select="comment"/>
												 </textarea>
										   </div>
									       <div class="form-buttons">
												<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'register_error')" /></xsl:variable>
												<input type="submit" name="save_control" value="Registrer avvik" class="not_active" title="{$lang_save}" />
											</div>
										</div>
									</xsl:when>
								</xsl:choose>														
									
								</form>
						    </li>
						</xsl:for-each>
					</ul>			
					</xsl:when>
					<xsl:otherwise>
						Alle sjekkpunkter for kontroll er registert som åpent/håndtert avvik eller måling 
					</xsl:otherwise>
			</xsl:choose>
		</div>
		</div>
		
		<div id="view_errors">
		
		<div class="tab_menu">
			<a class="active" href="#view_open_errors">Vis åpne saker</a>
			<a href="#view_handled_errors">Vis lukkede saker</a>
			<a href="#view_measurements">Vis målinger</a>
		</div>	
		
		<div id="view_open_errors" class="tab_item active">
			<xsl:choose>
				<xsl:when test="open_check_items/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
			
					<ul id="check_list_not_fixed_list" class="check_items expand_list">
						<xsl:for-each select="open_check_items">
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
									       <div class="form-buttons">
												<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
												<input style="width: 200px;" type="submit" name="save_control" value="Oppdater registrert avvik" class="not_active" title="{$lang_save}" />
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
				<xsl:when test="handled_check_items/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
					
					<ul id="check_list_fixed_list" class="check_items expand_list">
						<xsl:for-each select="handled_check_items">
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
										       				<option value="0" SELECTED="SELECTED">Avvik er åpent</option>
										       				<option value="1">Avvik er håndtert</option>
										       			</xsl:when>
										       			<xsl:when test="status = 1">
										       				<option value="0">Avvik er åpent</option>
										       				<option value="1" SELECTED="SELECTED">Avvik er lukket</option>
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
									       <div class="form-buttons">
												<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
												<input type="submit" name="save_control" value="Oppdater håndtert avvik" class="not_active" title="{$lang_save}" />
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
				<xsl:when test="measurement_check_items/child::node()">
					
				<div class="expand_menu"><div class="expand_all">Vis alle</div><div class="collapse_all focus">Skjul alle</div></div>
			
					<ul id="check_list_not_fixed_list" class="check_items expand_list">
						<xsl:for-each select="measurement_check_items">
								<li>
								<xsl:if test="status = 0">
									<h4><img src="controller/images/arrow_right.png" width="14"/><span><xsl:value-of select="control_item/title"/></span></h4>						
									<form class="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list.save_check_item" method="post">
										<xsl:variable name="check_item_id"><xsl:value-of select="id"/></xsl:variable>
										<input type="hidden" name="check_item_id" value="{$check_item_id}" />
										<input type="hidden" name="type" value="measurement" />
										 
										<div class="check_item">
										  <div>
										       <label>Målingsverdi</label>
										       <input>
											      <xsl:attribute name="name">measurement</xsl:attribute>
											      <xsl:attribute name="type">text</xsl:attribute>
											      <xsl:attribute name="value">
											      	<xsl:value-of select="measurement"/>
											      </xsl:attribute>
											    </input>
									       </div>
									       
									       <div>
									         <label class="comment">Kommentar</label>
									         <textarea name="comment">
												<xsl:value-of select="comment"/>
											 </textarea>
									       </div>
									       <div class="form-buttons">
												<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_item')" /></xsl:variable>
												<input type="submit" name="save_control" value="Oppdatert registert måling" class="not_active" title="{$lang_save}" />
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
