<!-- $Id$ -->
<xsl:template name="control_locations" xmlns:php="http://php.net/xsl">

<div class="content-wrp">
	<div>
		  
	   <!-- ===========================  CHOOSE CONTROL LOCATIONS  =============================== -->
	   <h2>Velg Byggkategori/Eiendom/Bygg</h2>
	   
	   	<h4 class="expand_header"><div class="expand_all">Vis alle</div><div class="collapse_all">Skjul alle</div></h4>
		<form id="frm_control_items" action="#" method="post">	
		
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" name="control_id" value="{control_id}" />		
		<strong>Liste over byggkategorier/eiendommer/bygg el.</strong>
		<div class="form-buttons">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control_locations" value="{$lang_save}" title = "{$lang_save}" />
		</div>
		</form>
								
	</div>
</div>
</xsl:template>
