<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'New Season')" /></dt>
		</dl-->
    <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
            <div id="season_new"> 
                <div class="pure-control-group">
                    <label for="field_name">
                        <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                    </label>
                    <input id="field_name" name="name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/name"/></xsl:attribute>
                    </input>
                </div>
                <div class="pure-control-group">
                    <label for="field_building_name">
                        <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                    </label>
                    <!--div class="autocomplete"-->
                    <input id="field_building_id" name="building_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="season/building_id"/></xsl:attribute>
                    </input>
                    <input id="field_building_name" name="building_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/building_name"/></xsl:attribute>
                    </input>
                    <!--div id="building_container"/>
                    </div-->
                </div>
                <div class="pure-control-group">
                    <label for="field_officer_name">
                        <h4><xsl:value-of select="php:function('lang', 'Case officer')" /></h4>
                    </label>
                    <!--div class="autocomplete"-->
                    <input id="field_officer_id" name="officer_id" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="season/officer_id"/></xsl:attribute>
                    </input>
                    <input id="field_officer_name" name="officer_name" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/officer_name"/></xsl:attribute>
                    </input>
                    <!--div id="officer_container"/>
                    </div-->
                </div>
                <div class="pure-control-group">
                    <label for="" style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'Resources')" /></h4>
                    </label>
                    <div id="resources-container" style="display:inline-block;"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
                </div>
                <div class="pure-control-group">
                    <label for="status_field">
                        <h4><xsl:value-of select="php:function('lang', 'Status')" /></h4>
                    </label>                    
                    <select name="status" id=" ">
                        <option value="PLANNING">
                            <xsl:if test="season/status='PLANNING'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:value-of select="php:function('lang', 'Planning')" />
                        </option>
                        <option value="PUBLISHED">
                            <xsl:if test="season/status='PUBLISHED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:value-of select="php:function('lang', 'Published')" />
                        </option>
                        <option value="ARCHIVED">
                            <xsl:if test="season/status='ARCHIVED'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:value-of select="php:function('lang', 'Archived')" />
                        </option>
                    </select>
                </div>
                <div class="pure-control-group">
                    <!--div class="date-picker">
                    <input id="field_from" name="from_" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/from_"/></xsl:attribute>
                    </input>
                    </div-->
                    <label for="start_date">
                        <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                    </label>
                    <input class="datetime" id="start_date" name="start_date" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/from_"/></xsl:attribute>
                    </input>                        
                </div>
                <div class="pure-control-group">
                    <!--div class="date-picker">
                    <input id="field_to" name="to_" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/to_"/></xsl:attribute>
                    </input>
                    </div-->
                    <label for="end_date">
                        <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                    </label>
                    <input class="datetime" id="end_date" name="end_date" type="text">
                        <xsl:attribute name="value"><xsl:value-of select="season/to_"/></xsl:attribute>
                    </input>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
            </input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="season/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    <!--/div-->
<script type="text/javascript">
    YAHOO.booking.initialSelection = <xsl:value-of select="season/resources_json"/>;
    var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
</script>
</xsl:template>
