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
                    <label style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                    </label>
                    <!--div class="description"--><!--/div-->
                    <div style="display:inline-block;">
                        <xsl:value-of select="building/description" disable-output-escaping="yes"/>
                    </div>
                </div>
                        
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Street')" /></h4>
                    </label>
                    <xsl:value-of select="building/street"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Zip code')" /></h4>
                    </label>
                    <xsl:value-of select="building/zip_code"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Postal City')" /></h4>
                    </label>
                    <xsl:value-of select="building/city"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'District')" /></h4>
                    </label>
                    <xsl:value-of select="building/district"/>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="building/location_code !=''">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Location Code')" /></h4>
                        </label>
                        <a href="{building/location_link}"><xsl:value-of select="building/location_code"/></a>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Homepage')" /></h4>
                    </label>
                    <a>
                        <xsl:attribute name="href"><xsl:value-of select="building/homepage"/></xsl:attribute>
                        <xsl:value-of select="building/homepage"/>
                    </a>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                    </label>
                    <a>
                        <xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
                        <xsl:value-of select="building/email"/>
                    </a>
                </div>
                <div class="pure-control-group">
                    <label>
                        <h4><xsl:value-of select="php:function('lang', 'Telephone')" /></h4>
                    </label>
                    <xsl:value-of select="building/phone"/>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="building/tilsyn_name != ''">
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></h4>
                            </label>
                            <xsl:value-of select="building/tilsyn_name"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></h4>
                            </label>
                            <a>
                                <xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email"/></xsl:attribute>
                                <xsl:value-of select="building/tilsyn_email"/>
                            </a>
                        </div>
                        <div class="pure-control-group">    
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></h4>
                            </label>
                            <xsl:value-of select="building/tilsyn_phone"/>
                        </div>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="building/tilsyn_name2 != ''">
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></h4>
                            </label>
                            <xsl:value-of select="building/tilsyn_name2"/>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></h4>
                            </label>
                            <a>
                            <xsl:attribute name="href">mailto:<xsl:value-of select="building/tilsyn_email2"/></xsl:attribute>
                            <xsl:value-of select="building/tilsyn_email2"/></a>
                        </div>
                        <div class="pure-control-group">
                            <label>
                                <h4><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></h4>
                            </label>
                            <xsl:value-of select="building/tilsyn_phone2"/>
                        </div>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <label style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h4>
                    </label>
                    <div id="resources_container" style="display:inline-block;"></div>
                </div>
                <div class="pure-control-group">
                    <label style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
                    </label>
                    <div style="display:inline-block;">
                        <div id="documents_container"></div>
                        <a class='button'>
                            <xsl:attribute name="href"><xsl:value-of select="building/add_document_link"/></xsl:attribute>
                            <xsl:if test="building/permission/write">
                                <xsl:value-of select="php:function('lang', 'Add Document')" />
                            </xsl:if>
                        </a>
                    </div>                        
                </div>
                <div class="pure-control-group">
                    <label style="vertical-align:top;">
                        <h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
                    </label>
                    <div id="permissions_container" style="display:inline-block;"></div>
                </div>
                <div class="pure-control-group">
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
                </div>                
            </form>                       
        </div>
    </div>

    <!--/div-->

<script type="text/javascript">
    var building_id = <xsl:value-of select="building/id"/>;
    var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order')"/>;
    
    <![CDATA[
    var resourceURL     = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
    var documentURL     = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
    var permissionURL   = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
    ]]>
    
    
    $.get(resourceURL, function(resourceData){
        var resourceBody = '';
        var resourceTableClass = "pure-table";
        <![CDATA[
            var resourceHead = '<tr><th>'+lang['Sort order']+'</th><th>'+lang['Name']+'</th><th>'+lang['Resource Type']+'</th></tr>';
        ]]>;
        if (resourceData.data.length === 0) {
            resourceBody = '<tr><td colspan="3">'+lang['No records found']+'</td></tr>';
        }else {
            resourceTableClass = "pure-table pure-table-striped";
            $.each(resourceData.data, function(index, value) {
                <![CDATA[
                resourceBody += '<tr><td>'+value.sort+'</td><td><a href="'+value.link+'">'+value.name+'</a></td><td>'+value.type+'</td></tr>';
                ]]>
            });
        };
        <![CDATA[
            var resourceTable = '<table class="'+resourceTableClass+'"><thead>'+resourceHead+'</thead><tbody>'+resourceBody+'</tbody></table>'
        ]]>
        $('#resources_container').html(resourceTable);
    });
    
    
    
    
    $.get(documentURL, function(documentData){
        var documentBody = '';
        var documentTableClass = "pure-table";
        <![CDATA[
            var documentHead = '<tr><th>'+lang['Name']+'</th><th>'+lang['Category']+'</th><th>'+lang['Actions']+'</th></tr>';
        ]]>;
        if (documentData.data.length === 0) {
            documentBody = '<tr><td colspan="3">'+lang['No records found']+'</td></tr>';
        }else {
            documentTableClass = "pure-table pure-table-striped";
            $.each(documentData.data, function(index, value) {
                <![CDATA[
                documentBody += '<tr><td>'+value.name+'</td><td>'+value.category+'</td><td><a href="'+value.opcion_edit+'">'+lang['Edit']+'</a>&nbsp;<a href="'+value.opcion_delete+'">'+lang['Delete']+'</a></td></tr>';
                ]]>
            });
        };
        <![CDATA[
            var documentTable = '<table class="'+documentTableClass+'"><thead>'+documentHead+'</thead><tbody>'+documentBody+'</tbody></table>'
        ]]>
        $('#documents_container').html(documentTable);
    });
    
    
    
    $.get(permissionURL, function(permissionData){
        var permissionBody = '';
        var permissionTableClass = "pure-table";
        <![CDATA[
            var permissionHead = '<tr><th>'+lang['Account']+'</th><th>'+lang['Role']+'</th><th>'+lang['Actions']+'</th></tr>';
        ]]>;
        if (permissionData.data.length === 0) {
            permissionBody = '<tr><td colspan="3">'+lang['No records found']+'</td></tr>';
        }else {
            permissionTableClass = "pure-table pure-table-striped";
            $.each(permissionData.data, function(index, value) {
                <![CDATA[
                permissionBody += '<tr><td>'+value.subject_name+'</td><td>'+value.role+'</td><td><a href="'+value.opcion_edit+'">'+lang['Edit']+'</a>&nbsp;<a href="'+value.opcion_delete+'">'+lang['Delete']+'</a></td></tr>';
                ]]>
            });
        };
        <![CDATA[
            var permissionTable = '<table class="'+permissionTableClass+'"><thead>'+permissionHead+'</thead><tbody>'+permissionBody+'</tbody></table>'
        ]]>
        $('#permissions_container').html(permissionTable);
    });
 </script>
 <!--script>   
    
    
    
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
</script-->

</xsl:template>
