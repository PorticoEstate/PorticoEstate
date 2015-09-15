<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->
        <!--ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="allocation/allocations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Allocations')" />
                </a>
            </li>
            <li><xsl:value-of select="allocation/organization_name"/></li>
        </ul-->

        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->
        <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
                <div id="allocations">
                    <h1>
                        <xsl:value-of select="allocation/organization_name"/>
                    </h1>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                            </label>
                            <xsl:value-of select="php:function('pretty_timestamp', allocation/from_)"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                            </label>
                            <xsl:value-of select="php:function('pretty_timestamp', allocation/to_)"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Season')" /></h4>
                            </label>
                            <xsl:value-of select="allocation/season_name"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Organization')" /></h4>
                            </label>
                            <xsl:value-of select="allocation/organization_name"/>
                        </div>
                        <div class="pure-control-group">
                            <label style="vertical-align:top;">
                                <h4><xsl:value-of select="php:function('lang', 'Resources')" /></h4>
                            </label>
                            <div id="resources_container" style="display:inline-block;"></div>
                        </div>
                </div>
            </div>
        </form>
        <div class="pure-control-group">
            <xsl:if test="allocation/permission/write">
            <button>
                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/edit_link"/>"</xsl:attribute>
                <!--xsl:attribute name="onclick">square();</xsl:attribute-->
                <xsl:value-of select="php:function('lang', 'Edit')" />
            </button>
            <button>
                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/delete_link"/>"</xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Delete')" />
            </button>
            </xsl:if>
        </div>
    <!--/div-->
<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="allocation/resource_ids"/>';
    var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')"/>;
    <![CDATA[
        var resourcesURL = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
    ]]>
    var colDefs = [{key: 'name', label: lang['Name'], formatter: genericLink()}, {key: 'type', label: lang['Resource Type']}];
    createTable('resources_container',resourcesURL,colDefs);
</script>
<script type="text/javascript">    
    function square()
    {
        location.href = "<xsl:value-of select="allocation/edit_link"/>"; 
    }
</script>

</xsl:template>
