<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
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
        </ul>

        <xsl:call-template name="msgbox"/>

        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'From')" /></dt>
            <dd><xsl:value-of select="season/from_"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'To')" /></dt>
            <dd><xsl:value-of select="season/to_"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="season/building_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Status')" /></dt>
            <dd><xsl:value-of select="season/status"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Resources')" /></dt>
            <dd><div id="resources_container"/></dd>
        </dl>
        <div class="form-buttons">
	        <button>
	            <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/edit_link">"</xsl:value-of>"</xsl:attribute>
	            <xsl:value-of select="php:function('lang', 'Edit')" />
	        </button>
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
		<a class='button'>
			<xsl:attribute name="href"><xsl:value-of select="resource/add_permission_link"/></xsl:attribute>
			<xsl:value-of select="php:function('lang', 'Add Permission')" />
		</a>
    </div>



	<script type="text/javascript">
		var season_id = <xsl:value-of select="season/id"/>;
	    var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
	YAHOO.util.Event.addListener(window, "load", function() {
	    <![CDATA[
	    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
	        ]]>
	    var colDefs = [{key: 'name', label: '<xsl:value-of select="php:function('lang', 'Name')" />', formatter: YAHOO.booking.formatLink}];
	    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
	
		<![CDATA[ var url = 'index.php?menuaction=booking.uipermission_season.index&sort=name&filter_object_id=' + season_id + '&phpgw_return_as=json&'; ]]>
		var colDefs = [{key: 'subject_name', label: '<xsl:value-of select="php:function('lang', 'Account')" />'}, {key: 'role', label: '<xsl:value-of select="php:function('lang', 'Role')" />'}, {key: 'actions', label: '<xsl:value-of select="php:function('lang', 'Actions')" />', formatter: YAHOO.booking.formatGenericLink('Edit', 'Delete')}];
		YAHOO.booking.inlineTableHelper('permissions_container', url, colDefs);
	});
	</script>

</xsl:template>
