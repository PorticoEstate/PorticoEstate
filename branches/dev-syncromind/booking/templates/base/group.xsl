<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->
        <!--ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
                    <xsl:value-of select="group/organization_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Group')" /></li>
            <li>
                    <xsl:value-of select="group/name"/>
            </li>
        </ul-->
        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->
        <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">   
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="group/tabs"/>
                <div id="group"> 
                    <fieldset>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Organization')" />
                        </label>
                            <xsl:value-of select="group/organization_name"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Name')" />
                        </label>
                            <xsl:value-of select="group/name"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Group shortname')" />
                        </label>
                            <xsl:value-of select="group/shortname"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Activity')" />
                        </label>
                            <xsl:value-of select="group/activity_name" />
                    </div>
                    <div class="pure-control-group">
                                    <xsl:if test="count(group/contacts/*) &gt; 0">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'Team leaders')" />
                                        </label>
                                            <ul>
                                                                    <xsl:if test="group/contacts[1]">
                                                                            <li><xsl:value-of select="group/contacts[1]/name"/></li>
                                                                    </xsl:if>

                                                <xsl:if test="group/contacts[2]">
                                                    <li><xsl:value-of select="group/contacts[2]/name"/></li>
                                                                    </xsl:if>
                                            </ul>
                                    </xsl:if>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Description')" />
                        </label>
                            <xsl:value-of select="group/description" disable-output-escaping="yes"/>
                    </div>
                    </fieldset>
                </div>
            </div>
            <div class="form-buttons">
                <a class="button pure-button pure-button-primary">
                    <xsl:attribute name="href"><xsl:value-of select="group/edit_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Edit')" />
                </a>
            </div>
        </form>    
    <!--/div-->
</xsl:template>
