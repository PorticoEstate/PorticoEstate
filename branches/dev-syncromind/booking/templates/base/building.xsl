<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">
        <ul class="pathway">
       <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
            </li>
            <li>
                    
                    <xsl:value-of select="building/name"/>
            </li>
        </ul-->

        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

    <input type="hidden" name="tab" value=""/>
    <div id="tab-content">
        <xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
        <div id="building_show"> 
    <form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">     
            <div class="pure-control-group">          
                <label>
                    <h4>
                        <xsl:value-of select="php:function('lang', 'Description')" />
                    </h4>
                </label>
                <!--div class="description"--><xsl:value-of select="building/description" disable-output-escaping="yes"/><!--/div-->
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Street')" />
                </label>
                <xsl:value-of select="building/street"/>
            </div>
            <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Zip code')" />
                            </label>
                <xsl:value-of select="building/zip_code"/>
            </div>
            <div class="pure-control-group">
                <label><xsl:value-of select="php:function('lang', 'Postal City')" /></label>
                <xsl:value-of select="building/city"/>
            </div>
            <div class="pure-control-group">
                <label><xsl:value-of select="php:function('lang', 'District')" /></label>
                <xsl:value-of select="building/district"/>
            </div>
            <div class="pure-control-group">
                            <xsl:if test="building/location_code !=''">
                        <label><xsl:value-of select="php:function('lang', 'Location Code')" /></label>
                        <a href="{building/location_link}"><xsl:value-of select="building/location_code"/></a>
                            </xsl:if>

            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Homepage')" />
                </label>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/homepage"/></xsl:attribute>
                    <xsl:value-of select="building/homepage"/></a>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Email')" />
                </label>
                <a>
                    <xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
                    <xsl:value-of select="building/email"/></a>
            </div>
            <div class="pure-control-group">
                <label>
                    <xsl:value-of select="php:function('lang', 'Telephone')" />
                </label>
                <xsl:value-of select="building/phone"/>
            </div>
                    <div class="clr"/>
            <div class="pure-control-group">
                    <xsl:if test="building/tilsyn_name != ''">
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
                            </label>
                            <xsl:value-of select="building/tilsyn_name"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
                            </label>
                            <a>
                                <xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email"/></xsl:attribute>
                                <xsl:value-of select="building/tilsyn_email"/></a>
                        </div>
                        <div class="pure-control-group">    
                            <label>
                                <xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
                            </label>
                            <xsl:value-of select="building/tilsyn_phone"/>
                        </div>
                            </xsl:if>
            </div>
            <div class="pure-control-group">
                            <xsl:if test="building/tilsyn_name2 != ''">
                                <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></label>
                                    <xsl:value-of select="building/tilsyn_name2"/>
                                </div>
                                <div class="pure-control-group">
                                    <label>
                                        <xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
                                    </label>
                                    <a>
                                    <xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email2"/></xsl:attribute>
                                    <xsl:value-of select="building/tilsyn_email2"/></a>
                                </div>
                                <div class="pure-control-group">
                                    <label>
                                        <xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
                                    </label>
                                    <xsl:value-of select="building/tilsyn_phone2"/>
                                </div>
                            </xsl:if>
            </div>
</form>            
            <div class="form-buttons">
                            <xsl:if test="building/permission/write">
                            <button>
                                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="building/edit_link"/>"</xsl:attribute>
                                    <xsl:value-of select="php:function('lang', 'Edit')" />
                            </button>
                            </xsl:if>
                            <button>
                                <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="building/schedule_link"/>"</xsl:attribute>
                                <xsl:value-of select="php:function('lang', 'Building schedule')" />
                            </button>
            </div>

            <h4><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h4>
            <div id="resources_container"/>

                    <h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
            <div id="documents_container"/>
                    <a class='button'>
                            <xsl:attribute name="href"><xsl:value-of select="building/add_document_link"/></xsl:attribute>
                            <xsl:if test="building/permission/write">
                                    <xsl:value-of select="php:function('lang', 'Add Document')" />
                            </xsl:if>
                    </a>

                    <h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
            <div id="permissions_container"/>
        </div>
    </div>

    <!--/div-->

<script type="text/javascript">
var building_id = <xsl:value-of select="building/id"/>;
	var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order')"/>;
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
    var colDefs = [{key: 'sort', label: lang['Sort order']},{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);

	var url = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'category', label: lang['Category']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
]]>
	var colDefs = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
    <![CDATA[
	YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
});

]]>
</script>

</xsl:template>
