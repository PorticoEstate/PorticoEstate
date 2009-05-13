<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/buildings_link"/></xsl:attribute>
                    Buildings
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="resource/building_link"/></xsl:attribute>
                    <xsl:value-of select="resource/building_name"/>
                </a>
            </li>
            <li>Resources</li>
            <li>
                <a href="">
                    <xsl:value-of select="resource/name"/>
                </a>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>

        <dl class="proplist">
            <dt>Building</dt>
            <dd><xsl:value-of select="resource/building_name"/></dd>
            <dt>Name</dt>
            <dd><xsl:value-of select="resource/name"/></dd>
            <dt>Description</dt>
            <dd><xsl:value-of select="resource/description"/></dd>
            <dt>Activity</dt>
            <dd><xsl:value-of select="resource/activity_name"/></dd>
        </dl>

        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="resource/edit_link"/></xsl:attribute>
            Edit
        </a>
        <br/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="resource/schedule_link"/></xsl:attribute>
            Resource schedule
        </a>
        

        <h4>Equipment</h4>
        <div id="equipment_container"/>
		
		<h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
        <div id="documents_container"/>
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="resource/add_document_link"/></xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Add Document')" />
		</a>
    </div>

<script type="text/javascript">
var resource_id = <xsl:value-of select="resource/id"/>;
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiequipment.index&sort=name&filter_resource_id=' + resource_id + '&phpgw_return_as=json&';
    var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('equipment_container', url, colDefs);

	var url = 'index.php?menuaction=booking.uidocument_resource.index&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}, {key: 'category', label: 'Category'}, {key: 'actions', label: 'Actions', formatter: YAHOO.booking.formatGenericLink('Edit', 'Delete')}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
});
]]>
</script>

</xsl:template>
