<xsl:template match="data" xmlns:php="http://php.net/xsl">

    <!--div id="content"-->
    <!--ul class="pathway">
        <li>
            <a>
                <xsl:attribute name="href"><xsl:value-of select="resource/buildings_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Buildings')" />
            </a>
        </li>
        <li>
            <a>
                <xsl:attribute name="href"><xsl:value-of select="resource/building_link"/></xsl:attribute>
                <xsl:value-of select="resource/building_name"/>
            </a>
        </li>
        <li><xsl:value-of select="php:function('lang', 'Resources')" /></li>
        <li><xsl:value-of select="resource/name"/></li>
    </ul-->
    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->
    
    <form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
        <input type="hidden" name="tab" value="" />
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
            <div id="resource" class="booking-container">
                <div class="pure-control-group">
					<label>
                        <xsl:value-of select="php:function('lang', 'Description')" />
					</label>
                    <div class="custom-container"><xsl:value-of select="resource/description" disable-output-escaping="yes"/></div>
                </div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<span><xsl:value-of select="resource/building_name"/></span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<span><xsl:value-of select="resource/activity_name"/></span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resource Type')" />
					</label>
					<span><xsl:value-of select="php:function('lang', string(resource/type))"/></span>
				</div>
                <div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Documents')" />
					</label>
					<div class="pure-custom">
						<div id="documents_container" class="custom-container"></div>
						<div>
							<a class='button'>
								<xsl:attribute name="href"><xsl:value-of select="resource/add_document_link"/></xsl:attribute>
								<xsl:if test="resource/permission/write">
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
			<xsl:if test="resource/permission/write">
				<input type="button" class="pure-button pure-button-primary" name="edit">
					<xsl:attribute name="onclick">window.location.href='<xsl:value-of select="resource/edit_link"/>'</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Edit')" /></xsl:attribute>
				</input>
			</xsl:if>
			<input type="button" class="pure-button pure-button-primary" name="resource_schedule">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="resource/schedule_link"/>"</xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Resource schedule')" /></xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="resource/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Cancel')" /></xsl:attribute>	
			</input>			
		</div>
    </form>


    <!--/div-->



        

<script type="text/javascript">
    var resource_id = <xsl:value-of select="resource/id"/>;
    var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role', 'No records found')"/>;
        
    <![CDATA[
    var documentsURL = 'index.php?menuaction=booking.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
    var permissionsURL = 'index.php?menuaction=booking.uipermission_resource.index&sort=name&filter_object_id=' + resource_id + '&phpgw_return_as=json&';
    ]]>
    
    var colDefsDocuments = [
        {key: 'name', label: lang['Name'], formatter: genericLink},
        {key: 'category', label: lang['Category']},
        {key: 'actions', label: lang['Actions'], formatter: genericLink(lang['Edit'], lang['Delete'])}
    ];	
    var colDefsPermissions = [
        {key: 'subject_name', label: lang['Account']},
        {key: 'role', label: lang['Role']},
        {key: 'actions', label: lang['Actions'], formatter: genericLink(lang['Edit'], lang['Delete'])}
    ];
    createTable('documents_container',documentsURL,colDefsDocuments);
    createTable('permissions_container',permissionsURL,colDefsPermissions);

    /*
    $.get(documentsURL, function(documentData){
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
    
    
    $.get(permissionsURL, function(permissionData){
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
        
    <![CDATA[
        YAHOO.util.Event.addListener(window, "load", function() {

            var url = 'index.php?menuaction=booking.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
            var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'category', label: lang['Category']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
            YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);

            var url = 'index.php?menuaction=booking.uipermission_resource.index&sort=name&filter_object_id=' + resource_id + '&phpgw_return_as=json&';
            var colDefs = [{key: 'subject_name', label: lang['Account']}, {key: 'role', label: lang['Role']}, {key: 'actions', label: lang['Actions'], formatter: YAHOO.booking.formatGenericLink(lang['Edit'], lang['Delete'])}];
            YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
        });
    ]]>
*/
</script>

</xsl:template>
