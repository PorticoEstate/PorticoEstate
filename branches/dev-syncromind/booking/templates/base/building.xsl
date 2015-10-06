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


    <form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
    <input type="hidden" name="tab" value=""/>
    <div id="tab-content">
        <xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
        <div id="building_show" class="booking-container"> 
                <div class="pure-control-group">          
                    <label>
                        <xsl:value-of select="php:function('lang', 'Description')" />
                    </label>
                    <!--div class="description"--><!--/div-->
                    <div class="custom-container">
                        <xsl:value-of select="building/description" disable-output-escaping="yes"/>
                    </div>
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
                    <label>
                        <xsl:value-of select="php:function('lang', 'Postal City')" />
                    </label>
                    <xsl:value-of select="building/city"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'District')" />
                    </label>
                    <xsl:value-of select="building/district"/>
                </div>
                <div class="pure-control-group">
                    <xsl:if test="building/location_code !=''">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Location Code')" />
                        </label>
                        <a href="{building/location_link}"><xsl:value-of select="building/location_code"/></a>
                    </xsl:if>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Homepage')" />
                    </label>
                    <a>
                        <xsl:attribute name="href"><xsl:value-of select="building/homepage"/></xsl:attribute>
                        <xsl:value-of select="building/homepage"/>
                    </a>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Email')" />
                    </label>
                    <a>
                        <xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
                        <xsl:value-of select="building/email"/>
                    </a>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Telephone')" />
                    </label>
                    <xsl:value-of select="building/phone"/>
                </div>
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
							<xsl:value-of select="building/tilsyn_email"/>
						</a>
					</div>
					<div class="pure-control-group">    
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
						</label>
						<xsl:value-of select="building/tilsyn_phone"/>
					</div>
				</xsl:if>
				<xsl:if test="building/tilsyn_name2 != ''">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
						</label>
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
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Bookable resources')" />
                    </label>
                    <div id="resources_container" class="custom-container"></div>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Documents')" />
                    </label>
                    <div class="pure-custom">
                        <div id="documents_container" class="custom-container"></div>
						<div>
							<a class='button'>
								<xsl:attribute name="href"><xsl:value-of select="building/add_document_link"/></xsl:attribute>
								<xsl:if test="building/permission/write">
									<xsl:value-of select="php:function('lang', 'Add Document')" />
								</xsl:if>
							</a>
						</div>
                    </div>                        
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Permissions')" />
                    </label>
                    <div id="permissions_container" class="custom-container"></div>
                </div>
					</div>
				</div>
				<div class="form-buttons">			
					<xsl:if test="building/permission/write">			
						<input type="button" class="pure-button pure-button-primary" name="edit">
							<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/edit_link"/>"</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Edit')" /></xsl:attribute>	
						</input>								
					</xsl:if>
					<input type="button" class="pure-button pure-button-primary" name="schedule">
						<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/schedule_link"/>"</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Building schedule')" /></xsl:attribute>	
					</input>
					<input type="button" class="pure-button pure-button-primary" name="cancel">
						<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/cancel_link"/>"</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Cancel')" /></xsl:attribute>	
					</input>
				</div>				              
            </form>                       


    <!--/div-->

<script type="text/javascript">
    var building_id = <xsl:value-of select="building/id"/>;
    var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order')"/>;
    
    <![CDATA[
    var resourcesURL     = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
    var documentsURL     = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
    var permissionsURL   = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
    ]]>
    var colDefsResources = [{key: 'sort', label: lang['Sort order']},{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}];
    var colDefsDocuments = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'category', label: lang['Category']}, {key: 'actions', label: lang['Actions'], formatter: genericLink(lang['Edit'], lang['Delete'])}];
    var colDefsPermissions = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label: 'Edit'}, lang['Delete'])}];
    
    createTable('resources_container',resourcesURL,colDefsResources);
    createTable('documents_container',documentsURL,colDefsDocuments);
    createTable('permissions_container',permissionsURL,colDefsPermissions);


    /*
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
    */
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
