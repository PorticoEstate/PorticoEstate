
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="view">
    <div class="content">
        <script>
            var confirm_msg = "<xsl:value-of select="confirm_msg"/>";
            var lang_descr = "<xsl:value-of select="lang_descr"/>";
        </script>
        
        <div id='receipt'></div>
        <div>
            <form id="form" name="form" method="post" action=""  class="pure-form pure-form-aligned">
                <div id="tab-content">
                    <xsl:value-of disable-output-escaping="yes" select="tabs"/>

                    <input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

                    <div id="comm_descr_tab">
                        <fieldset>                                  
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Type')"/>
                                </label>
                                <select id="comm_type" name="comm_type">
                                    <xsl:apply-templates select="all_comm_type/options"/>
                                </select>
                            </div> 
                            <div class="pure-control-group">
                                <label>
                                    <xsl:value-of select="php:function('lang', 'Description')"/>
                                </label>
                                <input type="text" name="comm_descr" id="comm_descr" size="30" value="" />
                                <input type="hidden" name="comm_descr_id" id="comm_descr_id" size="30" value="" />                                	                                           
                            </div>                             
                            <div class="pure-control-group">
                                <label> </label>
                                <xsl:variable name="cancel">
                                        <xsl:value-of select="php:function('lang', 'cancel')"/>
                                </xsl:variable>                                                             
                                <xsl:variable name="add">
                                    <xsl:value-of select="php:function('lang', 'save')"/>
                                </xsl:variable>                                 
                                <input type="button" class="pure-button" name="add" id="add" value="{$add}" onClick="addDescr()" />
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

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>