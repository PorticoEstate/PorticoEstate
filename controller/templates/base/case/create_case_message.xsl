<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content" class="medium">
	
		<h1>Utførelse av kontroll: <xsl:value-of select="control/title"/></h1>
		<xsl:choose>
			<xsl:when test="type = 'component'">
				<h2><xsl:value-of select="component_array/xml_short_desc"/></h2>
			</xsl:when>
			<xsl:otherwise>
				<h2>Bygg: <xsl:value-of select="location_array/loc1_name"/></h2>
			</xsl:otherwise>
		</xsl:choose>
		
		<xsl:call-template name="check_list_tab_menu" />
	
		<!-- =======================  INFO ABOUT MESSAGE  ========================= -->
		<h3 class="box_header ext">Registrer melding</h3>
		<div id="caseMessage" class="box ext">
			<xsl:choose>
				<xsl:when test="check_items_and_cases/child::node()">
				
				<form ENCTYPE="multipart/form-data" id="frmRegCaseMessage" action="index.php?menuaction=controller.uicase.register_case_message" method="post">
					<input>
						<xsl:attribute name="name">check_list_id</xsl:attribute>
					    <xsl:attribute name="type">hidden</xsl:attribute>
					    <xsl:attribute name="value">
					    	<xsl:value-of select="check_list/id"/>
					    </xsl:attribute>
					</input>
					
					<!-- === TITLE === -->
				    <div class="row">
						<label>Tittel på melding:</label>
						<input name="message_title" type="text" class="required" />
					</div>
									
					<!-- ==================  BYGG  ===================== -->
					<div class="row">
						<xsl:choose>
							<xsl:when test="buildings_array/child::node()">
								<label>Bygg:</label>
								<select id="building_id" name="building_id" class="required">
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
								<label>Bygg:</label> <xsl:value-of select="building/loc1_name"/>	
							</xsl:otherwise>
						</xsl:choose>
					</div>
					
					<!-- === CATEGORY === -->
					<div class="row">
						<label>Kategori:</label>
						 <select name="message_cat_id" class="required">
						 	<option value="0">Velg kategori</option>
							<xsl:for-each select="categories/cat_list">
								<xsl:variable name="cat_id"><xsl:value-of select="./cat_id"/></xsl:variable>
								<option value="{$cat_id}">
									<xsl:value-of select="./name"/>
								</option>			
							</xsl:for-each>
						</select>
					</div>
					<!-- === UPLOAD FILE === -->
					<div class="row">
						<label>Filvedlegg:</label>
						<input type="file" id="file" name="file" />
					</div>
			
					<h3>Velg hvilke saker meldingen gjelder</h3>					
					<ul class="cases">
						<xsl:for-each select="check_items_and_cases">
							<xsl:choose>
							 	<xsl:when test="cases_array/child::node()">
							 		<li class="check_item">
								 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
								 		<ul>		
											<xsl:for-each select="cases_array">
												<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
												<li><input type="checkbox"  name="case_ids[]" value="{$cases_id}" /><xsl:value-of select="descr"/></li>
											</xsl:for-each>
										</ul>
							 		</li>
							 	</xsl:when>
						 	</xsl:choose>
						</xsl:for-each>
					</ul>
					
					<div class="form-buttons">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input class="btn" type="submit" name="save_control" value="Send melding" title="{$lang_save}" />
					</div>
				</form>			
				</xsl:when>
				<xsl:otherwise>
					Ingen registrerte saker eller det er blitt registrert en melding for alle registrerte saker
				</xsl:otherwise>
			</xsl:choose>
		</div>
			
</div>
</xsl:template>
