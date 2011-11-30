<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
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
            <li>
                    <xsl:value-of select="resource/name"/>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
	

		<h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
        <div class="description"><xsl:value-of select="resource/description" disable-output-escaping="yes"/></div>

        <dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="resource/building_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
            <dd><xsl:value-of select="resource/activity_name"/></dd>
			<dt><xsl:value-of select="php:function('lang', 'Resource Type')" /></dt>
            <dd><xsl:value-of select="php:function('lang', string(resource/type))"/></dd>
        </dl>
		<div class="clr"/>
		<dl class="form-col">
			<xsl:if test="resource/campsites!=''">				
				<dt><label for="field_campsites"><xsl:value-of select="php:function('lang', 'Campsites')"/></label></dt>
				<dd><xsl:value-of select="resource/campsites"/></dd>
			</xsl:if>
			<xsl:if test="resource/bedspaces!=''">				
				<dt><label for="field_bedspaces"><xsl:value-of select="php:function('lang', 'Bedspaces')"/></label></dt>
				<dd><xsl:value-of select="resource/bedspaces"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/heating='')">				
				<dt><label for="field_heating"><xsl:value-of select="php:function('lang', 'Heating')"/></label></dt>
				<dd><xsl:value-of select="resource/heating"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/kitchen='')">				
				<dt><label for='field_kitchen'><xsl:value-of select="php:function('lang', 'Kitchen')"/></label></dt>
				<dd><xsl:value-of select="resource/kitchen"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<xsl:if test="not(resource/water='')">				
				<dt><label for="field_water"><xsl:value-of select="php:function('lang', 'Water')"/></label></dt>
				<dd><xsl:value-of select="resource/water"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/location='')">				
				<dt><label for="field_location"><xsl:value-of select="php:function('lang', 'Locality')"/></label></dt>
				<dd><xsl:value-of select="resource/location"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/communication='')">				
				<dt><label for='field_communication'><xsl:value-of select="php:function('lang', 'Communication')"/></label></dt>
				<dd><xsl:value-of select="resource/communication"/></dd>
			</xsl:if>
			<xsl:if test="not(resource/usage_time='')">				
				<dt><label for='field_usage_time'><xsl:value-of select="php:function('lang', 'Usage time')"/></label></dt>
				<dd><xsl:value-of select="resource/usage_time"/></dd>
			</xsl:if>
		</dl>
	
		<div class='clr'/>
		<dl class="form-col">
			<xsl:if test="resource/internal_cost!=''">				
				<dt><label for="field_internal_cost"><xsl:value-of select="php:function('lang', 'Internal cost')"/></label></dt>
				<dd><xsl:value-of select="resource/internal_cost"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<xsl:if test="resource/external_cost!=''">				
				<dt><label for="field_external_cost"><xsl:value-of select="php:function('lang', 'External cost')"/></label></dt>
				<dd><xsl:value-of select="resource/external_cost"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<xsl:if test="resource/cost_type!=''">				
				<dt><label for="field_cost_type"><xsl:value-of select="php:function('lang', 'Cost type')"/></label></dt>
	            <dd><xsl:value-of select="php:function('lang', string(resource/cost_type))"/></dd>
			</xsl:if>
		</dl>
		<div class='clr'/>
		
		<div class="form-buttons">
			<xsl:if test="resource/permission/write">
		        <button>
		            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="resource/edit_link"/>"</xsl:attribute>
		            <xsl:value-of select="php:function('lang', 'Edit')" />
		        </button>
			</xsl:if>
	        <button>
	            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="resource/schedule_link"/>"</xsl:attribute>
	            <xsl:value-of select="php:function('lang', 'Resource schedule')" />
	        </button>
    	</div>

		<h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
        <div id="documents_container"/>
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="resource/add_document_link"/></xsl:attribute>
			<xsl:if test="resource/permission/write">
				<xsl:value-of select="php:function('lang', 'Add Document')" />
			</xsl:if>
		</a>
		
		<h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
        <div id="permissions_container"/>
    </div>

<script type="text/javascript">
var resource_id = <xsl:value-of select="resource/id"/>;
	var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Edit', 'Delete', 'Account', 'Role')"/>;
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
</script>

</xsl:template>
