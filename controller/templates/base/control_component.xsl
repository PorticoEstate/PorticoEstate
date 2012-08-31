<!-- $Id$ -->
<xsl:template name="control_component" xmlns:php="http://php.net/xsl">

  <div class="yui-content">
    <div>
		  
      <!-- ===========================  CHOOSE component  =============================== -->
      <h2>Velg Utstyrskategori/utstyr</h2>
	   
      <h4 class="expand_header">
        <div class="expand_all">Vis alle</div>
        <div class="collapse_all">Skjul alle</div>
      </h4>
      <form id="frm_control_items" action="#" method="post">	
		
        <xsl:variable name="control_id">
          <xsl:value-of select="control_id"/>
        </xsl:variable>
        <input type="hidden" name="control_id" value="{control_id}" />		
        <strong>Velg utstyrskategori/utstyr</strong>	
        <div class="form-buttons">
          <xsl:variable name="lang_save">
            <xsl:value-of select="php:function('lang', 'save')" />
          </xsl:variable>
          <input type="submit" name="save_control_component" value="{$lang_save}" title = "{$lang_save}" />
        </div>
      </form>
								
    </div>
  </div>
</xsl:template>
