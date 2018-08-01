
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="index">
			<xsl:apply-templates select="index" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="index">
    <div class="content">
        <script>
            var confirm_msg = "<xsl:value-of select="confirm_msg"/>";
            var lang_field_name = "<xsl:value-of select="lang_field_name"/>";
        </script>
        
        <div id='receipt'></div>
        <div>
            <form id="form" name="form" method="post" action=""  class="pure-form pure-form-aligned">
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>

                    <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                    <div id="tab_field">
                        <fieldset>                                  
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Field Name')"/>
                                </label>
                                <input type="hidden" name="name" id="name" size="30" value="" />
                                <input type="text" name="field_name" id="field_name" size="30" value="" />                                	                                           
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Apply for')"/>
                                </label>
                                <div class="pure-custom">
                                    <div><input type="radio" name="apply_for" value="person" /> <xsl:value-of select="php:function('lang', 'Person')"/></div>
                                    <div><input type="radio" name="apply_for" value="org" /> <xsl:value-of select="php:function('lang', 'Organizations')"/></div>
                                    <div><input type="radio" name="apply_for" value="both" checked="true"/> <xsl:value-of select="php:function('lang', 'Both')"/></div>     
                                </div>                          	                                           
                            </div>                                               
                            <div class="pure-control-group">
                                <label> </label>
                                <xsl:variable name="cancel">
                                        <xsl:value-of select="php:function('lang', 'cancel')"/>
                                </xsl:variable>                                                             
                                <xsl:variable name="add">
                                    <xsl:value-of select="php:function('lang', 'save')"/>
                                </xsl:variable>                                 
                                <input type="button" class="pure-button" name="add" id="add" value="{$add}" onClick="addField()" />
                                <input type="button" class="pure-button" name="cancel_save" id="cancel_save" disabled="true" value="{$cancel}" onClick="cancelSave()" />	                                           
                            </div>                                                    
                            <div>
                                <xsl:for-each select="datatable_def">
                                        <xsl:if test="container = 'datatable-container_0'">
                                                <xsl:call-template name="table_setup">
                                                        <xsl:with-param name="container" select ='container'/>
                                                        <xsl:with-param name="requestUrl" select ='requestUrl' />
                                                        <xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
                                                        <xsl:with-param name="tabletools" select ='tabletools' />
                                                        <xsl:with-param name="data" select ='data' />
                                                        <xsl:with-param name="config" select ='config' />
                                                </xsl:call-template>
                                        </xsl:if>
                                </xsl:for-each>
                            </div>                                                           
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
</xsl:template>