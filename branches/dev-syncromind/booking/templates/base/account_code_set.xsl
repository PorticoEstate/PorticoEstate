<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--xsl:call-template name="yui_booking_i18n"/-->
    <!--div id="content"-->
    <!--ul class="pathway">
            <li>
                    <a>
                            <xsl:attribute name="href"><xsl:value-of select="account_code_set/account_codes_link"/></xsl:attribute>
                            <xsl:value-of select="php:function('lang', 'Account Codes')" />
                    </a>
            </li>
            <li>
                    <xsl:value-of select="account_code_set/name"/>
            </li>
    </ul-->
    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="account_code_set/tabs"/>
            <div id="account">                
		<div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                    </label>
                    <xsl:value-of select="account_code_set/name"/>
                </div>
		<div class="pure-control-group">
                    <xsl:if test="config_data/dim_3">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_3" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/object_number"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">	
                    <xsl:if test="config_data/article">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Article')" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/article"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="config_data/dim_value_1">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_value_1" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/unit_number"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">	
                    <xsl:if test="config_data/dim_value_4">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_value_4" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/dim_value_4"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">	
                    <xsl:if test="config_data/dim_value_5">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_value_5" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/dim_value_5"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">	
                    <xsl:if test="config_data/external_format != 'KOMMFAKT'">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Unit Prefix')" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/unit_prefix"/>
                    </xsl:if>
                </div>		
		<div class="pure-control-group">
                    <xsl:if test="config_data/dim_1">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_1" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/responsible_code"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">	
                    <xsl:if test="config_data/dim_2">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_2" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/service"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="config_data/dim_4">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_4" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/dim_4"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="config_data/dim_5">
                        <label>
                            <h4><xsl:value-of select="config_data/dim_5" /></h4>
                        </label>
                        <xsl:value-of select="account_code_set/project_number"/>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="config_data/external_format != 'KOMMFAKT'">
                        <xsl:value-of select="php:function('lang', 'Reference')" />
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Invoice instruction')" />      </h4>
                    </label>              
                    <div class="description" style="display:inline-block;max-width:80%;"><xsl:value-of select="account_code_set/invoice_instruction"/></div>
                </div>
            </div>
        </div>
    </form>
    <div class="form-buttons">
        <xsl:if test="account_code_set/permission/write">
            <div class="form-buttons">
                <button onclick="window.location.href='{account_code_set/edit_link}'">
                    <xsl:value-of select="php:function('lang', 'Edit')" />
                </button>
            </div>
        </xsl:if>
    </div>
	<!--/div-->
</xsl:template>
