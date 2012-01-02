<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	
		<h1>Send avviksmelding</h1>
		
		<div id="error_report_message_details">
			<h3>Bygg: <xsl:value-of select="location_array/loc1_name"/></h3>
			<h3>Tittel på kontroll: <xsl:value-of select="control_array/title"/></h3>
			<xsl:choose>
				<xsl:when test="check_list/completed_date != 0">
					<h3>Kontroll ble utført dato: <xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></h3>
				</xsl:when>
				<xsl:otherwise>
					<h3>Kontroll ble utført dato:  Ikke registrert utført</h3>
				</xsl:otherwise>
			</xsl:choose>
		</div>
				
		<fieldset id="error_report_message_details">
			

			<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					
				<form class="frm_save_error_report_message" action="index.php?menuaction=controller.uierror_report_message.save_error_report_message" method="post">
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
						<label style="font-size: 15px;font-weight: bold;">Tittel på melding</label>
						<input name="message_title" type="text" />
					</div>
					
					<div>
						<label style="font-size: 15px;font-weight: bold;">Kategori</label>
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
						<input type="submit" name="save_control" value="{$lang_save}" title="{$lang_save}" />
					  </div>
				</form>			
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekkpunkter
				</xsl:otherwise>
			</xsl:choose>
		</fieldset>
		
		
			
</div>
</xsl:template>
