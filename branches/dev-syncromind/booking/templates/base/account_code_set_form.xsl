<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--div id="content"-->
		<!--dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', phpgw:conditional(new_form, 'Add', 'Edit'))"/><xsl:text> </xsl:text><xsl:value-of select="php:function('lang', 'Account Codes')"/></dt>
		</dl-->

		<xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

		<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
                    <input type="hidden" name="tab" value=""/>
                    <div id="tab-content">
                        <xsl:value-of disable-output-escaping="yes" select="account_code_set/tabs"/>
                        <div id="account">
                            <fieldset>
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Name')" />
                                </label>
                                <input name="name" type="text" id="field_name" value="{account_code_set/name}"/>
                            </div>
                            <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Active')"/></label>
                                            <select id="field_active" name="active">
                                                    <xsl:if test="new_form">
                                                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                    </xsl:if>

                                                    <option value="1">
                                                            <xsl:if test="account_code_set/active=1">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', 'Active')"/>
                                                    </option>
                                                    <option value="0">
                                                            <xsl:if test="account_code_set/active=0">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', 'Inactive')"/>
                                                    </option>
                                            </select>
                            </div>

                            <div class="clr"/>

                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_3">
                                            <label>
                                                <xsl:value-of select="config_data/dim_3" />
                                            </label>
                                            <input name="object_number" type="text" id="field_object_number" value="{account_code_set/object_number}" maxlength='8'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/article">
                                            <label>
                                                <xsl:value-of select="config_data/article" />
                                            </label>
                                            <input name="article" type="text" id="field_article" value="{account_code_set/article}" maxlength='15'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_value_1">
                                            <label>
                                                <xsl:value-of select="config_data/dim_value_1" />
                                            </label>
                                            <input name="unit_number" type="text" id="field_unit_number" value="{account_code_set/unit_number}" maxlength='12'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_value_4">
                                            <label>
                                                <xsl:value-of select="config_data/dim_value_4" />
                                            </label>
                                            <input name="dim_value_4" type="text" id="field_dim_value_4" value="{account_code_set/dim_value_4}" maxlength='12'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_value_5">
                                            <label><xsl:value-of select="config_data/dim_value_5" /></label>
                                            <input name="dim_value_5" type="text" id="field_dim_value_5" value="{account_code_set/dim_value_5}" maxlength='12'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/external_format!= 'KOMMFAKT'">
                                            <label>
                                                <xsl:value-of select="php:function('lang', 'Unit Prefix')" />
                                            </label>
                                            <input name="unit_prefix" type="text" id="field_unit_prefix" value="{account_code_set/unit_prefix}" maxlength='1'/>
                                    </xsl:if>
                            </div>

                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_1">
                                            <label><xsl:value-of select="config_data/dim_1" /></label>
                                            <input name="responsible_code" type="text" id="field_responsible_code" value="{account_code_set/responsible_code}" maxlength='6'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_2">
                                            <label><xsl:value-of select="config_data/dim_2" /></label>
                                            <input name="service" type="text" id="field_service" value="{account_code_set/service}" maxlength='8'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_4">
                                            <label><xsl:value-of select="config_data/dim_4" /></label>
                                            <input name="dim_4" type="text" id="field_dim_4" value="{account_code_set/dim_4}" maxlength='8'/>
                                    </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                    <xsl:if test="config_data/dim_5">
                                            <label><xsl:value-of select="config_data/dim_5" /></label>
                                            <input name="project_number" type="text" id="field_project_number" value="{account_code_set/project_number}" maxlength='12'/>
                                    </xsl:if>
                            </div>

                            <div class="clr"/>

                            <div class="pure-control-group">
                                <xsl:if test="config_data/external_format!= 'KOMMFAKT'">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'Invoice instruction')" />
                                        </label>
                                </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                <xsl:if test="config_data/external_format = 'KOMMFAKT'">
                                        <label><xsl:value-of select="php:function('lang', 'Reference')" /></label>
                                </xsl:if>
                                        <input size="120" id="field_invoice_instruction" name="invoice_instruction" value="{account_code_set/invoice_instruction}" />
                            </div>

                            <div class="clr"/>
                        </fieldset>
                        </div>
                    </div>
                    <div class="form-buttons">
                            <input type="submit" value="{php:function('lang', phpgw:conditional(new_form, 'Create', 'Save'))}" class="button pure-button pure-button-primary"/>
                            <a class="cancel" href="{account_code_set/cancel_link}">
                                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                            </a>
                    </div>
		</form>
	<!--/div-->
</xsl:template>


