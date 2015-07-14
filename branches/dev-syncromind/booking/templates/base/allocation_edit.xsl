<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->

       <!--ul class="pathway">
           <li><xsl:value-of select="php:function('lang', 'Allocations')" /></li>
           <li>#<xsl:value-of select="allocation/id"/></li>
       </ul-->
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
                <div id="allocations_edit">
                    <fieldset>
                        
                        <h1>
                            #<xsl:value-of select="allocation/id"/>
                        </h1>
                        
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Application')"/>
                            </label>
                                <xsl:if test="allocation/application_id!=''">
                                        <a href="{allocation/application_link}">#<xsl:value-of select="allocation/application_id"/></a>
                                </xsl:if>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Building')" />
                            </label>
                                
                                    <input id="field_building_id" name="building_id" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_id"/></xsl:attribute>
                                    </input>
                                    <input id="field_building_name" name="building_name" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="allocation/building_name"/></xsl:attribute>
                                    </input>
                                    <div id="building_container"/>
                                
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Active')"/>
                            </label>
                                <select id="field_active" name="active">
                                    <option value="1">
                                        <xsl:if test="allocation/active=1">
                                                <xsl:attribute name="selected">checked</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="php:function('lang', 'Active')"/>
                                    </option>
                                    <option value="0">
                                        <xsl:if test="allocation/active=0">
                                                <xsl:attribute name="selected">checked</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                                    </option>
                                </select>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Season')" />
                            </label>
                                <xsl:value-of select="php:function('lang', 'Select a building first')" />
                        </div>
                        <div class="pure-control-group">    
                            <label>
                                <xsl:value-of select="php:function('lang', 'Resources')" />
                            </label>
                                <xsl:value-of select="php:function('lang', 'Select a building first')" />
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Organization')" />
                            </label>
                                    <input id="field_org_id" name="organization_id" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_id"/></xsl:attribute>
                                    </input>
                                    <input id="field_org_name" name="organization_name" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="allocation/organization_name"/></xsl:attribute>
                                    </input>
                                    <div id="org_container"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'From')" />
                            </label>
                                <input class="datetime" id="field_from" name="from_" type="text">
                                <!--input id="field_from" name="from_" type="text"-->
                                    <xsl:attribute name="value"><xsl:value-of select="allocation/from_"/></xsl:attribute>
                                </input>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'To')" />
                            </label>
                                <input class="datetime" id="field_to" name="to_" type="text">
                                <!--input id="field_to" name="to_" type="text"-->
                                    <xsl:attribute name="value"><xsl:value-of select="allocation/to_"/></xsl:attribute>
                                </input>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Cost')" />
                            </label>
                                <input id="field_cost" name="cost" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="allocation/cost"/></xsl:attribute>
                                </input>
                        </div>
		<div style="clear: both" />
                        <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Inform contact persons')" />
                                </label>
                                        <xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" />
                                        <textarea id="field_mail" name="mail" class="full-width"></textarea>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" class="pure-button pure-button-primary">
                            <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
                            </input>
                <a class="cancel">
                    <xsl:attribute name="href"><xsl:value-of select="allocation/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                </a>
            </div>
    </form>
    <!--/div-->
    <!--script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="allocation/season_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script-->
</xsl:template>
