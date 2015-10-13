<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->
    <!--ul class="pathway">
        <li>
            <a>
                <xsl:attribute name="href"><xsl:value-of select="season/buildings_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Buildings')" />
            </a>
        </li>
        <li>
            <a>
                <xsl:attribute name="href"><xsl:value-of select="season/building_link"/></xsl:attribute>
                <xsl:value-of select="season/building_name"/>
            </a>
        </li>
        <li><xsl:value-of select="php:function('lang', 'Season')" /></li>
        <li><a href=""><xsl:value-of select="season/name"/></a></li>
    </ul-->

    <xsl:call-template name="msgbox"/>		
    <xsl:if test="step = 1">
        <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
                <div id="season_generate" class="booking-container">
                    <div class="heading">
                        <h3><xsl:value-of select="php:function('lang', 'Generate Allocations from week template')" /> (1/2)</h3>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label>
                            <input id="field_from" name="from_" type="text" value="{from_}"/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label>
                            <input id="field_to" name="to_" type="text" value="{to_}"/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_interval"><xsl:value-of select="php:function('lang', 'Interval')" /></label>
                            <select id="field_interval" name="field_interval">
                                <option value="1">
                                    <xsl:if test="interval=1">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', '1 week')" />
                                </option>
                                <option value="2">
                                    <xsl:if test="interval=2">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', '2 weeks')" />
                                </option>
                                <option value="3">
                                    <xsl:if test="interval=3">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', '3 weeks')" />
                                </option>
                                <option value="4">
                                    <xsl:if test="interval=4">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', '4 weeks')" />
                                </option>
                            </select>
                    </div>
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" name="calculate" class="pure-button pure-button-primary">
                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Preview')" /></xsl:attribute>
                </input>
                <a class="cancel pure-button pure-button-primary">
                    <xsl:attribute name="href"><xsl:value-of select="season/wtemplate_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                </a>
            </div>
        </form>
    </xsl:if>
    <xsl:if test="step = 2">
        <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
                <div id="season_generate" class="booking-container">
                    <input type="hidden" name="from_"><xsl:attribute name="value"><xsl:value-of select="from_" /></xsl:attribute></input>
                    <input type="hidden" name="to_"><xsl:attribute name="value"><xsl:value-of select="to_" /></xsl:attribute></input>
                    <div class="heading">
                        <h3><xsl:value-of select="php:function('lang', 'Generate Allocations from week template')" /> (2/2)</h3>
                    </div>
                    <div class="pure-control-group">
                        <h4><xsl:value-of select="php:function('lang', 'Allocations that can be created (%1)', count(result/valid[from_]))" /></h4>
                        <div class="allocation-list">
                            <xsl:for-each select="result/valid[from_]">
                                <li>
                                    <xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
                                </li>
                            </xsl:for-each>
                        </div>
                    </div>
                    <div class="pure-control-group">
                        <h4><xsl:value-of select="php:function('lang', 'Allocations colliding with existing bookings or allocations (%1)', count(result/invalid[from_]))" /></h4>
                        <div class="allocation-list">
                            <xsl:for-each select="result/invalid[from_]">
                                <li>
                                    <xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
                                </li>
                            </xsl:for-each>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" name="create" class="pure-button pure-button-primary">
                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
                </input>
                <a class="cancel pure-button pure-button-primary">
                    <xsl:attribute name="href"><xsl:value-of select="season/wtemplate_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                </a>
            </div>
        </form>
    </xsl:if>
    <xsl:if test="step = 3">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
            <div id="season_generate" class="booking-container">
                <h4><xsl:value-of select="php:function('lang', 'Successfully created %1 allocations:', count(result/valid[from_]))" /></h4>
                <div class="allocation-list">
                    <xsl:for-each select="result/valid[from_]">
                        <li>
                            <xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>: <xsl:value-of select="organization_name"/>
                        </li>
                    </xsl:for-each>
                </div>
            </div>
        </div>
        <a class="cancel pure-button pure-button-primary">
            <xsl:attribute name="href"><xsl:value-of select="season/wtemplate_link"/></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Go back to the template week')" />
        </a>
    </xsl:if>
    <!--/div-->
</xsl:template>
