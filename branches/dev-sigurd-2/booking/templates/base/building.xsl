<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
       <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/building_link"/></xsl:attribute>
                    <xsl:value-of select="building/name"/>
                </a>
            </li>
        </ul>

        <xsl:call-template name="msgbox"/>
	<xsl:if test="building/active=0">
		<div id="inactive">
		<form method='POST' action=''>
		<xsl:value-of select="php:function('lang', 'This object has been inactivated')" />
		<input type="hidden" name="activate_id">
		<xsl:attribute name="value"><xsl:value-of select="building/id"/></xsl:attribute>
		</input>
		<input type="hidden" name="status" value="1" />
		<input type="hidden" name="menuaction" value="booking.uibuilding.show" />
		<input type="submit" name="submit" id="activate-button">
		<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Re-activate')"/></xsl:attribute>
		</input>
		</form>
		</div>
	</xsl:if>
	<xsl:if test="building/active=1">
		<div id="active">
		<form method='POST' action=''>
		<input type="hidden" name="activate_id">
		<xsl:attribute name="value"><xsl:value-of select="building/id"/></xsl:attribute>
		</input>
		<input type="hidden" name="status" value="0" />
		<input type="hidden" name="menuaction" value="booking.uibuilding.show" />
		<input type="submit" name="submit" id="inactivate-button">
		<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Inactivate')"/></xsl:attribute>
		</input>
		</form>
		</div>
	</xsl:if>
        <h4>
                    <xsl:value-of select="php:function('lang', 'Description')" /></h4>
        <div class="description"><xsl:value-of select="building/description"/></div>


        <dl class="proplist-col">
            <dt>
                    <xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
            <dd><a>
                <xsl:attribute name="href"><xsl:value-of select="building/homepage"/></xsl:attribute>
                <xsl:value-of select="building/homepage"/></a>
            </dd>
            <dt>
                    <xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><a>
                <xsl:attribute name="href">mailto:<xsl:value-of select="building/email"/></xsl:attribute>
                <xsl:value-of select="building/email"/></a>
            </dd>
        </dl>
        <dl class="proplist-col">
            <dt>
                    <xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
            <dd><xsl:value-of select="building/phone"/></dd>
            <dt>
                    <xsl:value-of select="php:function('lang', 'Address')" /></dt>
            <dd class="address"><xsl:value-of select="building/address"/></dd>
        </dl>
        <div class="clr"/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="building/edit_link"/></xsl:attribute>
            
                    <xsl:value-of select="php:function('lang', 'Edit')" />
        </a>
        <br/>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="building/schedule_link"/></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Building schedule')" />
        </a>
        
        <h4><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h4>
        <div id="resources_container"/>

		<h4><xsl:value-of select="php:function('lang', 'Documents')" /></h4>
        <div id="documents_container"/>
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="building/add_document_link"/></xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Add Document')" />
		</a>
		
		<h4><xsl:value-of select="php:function('lang', 'Permissions')" /></h4>
        <div id="permissions_container"/>
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="building/add_permission_link"/></xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Add Permission')" />
		</a>
    </div>

<script type="text/javascript">
var building_id = <xsl:value-of select="building/id"/>;
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&filter_building_id=' + building_id + '&phpgw_return_as=json&';
    var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);

	var url = 'index.php?menuaction=booking.uidocument_building.index&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'name', label: 'Name', formatter: YAHOO.booking.formatLink}, {key: 'category', label: 'Category'}, {key: 'actions', label: 'Actions', formatter: YAHOO.booking.formatGenericLink('Edit', 'Delete')}];
	YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
	
	var url = 'index.php?menuaction=booking.uipermission_building.index&sort=name&filter_object_id=' + building_id + '&phpgw_return_as=json&';
	var colDefs = [{key: 'subject_name', label: 'Account'}, {key: 'role', label: 'Role'}, {key: 'actions', label: 'Actions', formatter: YAHOO.booking.formatGenericLink('Edit', 'Delete')}];
	YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
});

]]>
</script>

</xsl:template>
