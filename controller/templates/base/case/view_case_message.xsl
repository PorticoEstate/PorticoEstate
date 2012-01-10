<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	
		<h1>Avviksmelding registrert</h1>
		
		
		<h3 class="box_header" href="#">Meldingen gjaldt</h3>
		<div id="case_details">
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
		
		<h3 class="box_header" href="#">Detaljer for melding</h3>
		<div id="case_details">
			<div>	    
				<label>Tittel på melding</label>
				<xsl:value-of select="message_ticket/subject"/>
			</div>
			
			<div>
				<label>Kategori</label>
				<span><xsl:value-of select="category"/></span> 
			</div>
	
	
			<h3 class="check_item_details">Avviksmeldingen omfattet følgende saker</h3>					
			<ul class="check_items">
				<xsl:for-each select="check_items_and_cases">
					<xsl:choose>
					 	<xsl:when test="cases_array/child::node()">
					 		<li class="check_item_cases">
						 		<h4><span><xsl:value-of select="control_item/title"/></span></h4>
						 		<ul>		
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id"><xsl:value-of select="id"/></xsl:variable>
										<li><xsl:value-of select="descr"/></li>
									</xsl:for-each>
								</ul>
					 		</li>
					 	</xsl:when>
				 	</xsl:choose>
				</xsl:for-each>
			</ul>
		</div>
		
		<a class="btn">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
	      Registrer ny melding
	    </a>
		<a class="btn">
	    	<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
	      Endre sjekkliste
	    </a>
</div>
</xsl:template>
