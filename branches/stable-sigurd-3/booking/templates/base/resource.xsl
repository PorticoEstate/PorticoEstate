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
                <a href="">
                    <xsl:value-of select="resource/name"/>
                </a>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="resource/building_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Resource')" /></dt>
            <dd><xsl:value-of select="resource/name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
            <dd><xsl:value-of select="resource/description"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
            <dd><xsl:value-of select="resource/activity_name"/></dd>
        </dl>

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
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {

	var url = 'index.php?menuaction=booking.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}, {key: 'category', label: 'Category'}, {key: 'actions', label: 'Actions', formatter: YAHOO.booking.formatGenericLink('Edit', 'Delete')}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=booking.uipermission_resource.index&sort=name&filter_object_id=' + resource_id + '&phpgw_return_as=json&';
]]>
	var colDefs = [{key: 'subject_name', label: '<xsl:value-of select="php:function('lang', 'Account')" />'}, {key: 'role', label: '<xsl:value-of select="php:function('lang', 'Role')" />'}, {key: 'actions', label: '<xsl:value-of select="php:function('lang', 'Actions')" />', formatter: YAHOO.booking.formatGenericLink('<xsl:value-of select="php:function('lang', 'Edit')" />', '<xsl:value-of select="php:function('lang', 'Delete')" />')}];
    <![CDATA[
	YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
});
]]>
</script>

</xsl:template>
