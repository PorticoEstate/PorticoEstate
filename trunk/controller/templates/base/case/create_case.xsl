<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	
		<h1>Registrer avviksmelding</h1>
		
		<div class="tab_menu">
			<a class="active" href="#">Meldingen gjelder</a>
		</div>	
		<div id="case_details">
			<h3 class="first">Tittel på kontroll: <xsl:value-of select="control_array/title"/></h3>
			<xsl:choose>
				<xsl:when test="check_list/completed_date != 0">
					<h3>Kontroll ble utført dato:<xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></h3>
				</xsl:when>
				<xsl:otherwise>
					<h3>Kontroll ble utført dato: Ikke registrert utført</h3>
				</xsl:otherwise>
			</xsl:choose>
			<h3 class="last">Bygg: <xsl:value-of select="location_array/loc1_name"/></h3>
		</div>
		
		<div class="tab_menu">
			<a class="active" href="#">Detaljer for meldingen</a>
		</div>
		<fieldset id="case_details">
			<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					
				<form class="frm_save_case" action="index.php?menuaction=controller.uicase.save_case" method="post">
					<input>
				      <xsl:attribute name="name">check_list_id</xsl:attribute>
				      <xsl:attribute name="type">hidden</xsl:attribute>
				      <xsl:attribute name="value">
				      	<xsl:value-of select="check_list/id"/>
				      </xsl:attribute>
				    </input>
				    <input>
				      <xsl:attribute name="name">location_code</xsl:attribute>
				      <xsl:attribute name="type">hidden</xsl:attribute>
				      <xsl:attribute name="value">
				      	<xsl:value-of select="location_array/location_code"/>
				      </xsl:attribute>
				    </input>
				    
				    <div>
						<label>Tittel på melding</label>
						<input name="message_title" type="text" />
					</div>
					
					<div>
						<label>Kategori</label>
						 <select name="message_cat_id">
						 	<option value="0">Velg kategori</option>
							<xsl:for-each select="categories/cat_list">
								<xsl:variable name="cat_id"><xsl:value-of select="./cat_id"/></xsl:variable>
								<option value="{$cat_id}">
									<xsl:value-of select="./name"/>
								</option>			
							</xsl:for-each>
						</select>
					</div>
			
					<h3 class="check_item_details">Velg sjekkpunkter som skal være med i avviksmelding</h3>					
					<ul class="check_items">
						<xsl:for-each select="check_list/check_item_array">
							<li>
								<xsl:variable name="check_item_id"><xsl:value-of select="id" /></xsl:variable>
								<h5><input type="checkbox" name="check_item_ids[]" value="{$check_item_id}" /><span><xsl:value-of select="control_item/title"/></span></h5>						
							</li>
						</xsl:for-each>
					</ul>
					
					  <div class="form-buttons">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input class="btn" type="submit" name="save_control" value="Registrer avviksmelding" title="{$lang_save}" />
					  </div>
				</form>			
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte avvik
				</xsl:otherwise>
			</xsl:choose>
		</fieldset>
		
		<a class="btn">
	    	<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
	      Vis sjekkliste
	    </a>
			
</div>
</xsl:template>
