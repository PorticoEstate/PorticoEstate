<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">
        <ul class="pathway">
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
		<!--xsl:call-template name="yui_booking_i18n"/-->  
    <input type="hidden" name="tab" value=""/>
    <div id="tab-content">
        <xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
        <div id="season_show"> 
    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Case officer')" />
                </label>
                <xsl:value-of select="season/officer_name"/>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'From')" />
                </label>
                <xsl:value-of select="php:function('pretty_timestamp', season/from_)"/>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'To')" />
                </label>
                <xsl:value-of select="php:function('pretty_timestamp', season/to_)"/>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Building')" />
                </label>
                <xsl:value-of select="season/building_name"/>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Status')" />
                </label>
                <xsl:value-of select="season/status"/>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Resources')" />
                </label>
                <div id="resources_container"/>
            </div>
    </form>
            <div class="form-buttons">
                            <xsl:if test="season/permission/write">
                                    <button>
                                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/edit_link">"</xsl:value-of>"</xsl:attribute>
                                <xsl:value-of select="php:function('lang', 'Edit')" />
                            </button>
                            </xsl:if>
                    <button>
                        <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/boundaries_link">"</xsl:value-of>"</xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Boundaries')" />
                    </button>
                    <button>
                        <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/wtemplate_link">"</xsl:value-of>"</xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Week template')" />
                    </button>
                    </div>

                    <h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
                <div id="permissions_container"/>
        </div>
    </div>

    <!--/div-->

	<script type="text/javascript">
		var season_id = <xsl:value-of select="season/id"/>;
	    var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Account', 'Role', 'Actions', 'Edit', 'Delete', 'Resource Type')"/>;
	YAHOO.util.Event.addListener(window, "load", function() {
	    <![CDATA[
	    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
	        ]]>
	    var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
	    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
	
		<![CDATA[ var url = 'index.php?menuaction=booking.uipermission_season.index&sort=name&filter_object_id=' + season_id + '&phpgw_return_as=json&'; ]]>
		var colDefs = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
		YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
	});
	</script>

</xsl:template>
