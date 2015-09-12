<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content">
		<ul class="pathway">
			<li><a href="{export/index_link}"><xsl:value-of select="php:function('lang', 'Invoice Data Exports')" /></a></li>
			<li><xsl:value-of select="export/id"/></li>
		</ul-->
		
    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->
	<form action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form" >
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="export/tabs"/>
                <div id="export">	
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                        </label>
                        <xsl:copy-of select="phpgw:booking_link(export/building_id)"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Season')" /></h4>
                        </label>
                        <xsl:copy-of select="phpgw:booking_link(export/season_id)"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Total Items')" /></h4>
                        </label>
                        <span><xsl:value-of select="export/total_items"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Total Cost')" /></h4>
                        </label>
                        <span><xsl:value-of select="export/total_cost"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Created')" /></h4>
                        </label>
                        <span><xsl:value-of select="export/created_on"/></span>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Created by')" /></h4>
                        </label>
                        <span><xsl:value-of select="export/created_by_name"/></span>
                    </div>
                </div>
            </div>
        </form>
	<!--/div-->
		
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'ID', 'Building', 'Season', 'From', 'To')"/>;
	</script>
</xsl:template>