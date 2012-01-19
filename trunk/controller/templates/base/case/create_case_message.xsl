<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	
		<h1>Registrer avviksmelding</h1>
	
		<xsl:call-template name="check_list_tab_menu" />
	
		<h3 class="box_header">Meldingen gjelder</h3>
		<div id="case_details">
			<h3 class="first">Tittel på kontroll: <xsl:value-of select="control/title"/></h3>
			<xsl:choose>
				<xsl:when test="check_list/completed_date != 0">
					<h3>Kontroll ble utført dato:<xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></h3>
				</xsl:when>
				<xsl:otherwise>
					<h3>Kontroll ble utført dato: Ikke registrert utført</h3>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="buildings_array/child::node()">
					<select id="building_id" name="building_id">
							<option value="0">
								Velg bygning
							</option>
							<xsl:for-each select="buildings_array">
								<option value="{id}">
									<xsl:value-of disable-output-escaping="yes" select="name"/>
								</option>
							</xsl:for-each>
						</select>
				</xsl:when>
				<xsl:otherwise>
					<h3 class="last">Bygg: <xsl:value-of select="building/loc1_name"/></h3>	
				</xsl:otherwise>
			</xsl:choose>
		</div>
		
		<h3 class="box_header">Detaljer for meldingen</h3>
		<fieldset id="case_details">
			<xsl:choose>
				<xsl:when test="check_items_and_cases/child::node()">
					
				<form class="frm_save_case" action="index.php?menuaction=controller.uicase.register_case_message" method="post">
					<input>
				      <xsl:attribute name="name">check_list_id</xsl:attribute>
				      <xsl:attribute name="type">hidden</xsl:attribute>
				      <xsl:attribute name="value">
				      	<xsl:value-of select="check_list/id"/>
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
					
					<div>
						<label>Last opp filvedlegg til meldingen:</label>
						<input type="file" id="file" name="file" />
					</div>
			
					<h3 class="check_item_details">Velg sjekkpunkter som skal være med i avviksmelding</h3>					
					<ul class="check_items">
						<xsl:for-each select="check_items_and_cases">
							<xsl:choose>
							 	<xsl:when test="cases_array/child::node()">
							 		<li class="check_item_case">
								 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
								 		<ul>		
											<xsl:for-each select="cases_array">
												<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
												<li style="list-style:none;"><input type="checkbox"  name="case_ids[]" value="{$cases_id}" /><xsl:value-of select="descr"/></li>
											</xsl:for-each>
										</ul>
							 		</li>
							 	</xsl:when>
						 	</xsl:choose>
						</xsl:for-each>
					</ul>
					
					<div class="form-buttons">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input class="btn focus" type="submit" name="save_control" value="Registrer melding" title="{$lang_save}" />
					</div>
				</form>			
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte avvik
				</xsl:otherwise>
			</xsl:choose>
		</fieldset>
			
</div>
</xsl:template>
