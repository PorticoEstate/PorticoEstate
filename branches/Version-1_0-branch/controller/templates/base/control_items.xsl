<xsl:template name="control_items" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
	<xsl:value-of select="php:function('lang', 'Control_items')" />
</h1>
</div>

<div class="yui-content">
	<div>
		  
	   <!-- ===========================  CHOOSE CONTROL ITEMS  =============================== -->
	   <h2>Velg dine kontrollpunkt</h2>
		<form action="#" method="post">	
		
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" name="control_id" value="{control_id}" />
		
		<ul class="control_items">
			<xsl:for-each select="//control_items">
				<ul class="expand_list">
	    		<li>
		         	<h4><img src="controller/images/arrow_left.png" width="14"/><span><xsl:value-of select="control_group/group_name"/></span></h4>
		         	<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
		         	<ul>		
						<xsl:for-each select="control_item">
							<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
							
			     			<li><xsl:number/>.  <input type="checkbox"  name="control_tag_ids[]" value="{$control_group_id}:{$control_item_id}" /><xsl:value-of select="title"/></li>	
						</xsl:for-each>
					</ul>
				</li>
			</ul>
			</xsl:for-each>
		</ul>
		
		<div class="form-buttons">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control_items" value="{$lang_save}" title = "{$lang_save}" />
		</div>
		</form>
								
	</div>
</div>
</xsl:template>