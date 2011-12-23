<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
	
		<h1>Send avviksmelding på bygg: <xsl:value-of select="location_array/loc1_name"/></h1>
		
		<h2>Kontroll <xsl:value-of select="control_array/title"/></h2>
		<h2>Utført dato <xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></h2>
				
		<div class="tab_menu">
			<div class="active ext"><a href="#control_items_list">1: Velg sjekkpunkter</a></div>
			<div><a href="#check_list_not_fixed_list">2: Detaljer for avviksmelding</a></div>
			<div><a href="#check_list_fixed_list">3: Kvittering for avviksmelding</a></div>
		</div>
		
		<div id="control_items_list" class="tab_item active">
			<h2 class="check_item_details">Velg sjekkpunkter som skal være med i avviksmelding</h2>

			<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					
				<form class="frm_save_check_item" action="index.php?menuaction=controller.uicheck_list_for_location.register_error_report_message" method="post">
					<ul class="check_items expand_list">
						<xsl:for-each select="check_list/check_item_array">
							<li>
								<xsl:variable name="check_item_id"><xsl:value-of select="id" /></xsl:variable>
								<h4><input type="checkbox" name="check_item_id[]" value="{$check_item_id}" /><xsl:number />. <span><xsl:value-of select="control_item/title"/></span></h4>						
							</li>
						</xsl:for-each>
					</ul>
					
					  <div class="form-buttons">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input type="button" name="save_control" value="{$lang_save}" title="{$lang_save}" />
					  </div>
					</form>			
					</xsl:when>
					<xsl:otherwise>
						Ingen sjekkpunkter
					</xsl:otherwise>
			</xsl:choose>
		</div>
			
</div>
</xsl:template>
